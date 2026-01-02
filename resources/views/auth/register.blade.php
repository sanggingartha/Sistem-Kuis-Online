<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-white">
        <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-lg overflow-hidden max-w-5xl w-full">

            <!-- Bagian Kiri: Gambar -->
            <div class="hidden md:flex md:w-1/2 items-center justify-center bg-purple-600">
                <img src="{{ asset('images/murid.jpg') }}" alt="Register ilustrasi"
                    class="object-cover w-full h-full opacity-90">
            </div>

            <!-- Bagian Kanan: Form Register -->
            <div class="w-full md:w-1/2 bg-white p-10 flex flex-col justify-center">

                <h1 class="text-3xl font-bold text-center text-purple-700 mb-6">Daftar Akun Baru</h1>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Role -->
                    <div class="flex justify-center mb-6 border border-gray-300 rounded-lg overflow-hidden">
                        <input type="hidden" name="role" id="role" value="{{ old('role', 'mahasiswa') }}">
                        <button type="button" class="role-btn w-1/2 py-2 font-semibold" data-role="mahasiswa">
                            Mahasiswa
                        </button>
                        <button type="button" class="role-btn w-1/2 py-2 font-semibold" data-role="dosen">
                            Dosen
                        </button>
                    </div>

                    <!-- Nama -->
                    <div class="relative">
                        <x-text-input id="name"
                            class="block px-3 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg
                                   border border-gray-300 appearance-none focus:outline-none focus:ring-0
                                   focus:border-purple-600 peer"
                            type="text" name="name" :value="old('name')" required autocomplete="name"
                            placeholder=" " />
                        <x-input-label for="name"
                            class="absolute text-sm text-gray-500 duration-300 transform
                                   -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2
                                   peer-focus:px-2 peer-focus:text-purple-700
                                   peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2
                                   peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75
                                   peer-focus:-translate-y-4 left-1"
                            :value="__('Nama Lengkap')" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div class="relative">
                        <x-text-input id="email"
                            class="block px-3 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg
                                   border border-gray-300 appearance-none focus:outline-none focus:ring-0
                                   focus:border-purple-600 peer"
                            type="email" name="email" :value="old('email')" required autocomplete="username"
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

                    <!-- Password -->
                    <div class="relative">
                        <x-text-input id="password"
                            class="block px-3 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg
                                   border border-gray-300 appearance-none focus:outline-none focus:ring-0
                                   focus:border-purple-600 peer"
                            type="password" name="password" required autocomplete="new-password" placeholder=" " />
                        <x-input-label for="password"
                            class="absolute text-sm text-gray-500 duration-300 transform
                                   -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2
                                   peer-focus:px-2 peer-focus:text-purple-700
                                   peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2
                                   peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75
                                   peer-focus:-translate-y-4 left-1"
                            :value="__('Kata Sandi')" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="relative">
                        <x-text-input id="password_confirmation"
                            class="block px-3 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg
                                   border border-gray-300 appearance-none focus:outline-none focus:ring-0
                                   focus:border-purple-600 peer"
                            type="password" name="password_confirmation" required autocomplete="new-password"
                            placeholder=" " />
                        <x-input-label for="password_confirmation"
                            class="absolute text-sm text-gray-500 duration-300 transform
                                   -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2
                                   peer-focus:text-purple-700 peer-focus:px-2
                                   peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2
                                   peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75
                                   peer-focus:-translate-y-4 left-1"
                            :value="__('Konfirmasi Kata Sandi')" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Tombol Register -->
                    <button type="submit"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-purple-900 font-bold py-2 rounded-lg transition flex items-center justify-center">
                        {{ __('Daftar') }}
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-4">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-purple-700 font-semibold hover:underline">
                        Masuk di sini
                    </a>
                </p>

            </div>
        </div>
    </div>

    <!-- JS -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const roleInput = document.getElementById("role");
            const roleButtons = document.querySelectorAll(".role-btn");

            // Default role mahasiswa
            roleInput.value = "mahasiswa";
            document.querySelector('.role-btn[data-role="mahasiswa"]').className =
                "role-btn w-1/2 py-2 font-semibold bg-purple-700 text-white";

            roleButtons.forEach(button => {
                button.addEventListener("click", () => {
                    roleInput.value = button.dataset.role;

                    roleButtons.forEach(btn => {
                        btn.className =
                            "role-btn w-1/2 py-2 font-semibold bg-white text-gray-600 hover:bg-gray-50";
                    });

                    button.className = "role-btn w-1/2 py-2 font-semibold bg-purple-700 text-white";
                });
            });

            // Spinner saat form submit
            const form = document.querySelector("form");
            const submitBtn = form.querySelector('button[type="submit"]');

            form.addEventListener("submit", () => {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg aria-hidden="true" role="status" class="inline w-5 h-5 me-2 text-purple-900 animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858
                        100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50
                        0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08197
                        50.5908C9.08197 73.1895 27.4013 91.5088 50 91.5088C72.5987
                        91.5088 90.918 73.1895 90.918 50.5908C90.918
                        27.9921 72.5987 9.67285 50 9.67285C27.4013 9.67285 9.08197
                        27.9921 9.08197 50.5908Z" fill="#E5E7EB"/>
                        <path d="M93.9676 39.0409C96.393 38.4038
                        97.8624 35.9116 97.0079 33.5539C95.2932 28.8227
                        92.871 24.3692 89.8167 20.348C85.8452 15.1192
                        80.8826 10.7238 75.2124 7.41289C69.5422
                        4.10194 63.2754 1.94025 56.7698 1.05124C51.7666
                        0.367541 46.6976 0.446843 41.7345 1.27873C39.2613
                        1.69328 37.813 4.19778 38.4501 6.62326C39.0873
                        9.04874 41.5694 10.4717 44.0505 10.1071C47.8511
                        9.54855 51.7191 9.52689 55.5402 10.0491C60.8643
                        10.7766 65.9928 12.5457 70.6331 15.2552C75.2735
                        17.9648 79.3347 21.5619 82.5849 25.841C84.9175
                        28.9121 86.7997 32.2913 88.1811 35.8758C89.083
                        38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentColor"/>
                    </svg>
                    Mendaftar...
                `;
            });
        });
    </script>

</x-guest-layout>
