<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-white">
        <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-lg overflow-hidden max-w-5xl w-full">

            <!-- Bagian Kiri: Gambar -->
            <div class="hidden md:flex md:w-1/2 items-center justify-center bg-purple-600">
                <img src="{{ asset('images/murid.jpg') }}" alt="Forgot Password ilustrasi"
                    class="object-cover w-full h-full opacity-90">
            </div>

            <!-- Bagian Kanan: Form -->
            <div class="w-full md:w-1/2 bg-white p-10 flex flex-col justify-center">

                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-purple-700 mb-2">Lupa Kata Sandi?</h1>
                    <p class="text-gray-600 text-sm">
                        Tidak masalah. Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang kata sandi.
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div class="relative">
                        <x-text-input id="email"
                            class="block px-3 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg
                                   border border-gray-300 appearance-none focus:outline-none focus:ring-0
                                   focus:border-purple-600 peer"
                            type="email" name="email" :value="old('email')" required autofocus
                            placeholder=" " />

                        <x-input-label for="email"
                            class="absolute text-sm text-gray-500 duration-300 transform
                                   -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2
                                   peer-focus:px-2 peer-focus:text-purple-700
                                   peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2
                                   peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75
                                   peer-focus:-translate-y-4 left-1"
                            :value="__('Email')" />

                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold py-3 rounded-lg transition">
                        Kirim Link Reset Password
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-6">
                    Ingat kata sandi Anda?
                    <a href="{{ route('login') }}" class="text-purple-700 font-semibold hover:underline">
                        Kembali ke Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>