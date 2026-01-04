<div class="max-w-4xl mx-auto px-4 py-6">
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

    <!-- Header Kuis dengan Timer -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-purple-600 to-purple-800 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-purple-900">{{ $kuis->nama_kuis }}</h1>
                        <p class="text-gray-600 text-sm">Kerjakan dengan teliti dan jujur</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 mt-3 text-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-purple-600 mr-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700 font-medium">Durasi: {{ $kuis->waktu_pengerjaan }} menit</span>
                    </div>
                </div>
            </div>

            <!-- Timer Display -->
            <div id="timer-container" 
                class="flex items-center gap-3 px-6 py-4 rounded-xl border-2 shadow-lg transition-all duration-300"
                x-data="{ 
                    sisaWaktu: {{ (int) $sisaWaktu }},
                    warna: 'normal',
                    interval: null,
                    
                    init() {
                        console.log('Timer initialized dengan sisaWaktu:', this.sisaWaktu, 'detik');
                        this.updateTimer();
                        this.interval = setInterval(() => {
                            if (this.sisaWaktu > 0) {
                                this.sisaWaktu--;
                                this.updateTimer();
                            } else {
                                clearInterval(this.interval);
                                this.waktuHabis();
                            }
                        }, 1000);
                    },
                    
                    updateTimer() {
                        const menit = Math.floor(this.sisaWaktu / 60);
                        const detik = this.sisaWaktu % 60;
                        
                        const tampilan = String(menit).padStart(2, '0') + ':' + String(detik).padStart(2, '0');
                        document.getElementById('timer-display').textContent = tampilan;
                        
                        // Update warna berdasarkan sisa waktu
                        if (this.sisaWaktu <= 60) {
                            this.warna = 'kritis';
                        } else if (this.sisaWaktu <= 300) {
                            this.warna = 'peringatan';
                        } else {
                            this.warna = 'normal';
                        }
                    },
                    
                    waktuHabis() {
                        console.log('Waktu habis! Memanggil server...');
                        @this.call('waktuHabis');
                    }
                }"
                :class="{
                    'bg-purple-50 border-purple-200': warna === 'normal',
                    'bg-yellow-50 border-yellow-300': warna === 'peringatan',
                    'bg-red-50 border-red-400 animate-pulse': warna === 'kritis'
                }">
                <svg class="w-6 h-6 transition-colors duration-300" 
                    :class="{
                        'text-purple-700': warna === 'normal',
                        'text-yellow-700': warna === 'peringatan',
                        'text-red-700': warna === 'kritis'
                    }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-xs font-medium transition-colors duration-300"
                        :class="{
                            'text-purple-600': warna === 'normal',
                            'text-yellow-600': warna === 'peringatan',
                            'text-red-600': warna === 'kritis'
                        }">Sisa Waktu</p>
                    <span id="timer-display" 
                        class="font-bold text-2xl transition-colors duration-300"
                        :class="{
                            'text-purple-700': warna === 'normal',
                            'text-yellow-700': warna === 'peringatan',
                            'text-red-700': warna === 'kritis'
                        }">
                        {{ sprintf('%02d:%02d', floor($sisaWaktu / 60), $sisaWaktu % 60) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- SEMUA Soal Pilihan Ganda -->
    @if ($currentType === 'pg' && count($soalPG) > 0)
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-200">
                <div>
                    <h2 class="text-xl font-bold text-purple-900">Soal Pilihan Ganda</h2>
                    <p class="text-gray-600 text-sm mt-1">Pilih satu jawaban yang paling tepat untuk setiap soal</p>
                </div>
                <div class="px-4 py-2 bg-purple-100 rounded-lg">
                    <span class="text-purple-700 font-bold text-sm">{{ count($soalPG) }} Soal</span>
                </div>
            </div>

            <!-- Loop semua soal PG -->
            <div class="space-y-8">
                @foreach ($soalPG as $index => $soal)
                    <div class="pb-6 {{ $index < count($soalPG) - 1 ? 'border-b-2 border-gray-100' : '' }}">
                        <!-- Pertanyaan -->
                        <div class="flex items-start gap-4 mb-4">
                            <div
                                class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-purple-600 to-purple-800 text-white font-bold text-lg flex-shrink-0 shadow-lg">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 leading-relaxed">
                                    {!! $soal['pertanyaan'] !!}
                                </h3>

                                @if (!empty($soal['gambar_url']))
                                    <div class="mt-3">
                                        <img src="{{ asset('storage/' . $soal['gambar_url']) }}" alt="Gambar Soal"
                                            class="max-w-md rounded-lg shadow-md">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Opsi Jawaban -->
                        <div class="space-y-3 ml-16">
                            @foreach ($soal['opsi'] as $opsi)
                                <label
                                    class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition duration-200 
                                    {{ isset($jawabanSekarang[$soal['id']]) && $jawabanSekarang[$soal['id']] == $opsi['id']
                                        ? 'border-purple-600 bg-purple-50'
                                        : 'border-gray-200 hover:border-purple-300 hover:bg-purple-50' }}">
                                    <input type="radio" name="soal_{{ $soal['id'] }}"
                                        wire:model="jawabanSekarang.{{ $soal['id'] }}" value="{{ $opsi['id'] }}"
                                        class="w-5 h-5 text-purple-600 focus:ring-purple-500 border-gray-300">
                                    <div class="ml-3 flex-1">
                                        <span class="text-gray-800 font-medium">{{ $opsi['teks_opsi'] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Button Lanjut ke Essay -->
            <div class="flex justify-end pt-6 border-t-2 border-gray-200 mt-6">
                <button wire:click="lanjutKeEssay"
                    class="px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-purple-300 transition duration-300 transform hover:scale-[1.02] flex items-center gap-2">
                    @if (count($soalEssay) > 0)
                        <span>LANJUTKAN KE ESSAY</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span>KUMPULKAN JAWABAN</span>
                    @endif
                </button>
            </div>
        </div>
    @endif

    <!-- Soal Essay (tetap satu per satu) -->
    @if ($currentType === 'essay' && $soalSekarang)
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-200">
                <div>
                    <h2 class="text-xl font-bold text-purple-900">Soal Essay</h2>
                    <p class="text-gray-600 text-sm mt-1">Tulis jawaban dengan jelas dan lengkap</p>
                </div>
                <div class="px-4 py-2 bg-green-100 rounded-lg">
                    <span class="text-green-700 font-bold text-sm">ESSAY
                        {{ $currentIndex + 1 }}/{{ count($soalEssay) }}</span>
                </div>
            </div>

            <!-- Pertanyaan -->
            <div class="mb-8">
                <div class="flex items-start gap-4 mb-6">
                    <div
                        class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-green-600 to-green-700 text-white font-bold text-lg flex-shrink-0 shadow-lg">
                        {{ $currentIndex + 1 }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 leading-relaxed">
                            {!! $soalSekarang['pertanyaan'] !!}
                        </h3>

                        @if (!empty($soalSekarang['gambar_url']))
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $soalSekarang['gambar_url']) }}" alt="Gambar Soal"
                                    class="max-w-md rounded-lg shadow-md">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Textarea Jawaban -->
                <div class="ml-16">
                    <label class="block text-sm font-semibold text-purple-700 mb-2">Jawaban Anda:</label>
                    <div class="relative">
                        <textarea wire:model="jawabanSekarang" rows="8"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 focus:outline-none transition duration-200 resize-none shadow-sm"
                            placeholder="Tulis jawaban kamu di sini..."></textarea>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-gray-500">Jelaskan jawaban Anda secara rinci</span>
                            <span class="text-xs text-purple-600 font-medium">Minimal 50 karakter</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Submit/Next -->
            <div class="flex justify-end pt-4 border-t-2 border-gray-200">
                <button wire:click="next"
                    class="px-8 py-3 bg-gradient-to-r {{ $currentIndex + 1 >= count($soalEssay) ? 'from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:ring-green-300' : 'from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 focus:ring-purple-300' }} text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 transition duration-300 transform hover:scale-[1.02]">
                    @if ($currentIndex + 1 >= count($soalEssay))
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            KUMPULKAN JAWABAN
                        </span>
                    @else
                        LANJUT
                    @endif
                </button>
            </div>
        </div>
    @endif

    <!-- Info Footer -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mt-6 border border-gray-200">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="text-center md:text-left">
                <p class="text-gray-700 font-semibold mb-1">Tips Mengerjakan Kuis</p>
                <p class="text-gray-500 text-sm">Perhatikan sisa waktu dan pastikan semua jawaban tersimpan</p>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-purple-50 rounded-xl border border-purple-200">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-purple-700 text-sm font-medium">Jawaban akan tersimpan otomatis</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Prevent page reload warning
    window.addEventListener('beforeunload', function(e) {
        const timerContainer = document.getElementById('timer-container');
        if (timerContainer) {
            e.preventDefault();
            e.returnValue = 'Kuis masih berlangsung. Yakin ingin meninggalkan halaman?';
            return e.returnValue;
        }
    });
    
    // Debug timer
    console.log('Sisa waktu dari server:', {{ (int) $sisaWaktu }}, 'detik');
    console.log('Durasi kuis:', {{ $kuis->waktu_pengerjaan }}, 'menit');
</script>
@endpush