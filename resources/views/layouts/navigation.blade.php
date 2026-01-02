{{-- <!-- Top Banner -->
<div class="bg-purple-700 text-white text-center py-2 px-4">
    <div class="flex items-center justify-center gap-4 flex-wrap">
        <span class="font-semibold">DISKON 35% UNTUK BULAN OKTOBER</span>
        <a href="#"
            class="bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold px-3 py-1 rounded-full text-sm transition duration-300">
            DAPATKAN SEKARANG </a>
    </div>
</div> --}}

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
                        {{-- User sudah login, tampilkan link kuis --}}
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

                <!-- User Dropdown (Desktop) -->
                @auth
                <div class="relative">
                    <button id="desktop-user-btn"
                        class="flex items-center gap-2 border border-gray-200 px-3 py-1.5 rounded-full hover:bg-gray-100 transition">
                        <div class="flex items-center justify-center w-6 h-6 bg-purple-600 text-white rounded-full">
                            <span class="text-xs font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="desktop-user-menu"
                        class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible transform scale-95 transition-all duration-200">
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition border-b border-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Edit Profile
                        </a>
                        <a href="{{ route('riwayat.kuis') }}" 
                            class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition duration-200 border-b border-gray-100">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 3a9 9 0 100 18 9 9 0 000-18z"></path>
                                </svg>
                                Riwayat Kuis
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center gap-3">
                @guest
                <button id="lang-toggle"
                    class="flex items-center gap-1 border border-gray-200 px-2 py-1 rounded-full hover:bg-gray-100 transition">
                    <img src="https://flagcdn.com/w20/id.png" alt="ID" class="w-5 h-4 rounded-sm">
                    <span class="text-sm font-medium">ID</span>
                </button>
                @endguest

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
        </div>
    </div>

    <!-- Mobile Bottom Section -->
    <div class="border-t border-gray-200 p-4">
        @auth
        <!-- User Section untuk Mobile -->
        <div class="relative">
            <button id="mobile-user-btn" 
                    class="flex items-center w-full p-2 text-left hover:bg-gray-50 rounded-lg transition duration-200">
                <!-- Avatar -->
                <div class="flex items-center justify-center w-8 h-8 bg-purple-600 text-white rounded-full flex-shrink-0">
                    <span class="text-sm font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
                
                <!-- User Info -->
                <div class="ml-3 flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        {{ Auth::user()->email }}
                    </p>
                </div>
                
                <!-- Dropdown Arrow -->
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Mobile User Dropdown -->
            <div id="mobile-user-dropdown" 
                 class="absolute bottom-full left-0 mb-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible transform scale-95 transition-all duration-200 origin-bottom-right z-50">
                
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition duration-200 border-b border-gray-100">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Edit Profile
                </a>
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition duration-200 border-b border-gray-100">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 3a9 9 0 100 18 9 9 0 000-18z"></path>
                    </svg>
                    Riwayat Kuis
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition duration-200">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
        @else
        <!-- Login/Register Buttons untuk Guest -->
        <div class="space-y-3">
            <a href="{{ route('login') }}"
                class="block text-center border border-purple-700 text-purple-700 hover:bg-purple-700 hover:text-white font-semibold py-2 rounded-lg transition duration-300">
                MASUK
            </a>
            <a href="{{ route('register') }}"
                class="block text-center bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold py-2 rounded-lg transition duration-300">
                DAFTAR
            </a>
        </div>
        @endauth
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

        // Dropdown Bahasa Desktop
        const langBtn = document.getElementById('lang-btn');
        const langMenu = document.getElementById('lang-menu');

        if (langBtn && langMenu) {
            langBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                langMenu.classList.toggle('opacity-0');
                langMenu.classList.toggle('invisible');
                langMenu.classList.toggle('scale-95');
            });

            document.addEventListener('click', (e) => {
                if (!langMenu.contains(e.target) && !langBtn.contains(e.target)) {
                    langMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                }
            });
        }

        // Dropdown User Desktop
        const desktopUserBtn = document.getElementById('desktop-user-btn');
        const desktopUserMenu = document.getElementById('desktop-user-menu');

        if (desktopUserBtn && desktopUserMenu) {
            desktopUserBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                desktopUserMenu.classList.toggle('opacity-0');
                desktopUserMenu.classList.toggle('invisible');
                desktopUserMenu.classList.toggle('scale-95');
            });

            document.addEventListener('click', (e) => {
                if (!desktopUserMenu.contains(e.target) && !desktopUserBtn.contains(e.target)) {
                    desktopUserMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                }
            });
        }

        // Dropdown User Mobile
        const mobileUserBtn = document.getElementById('mobile-user-btn');
        const mobileUserDropdown = document.getElementById('mobile-user-dropdown');

        if (mobileUserBtn && mobileUserDropdown) {
            mobileUserBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                mobileUserDropdown.classList.toggle('opacity-0');
                mobileUserDropdown.classList.toggle('invisible');
                mobileUserDropdown.classList.toggle('scale-95');
            });

            document.addEventListener('click', (e) => {
                if (!mobileUserDropdown.contains(e.target) && !mobileUserBtn.contains(e.target)) {
                    mobileUserDropdown.classList.add('opacity-0', 'invisible', 'scale-95');
                }
            });

            mobileUserDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
    });
</script>