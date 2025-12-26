<div class="max-w-2xl mx-auto mt-10 p-4 border rounded shadow">

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-xl font-bold mb-4">{{ $kuis->nama_kuis }}</h1>
    <p>Waktu pengerjaan: {{ $kuis->waktu_pengerjaan }} menit</p>

    @if ($currentType === 'pg' && $soalSekarang)
        <div class="mt-4">
            <p class="font-bold">{{ $soalSekarang['pertanyaan'] }}</p>
            @foreach ($soalSekarang['opsi'] as $opsi)
                <div>
                    <label>
                        <input type="radio" wire:model="jawabanSekarang" value="{{ $opsi['id'] }}">
                        {{ $opsi['teks_opsi'] }}
                    </label>
                </div>
            @endforeach
        </div>
    @elseif($currentType === 'essay' && $soalSekarang)
        <div class="mt-4">
            <p class="font-bold">{{ $soalSekarang['pertanyaan'] }}</p>
            <textarea wire:model="jawabanSekarang" class="border p-2 w-full mt-2" rows="4"></textarea>
        </div>
    @endif

    <button wire:click="next" class="bg-blue-500 text-white px-4 py-2 mt-4 rounded">
        @if ($currentType === 'essay' && $currentIndex + 1 >= count($soalEssay))
            Submit
        @else
            Next
        @endif
    </button>
</div>
