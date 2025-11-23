<!DOCTYPE html>
<html lang="en">
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
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 50%, #ffffff 100%);
            min-height: 100vh;
        }

        .slide-in {
            animation: slideIn 0.8s ease-out forwards;
            opacity: 0;
            transform: translateX(-20px);
        }

        .slide-in-right {
            animation: slideInRight 0.8s ease-out forwards;
            opacity: 0;
            transform: translateX(20px);
        }

        .fade-in {
            animation: fadeIn 1s ease-out forwards;
            opacity: 0;
        }

        .bounce-in {
            animation: bounceIn 0.8s ease-out forwards;
            opacity: 0;
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: white;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .welcome-section {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full login-container rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
        <!-- Left Side - Login Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center slide-in">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2 fade-in" style="animation-delay: 0.2s;">Sign in to Kasir Resto</h1>
                <p class="text-gray-500 fade-in" style="animation-delay: 0.4s;">Access your admin dashboard</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg bounce-in">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-6">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition duration-300">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" required
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition duration-300">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>

                <button type="submit" class="btn-primary w-full py-3 px-4 text-white font-medium rounded-lg mt-6">
                    SIGN IN
                </button>
            </form>
        </div>

        <!-- Right Side - Welcome Message -->
        <div class="w-full md:w-1/2 welcome-section text-white p-8 md:p-12 flex flex-col justify-center items-center text-center slide-in-right">
            <div class="max-w-md">
                <h2 class="text-4xl font-bold mb-6 fade-in" style="animation-delay: 0.6s;">Hello, Friend!</h2>
                <p class="text-xl mb-8 fade-in" style="animation-delay: 0.8s;">Welcome back to Kasir Resto Admin Panel</p>
                <div class="w-24 h-1 bg-white mx-auto mb-8 fade-in" style="animation-delay: 1s;"></div>
                <p class="text-lg fade-in" style="animation-delay: 1.2s;">Manage your restaurant operations efficiently</p>
            </div>
        </div>
    </div>

    <script>
        // Menambahkan sedikit interaktivitas
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi form sederhana
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const email = form.querySelector('input[type="email"]');
                const password = form.querySelector('input[type="password"]');

                if (!email.value || !password.value) {
                    e.preventDefault();
                    alert('Please fill in all fields');
                }
            });

            // Efek hover untuk input fields
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('transform', 'scale-105');
                    this.parentElement.classList.add('transition', 'duration-300');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('transform', 'scale-105');
                });
            });
        });
    </script>
</body>
</html>
