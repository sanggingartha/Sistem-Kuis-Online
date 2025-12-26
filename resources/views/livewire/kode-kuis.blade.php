<div class="w-full max-w-md mx-auto">

    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-purple-700 mb-2">
            Masukkan Kode Kuis
        </h2>
        <p class="text-gray-600 text-sm">
            Siap untuk memulai kuis?
        </p>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form wire:submit.prevent="mulaiKuis">
            <input type="text" wire:model.defer="kode" class="w-full border p-3 rounded mb-3"
                placeholder="Contoh: ABC123" required>

            @error('kode')
                <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
            @enderror

            <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded">
                Mulai Kuis
            </button>
        </form>
    </div>

    <p class="mt-4 text-center text-xs text-gray-500">
        Pastikan koneksi internet stabil
    </p>

</div>
