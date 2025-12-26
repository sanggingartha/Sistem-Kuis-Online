<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Kuis - {{ $kuis->nama_kuis }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .print-card {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
            }
        }

        .format-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #374151;
        }

        .format-badge svg {
            width: 0.875rem;
            height: 0.875rem;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">

            {{-- Header with Branding --}}
            <div class="text-center mb-10">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Wana Quiz</h1>
                        <p class="text-sm text-gray-500">Sistem Manajemen Kuis Digital</p>
                    </div>
                </div>

                <h2 class="text-3xl font-bold text-gray-900 mb-3">{{ $kuis->nama_kuis }}</h2>
                @if ($kuis->deskripsi)
                    <p class="text-gray-600 max-w-2xl mx-auto">{{ $kuis->deskripsi }}</p>
                @endif
            </div>

            {{-- Main Content Card --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 print-card">

                {{-- Information Grid --}}
                <div class="grid md:grid-cols-3 gap-8 mb-10">
                    {{-- Access Information --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Akses</h3>

                        </div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Kode Kuis</p>
                                <div
                                    class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-100 rounded-lg px-4 py-3">
                                    <p class="text-xl font-bold text-gray-900 font-mono tracking-wider">
                                        {{ $kuis->kode_kuis }}</p>
                                </div>
                            </div>
                            @if ($kuis->waktu_pengerjaan)
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Durasi Pengerjaan</p>
                                    <p class="text-gray-900 font-medium">{{ $kuis->waktu_pengerjaan }} menit</p>
                                </div>
                            @endif
                            @if ($kuis->mulai_dari)
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Periode Kuis</p>
                                    <p class="text-gray-900 font-medium">
                                        {{ date('d M Y H:i', strtotime($kuis->mulai_dari)) }}</p>
                                </div>
                            @endif
                            @if ($kuis->status)
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Status Kuis</p>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                        @if ($kuis->status == 'aktif') bg-green-100 text-green-800
                                        @elseif($kuis->status == 'draft') bg-yellow-100 text-yellow-800
                                        @elseif($kuis->status == 'selesai') bg-blue-100 text-blue-800
                                        @elseif($kuis->status == 'arsip') bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($kuis->status) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- QR Code Section --}}
                    <div class="md:col-span-2">
                        @if ($kuis->barcode_path)
                            <div class="text-center">
                                <div
                                    class="inline-block p-6 bg-white border border-gray-200 rounded-xl shadow-sm relative">
                                    {{-- Corner Decorations --}}
                                    <div
                                        class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-blue-500 rounded-tl">
                                    </div>
                                    <div
                                        class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-purple-500 rounded-tr">
                                    </div>
                                    <div
                                        class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-purple-500 rounded-bl">
                                    </div>
                                    <div
                                        class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-blue-500 rounded-br">
                                    </div>

                                    <img src="{{ Storage::disk('public')->url($kuis->barcode_path) }}"
                                        alt="QR Code {{ $kuis->kode_kuis }}" class="w-64 h-64 mx-auto">
                                </div>

                                <div class="flex items-center justify-center gap-2 mt-4 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span>Siap untuk discan</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-xl">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <p class="text-gray-600">QR Code tidak tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-100 rounded-xl p-6 mb-8">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-blue-900 mb-2">Instruksi Akses</h4>
                            <ol class="space-y-2 text-blue-800">
                                <li class="flex items-start gap-2">
                                    <span class="font-medium">1.</span>
                                    <span>Buka aplikasi kamera atau scanner QR di perangkat Anda</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="font-medium">2.</span>
                                    <span>Arahkan kamera ke QR Code di atas</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="font-medium">3.</span>
                                    <span>Ikuti tautan yang muncul untuk mengakses kuis</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="font-medium">4.</span>
                                    <span>Gunakan kode akses di atas jika QR Code tidak dapat discan</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                @if ($kuis->barcode_path)
                    <div class="flex flex-wrap gap-4 justify-center no-print mb-8">
                        {{-- Download SVG --}}
                        <a href="{{ Storage::disk('public')->url($kuis->barcode_path) }}"
                            download="QR-Kuis-{{ $kuis->kode_kuis }}.svg"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download QR Code (SVG)
                        </a>

                        {{-- Print --}}
                        <button onclick="window.print()"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors shadow-sm hover:shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Halaman
                        </button>

                        {{-- Copy Code --}}
                        <button onclick="copyCode('{{ $kuis->kode_kuis }}')"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors shadow-sm hover:shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Salin Kode
                        </button>

                        {{-- Convert to PNG (Optional) --}}
                        <button
                            onclick="convertToPNG('{{ Storage::disk('public')->url($kuis->barcode_path) }}', '{{ $kuis->kode_kuis }}')"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Konversi ke PNG
                        </button>
                    </div>
                @endif

                {{-- Technical Information --}}
                <div class="border-t border-gray-200 pt-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Informasi Teknis
                            </h5>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">Format File</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900">SVG (Vector)</span>
                                        <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded">High
                                            Quality</span>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Tanggal Generate</span>
                                    <span class="text-gray-900 font-medium">{{ date('d M Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Ukuran Optimal</span>
                                    <span class="text-gray-900 font-medium">256×256 px</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Kompatibilitas</span>
                                    <span class="text-gray-900 font-medium">Semua browser & device</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                Bantuan & Dukungan
                            </h5>
                            <div class="space-y-2 text-sm">
                                <p class="text-gray-600">Jika mengalami kesulitan dengan format SVG:</p>
                                <ul class="space-y-1 text-gray-700">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Gunakan tombol "Konversi ke PNG" di atas
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Buka dengan Chrome, Firefox, atau Edge
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Import ke Adobe Illustrator/Photoshop
                                    </li>
                                </ul>
                                <div class="pt-2">
                                    <p class="text-blue-600 font-medium">support@wanaquiz.ac.id</p>
                                    <p class="text-gray-500 text-xs mt-1">Respon dalam 1×24 jam</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-8 text-sm text-gray-500">
                <p>© {{ date('Y') }} Wana Quiz. Hak cipta dilindungi undang-undang.</p>
                <p class="mt-1">Dokumen ini hanya untuk keperluan akademik.</p>
            </div>
        </div>
    </div>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                showNotification('Kode berhasil disalin: ' + code, 'success');
            }).catch(err => {
                showNotification(' Gagal menyalin kode', 'error');
                console.error('Copy failed:', err);
            });
        }

        function showNotification(message, type) {
            // Remove existing notifications
            document.querySelectorAll('.custom-notification').forEach(n => n.remove());

            const notification = document.createElement('div');
            notification.className = `custom-notification fixed top-4 right-4 px-4 py-3 rounded-lg shadow-xl z-[9999] transform translate-x-[120%] transition-transform duration-500 ease-out ${
            type === 'success' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 
            type === 'error' ? 'bg-gradient-to-r from-red-500 to-rose-600 text-white' :
            'bg-gradient-to-r from-blue-500 to-indigo-600 text-white'
        }`;

            notification.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    ${type === 'success' ? '' : type === 'error' ? '' : ''}
                </div>
                <div class="font-medium">${message}</div>
            </div>
            <div class="absolute bottom-0 left-0 h-1 bg-white/30 rounded-b-lg overflow-hidden">
                <div class="h-full bg-white/70 progress-bar-animation"></div>
            </div>
        `;

            document.body.appendChild(notification);

            // Trigger animation
            requestAnimationFrame(() => {
                notification.classList.remove('translate-x-[120%]');
                notification.classList.add('translate-x-0');
            });

            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.classList.remove('translate-x-0');
                notification.classList.add('translate-x-[120%]');
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }

        function convertToPNG(svgUrl, kodeKuis) {

            // Create loading overlay
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'fixed inset-0 bg-black/50 z-[10000] flex items-center justify-center';
            loadingOverlay.innerHTML = `
            <div class="bg-white rounded-xl p-6 shadow-2xl max-w-sm mx-4 transform scale-0 transition-transform duration-300">
                <div class="text-center">
                    <div class="spinner mb-4 mx-auto">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Mengonversi ke PNG</h3>
                    <p class="text-gray-600 text-sm">Sedang memproses QR Code...</p>
                </div>
            </div>
        `;
            document.body.appendChild(loadingOverlay);

            // Animate loading modal in
            requestAnimationFrame(() => {
                loadingOverlay.querySelector('.bg-white').classList.remove('scale-0');
                loadingOverlay.querySelector('.bg-white').classList.add('scale-100');
            });

            // Create canvas for conversion
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = 1024; // Higher resolution for better quality
            canvas.height = 1024;

            const img = new Image();
            img.crossOrigin = 'anonymous';

            // Add subtle loading animation to image
            img.onloadstart = function() {
                console.log('Mulai memuat gambar...');
            };

            img.onload = function() {
                // Animate drawing process
                setTimeout(() => {
                    // Clear canvas with fade effect
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    // Draw with slight animation
                    let opacity = 0;
                    const drawInterval = setInterval(() => {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.globalAlpha = opacity;
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        opacity += 0.1;

                        if (opacity >= 1) {
                            clearInterval(drawInterval);
                            ctx.globalAlpha = 1;

                            // Convert to PNG
                            const pngDataUrl = canvas.toDataURL('image/png', 1.0);

                            // Create download with animation
                            const link = document.createElement('a');
                            link.href = pngDataUrl;
                            link.download = `QR-Kuis-${kodeKuis}.png`;

                            // Animate download
                            link.style.opacity = '0';
                            link.style.position = 'absolute';
                            document.body.appendChild(link);

                            setTimeout(() => {
                                link.click();
                                document.body.removeChild(link);

                                // Animate loading modal out
                                loadingOverlay.querySelector('.bg-white').classList.remove(
                                    'scale-100');
                                loadingOverlay.querySelector('.bg-white').classList.add(
                                    'scale-0');

                                setTimeout(() => {
                                    loadingOverlay.remove();

                                }, 300);
                            }, 100);
                        }
                    }, 30);
                }, 500);
            };

            img.onerror = function() {
                // Animate error
                loadingOverlay.querySelector('.bg-white').classList.remove('scale-100');
                loadingOverlay.querySelector('.bg-white').classList.add('scale-0');

                setTimeout(() => {
                    loadingOverlay.remove();
                    showNotification('Gagal mengonversi SVG ke PNG', 'error');
                }, 300);
            };

            img.src = svgUrl + '?t=' + new Date().getTime(); // Cache busting
        }

        // Add styles for animations
        const style = document.createElement('style');
        style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(120%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(120%);
                opacity: 0;
            }
        }
        
        @keyframes progressBar {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }
        
        .custom-notification {
            animation: slideInRight 0.5s ease-out;
            min-width: 300px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .progress-bar-animation {
            animation: progressBar 3s linear forwards;
        }
        
        .spinner {
            width: 70px;
            text-align: center;
        }
        
        .spinner div {
            width: 18px;
            height: 18px;
            background-color: #3B82F6;
            border-radius: 100%;
            display: inline-block;
            animation: bounce 1.4s infinite ease-in-out both;
        }
        
        .spinner .bounce1 {
            animation-delay: -0.32s;
        }
        
        .spinner .bounce2 {
            animation-delay: -0.16s;
        }
        
        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
            } 
            40% {
                transform: scale(1.0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        .scale-up {
            animation: scaleUp 0.3s ease-out;
        }
        
        @keyframes scaleUp {
            from {
                transform: scale(0.9);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .scale-down {
            animation: scaleDown 0.3s ease-in;
        }
        
        @keyframes scaleDown {
            from {
                transform: scale(1);
                opacity: 1;
            }
            to {
                transform: scale(0.9);
                opacity: 0;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .rotate-animation {
            animation: rotate 1s linear infinite;
        }
        
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
    `;
        document.head.appendChild(style);

        // Add hover effects to buttons
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('button, [role="button"], a[download]');
            buttons.forEach(button => {
                // Add pulse animation on hover
                button.addEventListener('mouseenter', function() {
                    this.classList.add('pulse-animation');
                });

                button.addEventListener('mouseleave', function() {
                    this.classList.remove('pulse-animation');
                });

                // Add click animation
                button.addEventListener('mousedown', function() {
                    this.classList.add('scale-95');
                });

                button.addEventListener('mouseup', function() {
                    this.classList.remove('scale-95');
                });

                button.addEventListener('mouseleave', function() {
                    this.classList.remove('scale-95');
                });
            });
        });
    </script>
</body>

</html>
