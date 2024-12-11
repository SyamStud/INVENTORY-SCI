<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Perizinan Expired</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .license-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .license-table th,
        .license-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .license-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .expired {
            color: red;
            font-weight: bold;
        }

        .soon-expire {
            color: orange;
        }

        .btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.8em;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="alert">
        <h1>⚠️ Peringatan Dokumen Perizinan Expired</h1>
        <p>Beberapa dokumen perizinan telah atau akan segera expired. Harap segera ditindaklanjuti.</p>
    </div>

    <table class="license-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
                <th>Nomor Dokumen</th>
                <th>Tanggal Expired</th>
                {{-- <th>Status</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($expiredDocuments as $index => $doc)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $doc->name }}</td>
                    <td>{{ $doc->document_number }}</td>
                    <td>{{ $doc->due_date->format('d F Y') }}</td>
                    {{-- <td class="{{ $doc->is_expired ? 'expired' : 'soon-expire' }}">
                        {{ $doc->is_expired ? 'Expired' : 'Akan Expired' }}
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: center;">
        <a href="{{ route('permits.index') }}" class="btn">Kelola Dokumen Perizinan</a>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis pada {{ now()->format('d F Y H:i') }}</p>
        <p>Harap segera perpanjang dokumen yang telah expired atau akan segera expired.</p>
    </div>
</body>

</html>
