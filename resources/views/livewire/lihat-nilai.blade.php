<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-purple-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">

        <!-- HEADER -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden mb-6">
            <div class="h-3 bg-gradient-to-r from-purple-500 via-blue-500 to-green-500"></div>

            <div class="p-8">
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

                <!-- NILAI UTAMA -->
                <div class="flex flex-col items-center mb-8">
                    <div
                        class="w-48 h-48 rounded-full bg-gradient-to-br {{ $colorClasses[$gradeColor] }} flex items-center justify-center shadow-2xl">
                        <div class="w-40 h-40 rounded-full bg-white flex flex-col items-center justify-center">
                            <span class="text-5xl font-bold text-gray-900">
                                {{ number_format($hasilKuis->persentase, 0) }}
                            </span>
                            <span class="text-2xl font-semibold text-gray-600">/ 100</span>
                        </div>
                    </div>

                    <div class="mt-4 px-6 py-2 bg-{{ $gradeColor }}-100 rounded-full">
                        <span class="text-{{ $gradeColor }}-800 font-bold text-lg">
                            {{ $gradeText }}
                        </span>
                    </div>
                </div>

                <!-- STATISTIK -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-purple-50 rounded-xl p-4 border-2 border-purple-200 text-center">
                        <p class="text-sm text-gray-600 mb-1">Total Poin</p>
                        <p class="text-2xl font-bold text-purple-700">{{ $hasilKuis->poin_diperoleh }}</p>
                        <p class="text-xs text-gray-500">dari {{ $hasilKuis->total_poin }}</p>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-4 border-2 border-blue-200 text-center">
                        <p class="text-sm text-gray-600 mb-1">Pilihan Ganda</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $hasilKuis->poin_pilgan }}</p>
                        <p class="text-xs text-gray-500">poin</p>
                    </div>

                    <div class="bg-green-50 rounded-xl p-4 border-2 border-green-200 text-center">
                        <p class="text-sm text-gray-600 mb-1">Essay</p>
                        <p class="text-2xl font-bold text-green-700">{{ $hasilKuis->poin_essay }}</p>
                        <p class="text-xs text-gray-500">poin</p>
                    </div>

                    <div class="bg-yellow-50 rounded-xl p-4 border-2 border-yellow-200 text-center">
                        <p class="text-sm text-gray-600 mb-1">Waktu</p>
                        <p class="text-2xl font-bold text-yellow-700">
                            {{ $hasilKuis->durasi_pengerjaan ? floor($hasilKuis->durasi_pengerjaan / 60) : '-' }}
                        </p>
                        <p class="text-xs text-gray-500">menit</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTION -->
        <div class="flex gap-4 justify-center">
            <a href="{{ route('kode.kuis') }}"
                class="px-8 py-3 bg-purple-700 text-white font-bold rounded-xl shadow hover:scale-105 transition">
                Kembali
            </a>

            <button onclick="window.print()"
                class="px-8 py-3 bg-blue-700 text-white font-bold rounded-xl shadow hover:scale-105 transition">
                Cetak
            </button>
        </div>

    </div>
</div>

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .max-w-6xl,
            .max-w-6xl * {
                visibility: visible;
            }

            .max-w-6xl {
                position: absolute;
                inset: 0;
                width: 100%;
            }

            button,
            a {
                display: none !important;
            }
        }
    </style>
@endpush
