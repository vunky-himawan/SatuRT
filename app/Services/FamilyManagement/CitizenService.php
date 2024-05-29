<?php

namespace App\Services\FamilyManagement;

use App\Models\Penduduk;
use App\Services\ImageManager\ImageService;
use App\Services\Interfaces\DatatablesInterface;
use App\Services\Interfaces\RecordServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CitizenService implements RecordServiceInterface, DatatablesInterface
{
    public static function all(): Collection
    {
        return Penduduk::all();
    }

    public static function find(string $id): Penduduk
    {
        return Penduduk::findOrFail($id);
    }

    public static function create(Request $request): Collection | Model
    {
        $request->merge(['status_kehidupan' => $request->has('status_kehidupan') ? $request->status_kehidupan : 'Hidup']);
        $existingPenduduk = Penduduk::withTrashed()->where('nik', $request->nik)->first();

        if ($existingPenduduk) {
            if ($existingPenduduk->trashed()) {
                $existingPenduduk->restore();
                $existingPenduduk->umkm()->withTrashed()->restore();
                $existingPenduduk->informasi()->withTrashed()->restore();
                $existingPenduduk->fill($request->all());
                $existingPenduduk->save();
                return $existingPenduduk;
            } else {
                // Jika penduduk ditemukan dan tidak dihapus, NIK sudah digunakan
                throw new \Exception('NIK sudah digunakan oleh penduduk lain.');
            }
        }

        // Penanganan upload gambar
        if ($request->hasFile('images')) {
            try {
                $imageName = ImageService::uploadFile('storage_ktp', $request);
                $request->merge(['foto_ktp' => $imageName]);
            } catch (\Exception $e) {
                throw new \Exception('Error uploading image: ' . $e->getMessage());
            }
        }

        return Penduduk::create($request->only([
            'kartu_keluarga_id',
            'nik',
            'nama',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'golongan_darah',
            'agama',
            'status_hubungan_dalam_keluarga',
            'status_perkawinan',
            'kota',
            'kecamatan',
            'desa',
            'nomor_rt',
            'nomor_rw',
            'status_kehidupan',
            'pekerjaan',
            'pendidikan_terakhir',
            'status_penduduk',
            'foto_ktp'
        ]));
    }

    public static function update(string $id, Request $request): Collection | Model
    {
        $request->merge(['status_kehidupan' => $request->has('status_kehidupan') ? $request->status_kehidupan : 'Hidup']);

        $citizen = Penduduk::findOrFail($id);
        if ($request->hasFile('images')) {
            $imageName = ImageService::uploadFile('storage_ktp', $request);
            $request->merge(['foto_ktp' => $imageName]);
            if ($citizen && $citizen->foto_ktp) {
                ImageService::deleteFile('storage_ktp', $citizen->foto_ktp);
            }
        } else {
            $request->merge(['foto_ktp' => $citizen->foto_ktp]);
        }

        if ($request->status_hubungan_dalam_keluarga !== 'Kepala Keluarga' && Penduduk::where('kartu_keluarga_id', $citizen->kartu_keluarga_id)->where('status_hubungan_dalam_keluarga', 'Kepala Keluarga')->count() === 1 && $citizen->status_hubungan_dalam_keluarga === 'Kepala Keluarga') {
            throw new \Exception('Kartu Keluarga harus memiliki kepala keluarga');
        }

        if ($request->status_hubungan_dalam_keluarga === 'Kepala Keluarga' && $citizen->status_hubungan_dalam_keluarga !== 'Kepala Keluarga') {
            $leadCitizen = Penduduk::where('kartu_keluarga_id', $citizen->kartu_keluarga_id)->where('status_hubungan_dalam_keluarga', 'Kepala Keluarga')->first();
            $leadCitizen->update(['status_hubungan_dalam_keluarga' => null]);
        }



        $citizen->update($request->only([
            'kartu_keluarga_id',
            'nik',
            'nama',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'golongan_darah',
            'agama',
            'status_hubungan_dalam_keluarga',
            'status_perkawinan',
            'kota',
            'kecamatan',
            'desa',
            'nomor_rt',
            'nomor_rw',
            'status_kehidupan',
            'pekerjaan',
            'pendidikan_terakhir',
            'status_penduduk',
            'foto_ktp'
        ]));
        return $citizen;
    }

    public static function delete(string $id): bool
    {
        try {
            $citizen = Penduduk::with(['kartuKeluarga', 'umkm', 'informasi'])->findOrFail($id);

            // Memeriksa jika status hubungan dalam keluarga adalah 'Kepala Keluarga'
            if ($citizen->status_hubungan_dalam_keluarga === 'Kepala Keluarga' && Penduduk::where('kartu_keluarga_id', $citizen->kartuKeluarga->kartu_keluarga_id)->count() > 1) {
                throw new \Exception('Hapus anggota keluarga terlebih dahulu');
            } else if (Penduduk::where('kartu_keluarga_id', $citizen->kartuKeluarga->kartu_keluarga_id)->count() === 1) {
                $citizen->kartuKeluarga->delete();
            }

            // Memeriksa dan menghapus user terkait jika ada
            if ($citizen->user_id !== null) {
                $citizen->user->delete();
            }

            // Menghapus relasi UMKM terkait jika ada
            if ($citizen->umkm) {
                foreach ($citizen->umkm as $umkm) {
                    $umkm->delete();
                }
            }

            // Menghapus relasi Informasi terkait jika ada
            if ($citizen->informasi) {
                foreach ($citizen->informasi as $informasi) {
                    $informasi->delete();
                }
            }

            // Menghapus data penduduk
            $citizen->delete();
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getDatatable($id = null): Collection
    {
        $role = Auth::user()->role->role_name;
        return $role === "Ketua RT" ? Penduduk::select(
            'penduduk_id',
            'nama',
            'nik',
            'jenis_kelamin',
            'status_hubungan_dalam_keluarga'
        )->where('kartu_keluarga_id', $id)->get()
            : Penduduk::select(
                'penduduk_id',
                'nama',
                'nik',
                'jenis_kelamin',
                'status_hubungan_dalam_keluarga'
            )->where('kartu_keluarga_id', $id)
            ->where('status_kehidupan', 'Hidup')->get();
    }
}
