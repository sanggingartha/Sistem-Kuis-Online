<div class="max-w-lg mx-auto mt-10 p-4 border rounded shadow">

    @if (session()->has('error'))
        <div class="bg-red-100 text-red-800 p-2 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <h1 class="text-xl font-bold mb-4">Masukkan Kode Kuis atau Scan QR</h1>

    <input type="text" wire:model.defer="kode_input" placeholder="Kode Kuis" class="border p-2 w-full mb-4 rounded" />

    <button wire:click="loadKuis" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
        Cek Kode
    </button>

    @if ($kuis)
        <div class="p-4 border rounded mb-4 bg-gray-50">
            <h2 class="text-lg font-bold mb-2">{{ $kuis->nama_kuis }}</h2>
            <p>Waktu: {{ $kuis->waktu_pengerjaan }} menit</p>
            <p>Status: {{ ucfirst($kuis->status) }}</p>
            <p>Mulai dari: {{ $kuis->mulai_dari }}</p>
            <p>Berakhir pada: {{ $kuis->berakhir_pada }}</p>

            <button wire:click="mulaiKuis" class="bg-green-500 text-white px-4 py-2 mt-4 rounded">
                Mulai Kuis
            </button>
        </div>

        {{-- QR Code
        <div class="mt-4 text-center">
            {!! QrCode::size(200)->generate(route('kode.kuis', ['kode' => $kuis->kode_kuis])) !!}
            <p class="mt-2 text-sm text-gray-600">Scan QR untuk masuk ke halaman ini</p>
        </div> --}}
    @endif

</div>
