<div class="min-h-screen bg-gradient-to-br py-12 px-4">
    <div class="w-full max-w-md mx-auto">
        <!-- Header dengan Icon -->
        <div class="text-center mb-6">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-800 rounded-full mb-3 shadow-lg">
                <svg class="w-8 h-8 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-purple-700 mb-2">Masukkan Kode Kuis</h2>
            <p class="text-gray-600 text-sm">Siap untuk memulai pembelajaran interaktif?</p>
        </div>

        <!-- Card Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <!-- Purple gradient header -->
            <div class="h-2 bg-gradient-to-r from-purple-600 to-purple-800"></div>

            <div class="p-6">
                <!-- Error Message -->
                @if (session()->has('error'))
                    <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-red-700 font-medium text-sm">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Input Kode Kuis -->
                <div class="mb-4">
                    <label for="kode_input" class="block text-sm font-semibold text-purple-700 mb-2">
                        Kode Kuis
                    </label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <input type="text" id="kode_input" wire:model.defer="kode_input"
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 text-base"
                                placeholder="Masukkan kode kuis" autofocus>
                        </div>

                        <!-- Tombol Scan QR -->
                        <button type="button" @click="window.dispatchEvent(new Event('open-scanner'))"
                            class="px-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-300 transform hover:scale-[1.02]"
                            title="Scan QR Code">
                            <div class="flex items-center justify-center h-full">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Button Cek Kode -->
                <button wire:click="loadKuis"
                    class="w-full bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-purple-300 transition duration-300 transform hover:scale-[1.02] mb-4">
                    CEK KODE
                </button>

                <!-- Detail Kuis (jika kode valid) -->
                @if ($kuis)
                    <div
                        class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border-2 border-purple-200 mb-4">
                        <div class="flex items-center mb-3">
                            <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-bold text-purple-900">{{ $kuis->nama_kuis }}</h3>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-purple-600 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-purple-800"><strong>Waktu:</strong> {{ $kuis->waktu_pengerjaan }}
                                    menit</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-purple-600 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-purple-800"><strong>Status:</strong> <span
                                        class="font-semibold capitalize">{{ ucfirst($kuis->status) }}</span></span>
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-purple-600 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-purple-800"><strong>Mulai:</strong> {{ $kuis->mulai_dari }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-purple-600 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-purple-800"><strong>Berakhir:</strong>
                                    {{ $kuis->berakhir_pada }}</span>
                            </div>
                        </div>

                        <!-- Button Mulai Kuis -->
                        <button wire:click="mulaiKuis"
                            class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-green-300 transition duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            MULAI KUIS SEKARANG
                        </button>
                    </div>
                @endif

                <!-- Informasi tambahan dengan icon -->
                <div class="mt-4 p-3 bg-purple-50 rounded-xl border border-purple-100">
                    <div class="flex items-start">
                        <svg class="w-4 h-4 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-purple-700">
                            Masukkan kode kuis yang diberikan oleh pengajar untuk memulai kuis atau scan QR Code yang
                            tersedia.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Text -->
        <p class="mt-4 text-center text-xs text-gray-500">
            Pastikan koneksi internet Anda stabil selama mengerjakan kuis
        </p>
    </div>
    <x-qr-scanner-modal />
</div>
