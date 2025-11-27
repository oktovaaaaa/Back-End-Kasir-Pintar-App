{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard Admin - Kasir Resto')
@section('page-title', 'Dashboard Admin')

@section('content')
    @php
        $primaryBlue = '#57A0D3';
    @endphp

    <div class="max-w-6xl mx-auto space-y-8">

        {{-- Alert sukses --}}
        @if (session('success'))
            <div
                class="flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                     class="h-5 w-5 text-green-500" fill="none" stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75 11.25 15 15 9.75"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Kasir Pending --}}
            <div
                class="rounded-2xl border border-[#57A0D3]/30 bg-gradient-to-br from-[#57A0D3]/5 via-white to-[#57A0D3]/10 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-[#57A0D3]">
                            Kasir Pending
                        </p>
                        <h3 class="text-3xl font-extrabold text-slate-800">
                            {{ $pendingCashiers->count() }}
                        </h3>
                        <p class="text-xs text-gray-500">Menunggu persetujuan Anda</p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-50 shadow-sm">
                        {{-- Icon jam / pending (amber) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6v6l3 3"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Kasir Approved --}}
            <div
                class="rounded-2xl border border-[#57A0D3]/30 bg-gradient-to-br from-[#57A0D3]/5 via-white to-[#57A0D3]/10 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-[#57A0D3]">
                            Kasir Approved
                        </p>
                        <h3 class="text-3xl font-extrabold text-emerald-600">
                            {{ $approvedCashiers->count() }}
                        </h3>
                        <p class="text-xs text-gray-500">Sudah aktif menggunakan sistem</p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 shadow-sm">
                        {{-- Icon shield check (green) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12.75 11.25 15 15 9.75"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 3.75 5.25 6v6.75c0 1.507.804 2.887 2.116 3.64L12 20.25l4.634-3.86A4.2 4.2 0 0 0 18.75 12.75V6L12 3.75Z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Kasir Rejected --}}
            <div
                class="rounded-2xl border border-[#57A0D3]/30 bg-gradient-to-br from-[#57A0D3]/5 via-white to-[#57A0D3]/10 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-[#57A0D3]">
                            Kasir Rejected
                        </p>
                        <h3 class="text-3xl font-extrabold text-red-600">
                            {{ $rejectedCashiers->count() }}
                        </h3>
                        <p class="text-xs text-gray-500">Pengajuan yang ditolak</p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-full bg-red-50 shadow-sm">
                        {{-- Icon X circle (red) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             class="h-6 w-6 text-red-500" fill="none" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m15 9-6 6m0-6 6 6"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kasir Pending --}}
        <div class="overflow-hidden rounded-2xl border border-[#57A0D3]/20 bg-white shadow-sm">
            <div class="flex flex-col gap-2 border-b border-[#57A0D3]/10 bg-[#57A0D3]/5 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-[#57A0D3]/15 text-[#57A0D3]">
                        {{-- Icon user group (blue) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             class="h-5 w-5" fill="none" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M18 18a3 3 0 0 0-3-3H9a3 3 0 0 0-3 3"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.5 7.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8.25 8.25A2.25 2.25 0 1 1 8.26 3.75a2.25 2.25 0 0 1-.01 4.5Z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-semibold text-slate-800">Kasir Pending</h2>
                        <p class="text-xs text-slate-500">Menunggu persetujuan admin</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                @if($pendingCashiers->isEmpty())
                    <div class="py-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             class="mx-auto mb-3 h-10 w-10 text-[#57A0D3]/20" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6v6l3 3"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Tidak ada kasir pending untuk saat ini.</p>
                    </div>
                @else
                    <div class="max-w-full overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#57A0D3]/10 text-sm">
                            <thead class="bg-[#57A0D3]/5">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[#57A0D3]">
                                        Nama
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[#57A0D3]">
                                        Email
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[#57A0D3]">
                                        Phone
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[#57A0D3]">
                                        Tanggal Lahir
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-[#57A0D3]">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#57A0D3]/10 bg-white">
                                @foreach($pendingCashiers as $cashier)
                                    <tr class="transition hover:bg-[#57A0D3]/5">
                                        <td class="whitespace-nowrap px-4 py-3">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-[#57A0D3]/15 text-[#57A0D3]">
                                                    {{-- Icon user --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 24 24"
                                                         class="h-5 w-5" fill="none"
                                                         stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              d="M15.75 7.5A3.75 3.75 0 1 1 8.25 7.5a3.75 3.75 0 0 1 7.5 0Z"/>
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              d="M4.5 20.25a7.5 7.5 0 0 1 15 0"/>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-xs font-semibold text-gray-900 sm:text-sm">
                                                        {{ $cashier->name }}
                                                    </div>
                                                    <div class="text-[11px] text-gray-400">
                                                        ID #{{ $cashier->id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-700 sm:text-sm">
                                            {{ $cashier->email }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-700 sm:text-sm">
                                            {{ $cashier->phone }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-700 sm:text-sm">
                                            {{ $cashier->birth_date }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-xs sm:text-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                {{-- Button Approve (HIJAU) --}}
                                                <form method="POST"
                                                      action="{{ route('admin.cashiers.approve', $cashier->id) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-500 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:ring-offset-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                             viewBox="0 0 24 24"
                                                             class="h-4 w-4" fill="none"
                                                             stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round"
                                                                  stroke-linejoin="round"
                                                                  d="M9 12.75 11.25 15 15 9.75"/>
                                                            <path stroke-linecap="round"
                                                                  stroke-linejoin="round"
                                                                  d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>

                                                {{-- Button Reject (MERAH) --}}
                                                <form method="POST"
                                                      action="{{ route('admin.cashiers.reject', $cashier->id) }}"
                                                      class="form-reject">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-200 focus:ring-offset-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                             viewBox="0 0 24 24"
                                                             class="h-4 w-4" fill="none"
                                                             stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round"
                                                                  stroke-linejoin="round"
                                                                  d="m15 9-6 6m0-6 6 6"/>
                                                            <path stroke-linecap="round"
                                                                  stroke-linejoin="round"
                                                                  d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                        </svg>
                                                        Reject
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

        {{-- Approved & Rejected list --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Kasir Approved --}}
            <div class="overflow-hidden rounded-2xl border border-[#57A0D3]/20 bg-white shadow-sm">
                <div class="border-b border-[#57A0D3]/10 bg-[#57A0D3]/5 px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                            {{-- Icon user check (green) --}}
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 class="h-5 w-5" fill="none"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12.75 11.25 15 15 9.75"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 7.5A3.75 3.75 0 1 1 8.25 7.5a3.75 3.75 0 0 1 7.5 0Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4.5 20.25a7.5 7.5 0 0 1 15 0"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-slate-800">Kasir Approved</h2>
                            <p class="text-xs text-slate-500">Kasir yang telah disetujui</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    @if($approvedCashiers->isEmpty())
                        <div class="py-4 text-center">
                            <p class="text-sm text-gray-500">Belum ada kasir approved.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-[#57A0D3]/10">
                            @foreach($approvedCashiers as $cashier)
                                <li class="flex items-center justify-between py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $cashier->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $cashier->email }}</p>
                                    </div>
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-600">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 24 24"
                                             class="h-4 w-4" fill="none"
                                             stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M9 12.75 11.25 15 15 9.75"/>
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        Approved
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Kasir Rejected --}}
            <div class="overflow-hidden rounded-2xl border border-[#57A0D3]/20 bg-white shadow-sm">
                <div class="border-b border-[#57A0D3]/10 bg-[#57A0D3]/5 px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-red-50 text-red-600">
                            {{-- Icon user minus/X (red) --}}
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 class="h-5 w-5" fill="none"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m15 9-6 6m0-6 6 6"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15.75 7.5A3.75 3.75 0 1 1 8.25 7.5a3.75 3.75 0 0 1 7.5 0Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4.5 20.25a7.5 7.5 0 0 1 15 0"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-slate-800">Kasir Rejected</h2>
                            <p class="text-xs text-slate-500">Kasir yang pengajuannya ditolak</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    @if($rejectedCashiers->isEmpty())
                        <div class="py-4 text-center">
                            <p class="text-sm text-gray-500">Belum ada kasir rejected.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-[#57A0D3]/10">
                            @foreach($rejectedCashiers as $cashier)
                                <li class="flex items-center justify-between py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $cashier->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $cashier->email }}</p>
                                    </div>
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-semibold text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 24 24"
                                             class="h-4 w-4" fill="none"
                                             stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="m15 9-6 6m0-6 6 6"/>
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        Rejected
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rejectForms = document.querySelectorAll('.form-reject');
    rejectForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin menolak kasir ini?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
