<div class="min-h-screen bg-gradient-to-br py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden mb-6">
            <div class="h-3 bg-gradient-to-r from-purple-500 via-blue-500 to-green-500"></div>
            
            <div class="p-8">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full mb-4 shadow-xl">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                        Riwayat Kuis
                    </h1>
                    <p class="text-gray-600">
                        Lihat semua kuis yang pernah Anda ikuti
                    </p>
                </div>

                <!-- Filter Status -->
                <div class="flex flex-wrap gap-3 justify-center">
                    <button 
                        wire:click="filterByStatus('all')"
                        class="px-6 py-2 rounded-xl font-semibold transition duration-300 transform hover:scale-105 {{ $statusFilter === 'all' ? 'bg-gradient-to-r from-purple-600 to-purple-800 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Semua
                    </button>
                    <button 
                        wire:click="filterByStatus('selesai')"
                        class="px-6 py-2 rounded-xl font-semibold transition duration-300 transform hover:scale-105 {{ $statusFilter === 'selesai' ? 'bg-gradient-to-r from-green-600 to-green-800 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Selesai
                    </button>
                    <button 
                        wire:click="filterByStatus('sedang_mengerjakan')"
                        class="px-6 py-2 rounded-xl font-semibold transition duration-300 transform hover:scale-105 {{ $statusFilter === 'sedang_mengerjakan' ? 'bg-gradient-to-r from-yellow-600 to-yellow-800 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Sedang Mengerjakan
                    </button>
                </div>
            </div>
        </div>

        <!-- List Riwayat -->
        @if($riwayatKuis->count() > 0)
            <div class="space-y-4">
                @foreach($riwayatKuis as $hasil)
                    @php
                        $statusColor = $this->getStatusColor($hasil->status);
                        $statusText = $this->getStatusText($hasil->status);
                    @endphp

                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition duration-300">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <!-- Info Kuis -->
                                <div class="flex-1">
                                    <div class="flex items-start gap-4">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>

                                        <!-- Detail -->
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-gray-900 mb-1">
                                                {{ $hasil->kuis->nama_kuis }}
                                            </h3>
                                            <div class="flex flex-wrap gap-3 text-sm text-gray-600 mb-2">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $hasil->waktu_mulai->format('d M Y, H:i') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    @if($hasil->durasi_pengerjaan)
                                                        {{ floor($hasil->durasi_pengerjaan / 60) }} menit
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                    </svg>
                                                    Percobaan ke-{{ $hasil->percobaan_ke }}
                                                </span>
                                            </div>

                                            <!-- Status Badge -->
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                                @if($statusColor === 'green')
                                                    ✓
                                                @elseif($statusColor === 'yellow')
                                                    ⏳
                                                @else
                                                    ✗
                                                @endif
                                                {{ $statusText }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nilai & Actions -->
                                <div class="flex items-center gap-4">
                                    @if($hasil->status === 'selesai')
                                        <!-- Box Nilai -->
                                        <div class="text-center bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border-2 border-purple-200">
                                            <p class="text-sm text-gray-600 mb-1">Nilai</p>
                                            <p class="text-3xl font-bold text-purple-700">
                                                {{ number_format($hasil->persentase, 0) }}
                                            </p>
                                            <p class="text-xs text-gray-500">/ 100</p>
                                        </div>

                                        <!-- Button Lihat Detail -->
                                        <a href="{{ route('kuis.lihat-nilai', $hasil->id) }}" 
                                           class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-purple-300 transition duration-300 transform hover:scale-105 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </a>
                                    @elseif($hasil->status === 'sedang_mengerjakan')
                                        <!-- Button Lanjutkan -->
                                        <a href="{{ route('kuis.mulai', $hasil->kuis->kode_kuis) }}" 
                                           class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-yellow-300 transition duration-300 transform hover:scale-105 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Lanjutkan
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Detail Poin (jika selesai) -->
                            @if($hasil->status === 'selesai')
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-3 gap-4 text-center">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Total Poin</p>
                                            <p class="text-lg font-bold text-gray-900">{{ $hasil->poin_diperoleh }}/{{ $hasil->total_poin }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Pilihan Ganda</p>
                                            <p class="text-lg font-bold text-blue-600">{{ $hasil->poin_pilgan }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Essay</p>
                                            <p class="text-lg font-bold text-green-600">{{ $hasil->poin_essay }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $riwayatKuis->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Riwayat</h3>
                    <p class="text-gray-600 mb-6">Anda belum pernah mengikuti kuis apapun.</p>
                    <a href="{{ route('kode.kuis') }}" 
                       class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-purple-300 transition duration-300 transform hover:scale-105 gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Mulai Kuis
                    </a>
                </div>
            </div>
        @endif

        <!-- Button Kembali -->
        <div class="mt-6 text-center">
            <a href="{{ route('kode.kuis') }}" 
               class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border-2 border-gray-200 shadow hover:shadow-lg transition duration-300 gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>