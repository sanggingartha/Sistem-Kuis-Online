<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-purple-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header Card -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden mb-6">
            <div class="h-3 bg-gradient-to-r from-purple-500 via-blue-500 to-green-500"></div>
            
            <div class="p-8">
                <!-- Judul -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                        Hasil Penilaian Kuis
                    </h1>
                    <p class="text-lg text-gray-600">
                        {{ $hasilKuis->kuis->nama_kuis }}
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        Dikerjakan pada: {{ $hasilKuis->waktu_mulai->format('d F Y, H:i') }}
                    </p>
                </div>

                <!-- Nilai Utama -->
                @php
                    $gradeColor = $this->getGradeColor();
                    $gradeText = $this->getGradeText();
                    $colorClasses = [
                        'green' => 'from-green-400 to-green-600',
                        'blue' => 'from-blue-400 to-blue-600',
                        'yellow' => 'from-yellow-400 to-yellow-600',
                        'red' => 'from-red-400 to-red-600',
                    ];
                @endphp

                <div class="flex flex-col items-center mb-8">
                    <div class="relative">
                        <!-- Circle Progress -->
                        <div class="w-48 h-48 rounded-full bg-gradient-to-br {{ $colorClasses[$gradeColor] }} flex items-center justify-center shadow-2xl">
                            <div class="w-40 h-40 rounded-full bg-white flex flex-col items-center justify-center">
                                <span class="text-5xl font-bold text-gray-900">
                                    {{ number_format($hasilKuis->persentase, 0) }}
                                </span>
                                <span class="text-2xl font-semibold text-gray-600">/ 100</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 px-6 py-2 bg-{{ $gradeColor }}-100 rounded-full">
                        <span class="text-{{ $gradeColor }}-800 font-bold text-lg">{{ $gradeText }}</span>
                    </div>
                </div>

                <!-- Statistik Detail -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <!-- Total Poin -->
                    <div class="bg-purple-50 rounded-xl p-4 border-2 border-purple-200 text-center">
                        <p class="text-sm text-gray-600 mb-1">Total Poin</p>
                        <p class="text-2xl font-bold text-purple-700">{{ $hasilKuis->poin_diperoleh }}</p>
                        <p class="text-xs text-gray-500">dari {{ $hasilKuis->total_poin }}</p>
                    </div>

                    <!-- Poin Pilgan -->
                    <div class="bg-blue-50 rounded-xl p-4 border-2 border-blue-200 text-center">
                        <p class="text-sm text-gray-600 mb-1">Pilihan Ganda</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $hasilKuis->poin_pilgan }}</p>
                        <p class="text-xs text-gray-500">poin</p>
                    </div>

                    <!-- Poin Essay -->
                    <div class="bg-green-50 rounded-xl p-4 border-2 border-green-200 text-center">
                        <p class="text-sm text-gray-600 mb-1">Essay</p>
                        <p class="text-2xl font-bold text-green-700">{{ $hasilKuis->poin_essay }}</p>
                        <p class="text-xs text-gray-500">poin</p>
                    </div>

                    <!-- Durasi -->
                    <div class="bg-yellow-50 rounded-xl p-4 border-2 border-yellow-200 text-center">
                        <p class="text-sm text-gray-600 mb-1">Waktu</p>
                        <p class="text-2xl font-bold text-yellow-700">
                            @if($hasilKuis->durasi_pengerjaan)
                                {{ floor($hasilKuis->durasi_pengerjaan / 60) }}
                            @else
                                -
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">menit</p>
                    </div>
                </div>

                <!-- Progress Penilaian Essay -->
                @if($progressPenilaian['total'] > 0 && $progressPenilaian['persentase'] < 100)
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-yellow-800 font-semibold">
                                Penilaian masih dalam proses: {{ $progressPenilaian['dinilai'] }}/{{ $progressPenilaian['total'] }} essay telah dinilai
                            </p>
                        </div>
                        <div class="w-full bg-yellow-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $progressPenilaian['persentase'] }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detail Jawaban Pilihan Ganda -->
        @if($jawabanPG->count() > 0)
            <div class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-4">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Jawaban Pilihan Ganda
                    </h2>
                </div>

                <div class="p-8 space-y-6">
                    @foreach($jawabanPG as $index => $jawaban)
                        <div class="border-2 {{ $jawaban->benar ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50' }} rounded-xl p-6">
                            <!-- Nomor dan Status -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg {{ $jawaban->benar ? 'bg-green-500' : 'bg-red-500' }} text-white font-bold flex items-center justify-center mr-3">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <span class="px-3 py-1 rounded-full text-sm font-bold {{ $jawaban->benar ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                            {{ $jawaban->benar ? '✓ Benar' : '✗ Salah' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold {{ $jawaban->benar ? 'text-green-700' : 'text-red-700' }}">
                                        {{ $jawaban->poin_diperoleh }} / {{ $jawaban->soal->poin }} poin
                                    </p>
                                </div>
                            </div>

                            <!-- Pertanyaan -->
                            <div class="mb-4">
                                <p class="font-semibold text-gray-700 mb-2">Pertanyaan:</p>
                                <div class="text-gray-900">{!! $jawaban->soal->pertanyaan !!}</div>
                            </div>

                            <!-- Jawaban Anda -->
                            <div class="mb-3">
                                <p class="font-semibold text-gray-700 mb-2">Jawaban Anda:</p>
                                <div class="px-4 py-3 {{ $jawaban->benar ? 'bg-green-100 border-l-4 border-green-500' : 'bg-red-100 border-l-4 border-red-500' }} rounded">
                                    <p class="text-gray-900">{{ $jawaban->opsi->teks_opsi ?? 'Tidak dijawab' }}</p>
                                </div>
                            </div>

                            <!-- Jawaban Benar (jika salah) -->
                            @if(!$jawaban->benar)
                                @php
                                    $jawabanBenar = $jawaban->soal->opsi->where('opsi_benar', true)->first();
                                @endphp
                                @if($jawabanBenar)
                                    <div>
                                        <p class="font-semibold text-gray-700 mb-2">Jawaban Benar:</p>
                                        <div class="px-4 py-3 bg-green-100 border-l-4 border-green-500 rounded">
                                            <p class="text-gray-900">{{ $jawabanBenar->teks_opsi }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Detail Jawaban Essay -->
        @if($jawabanEssay->count() > 0)
            <div class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-8 py-4">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Jawaban Essay
                    </h2>
                </div>

                <div class="p-8 space-y-6">
                    @foreach($jawabanEssay as $index => $jawaban)
                        <div class="border-2 border-gray-200 rounded-xl p-6 {{ $jawaban->status_penilaian === 'sudah_dinilai' ? 'bg-white' : 'bg-yellow-50' }}">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-green-600 text-white font-bold flex items-center justify-center mr-3">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        @if($jawaban->status_penilaian === 'sudah_dinilai')
                                            <span class="px-3 py-1 rounded-full text-sm font-bold bg-green-200 text-green-800">
                                                ✓ Sudah Dinilai
                                            </span>
                                        @elseif($jawaban->status_penilaian === 'sedang_proses')
                                            <span class="px-3 py-1 rounded-full text-sm font-bold bg-blue-200 text-blue-800">
                                                ⏳ Sedang Proses
                                            </span>
                                        @elseif($jawaban->status_penilaian === 'error')
                                            <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-200 text-red-800">
                                                ✗ Error
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-sm font-bold bg-yellow-200 text-yellow-800">
                                                ⏳ Belum Dinilai
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($jawaban->status_penilaian === 'sudah_dinilai')
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-green-700">
                                            {{ $jawaban->poin_diperoleh }} / {{ $jawaban->poin_maksimal }} poin
                                        </p>
                                        <p class="text-sm text-gray-600">Skor: {{ number_format($jawaban->skor_ai, 0) }}/100</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Pertanyaan -->
                            <div class="mb-4">
                                <p class="font-semibold text-gray-700 mb-2">Pertanyaan:</p>
                                <div class="text-gray-900">{!! $jawaban->soal->pertanyaan !!}</div>
                            </div>

                            <!-- Jawaban Kunci (jika ada) -->
                            @if(!empty($jawaban->soal->jawaban_acuan))
                                <div class="mb-4">
                                    <p class="font-semibold text-gray-700 mb-2">Jawaban Acuan:</p>
                                    <div class="px-4 py-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                                        <p class="text-gray-900">{{ $jawaban->soal->jawaban_acuan }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Jawaban Anda -->
                            <div class="mb-4">
                                <p class="font-semibold text-gray-700 mb-2">Jawaban Anda:</p>
                                <div class="px-4 py-3 bg-gray-50 border-l-4 border-gray-400 rounded">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $jawaban->jawaban_siswa }}</p>
                                </div>
                            </div>

                            <!-- Feedback AI -->
                            @if($jawaban->status_penilaian === 'sudah_dinilai' && $jawaban->feedback_ai)
                                <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-green-600 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        <div class="flex-1">
                                            <p class="font-semibold text-green-800 mb-1">Feedback AI:</p>
                                            <p class="text-green-900">{{ $jawaban->feedback_ai }}</p>
                                            <p class="text-xs text-green-600 mt-2">Dinilai oleh: {{ $jawaban->nilai_oleh }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tombol Aksi -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('kode.kuis') }}" 
               class="px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-purple-300 transition duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Beranda
            </a>

            <button 
                onclick="window.print()"
                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Hasil
            </button>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-6xl, .max-w-6xl * {
            visibility: visible;
        }
        .max-w-6xl {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        button {
            display: none !important;
        }
    }
</style>