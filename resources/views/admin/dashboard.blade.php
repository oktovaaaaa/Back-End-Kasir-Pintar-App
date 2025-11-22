<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - Kasir Resto</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Dashboard Admin</h1>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    @if (session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    <h2>Kasir Pending</h2>
    @if($pendingCashiers->isEmpty())
        <p>Tidak ada kasir pending.</p>
    @else
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Tanggal Lahir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingCashiers as $cashier)
                    <tr>
                        <td>{{ $cashier->name }}</td>
                        <td>{{ $cashier->email }}</td>
                        <td>{{ $cashier->phone }}</td>
                        <td>{{ $cashier->birth_date }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.cashiers.approve', $cashier->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.cashiers.reject', $cashier->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit">Reject</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Kasir Approved</h2>
    <ul>
        @foreach($approvedCashiers as $cashier)
            <li>{{ $cashier->name }} ({{ $cashier->email }})</li>
        @endforeach
    </ul>

    <h2>Kasir Rejected</h2>
    <ul>
        @foreach($rejectedCashiers as $cashier)
            <li>{{ $cashier->name }} ({{ $cashier->email }})</li>
        @endforeach
    </ul>

</body>
</html>
