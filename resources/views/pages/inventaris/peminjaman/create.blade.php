<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :list="$breadcrumb['list']" :url="$breadcrumb['url']" />
    </x-slot>

    <div class="p-6 lg:px-14 gap-y-5 mx-auto max-w-screen-2xl md:p-6 2xl:p-10">
        <div class="p-6 rounded-xl bg-white-snow">
            @if (session('success'))
                <div role="alert" class="rounded border-s-4 border-green-500 bg-white p-4">
                    <div class="flex items-start gap-4">
                        <span class="text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>

                        <div class="flex-1">
                            <strong class="block font-medium text-gray-900">Berhasil</strong>
                            <p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="rounded border-s-4 border-red-500 bg-red-50 p-4">
                    <div class="flex items-center gap-2 text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                        </svg>
                        <strong class="block font-medium">Terjadi Kesalahan</strong>
                    </div>
                    <p class="mt-2 text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Content Start --}}
            <section>
                <div class="bg-blue-gray p-5 rounded-md">
                    <h1 class="font-bold md:text-2xl text-xl">Tambah Peminjaman Inventaris</h1>
                </div>
            </section>
            {{-- End Header --}}

            {{-- Form --}}
            <section>
                <form method="POST" action="{{ route('inventaris.peminjaman.store') }}" class="px-5" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 mt-5">
                        <div>
                            <label for="inventaris_id" class="py-2 after:content-['*'] after:ml-0.5 after:text-red-500 font-semibold text-navy-night">Inventaris</label>
                            <select class="form-control w-full rounded-md placeholder:text-xs border-gray-200 p-3 text-sm" id="inventaris_id" name="inventaris_id" required>
                                <option value="" disabled selected>Pilih Inventaris</option>
                                @foreach ($inventaris as $item)
                                    <option value="{{ $item->inventaris_id }}">{{ $item->nama_inventaris }}</option>
                                @endforeach
                            </select>
                            @error('inventaris_id')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div>
                            <label for="penduduk_id" class="py-2 after:content-['*'] after:ml-0.5 after:text-red-500 font-semibold text-navy-night">Peminjam</label>
                            <select class="form-control w-full rounded-md placeholder:text-xs border-gray-200 p-3 text-sm" id="penduduk_id" name="penduduk_id" required>
                                <option value="" disabled selected>Pilih Peminjam</option>
                                @foreach ($penduduk as $item)
                                    <option value="{{ $item->penduduk_id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            @error('penduduk_id')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-5">
                        <div>
                            <label for="jumlah" class="py-2 after:content-['*'] after:ml-0.5 after:text-red-500 font-semibold text-navy-night">Jumlah</label>
                            <input class="placeholder-gray-300 w-full rounded-md placeholder:text-xs border-gray-200 p-3 text-sm" placeholder="Jumlah" type="number" id="jumlah" name="jumlah" required />
                            @error('jumlah')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="kondisi" class="after:content-['*'] after:ml-0.5 after:text-red-500 font-semibold text-navy-night w-fit">Kondisi</label>
                            @error('kondisi')
                                <small class="text-red-500 text-xs py-3">{{ $message }}</small>
                            @enderror
                            <input type="text" name="kondisi" id="kondisi" value="Normal" disabled class="placeholder-gray-300 w-full rounded-md placeholder:text-xs border-gray-200 p-3 text-sm">
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="tanggal_pinjam" class="after:content-['*'] after:ml-0.5 after:text-red-500 font-semibold text-navy-night w-fit">Tanggal Pinjam</label>
                        @error('tanggal_pinjam')
                            <small class="text-red-500 text-xs py-3">{{ $message }}</small>
                        @enderror
                        <input class="placeholder-gray-300 w-full rounded-md placeholder:text-xs border-gray-200 p-3 text-sm" placeholder="Tanggal Pinjam" type="date" id="tanggal_pinjam" name="tanggal_pinjam" value="{{ now()->format('Y-m-d') }}" />
                    </div>
                    

                    <div class="mt-10 flex gap-x-5">
                        <button type="submit" class="bg-azure-blue text-white-snow text-sm px-4 py-2 rounded-md flex justify-center items-center gap-x-3">
                            <p>Simpan</p>
                        </button>
                        <a href="{{ route('inventaris.peminjaman.index') }}" class="border border-navy-night/50 rounded-md px-4 py-2 text-sm flex justify-center items-center gap-x-3">
                            <p>Kembali</p>
                        </a>
                    </div>
                </form>
            </section>
            {{-- End Form --}}
        </div>
    </div>
</x-app-layout>
