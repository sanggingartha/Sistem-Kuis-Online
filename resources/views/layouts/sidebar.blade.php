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
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="flex h-screen bg-gray-100">
        <!-- Overlay untuk mobile -->
        <div id="sidebar-overlay"
            class="fixed inset-0 bg-black bg-opacity-40 hidden z-30 transition-opacity duration-300 lg:hidden"></div>

        <!-- Sidebar Component -->
        <div id="sidebar"
            class="w-64 bg-white shadow-lg flex flex-col flex-shrink-0 transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out fixed lg:relative inset-y-0 right-0 z-40">
            <!-- Header Sidebar -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-black text-purple-700">WANAQUIZ</h2>
            </div>

            <!-- Navigation Menu -->
            <div class="flex flex-col p-4 space-y-2 flex-1">
                <!-- Mulai Kuis -->
                <a href="{{ route('kode.kuis') }}"
                    class="flex items-center text-gray-800 font-medium transition duration-300 py-3 px-3 rounded-lg
                    {{ request()->routeIs('kode.kuis') ? 'bg-purple-50 text-purple-700' : 'hover:bg-gray-50 hover:text-purple-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                    </svg>
                    Mulai Kuis
                </a>

                <!-- Riwayat -->
                <a href="{{ route('riwayat.kuis') }}"
                    class="flex items-center text-gray-800 hover:text-purple-700 font-medium transition duration-300 py-3 px-3 rounded-lg {{ request()->routeIs('quiz.history') ? 'bg-purple-50 text-purple-700' : 'hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat
                </a>
            </div>

            <!-- User Section dengan Dropdown -->
            <div class="border-t border-gray-200 p-4">
                <div class="relative">
                    <!-- User Button -->
                    <button id="user-menu-btn"
                        class="flex items-center w-full p-2 text-left hover:bg-gray-50 rounded-lg transition duration-200">
                        <!-- Avatar/User Icon -->
                        <div
                            class="flex items-center justify-center w-8 h-8 bg-purple-600 text-white rounded-full flex-shrink-0">
                            <span class="text-sm font-semibold">
                                {{ Auth::user() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'U' }}
                            </span>
                        </div>

                        <!-- User Info -->
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ Auth::user()->name ?? 'User' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ Auth::user()->email ?? 'user@example.com' }}
                            </p>
                        </div>

                        <!-- Dropdown Arrow -->
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div id="user-dropdown"
                        class="absolute bottom-full left-0 mb-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible transform scale-95 transition-all duration-200 origin-bottom-right z-50">

                        <!-- Edit Profile -->
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition duration-200 border-b border-gray-100">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Edit Profile
                        </a>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition duration-200">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow flex-shrink-0">
                <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-800 truncate">
                        @if (request()->routeIs('kode.kuis'))
                            Masuk Kuis
                        @elseif (request()->routeIs('kuis.mulai'))
                            Mengerjakan Kuis
                        @elseif (request()->routeIs('kuis.result-kuis'))
                            Hasil Kuis
                        @elseif (request()->routeIs('kuis.riwayat-kuis'))
                            Riwayat Kuis
                        @elseif (request()->routeIs('kuis.lihat-nilai'))
                            Lihat Nilai
                        @else
                            Riwayat Kuis
                        @endif
                    </h2>

                    <!-- Mobile menu button di kanan -->
                    <div class="flex items-center gap-3">
                        <button id="mobile-sidebar-toggle"
                            class="lg:hidden text-gray-900 hover:text-purple-700 focus:outline-none">
                            <svg id="menu-open-icon" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg id="menu-close-icon" class="h-7 w-7 hidden" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts

    <!-- JavaScript untuk Responsive dan Dropdown -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuBtn = document.getElementById('user-menu-btn');
            const userDropdown = document.getElementById('user-dropdown');
            const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const menuOpenIcon = document.getElementById('menu-open-icon');
            const menuCloseIcon = document.getElementById('menu-close-icon');

            // Toggle dropdown user
            if (userMenuBtn && userDropdown) {
                userMenuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userDropdown.classList.toggle('opacity-0');
                    userDropdown.classList.toggle('invisible');
                    userDropdown.classList.toggle('scale-95');
                });

                // Tutup dropdown ketika klik di luar
                document.addEventListener('click', (e) => {
                    if (!userDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
                        userDropdown.classList.add('opacity-0', 'invisible', 'scale-95');
                    }
                });

                // Prevent dropdown dari menutup ketika klik di dalam dropdown
                userDropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }

            // Mobile sidebar toggle dengan animasi smooth dari kanan
            if (mobileSidebarToggle && sidebar && overlay) {
                mobileSidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('translate-x-full');
                    overlay.classList.toggle('hidden');
                    menuOpenIcon.classList.toggle('hidden');
                    menuCloseIcon.classList.toggle('hidden');

                    // Prevent body scroll ketika sidebar terbuka
                    if (!sidebar.classList.contains('translate-x-full')) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                });

                // Close sidebar ketika klik overlay
                overlay.addEventListener('click', () => {
                    sidebar.classList.add('translate-x-full');
                    overlay.classList.add('hidden');
                    menuOpenIcon.classList.remove('hidden');
                    menuCloseIcon.classList.add('hidden');
                    document.body.style.overflow = '';
                });

                // Close sidebar ketika klik link di sidebar (mobile)
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 1024) {
                            sidebar.classList.add('translate-x-full');
                            overlay.classList.add('hidden');
                            menuOpenIcon.classList.remove('hidden');
                            menuCloseIcon.classList.add('hidden');
                            document.body.style.overflow = '';
                        }
                    });
                });

                // Close sidebar ketika tekan escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !sidebar.classList.contains('translate-x-full')) {
                        sidebar.classList.add('translate-x-full');
                        overlay.classList.add('hidden');
                        menuOpenIcon.classList.remove('hidden');
                        menuCloseIcon.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    // Desktop: pastikan sidebar visible dan reset body scroll
                    sidebar.classList.remove('translate-x-full');
                    overlay.classList.add('hidden');
                    menuOpenIcon.classList.remove('hidden');
                    menuCloseIcon.classList.add('hidden');
                    document.body.style.overflow = '';
                } else {
                    // Mobile: pastikan sidebar hidden
                    if (!sidebar.classList.contains('translate-x-full')) {
                        sidebar.classList.add('translate-x-full');
                        overlay.classList.add('hidden');
                    }
                }
            });

            // Initialize state untuk mobile
            if (window.innerWidth < 1024) {
                sidebar.classList.add('translate-x-full');
            }
        });
    </script>
</body>

</html>
