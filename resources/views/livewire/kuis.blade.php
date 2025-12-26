<div class="max-w-3xl mx-auto mt-6">

    <div class="mb-4 p-3 bg-red-100 rounded">
        ⏱ Sisa waktu: {{ gmdate('i:s', $sisaDetik) }}
    </div>

    {{-- PILIHAN GANDA --}}
    @foreach ($hasil->kuis->soalPilihanGanda as $soal)
        <div class="mb-4 p-4 border rounded">
            <p class="font-semibold">{{ $soal->pertanyaan }}</p>

            @foreach ($soal->opsi as $opsi)
                <label class="block">
                    <input type="radio" wire:click="pilihOpsi({{ $soal->id }}, {{ $opsi->id }})">
                    {{ $opsi->teks_opsi }}
                </label>
            @endforeach
        </div>
    @endforeach

    {{-- ESSAY --}}
    @foreach ($hasil->kuis->soalEssay as $soal)
        <div class="mb-4">
            <p class="font-semibold">{{ $soal->pertanyaan }}</p>
            <textarea wire:model.defer="jawabanEssay.{{ $soal->id }}" class="w-full border p-2 rounded"></textarea>
        </div>
    @endforeach

    <button wire:click="selesaikanKuis" class="bg-green-600 text-white px-6 py-3 rounded">
        Selesai
    </button>
</div>

<script>
    setInterval(() => {
        Livewire.dispatch('autoSubmit')
    }, 1000)
</script>
