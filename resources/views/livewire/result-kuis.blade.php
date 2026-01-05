<div
    class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-purple-50 py-12 px-4 flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <!-- Success Card -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden">
            <!-- Header dengan animasi -->
            <div class="h-3 bg-gradient-to-r from-green-500 via-blue-500 to-purple-500"></div>

            <div class="p-8 md:p-12 text-center">
                <!-- Icon Success -->
                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-green-400 to-green-600 rounded-full mb-6 shadow-xl animate-bounce">
                    <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <!-- Pesan Terima Kasih -->
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Terima Kasih!
                </h1>
                <p class="text-lg text-gray-700 mb-2">
                    Jawaban Anda telah berhasil dikumpulkan
                </p>
                <p class="text-gray-600 mb-8">
                    Kuis: <span class="font-semibold text-purple-700">{{ $hasilKuis->kuis->nama_kuis }}</span>
                </p>

                <!-- Info Hasil -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <!-- Total Soal -->
                    <div class="bg-purple-50 rounded-xl p-4 border-2 border-purple-200">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-purple-700">
                            {{ $hasilKuis->jawabanPilihanGanda->count() + $hasilKuis->jawabanEssay->count() }}</p>
                        <p class="text-sm text-gray-600">Total Soal Dijawab</p>
                    </div>

                    <!-- Waktu Pengerjaan -->
                    <!-- Waktu Pengerjaan -->
                    <div class="bg-blue-50 rounded-xl p-4 border-2 border-blue-200">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-blue-700">
                            {{ $hasilKuis->durasi_format }}
                        </p>
                        <p class="text-sm text-gray-600">Waktu Pengerjaan</p>
                    </div>

                    <!-- Status Penilaian -->
                    <div class="bg-yellow-50 rounded-xl p-4 border-2 border-yellow-200">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-yellow-700">
                            @if ($hasilKuis->isSemuaEssayDinilai())
                                Selesai
                            @else
                                Proses
                            @endif
                        </p>
                        <p class="text-sm text-gray-600">Status Penilaian</p>
                    </div>
                </div>

                <!-- Informasi Penilaian Essay -->
                @if ($hasilKuis->jawabanEssay->count() > 0)
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-8">
                        <div class="flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-bold text-blue-900">Penilaian Essay</h3>
                        </div>
                        <p class="text-blue-800 mb-4">
                            Jawaban essay Anda sedang dinilai oleh AI. Proses ini membutuhkan waktu beberapa saat.
                        </p>

                        <!-- Progress Bar -->
                        @php
                            $progress = $hasilKuis->getProgressPenilaianEssay();
                        @endphp
                        <div class="w-full bg-blue-200 rounded-full h-3 mb-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-500"
                                style="width: {{ $progress['persentase'] }}%"></div>
                        </div>
                        <p class="text-sm text-blue-700 font-medium">
                            {{ $progress['dinilai'] }} dari {{ $progress['total'] }} essay telah dinilai
                            ({{ number_format($progress['persentase'], 0) }}%)
                        </p>
                    </div>
                @endif

                <!-- Tombol Aksi -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('kode.kuis') }}"
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-purple-300 transition duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        Kembali ke Beranda
                    </a>

                    @if ($hasilKuis->isSemuaEssayDinilai())
                        <a href="{{ route('kuis.lihat-nilai', $hasilKuis->id) }}"
                            class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-green-300 transition duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Lihat Nilai
                        </a>
                    @endif
                </div>

                <!-- Footer Note -->
                <div class="mt-8 pt-6 border-t-2 border-gray-200">
                    <p class="text-sm text-gray-600">
                        Nilai akan segera dikirimkan setelah semua jawaban essay selesai dinilai.
                    </p>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                Jika ada pertanyaan, silakan hubungi pengajar Anda
            </p>
        </div>
    </div>
</div>
