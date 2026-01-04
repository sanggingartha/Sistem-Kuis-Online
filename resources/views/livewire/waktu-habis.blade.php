<div class="max-w-4xl mx-auto px-4 py-6" x-data="{
    statusPenilaian: '{{ $statusPenilaian }}',
    
    init() {
        console.log('=== WAKTU HABIS PAGE ===');
        console.log('Status kuis:', '{{ $hasil->status }}');
        console.log('Waktu mulai:', '{{ $waktuMulai }}');
        console.log('Waktu selesai:', '{{ $waktuSelesai }}');
        console.log('Durasi pengerjaan:', '{{ $durasiPengerjaan }}', 'detik');
        console.log('Status penilaian:', this.statusPenilaian);
        console.log('=======================');
    }
}">
    <!-- Success/Warning Message -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="text-yellow-800 font-medium">{{ session('warning') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Header Kuis dengan Status Waktu Habis -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-800 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-red-900">{{ $kuis->nama_kuis }}</h1>
                        <p class="text-gray-600 text-sm">Waktu pengerjaan telah habis</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 mt-3 text-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-red-600 mr-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700 font-medium">Durasi: {{ $kuis->waktu_pengerjaan }} menit</span>
                    </div>
                </div>
            </div>

            <!-- Status Waktu Habis Display -->
            <div id="timer-container" 
                class="flex items-center gap-3 px-6 py-4 rounded-xl border-2 shadow-lg bg-red-50 border-red-400">
                <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-xs font-medium text-red-600">Status</p>
                    <span class="font-bold text-2xl text-red-700">
                        WAKTU HABIS
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Waktu Pengerjaan -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Pengerjaan</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-600 font-medium mb-1">Waktu Mulai</p>
                <p class="text-lg font-bold text-blue-900">
                    {{ $waktuMulai ? $waktuMulai->format('H:i:s') : '-' }}
                </p>
                <p class="text-xs text-blue-600">
                    {{ $waktuMulai ? $waktuMulai->format('d M Y') : '-' }}
                </p>
            </div>

            <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                <p class="text-sm text-red-600 font-medium mb-1">Waktu Selesai</p>
                <p class="text-lg font-bold text-red-900">
                    {{ $waktuSelesai ? $waktuSelesai->format('H:i:s') : '-' }}
                </p>
                <p class="text-xs text-red-600">
                    {{ $waktuSelesai ? $waktuSelesai->format('d M Y') : '-' }}
                </p>
            </div>

            <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                <p class="text-sm text-purple-600 font-medium mb-1">Durasi Pengerjaan</p>
                <p class="text-lg font-bold text-purple-900">
                    @if($durasiPengerjaan)
                        {{ floor($durasiPengerjaan / 60) }} menit {{ $durasiPengerjaan % 60 }} detik
                    @else
                        -
                    @endif
                </p>
                <p class="text-xs text-purple-600">Total waktu yang digunakan</p>
            </div>
        </div>
    </div>

    <!-- Status Penilaian -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Status Penilaian</h2>
        
        @if($statusPenilaian === 'no_essay')
            <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-blue-900">Tidak Ada Soal Essay</p>
                        <p class="text-sm text-blue-700">Kuis ini hanya berisi soal pilihan ganda</p>
                    </div>
                </div>
            </div>
        @elseif($statusPenilaian === 'sedang_proses')
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-yellow-600 mr-3"></div>
                    <div>
                        <p class="font-semibold text-yellow-900">Sedang Memproses</p>
                        <p class="text-sm text-yellow-700">AI sedang menilai jawaban essay Anda...</p>
                    </div>
                </div>
                <button wire:click="refreshStatus" 
                    class="mt-3 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm font-medium transition">
                    Refresh Status
                </button>
            </div>
        @elseif($statusPenilaian === 'belum_dinilai')
            <div class="p-4 bg-orange-50 border-l-4 border-orange-500 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-orange-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-orange-900">Menunggu Penilaian</p>
                        <p class="text-sm text-orange-700">Jawaban essay Anda sedang dalam antrian penilaian</p>
                    </div>
                </div>
                <button wire:click="refreshStatus" 
                    class="mt-3 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium transition">
                    Refresh Status
                </button>
            </div>
        @elseif($statusPenilaian === 'error')
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-900">Terjadi Kesalahan</p>
                        <p class="text-sm text-red-700">Ada masalah saat menilai jawaban essay. Silakan hubungi pengajar.</p>
                    </div>
                </div>
            </div>
        @elseif($statusPenilaian === 'selesai')
            <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-green-900">Penilaian Selesai</p>
                        <p class="text-sm text-green-700">Semua jawaban telah dinilai. Anda dapat melihat hasilnya sekarang.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-blue-900">Penilaian Sebagian Selesai</p>
                        <p class="text-sm text-blue-700">Beberapa jawaban sudah dinilai, yang lain masih dalam proses</p>
                    </div>
                </div>
                <button wire:click="refreshStatus" 
                    class="mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                    Refresh Status
                </button>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class=>
    <div class="flex flex-col md:flex-row gap-4 justify-center">
        <a href="{{ route('kode.kuis') }}"
               class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-green-300 transition duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
    </div>
</div>

</div>

@push('scripts')
<script>
    // Auto refresh status setiap 30 detik jika masih dalam proses penilaian
    document.addEventListener('livewire:initialized', () => {
        const statusPenilaian = '{{ $statusPenilaian }}';
        
        if (['sedang_proses', 'belum_dinilai', 'partial'].includes(statusPenilaian)) {
            console.log('Auto refresh enabled untuk status:', statusPenilaian);
            
            const refreshInterval = setInterval(() => {
                console.log('Auto refreshing status...');
                @this.call('refreshStatus');
            }, 30000); // Refresh setiap 30 detik
            
            // Cleanup saat navigasi
            document.addEventListener('livewire:navigating', () => {
                clearInterval(refreshInterval);
            });
        }
    });
</script>
@endpush