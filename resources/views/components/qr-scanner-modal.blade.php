<div x-data="qrScanner()" x-show="open" x-transition
    class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 relative">
        <h2 class="text-xl font-bold mb-4 text-center text-purple-700">
            Scan QR Code
        </h2>

        <!-- Camera -->
        <div id="qr-reader" class="w-full"></div>

        <!-- Upload -->
        <input type="file" accept="image/*" class="mt-4 w-full text-sm" @change="scanFile($event)">

        <button @click="close()" class="mt-4 w-full bg-gray-200 hover:bg-gray-300 rounded-lg py-2 font-semibold">
            Tutup
        </button>
    </div>
</div>

<script>
    function qrScanner() {
        let html5Qr;

        return {
            open: false,

            init() {
                window.addEventListener('open-scanner', () => {
                    this.open = true;
                    this.$nextTick(() => this.startCamera());
                });
            },

            close() {
                this.open = false;
                if (html5Qr) html5Qr.stop();
            },

            startCamera() {
                html5Qr = new Html5Qrcode("qr-reader");
                html5Qr.start({
                        facingMode: "environment"
                    }, {
                        fps: 10,
                        qrbox: 250
                    },
                    (decodedText) => {
                        this.sendToLivewire(decodedText);
                    }
                );
            },

            scanFile(e) {
                const file = e.target.files[0];
                if (!file) return;

                html5Qr = new Html5Qrcode("qr-reader");
                html5Qr.scanFile(file, true)
                    .then(text => this.sendToLivewire(text));
            },

            sendToLivewire(text) {
                this.close();

                try {
                    const url = new URL(text);
                    const kode = url.searchParams.get('kode');
                    if (kode) {
                        Livewire.dispatch('qr-scanned', {
                            kode
                        });
                    }
                } catch {
                    Livewire.dispatch('qr-scanned', {
                        kode: text
                    });
                }
            }
        }
    }
</script>
