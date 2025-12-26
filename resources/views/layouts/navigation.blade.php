<!-- Top Banner -->
<div class="bg-purple-700 text-white text-center py-2 px-4">
    <div class="flex items-center justify-center gap-4 flex-wrap">
        <span class="font-semibold">DISKON 35% UNTUK BULAN OKTOBER</span>
        <a href="#"
            class="bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold px-3 py-1 rounded-full text-sm transition duration-300">
            DAPATKAN SEKARANG </a>
    </div>
</div>

<!-- Navigation -->
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <h1 class="text-2xl font-black text-purple-700">WANAQUIZ</h1>
            </div>

            <!-- Desktop Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-gray-900 hover:text-purple-700 font-medium transition">Beranda</a>
                <a href="about" class="text-gray-900 hover:text-purple-700 font-medium transition">Tentang Kami</a>

                @if (Route::has('login'))
                    @auth
                        {{-- <a href="{{ url('/quiz-login') }}"
                            class="text-gray-900 hover:text-purple-700 font-medium transition">Mulai Kuis</a> --}}
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-900 hover:text-purple-700 font-medium transition">MASUK</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold px-5 py-1.5 rounded-full transition duration-300">
                                DAFTAR
                            </a>
                        @endif
                    @endauth
                @endif

                <!-- Language Dropdown -->
                <div class="relative">
                    <button id="lang-btn"
                        class="flex items-center gap-2 border border-gray-200 px-3 py-1.5 rounded-full hover:bg-gray-100 transition">
                        <img src="https://flagcdn.com/w20/id.png" alt="ID" class="w-5 h-4 rounded-sm">
                        <span class="text-sm font-medium">Bahasa</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="lang-menu"
                        class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible transform scale-95 transition-all duration-200">
                        <a href="#" class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100 transition">
                            <img src="https://flagcdn.com/w20/id.png" alt="ID" class="w-5 h-4 rounded-sm">
                            <span class="text-sm">Bahasa</span>
                        </a>
                        <a href="#" class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100 transition">
                            <img src="https://flagcdn.com/w20/us.png" alt="EN" class="w-5 h-4 rounded-sm">
                            <span class="text-sm">English</span>
                        </a>
                    </div>
                </div>

                <!-- Settings Dropdown -->
                {{-- <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700
                            hover:border-gray-300 focus:outline-none transition ease-in-out duration-150">
                                {{-- <div>{{ Auth::user()->name }}</div> --}}
                {{-- 
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06
                                        1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25
                                        8.29a.75.75 0 01-.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{-- {{ __('Profile') }} --}}
                {{-- </x-dropdown-link> --}}

                <!-- Authentication -->
                {{-- <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown> --}}
                {{-- </div> --}}
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center gap-3">
                <button id="lang-toggle"
                    class="flex items-center gap-1 border border-gray-200 px-2 py-1 rounded-full hover:bg-gray-100 transition">
                    <img src="https://flagcdn.com/w20/id.png" alt="ID" class="w-5 h-4 rounded-sm">
                    <span class="text-sm font-medium">ID</span>
                </button>

                <button id="menu-toggle" class="text-gray-900 hover:text-purple-700 focus:outline-none">
                    <svg id="menu-open" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="menu-close" class="h-7 w-7 hidden" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Overlay -->
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 hidden z-40 transition-opacity duration-300"></div>

<!-- Mobile Side Menu -->
<div id="side-menu"
    class="fixed top-0 right-0 w-64 h-full bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 flex flex-col justify-between">

    <div>
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-purple-700">WANAQUIZ</h2>
        </div>
        <div class="flex flex-col p-4 space-y-3">
            <a href="/" class="text-gray-800 hover:text-purple-700 font-medium">Beranda</a>
            <a href="about" class="text-gray-800 hover:text-purple-700 font-medium">Tentang Kami</a>
            {{-- @auth
                <a href="{{ url('/quiz-login') }}" class="text-gray-800 hover:text-purple-700 font-medium">Mulai Kuis</a>
            @endauth --}}
        </div>
    </div>

    <div class="p-4 border-t border-gray-200 space-y-3">
        @guest
            <a href="{{ route('login') }}"
                class="block text-center border border-purple-700 text-purple-700 hover:bg-purple-700 hover:text-white font-semibold py-2 rounded-lg transition duration-300">
                MASUK
            </a>
            <a href="{{ route('register') }}"
                class="block text-center bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold py-2 rounded-lg transition duration-300">
                DAFTAR
            </a>
        @endguest
    </div>
</div>

<!-- JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('menu-toggle');
        const menuOpen = document.getElementById('menu-open');
        const menuClose = document.getElementById('menu-close');
        const sideMenu = document.getElementById('side-menu');
        const overlay = document.getElementById('overlay');

        // Hamburger toggle
        toggleBtn.addEventListener('click', () => {
            sideMenu.classList.toggle('translate-x-full');
            overlay.classList.toggle('hidden');
            menuOpen.classList.toggle('hidden');
            menuClose.classList.toggle('hidden');
        });

        overlay.addEventListener('click', () => {
            sideMenu.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            menuOpen.classList.remove('hidden');
            menuClose.classList.add('hidden');
        });

        // Dropdown Bahasa
        const langBtn = document.getElementById('lang-btn');
        const langMenu = document.getElementById('lang-menu');

        langBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            langMenu.classList.toggle('opacity-0');
            langMenu.classList.toggle('invisible');
            langMenu.classList.toggle('scale-95');
        });

        // Klik di luar untuk menutup dropdown
        document.addEventListener('click', (e) => {
            if (!langMenu.contains(e.target) && !langBtn.contains(e.target)) {
                langMenu.classList.add('opacity-0', 'invisible', 'scale-95');
            }
        });
    });
</script>
