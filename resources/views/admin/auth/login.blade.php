<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kasir Resto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
        }

        /* warna utama project #57A0D3 */
        .primary-bg {
            background-color: #57A0D3;
        }

        .primary-gradient {
            background: linear-gradient(135deg, #57A0D3, #1D4ED8);
        }

        .form-input-custom {
            background-color: #eff6ff;
            border: 1px solid #57A0D3;
            transition: all 0.2s ease;
        }

        .form-input-custom:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(87, 160, 211, 0.3);
            border-color: #1D4ED8;
        }

        .btn-primary {
            background: linear-gradient(135deg, #57A0D3, #1D4ED8);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(87, 160, 211, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

<div class="max-w-5xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

    <!-- LEFT: ilustrasi + welcome -->
    <div class="w-full md:w-1/2 primary-gradient text-white flex flex-col justify-center p-8 md:p-10 relative">
        <div class="absolute -top-10 -left-10 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 rounded-full bg-white/10 blur-3xl"></div>

        <div class="flex flex-col items-center text-center gap-6 relative z-10">
            <div class="w-40 h-40 md:w-52 md:h-52 rounded-3xl bg-white/10 flex items-center justify-center overflow-hidden">
                <img
                    src="{{ asset('images/kasir_pintar.png') }}"
                    alt="Kasir Pintar"
                    class="w-full h-full object-contain"
                >
            </div>

            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">
                    Selamat Datang di Kasir Pintar
                </h2>
                <p class="text-sm md:text-base text-blue-100">
                    Pantau penjualan, kelola kasir, dan atur keuangan resto kamu
                    dengan lebih rapi dan cepat.
                </p>
            </div>

            <div class="mt-4 text-xs md:text-sm text-blue-100/80">
                “Dashboard untuk seluruh laporan dan kinerja kasir.”
            </div>
        </div>
    </div>

    <!-- RIGHT: form login -->
    <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-center">
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Login Admin
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Masuk ke dashboard admin menggunakan email dan password.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div class="space-y-1.5">
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <!-- ikon email -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-blue-500" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 4h16c.55 0 1 .45 1 1v14c0 .55-.45 1-1 1H4a1 1 0 0 1-1-1V5c0-.55.45-1 1-1z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m4 7 8 5 8-5"/>
                        </svg>
                    </span>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="form-input-custom w-full rounded-xl py-3 pl-11 pr-3 text-sm text-gray-800"
                        autocomplete="email"
                        placeholder="Masukkan email"
                    >
                </div>
            </div>

            {{-- Password --}}
            <div class="space-y-1.5">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <!-- ikon password -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5 text-blue-500" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <rect x="4" y="10" width="16" height="10" rx="2" ry="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8 10V7a4 4 0 0 1 8 0v3"/>
                        </svg>
                    </span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="form-input-custom w-full rounded-xl py-3 pl-11 pr-10 text-sm text-gray-800"
                        autocomplete="current-password"
                        placeholder="Masukkan password"
                    >
                </div>
            </div>

            <div class="flex items-center justify-between text-xs md:text-sm">
                <label class="inline-flex items-center gap-2 text-gray-600">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span>Ingat saya</span>
                </label>
                {{-- kalau nanti ada fitur lupa password, tinggal aktifkan link di sini --}}
                {{-- <a href="#" class="text-blue-600 hover:underline">Lupa password?</a> --}}
            </div>

            <button
                type="submit"
                class="btn-primary w-full rounded-xl py-3 text-sm md:text-base font-semibold text-white shadow-md"
            >
                Masuk
            </button>

            {{-- tidak ada login Google sesuai permintaan --}}
        </form>
    </div>
</div>

</body>
</html>
