<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-poppins antialiased bg-soft-snow">
    <div x-data="{ sidebar: false }" class="h-screen flex overflow-hidden">
        @include('layouts.sidebar')

        <!-- Page Content -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">

            <!-- Page Heading -->
            @if (isset($breadcrumb))
                <header class="p-6 lg:px-14 lg:py-8 sticky top-0 z-[999] flex w-full bg-soft-snow max-lg:drop-shadow">
                    <div class="mx-auto flex items-center justify-between lg:hidden w-full">
                        <button @click.stop="sidebar = !sidebar" class="z-50 w-10 h-10">
                            <x-heroicon-c-bars-3-center-left />
                        </button>
                        <div class="lg:hidden" x-data="{ profile: false }">
                            <div class="h-14 w-14 rounded-full overflow-hidden" @click.stop="profile = !profile">
                                <img class="h-full w-full object-cover"
                                    src="{{ asset('assets/images/milad-fakurian-PGdW_bHDbpI-unsplash.jpg') }}"
                                    alt="">
                            </div>
                            <div class="absolute right-11 p-2" :class="profile ? 'block' : 'hidden'">
                                <div class="flex flex-col overflow-hidden rounded-lg ">
                                    <x-nav-button>
                                        {{ __('Profil') }}
                                    </x-nav-button>
                                    <x-nav-button :class="'text-red-500'">
                                        {{ __('Logout') }}
                                    </x-nav-button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="max-lg:hidden flex justify-between items-center w-full">
                        <div>
                            <h1 class="text-5xl">Hallo {{ Auth::user()->name ?? '' }}</h1>
                            {{ $breadcrumb }}
                        </div>
                        <div x-data="{ profile: false }" class="cursor-pointer">
                            <div @click.stop="profile = !profile" class="h-14 w-14 rounded-full overflow-hidden">
                                <img class="h-full w-full object-cover"
                                    src="{{ asset('assets/images/milad-fakurian-PGdW_bHDbpI-unsplash.jpg') }}"
                                    alt="">
                            </div>
                            <div class="absolute right-14 p-2" :class="profile ? 'block' : 'hidden'">
                                <div class="flex flex-col overflow-hidden rounded-lg ">
                                    <x-nav-button>
                                        {{ __('Profil') }}
                                    </x-nav-button>
                                    <x-nav-button :class="'text-red-500'">
                                        {{ __('Logout') }}
                                    </x-nav-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            @endif

            {{-- Content --}}
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
