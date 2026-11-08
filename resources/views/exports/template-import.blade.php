<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; }

        /* KOP SEKOLAH */
        .kop-sekolah {
            background-color: #1e40af;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            padding: 12px;
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
        .judul-template {
            background-color: #f59e0b;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            padding: 10px;
            text-align: center;
            border: 1px solid #d97706;
        }

        /* PETUNJUK */
        .petunjuk-title {
            background-color: #fef3c7;
            color: #78350f;
            font-weight: bold;
            font-size: 11px;
            padding: 8px 12px;
            border-left: 5px solid #f59e0b;
            border-top: 1px solid #fcd34d;
            border-right: 1px solid #fcd34d;
            text-align: left;
        }
        .petunjuk-body {
            background-color: #fffbeb;
            color: #78350f;
            font-size: 10px;
            padding: 4px 12px 4px 30px;
            border-left: 5px solid #f59e0b;
            border-right: 1px solid #fcd34d;
            border-bottom: 1px solid #fcd34d;
            text-align: left;
            line-height: 1.6;
        }
        .petunjuk-body:last-child {
            padding-bottom: 10px;
        }

        /* HEADER TABEL */
        .header-row th {
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            padding: 8px;
            text-align: center;
            border: 1px solid #1e3a8a;
            font-size: 11px;
            vertical-align: middle;
        }
        .header-row th.required {
            background-color: #dc2626;
            border-color: #991b1b;
        }
        .star {
            color: #fde047;
            font-weight: bold;
        }

        /* BARIS CONTOH */
        .example-row td {
            background-color: #ecfdf5;
            padding: 6px 8px;
            border: 1px solid #a7f3d0;
            font-size: 10px;
            color: #065f46;
            font-style: italic;
        }

        /* BARIS KOSONG (buat user isi) */
        .empty-row td {
            background-color: #ffffff;
            padding: 10px 8px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
        }

        /* CATATAN BAWAH */
        .catatan {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 9px;
            padding: 8px 12px;
            text-align: center;
            border: 1px solid #cbd5e1;
            line-height: 1.5;
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

    {{-- ============ JUDUL TEMPLATE ============ --}}
    <tr>
        <td colspan="{{ count($columns) }}" class="judul-template">
            {{ $judul }}
        </td>
    </tr>

    {{-- ============ BARIS KOSONG ============ --}}
    <tr><td colspan="{{ count($columns) }}" style="height: 6px; background-color: #ffffff;"></td></tr>

    {{-- ============ PETUNJUK PENGISIAN ============ --}}
    <tr>
        <td colspan="{{ count($columns) }}" class="petunjuk-title">
            📋 PETUNJUK PENGISIAN
        </td>
    </tr>
    @foreach($petunjuk as $idx => $pt)
        <tr>
            <td colspan="{{ count($columns) }}" class="petunjuk-body">
                {{ $idx + 1 }}. {{ $pt }}
            </td>
        </tr>
    @endforeach

    {{-- ============ BARIS KOSONG ============ --}}
    <tr><td colspan="{{ count($columns) }}" style="height: 10px; background-color: #ffffff;"></td></tr>

    {{-- ============ HEADER TABEL ============ --}}
    <tr class="header-row">
        @foreach($columns as $col)
            <th class="{{ $col['required'] ? 'required' : '' }}"
                style="width: {{ $col['width'] ?? 120 }}px;">
                {{ $col['label'] }}
                @if($col['required']) <span class="star">*</span> @endif
            </th>
        @endforeach
    </tr>

    {{-- ============ BARIS CONTOH (readonly hint) ============ --}}
    <tr class="example-row">
        @foreach($example as $val)
            <td>{{ $val }}</td>
        @endforeach
    </tr>

    {{-- ============ 10 BARIS KOSONG BUAT USER ISI ============ --}}
    @for($i = 0; $i < 10; $i++)
        <tr class="empty-row">
            @foreach($columns as $col)
                <td>&nbsp;</td>
            @endforeach
        </tr>
    @endfor

    {{-- ============ BARIS KOSONG ============ --}}
    <tr><td colspan="{{ count($columns) }}" style="height: 10px; background-color: #ffffff;"></td></tr>

    {{-- ============ CATATAN BAWAH ============ --}}
    <tr>
        <td colspan="{{ count($columns) }}" class="catatan">
            <strong>CATATAN:</strong> Baris berwarna <span style="color: #059669; font-weight: bold;">hijau</span> di atas adalah <strong>CONTOH</strong> — HAPUS baris tersebut sebelum di-upload.<br>
            File ini digenerate otomatis oleh sistem PKL SMKS Maestro pada {{ $waktu }} oleh {{ $exporter }}.<br>
            &copy; {{ date('Y') }} {{ $sekolah['nama'] }}. All rights reserved.
        </td>
    </tr>
</table>

</body>
</html>