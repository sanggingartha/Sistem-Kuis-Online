<div class="max-w-4xl mx-auto px-4 py-6">

    <!-- Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
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
                        <p class="text-gray-600 text-sm">Kerjakan dengan teliti</p>
                    </div>
                </div>
            </div>

            <!-- Timer -->
            <div id="timer-container"
                class="flex items-center gap-3 px-6 py-4 rounded-xl border-2 shadow-lg transition-all bg-purple-50 border-purple-200">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-xs font-medium text-purple-600">Sisa Waktu</p>
                    <span id="timer-display" class="font-bold text-2xl text-purple-700">
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
                            <div
                                class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-purple-600 to-purple-800 text-white font-bold text-lg shadow-lg">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{!! $soal['pertanyaan'] !!}</h3>
                                @if (!empty($soal['gambar_url']))
                                    <img src="{{ asset('storage/' . $soal['gambar_url']) }}" alt="Soal"
                                        class="mt-3 max-w-md rounded-lg shadow-md">
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3 ml-16">
                            @foreach ($soal['opsi'] as $opsi)
                                <label
                                    class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition {{ isset($jawabanPG[$soal['id']]) && $jawabanPG[$soal['id']] == $opsi['id'] ? 'border-purple-600 bg-purple-50' : 'border-gray-200 hover:border-purple-300' }}">
                                    <input type="radio" name="soal_{{ $soal['id'] }}"
                                        wire:model.live="jawabanPG.{{ $soal['id'] }}" value="{{ $opsi['id'] }}"
                                        class="w-5 h-5 text-purple-600">
                                    <span class="ml-3 text-gray-800">{{ $opsi['teks_opsi'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Button -->
            <div class="flex justify-end pt-6 border-t-2 border-gray-200 mt-6">
                <button wire:click="lanjutKeEssay" wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:ring-4 focus:ring-purple-300 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">

                    <span wire:loading wire:target="lanjutKeEssay">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>

                    <span wire:loading.remove wire:target="lanjutKeEssay">
                        @if (count($soalEssay) > 0)
                            LANJUTKAN KE ESSAY
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
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
    @if ($currentType === 'essay' && !empty($soalEssay))
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-200">
                <div>
                    <h2 class="text-xl font-bold text-purple-900">Soal Essay</h2>
                    <p class="text-gray-600 text-sm mt-1">Tulis jawaban dengan jelas</p>
                </div>
                <div class="px-4 py-2 bg-green-100 rounded-lg">
                    <span class="text-green-700 font-bold text-sm">{{ count($soalEssay) }} Soal</span>
                </div>
            </div>

            <div class="space-y-8">
                @foreach ($soalEssay as $index => $soal)
                    <div class="pb-6 {{ $index < count($soalEssay) - 1 ? 'border-b-2 border-gray-100' : '' }}">
                        <div class="flex items-start gap-4 mb-4">
                            <div
                                class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-green-600 to-green-700 text-white font-bold text-lg shadow-lg">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{!! $soal['pertanyaan'] !!}</h3>
                                @if (!empty($soal['gambar_url']))
                                    <img src="{{ asset('storage/' . $soal['gambar_url']) }}" alt="Soal"
                                        class="mt-3 max-w-md rounded-lg shadow-md">
                                @endif
                            </div>
                        </div>

                        <div class="ml-16">
                            <label class="block text-sm font-semibold text-purple-700 mb-2">Jawaban Anda:</label>
                            <textarea wire:model.live.debounce.500ms="jawabanEssay.{{ $soal['id'] }}" rows="6"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 resize-none"
                                placeholder="Tulis jawaban...">{{ $jawabanEssay[$soal['id']] ?? '' }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Button -->
            <div class="flex justify-end pt-6 border-t-2 border-gray-200 mt-6">
                <button wire:click="selesaikanKuis" wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl focus:ring-4 focus:ring-green-300 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">

                    <span wire:loading wire:target="selesaikanKuis">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>

                    <span wire:loading.remove wire:target="selesaikanKuis">
                        KUMPULKAN
                    </span>

                    <span wire:loading wire:target="selesaikanKuis">Memproses...</span>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-purple-700 text-sm font-medium">Jawaban tersimpan otomatis</span>
            </div>
        </div>
    </div>
</div>


<script>
    (function() {
        console.log('🚀 TIMER SCRIPT LOADED - LIVEWIRE V3 COMPATIBLE');

        var WAKTU_SELESAI = {{ $waktuSelesai ?? 0 }};
        var TIMER_ACTIVE = {{ $timerActive ? 'true' : 'false' }};

        console.log('📊 Data:', {
            waktuSelesai: WAKTU_SELESAI,
            timerActive: TIMER_ACTIVE,
            date: new Date(WAKTU_SELESAI * 1000).toLocaleString('id-ID'),
            sisaWaktuHitung: Math.max(0, WAKTU_SELESAI - Math.floor(Date.now() / 1000))
        });

        if (!TIMER_ACTIVE) {
            console.log('⚠️ Timer tidak aktif');
            return;
        }

        if (!WAKTU_SELESAI || WAKTU_SELESAI <= 0) {
            console.log('❌ waktuSelesai invalid:', WAKTU_SELESAI);
            return;
        }

        var sisaWaktuReal = Math.max(0, WAKTU_SELESAI - Math.floor(Date.now() / 1000));
        console.log('🔍 Sisa waktu real:', sisaWaktuReal, 'detik');

        if (sisaWaktuReal <= 0) {
            console.log('⚠️ Waktu sudah habis saat load, langsung panggil waktuHabis');
            setTimeout(function() {
                const comp = Livewire.find(document.querySelector('[wire\\:id]'));
                if (comp) comp.call('waktuHabis');
                else {
                    window.location.href = "/kuis/waktu-habis/{{ $hasilKuis->id }}";
                }
            }, 1000);
            return;
        }

        function startTimer() {
            var displayEl = document.getElementById('timer-display');
            var containerEl = document.getElementById('timer-container');

            if (!displayEl) {
                console.log('⏳ Waiting for DOM...');
                setTimeout(startTimer, 100);
                return;
            }

            console.log('✅ DOM ready, starting countdown...');

            var timerInterval;
            var updateCount = 0;

            function update() {
                updateCount++;

                var now = Math.floor(Date.now() / 1000);
                var sisa = Math.max(0, WAKTU_SELESAI - now);

                var m = Math.floor(sisa / 60);
                var s = sisa % 60;
                displayEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;

                if (updateCount % 5 === 0) console.log('⏱️ Update #' + updateCount + ' - Sisa: ' + sisa + 's');

                // Warna dan animasi
                if (containerEl) {
                    containerEl.className =
                        'flex items-center gap-3 px-6 py-4 rounded-xl border-2 shadow-lg transition-all';
                    var svg = containerEl.querySelector('svg');
                    var label = containerEl.querySelector('p');

                    if (sisa <= 60) {
                        containerEl.className += ' bg-red-50 border-red-400';
                        if (sisa <= 30) containerEl.className += ' animate-pulse';
                        if (svg) svg.className = 'w-6 h-6 text-red-700';
                        if (label) label.className = 'text-xs font-medium text-red-600';
                        displayEl.className = 'font-bold text-2xl text-red-700';
                    } else if (sisa <= 300) {
                        containerEl.className += ' bg-yellow-50 border-yellow-300';
                        if (svg) svg.className = 'w-6 h-6 text-yellow-700';
                        if (label) label.className = 'text-xs font-medium text-yellow-600';
                        displayEl.className = 'font-bold text-2xl text-yellow-700';
                    } else {
                        containerEl.className += ' bg-purple-50 border-purple-200';
                        if (svg) svg.className = 'w-6 h-6 text-purple-700';
                        if (label) label.className = 'text-xs font-medium text-purple-600';
                        displayEl.className = 'font-bold text-2xl text-purple-700';
                    }
                }

                if (sisa <= 0) {
                    console.log('⏰ WAKTU HABIS!');
                    clearInterval(timerInterval);

                    setTimeout(function() {
                        const comp = Livewire.find(document.querySelector('[wire\\:id]'));
                        if (comp) comp.call('waktuHabis');
                        else {
                            window.location.href = "/kuis/waktu-habis/{{ $hasilKuis->id }}";
                        }
                    }, 500);
                }
            }

            update();
            timerInterval = setInterval(update, 1000);

            window.quizTimer = {
                stop: function() {
                    clearInterval(timerInterval);
                },
                getSisaWaktu: function() {
                    var now = Math.floor(Date.now() / 1000);
                    return Math.max(0, WAKTU_SELESAI - now);
                }
            };

            console.log('✅✅✅ TIMER READY ✅✅✅');
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startTimer);
        } else {
            startTimer();
        }
    })();
</script>
