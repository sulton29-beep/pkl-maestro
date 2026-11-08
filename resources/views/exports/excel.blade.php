<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; }

        /* HEADER KOP */
        .kop-sekolah {
            background-color: #1e40af;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            text-align: center;
            border: 1px solid #1e3a8a;
        }
        .kop-sub {
            background-color: #1e40af;
            color: #dbeafe;
            font-size: 10px;
            padding: 4px 10px;
            text-align: center;
            border: 1px solid #1e3a8a;
        }
        .kop-info {
            background-color: #eff6ff;
            color: #1e3a8a;
            font-size: 10px;
            padding: 4px 10px;
            text-align: center;
            border: 1px solid #bfdbfe;
        }
        .judul-laporan {
            background-color: #f59e0b;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            padding: 8px;
            text-align: center;
            border: 1px solid #d97706;
        }
        .meta-info {
            background-color: #fef3c7;
            color: #78350f;
            font-size: 10px;
            padding: 4px 10px;
            text-align: left;
            border: 1px solid #fcd34d;
        }

        /* HEADER TABEL */
        .header-row th {
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 8px;
            text-align: center;
            border: 1px solid #1e3a8a;
            font-size: 11px;
        }

        /* BODY */
        .data-row td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
            vertical-align: middle;
        }
        .data-row:nth-child(even) td {
            background-color: #f8fafc;
        }
        .data-row:nth-child(odd) td {
            background-color: #ffffff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        /* FOOTER */
        .footer-info {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 9px;
            padding: 6px 10px;
            text-align: center;
            border: 1px solid #cbd5e1;
        }
        .total-row td {
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 8px;
            border: 1px solid #1e3a8a;
            font-size: 11px;
            text-align: center;
        }
    </style>
</head>
<body>

<table>
    {{-- ============ KOP SEKOLAH ============ --}}
    <tr>
        <td colspan="{{ count($columns) }}" class="kop-sekolah">
            {{ strtoupper($sekolah['nama']) }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ count($columns) }}" class="kop-sub">
            NPSN: {{ $sekolah['npsn'] }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ count($columns) }}" class="kop-info">
            {{ $sekolah['alamat'] }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ count($columns) }}" style="height: 8px;"></td>
    </tr>

    {{-- ============ JUDUL LAPORAN ============ --}}
    <tr>
        <td colspan="{{ count($columns) }}" class="judul-laporan">
            {{ $judul }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ count($columns) }}" class="meta-info">
            Diekspor: {{ $waktu }} &nbsp; · &nbsp; Oleh: {{ $exporter }} &nbsp; · &nbsp; Total: {{ count($rows) }} baris
        </td>
    </tr>
    <tr>
        <td colspan="{{ count($columns) }}" style="height: 6px;"></td>
    </tr>

    {{-- ============ HEADER TABEL ============ --}}
    <tr class="header-row">
        @foreach($columns as $col)
            <th>{{ $col }}</th>
        @endforeach
    </tr>

    {{-- ============ ISI TABEL ============ --}}
    @forelse($rows as $row)
        <tr class="data-row">
            @foreach($row as $i => $cell)
                <td class="{{ $i === 0 ? 'text-center' : '' }}">
                    {{ $cell }}
                </td>
            @endforeach
        </tr>
    @empty
        <tr class="data-row">
            <td colspan="{{ count($columns) }}" class="text-center" style="padding: 20px; color: #94a3b8; font-style: italic;">
                Belum ada data.
            </td>
        </tr>
    @endforelse

    {{-- ============ TOTAL ============ --}}
    <tr>
        <td colspan="{{ count($columns) }}" style="height: 8px;"></td>
    </tr>
    <tr class="total-row">
        <td colspan="{{ count($columns) }}">
            TOTAL: {{ count($rows) }} DATA
        </td>
    </tr>

    {{-- ============ FOOTER ============ --}}
    <tr>
        <td colspan="{{ count($columns) }}" style="height: 8px;"></td>
    </tr>
    <tr>
        <td colspan="{{ count($columns) }}" class="footer-info">
            Dokumen ini digenerate otomatis oleh sistem <strong>PKL SMKS MAESTRO</strong> pada {{ $waktu }}.
            &copy; {{ date('Y') }} {{ $sekolah['nama'] }}. All rights reserved.
        </td>
    </tr>
</table>

</body>
</html>