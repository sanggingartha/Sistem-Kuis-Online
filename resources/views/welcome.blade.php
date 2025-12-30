<x-app-layout>
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>WanaQuiz - Kuis Interaktif</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .gradient-purple {
                background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }

            .float-animation {
                animation: float 3s ease-in-out infinite;
            }

            @keyframes pulse-scale {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }

            .pulse-hover:hover {
                animation: pulse-scale 0.6s ease-in-out;
            }
        </style>
    </head>

    <body class="antialiased font-sans">
        <!-- Hero Section -->
        <main class="relative bg-cover bg-center bg-no-repeat"
            style="background-image: url('{{ asset('images/murid-3.jpg') }}');">
            <div class="absolute inset-0 bg-purple-900/40"></div>

            <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-40 flex items-center">
                <div class="lg:w-1/2 text-white">
                    <h2 class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                        Kuis <span class="text-yellow-400">Interaktif</span> Cerdas untuk Pembelajaran Modern
                    </h2>
                    <p class="text-xl lg:text-2xl text-white/95 mb-8 font-medium">
                        Langkah baru menuju pembelajaran yang lebih cerdas dan interaktif.
                    </p>
                    @auth
                        @if (auth()->user()->role === 'pengajar')
                            <!-- Pengajar → Dashboard Filament -->
                            <a href="/pengajar"
                                class="bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold px-12 py-5 rounded-full text-xl transition duration-300 transform hover:scale-105 shadow-2xl pulse-hover">
                                BUAT KUIS
                            </a>
                        @else
                            <!-- Siswa → Input Kode Kuis -->
                            <a href="{{ route('kode.kuis') }}"
                                class="bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold px-12 py-5 rounded-full text-xl transition duration-300 transform hover:scale-105 shadow-2xl pulse-hover">
                                MULAI KUIS
                            </a>
                        @endif
                    @else
                        <!-- Belum login → Register -->
                        <a href="{{ route('register') }}"
                            class="bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold px-12 py-5 rounded-full text-xl transition duration-300 transform hover:scale-105 shadow-2xl pulse-hover">
                            DAFTAR GRATIS
                        </a>
                    @endauth
                </div>
            </div>
        </main>

        <!-- Features Section -->
        <section class="bg-gray-50 py-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h3 class="text-4xl font-bold text-purple-700 mb-4">Mengapa Memilih WanaQuiz?</h3>
                    <p class="text-xl text-gray-600">Platform pembelajaran modern dengan berbagai keunggulan</p>
                </div>

                <div class="grid md:grid-cols-3 gap-10">
                    <div class="bg-white p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border-2 border-transparent hover:border-purple-200 group">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl flex items-center justify-center mb-6 float-animation group-hover:scale-110 transition-transform">
                            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold text-purple-700 mb-3">Pembelajaran Interaktif</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">Metode belajar yang menyenangkan dan efektif dengan kuis interaktif</p>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border-2 border-transparent hover:border-purple-200 group">
                        <div class="w-20 h-20 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-2xl flex items-center justify-center mb-6 float-animation group-hover:scale-110 transition-transform" style="animation-delay: 0.2s;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <rect x="5" y="8" width="14" height="10" rx="2" ry="2"></rect>
                                <circle cx="9" cy="13" r="1.5"></circle>
                                <circle cx="15" cy="13" r="1.5"></circle>
                                <rect x="2" y="10" width="3" height="6" rx="1" ry="1"></rect>
                                <rect x="19" y="10" width="3" height="6" rx="1" ry="1"></rect>
                                <line x1="12" y1="4" x2="12" y2="8"></line>
                                <circle cx="12" cy="3" r="1"></circle>
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold text-purple-700 mb-3">Terintegrasi dengan AI</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">Jawaban kamu akan dinilai secara langsung oleh sistem AI dengan cepat
                            dan akurat</p>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border-2 border-transparent hover:border-purple-200 group">
                        <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center mb-6 float-animation group-hover:scale-110 transition-transform" style="animation-delay: 0.4s;">
                            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold text-purple-700 mb-3">Terpercaya</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">Dipercaya oleh ribuan siswa dan guru di seluruh Indonesia</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="gradient-purple py-20">
            <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
                <h3 class="text-4xl lg:text-5xl font-bold text-white mb-6">
                    Siap Memulai Pembelajaran yang Lebih Interaktif?
                </h3>
                <p class="text-xl text-white/95 mb-10">
                    Bergabunglah dengan ribuan pengguna yang telah merasakan pengalaman belajar yang berbeda
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        @if (auth()->user()->role === 'pengajar')
                            <!-- Pengajar → Dashboard Filament -->
                            <a href="/pengajar"
                                class="bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold px-12 py-5 rounded-full text-xl transition duration-300 transform hover:scale-105 shadow-2xl pulse-hover">
                                BUAT KUIS
                            </a>
                        @else
                            <!-- Siswa → Input Kode Kuis -->
                            <a href="{{ route('kode.kuis') }}"
                                class="bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold px-12 py-5 rounded-full text-xl transition duration-300 transform hover:scale-105 shadow-2xl pulse-hover">
                                MULAI KUIS
                            </a>
                        @endif
                    @else
                        <!-- Belum login → Register -->
                        <a href="{{ route('register') }}"
                            class="bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold px-12 py-5 rounded-full text-xl transition duration-300 transform hover:scale-105 shadow-2xl pulse-hover">
                            DAFTAR GRATIS
                        </a>
                    @endauth
                    <a href="{{ route('about') }}"
                        class="bg-white hover:bg-gray-100 text-purple-700 font-bold px-12 py-5 rounded-full text-xl transition duration-300 transform hover:scale-105 shadow-2xl">
                        PELAJARI LEBIH LANJUT
                    </a>
                </div>
            </div>
        </section>
    </body>

    </html>
</x-app-layout>