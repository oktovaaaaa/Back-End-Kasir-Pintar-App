<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kasir Resto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            transition: all 0.3s ease;
        }

        .main-content {
            transition: all 0.3s ease;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-6">
                <h1 class="text-xl font-bold">Kasir Resto</h1>
                <p class="text-blue-200 text-sm">Admin Dashboard</p>
            </div>

            <nav class="flex-1 px-4 py-6">
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 bg-blue-700 rounded-lg text-white">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 hover:bg-blue-700 rounded-lg text-blue-100 transition duration-300">
                            <i class="fas fa-users mr-3"></i>
                            Kelola Kasir
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 hover:bg-blue-700 rounded-lg text-blue-100 transition duration-300">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Laporan
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 hover:bg-blue-700 rounded-lg text-blue-100 transition duration-300">
                            <i class="fas fa-cog mr-3"></i>
                            Pengaturan
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-blue-700">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-blue-200 hover:bg-blue-700 rounded-lg transition duration-300">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm py-4 px-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="font-medium">Admin User</p>
                            <p class="text-sm text-gray-500">Administrator</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-300 fade-in">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="card bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Kasir Pending</p>
                                <h3 class="text-2xl font-bold mt-1">{{ $pendingCashiers->count() }}</h3>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Kasir Approved</p>
                                <h3 class="text-2xl font-bold mt-1">{{ $approvedCashiers->count() }}</h3>
                            </div>
                            <div class="p-3 bg-green-100 rounded-lg">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Kasir Rejected</p>
                                <h3 class="text-2xl font-bold mt-1">{{ $rejectedCashiers->count() }}</h3>
                            </div>
                            <div class="p-3 bg-red-100 rounded-lg">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kasir Pending Section -->
                <div class="bg-white rounded-xl shadow-sm mb-8 overflow-hidden fade-in">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-users mr-2 text-yellow-500"></i>
                            Kasir Pending
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Menunggu persetujuan admin</p>
                    </div>

                    <div class="p-6">
                        @if($pendingCashiers->isEmpty())
                            <div class="text-center py-8">
                                <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                                <p class="text-gray-500 text-lg">Tidak ada kasir pending.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Lahir</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pendingCashiers as $cashier)
                                            <tr class="hover:bg-gray-50 transition duration-300">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user text-blue-600"></i>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $cashier->name }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $cashier->email }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $cashier->phone }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $cashier->birth_date }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-2">
                                                        <form method="POST" action="{{ route('admin.cashiers.approve', $cashier->id) }}">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-300">
                                                                <i class="fas fa-check mr-1"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.cashiers.reject', $cashier->id) }}">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-300">
                                                                <i class="fas fa-times mr-1"></i> Reject
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Kasir Approved Section -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden fade-in">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-user-check mr-2 text-green-500"></i>
                                Kasir Approved
                            </h2>
                            <p class="text-gray-500 text-sm mt-1">Kasir yang telah disetujui</p>
                        </div>

                        <div class="p-6">
                            @if($approvedCashiers->isEmpty())
                                <div class="text-center py-4">
                                    <i class="fas fa-user-check text-gray-300 text-3xl mb-2"></i>
                                    <p class="text-gray-500">Tidak ada kasir approved.</p>
                                </div>
                            @else
                                <ul class="divide-y divide-gray-200">
                                    @foreach($approvedCashiers as $cashier)
                                        <li class="py-3 flex justify-between items-center">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $cashier->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $cashier->email }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-approved">
                                                <i class="fas fa-check mr-1"></i> Approved
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- Kasir Rejected Section -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden fade-in">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-user-times mr-2 text-red-500"></i>
                                Kasir Rejected
                            </h2>
                            <p class="text-gray-500 text-sm mt-1">Kasir yang ditolak</p>
                        </div>

                        <div class="p-6">
                            @if($rejectedCashiers->isEmpty())
                                <div class="text-center py-4">
                                    <i class="fas fa-user-times text-gray-300 text-3xl mb-2"></i>
                                    <p class="text-gray-500">Tidak ada kasir rejected.</p>
                                </div>
                            @else
                                <ul class="divide-y divide-gray-200">
                                    @foreach($rejectedCashiers as $cashier)
                                        <li class="py-3 flex justify-between items-center">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $cashier->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $cashier->email }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-rejected">
                                                <i class="fas fa-times mr-1"></i> Rejected
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Menambahkan efek interaktif
        document.addEventListener('DOMContentLoaded', function() {
            // Menambahkan animasi pada card saat dihover
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.1)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '';
                });
            });

            // Konfirmasi sebelum reject
            const rejectButtons = document.querySelectorAll('form[action*="reject"] button');
            rejectButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Apakah Anda yakin ingin menolak kasir ini?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>
