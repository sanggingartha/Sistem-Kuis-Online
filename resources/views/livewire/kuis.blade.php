<div class="max-w-4xl mx-auto px-4 py-6" 
x-data="{
    sisaWaktu: {{ max(0, (int) $sisaWaktu) }},
    warna: 'normal',
    interval: null,
    syncInterval: null,
    isSubmitting: false,
    lastSync: Date.now(),
    livewireReady: false,
    
    init() {
        console.log('=== TIMER INIT ===');
        console.log('Sisa waktu:', this.sisaWaktu, 'detik');
        
        // Check waktu habis
        if (this.sisaWaktu <= 0) {
            console.error('Waktu habis!');
            setTimeout(() => this.waktuHabis(), 500);
            return;
        }
        
        // Wait for Livewire then start timer
        this.waitForLivewire();
    },
    
    waitForLivewire() {
        const check = () => {
            if (typeof Livewire !== 'undefined' && window.Livewire && this.$wire) {
                this.livewireReady = true;
                console.log('Livewire ready!');
                this.updateTimer();
                this.startTimer();
            } else {
                setTimeout(check, 100);
            }
        };
        check();
    },
    
    startTimer() {
        // Timer countdown
        this.interval = setInterval(() => {
            if (this.sisaWaktu > 0 && !this.isSubmitting) {
                this.sisaWaktu--;
                this.updateTimer();
                
                if (this.sisaWaktu % 30 === 0) {
                    console.log('Sisa:', this.sisaWaktu, 'detik');
                }
            } else if (this.sisaWaktu <= 0 && !this.isSubmitting) {
                clearInterval(this.interval);
                this.waktuHabis();
            }
        }, 1000);
        
        // Sync with server
        this.syncInterval = setInterval(() => {
            if (!this.isSubmitting && this.livewireReady) {
                this.syncWithServer();
            }
        }, 30000);
    },
    
    updateTimer() {
        const menit = Math.floor(this.sisaWaktu / 60);
        const detik = this.sisaWaktu % 60;
        const tampilan = String(menit).padStart(2, '0') + ':' + String(detik).padStart(2, '0');
        
        const el = document.getElementById('timer-display');
        if (el) el.textContent = tampilan;
        
        if (this.sisaWaktu <= 60) this.warna = 'kritis';
        else if (this.sisaWaktu <= 300) this.warna = 'peringatan';
        else this.warna = 'normal';
    },
    
    async syncWithServer() {
        try {
            const now = Date.now();
            if (now - this.lastSync < 25000) return;
            
            this.lastSync = now;
            const result = await this.$wire.syncTimer();
            
            if (result && result.status === 'active') {
                const diff = Math.abs(this.sisaWaktu - result.sisaWaktu);
                if (diff > 10) {
                    this.sisaWaktu = result.sisaWaktu;
                    this.updateTimer();
                }
            } else if (result && result.status === 'expired') {
                this.sisaWaktu = 0;
                this.waktuHabis();
            }
        } catch (error) {
            console.error('Sync error:', error);
        }
    },
    
    stopTimer() {
        if (this.interval) clearInterval(this.interval);
        if (this.syncInterval) clearInterval(this.syncInterval);
    },
    
    async waktuHabis() {
        if (this.isSubmitting) return;
        if (!this.livewireReady || !this.$wire) {
            window.location.reload();
            return;
        }
        
        this.isSubmitting = true;
        this.stopTimer();
        
        try {
            await this.$wire.waktuHabis();
        } catch (error) {
            console.error('Error:', error);
        }
    }
}">

    <!-- Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-purple-800 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-purple-900">{{ $kuis->nama_kuis }}</h1>
                        <p class="text-gray-600 text-sm">Kerjakan dengan teliti</p>
                    </div>
                </div>
            </div>

            <!-- Timer -->
            <div class="flex items-center gap-3 px-6 py-4 rounded-xl border-2 shadow-lg transition-all"
                :class="{
                    'bg-purple-50 border-purple-200': warna === 'normal',
                    'bg-yellow-50 border-yellow-300': warna === 'peringatan',
                    'bg-red-50 border-red-400 animate-pulse': warna === 'kritis'
                }">
                <svg class="w-6 h-6" 
                    :class="{
                        'text-purple-700': warna === 'normal',
                        'text-yellow-700': warna === 'peringatan',
                        'text-red-700': warna === 'kritis'
                    }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-xs font-medium"
                        :class="{
                            'text-purple-600': warna === 'normal',
                            'text-yellow-600': warna === 'peringatan',
                            'text-red-600': warna === 'kritis'
                        }">Sisa Waktu</p>
                    <span id="timer-display" class="font-bold text-2xl"
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

    <!-- Soal PG -->
    @if ($currentType === 'pg' && count($soalPG) > 0)
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-200">
                <div>
                    <h2 class="text-xl font-bold text-purple-900">Soal Pilihan Ganda</h2>
                    <p class="text-gray-600 text-sm mt-1">Pilih satu jawaban yang paling tepat</p>
                </div>
                <div class="px-4 py-2 bg-purple-100 rounded-lg">
                    <span class="text-purple-700 font-bold text-sm">{{ count($soalPG) }} Soal</span>
                </div>
            </div>

            <div class="space-y-8">
                @foreach ($soalPG as $index => $soal)
                    <div class="pb-6 {{ $index < count($soalPG) - 1 ? 'border-b-2 border-gray-100' : '' }}">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-purple-600 to-purple-800 text-white font-bold text-lg shadow-lg">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{!! $soal['pertanyaan'] !!}</h3>
                                @if (!empty($soal['gambar_url']))
                                    <img src="{{ asset('storage/' . $soal['gambar_url']) }}" alt="Soal" class="mt-3 max-w-md rounded-lg shadow-md">
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3 ml-16">
                            @foreach ($soal['opsi'] as $opsi)
                                <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition {{ isset($jawabanSekarang[$soal['id']]) && $jawabanSekarang[$soal['id']] == $opsi['id'] ? 'border-purple-600 bg-purple-50' : 'border-gray-200 hover:border-purple-300' }}">
                                    <input type="radio" name="soal_{{ $soal['id'] }}" wire:model.live="jawabanSekarang.{{ $soal['id'] }}" value="{{ $opsi['id'] }}" class="w-5 h-5 text-purple-600">
                                    <span class="ml-3 text-gray-800">{{ $opsi['teks_opsi'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Button -->
            <div class="flex justify-end pt-6 border-t-2 border-gray-200 mt-6">
                <button 
                    wire:click="lanjutKeEssay"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                    class="px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:ring-4 focus:ring-purple-300 transition flex items-center gap-2">
                    
                    <span wire:loading wire:target="lanjutKeEssay">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    
                    <span wire:loading.remove wire:target="lanjutKeEssay">
                        @if (count($soalEssay) > 0)
                            LANJUTKAN KE ESSAY
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            KUMPULKAN JAWABAN
                        @endif
                    </span>
                    
                    <span wire:loading wire:target="lanjutKeEssay">Memproses...</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Soal Essay -->
    @if ($currentType === 'essay' && $soalSekarang)
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-200">
                <div>
                    <h2 class="text-xl font-bold text-purple-900">Soal Essay</h2>
                    <p class="text-gray-600 text-sm mt-1">Tulis jawaban dengan jelas</p>
                </div>
                <div class="px-4 py-2 bg-green-100 rounded-lg">
                    <span class="text-green-700 font-bold text-sm">{{ $currentIndex + 1 }}/{{ count($soalEssay) }}</span>
                </div>
            </div>

            <div class="mb-8">
                <div class="flex items-start gap-4 mb-6">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-green-600 to-green-700 text-white font-bold text-lg shadow-lg">
                        {{ $currentIndex + 1 }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{!! $soalSekarang['pertanyaan'] !!}</h3>
                        @if (!empty($soalSekarang['gambar_url']))
                            <img src="{{ asset('storage/' . $soalSekarang['gambar_url']) }}" alt="Soal" class="mt-3 max-w-md rounded-lg shadow-md">
                        @endif
                    </div>
                </div>

                <div class="ml-16">
                    <label class="block text-sm font-semibold text-purple-700 mb-2">Jawaban Anda:</label>
                    <textarea wire:model.live.debounce.500ms="jawabanSekarang" rows="8" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 resize-none" placeholder="Tulis jawaban..."></textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t-2 border-gray-200">
                <button wire:click="next" wire:loading.attr="disabled" wire:loading.class="opacity-50" class="px-8 py-3 bg-gradient-to-r {{ $currentIndex + 1 >= count($soalEssay) ? 'from-green-600 to-green-700' : 'from-purple-600 to-purple-800' }} text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:ring-4 transition flex items-center gap-2">
                    
                    <span wire:loading wire:target="next">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    
                    <span wire:loading.remove wire:target="next">
                        @if ($currentIndex + 1 >= count($soalEssay))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            KUMPULKAN
                        @else
                            LANJUT
                        @endif
                    </span>
                    
                    <span wire:loading wire:target="next">Memproses...</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="text-center md:text-left">
                <p class="text-gray-700 font-semibold">Tips Mengerjakan Kuis</p>
                <p class="text-gray-500 text-sm">Perhatikan sisa waktu</p>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-purple-50 rounded-xl border border-purple-200">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-purple-700 text-sm font-medium">Jawaban tersimpan otomatis</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let formSubmitting = false;
    
    window.addEventListener('beforeunload', function(e) {
        if (!formSubmitting) {
            e.preventDefault();
            e.returnValue = 'Kuis masih berlangsung';
            return e.returnValue;
        }
    });
    
    document.addEventListener('livewire:navigating', () => {
        formSubmitting = true;
    });
</script>
@endpush