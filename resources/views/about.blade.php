<x-app-layout>

    <!-- Hero Section -->
    <main class="relative bg-cover bg-center bg-no-repeat h-[300px]"
        style="background-image: url('{{ asset('images/murid-4.jpg') }}');">
        <div class="absolute inset-0 bg-purple-900/50"></div>

        <div class="relative flex items-center justify-center h-full text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-white tracking-wide">
                Tentang Kami
            </h2>
        </div>
    </main>

    <!-- Content Section -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
            {{-- <h3 class="text-3xl font-bold text-purple-700 mb-8">Belajar Jadi Pengalaman yang Berarti</h3> --}}

            <div class="space-y-6 text-gray-700 text-lg leading-relaxed text-justify">
                <p>
                    <span class="font-semibold text-purple-700">WanaQuiz</span> adalah platform pembelajaran modern
                    yang dirancang untuk menjadikan proses belajar lebih menarik dan efektif. Kami memahami bahwa
                    setiap siswa memiliki cara unik dalam memahami pelajaran, dan WanaQuiz hadir untuk menjawab
                    tantangan tersebut melalui kuis interaktif berbasis teknologi.
                </p>

                <p>
                    Dengan dukungan <span class="font-semibold text-purple-700">kecerdasan buatan (AI)</span>,
                    sistem kami mampu menilai jawaban secara otomatis dan memberikan umpan balik secara langsung.
                    Hal ini membantu siswa untuk mengetahui sejauh mana pemahaman mereka dan memperbaiki kesalahan
                    dengan cepat serta akurat.
                </p>

                <p>
                    Kami percaya bahwa pembelajaran yang baik bukan hanya soal hasil akhir, tetapi juga tentang
                    bagaimana setiap prosesnya dapat memotivasi dan menumbuhkan rasa ingin tahu. Melalui WanaQuiz,
                    kami ingin menghadirkan pengalaman belajar yang tidak hanya cerdas, tetapi juga menyenangkan
                    dan relevan dengan dunia pendidikan masa kini.
                </p>
            </div>

            {{-- <div class="mt-10">
                <a href="{{ route('register') }}" 
                   class="inline-block bg-purple-700 text-white px-8 py-3 rounded-full font-semibold hover:bg-purple-800 transition duration-300 shadow-md">
                    Mulai Sekarang
                </a>
            </div> --}}
        </div>
    </section>

</x-app-layout>
