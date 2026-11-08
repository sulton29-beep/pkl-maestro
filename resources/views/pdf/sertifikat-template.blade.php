<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page { size: 297mm 210mm; margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Serif', 'Times New Roman', serif;
        color: {{ $template->warna_teks }};
        margin: 0;
        padding: 0;
    }

    /* ============ STRUKTUR HALAMAN ============ */
    .page {
        width: 297mm;
        height: 209mm;
        position: relative;
        overflow: hidden;
    }
    .page-first { page-break-after: always; }
    .page-last  { page-break-after: auto; }

    /* ============ HALAMAN 1: OVERLAY DI BACKGROUND ============ */
    .background-img {
        position: absolute;
        top: 0; left: 0;
        width: 297mm;
        height: 209mm;
        z-index: 0;
    }
    .watermark {
        position: absolute;
        top: 40%; left: 0; right: 0;
        text-align: center; font-size: 110px;
        color: rgba(200, 0, 0, 0.12);
        font-weight: bold; letter-spacing: 18px;
        z-index: 5;
    }
    .overlay {
        position: absolute;
        top: 0; left: 0;
        width: 297mm; height: 209mm;
        z-index: 10;
    }

    .field-nomor {
        position: absolute;
        top: 76mm; left: 0;
        width: 297mm;
        text-align: center;
        font-size: 13px;
        font-family: 'DejaVu Sans', sans-serif;
    }
    .biodata-block {
        position: absolute;
        top: 92mm; left: 70mm;
        width: 150mm;
    }
    .biodata-row { margin-bottom: 1.3mm; font-size: 12.5px; }
    .biodata-label { display: inline-block; width: 36mm; font-family: 'DejaVu Sans', sans-serif; }
    .biodata-colon { display: inline-block; width: 4mm; text-align: center; }
    .biodata-value { display: inline-block; font-weight: bold; color: {{ $template->warna_aksen }}; }

    .info-block {
        position: absolute;
        top: 124mm; left: 70mm;
        width: 150mm;
    }
    .info-row { margin-bottom: 1.3mm; font-size: 12px; }
    .info-label { display: inline-block; width: 36mm; font-family: 'DejaVu Sans', sans-serif; }
    .info-colon { display: inline-block; width: 4mm; text-align: center; }
    .info-value { display: inline-block; font-weight: bold; color: {{ $template->warna_aksen }}; }

    .nilai-block {
        position: absolute;
        top: 146mm; left: 0;
        width: 297mm;
        text-align: center;
    }
    .nilai-akhir {
        font-size: 22px;
        font-weight: bold;
        color: {{ $template->warna_aksen }};
        font-family: 'DejaVu Sans', sans-serif;
    }
    .nilai-predikat {
        font-size: 12px;
        color: {{ $template->warna_teks }};
        font-family: 'DejaVu Sans', sans-serif;
        margin-top: 0.5mm;
    }

    .ttd-dudi {
        position: absolute;
        top: 155mm; left: 25mm;
        width: 70mm;
        text-align: center;
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
    }
    .ttd-kepsek {
        position: absolute;
        top: 155mm; right: 25mm;
        width: 70mm;
        text-align: center;
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
    }
    .ttd-jabatan { margin-bottom: 1.5mm; }
    .ttd-img { height: 20mm; margin: 1.5mm auto; }
    .ttd-img img { max-height: 20mm; max-width: 42mm; }
    .ttd-nama {
        font-weight: bold;
        text-decoration: underline;
        margin-top: 1mm;
        font-size: 11.5px;
    }
    .ttd-nip { font-size: 10px; color: #555; }

    .qr-block {
        position: absolute;
        bottom: 14mm; left: 0;
        width: 297mm;
        text-align: center;
    }
    .qr-block img { width: 22mm; height: 22mm; }
    .qr-label {
        font-size: 8px;
        color: #666;
        margin-top: 1mm;
        font-family: 'DejaVu Sans', sans-serif;
    }

    /* ============ HALAMAN 2: DAFTAR NILAI (FIXED) ============ */
    .page2-frame {
        position: absolute;
        top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
        border: 2px solid {{ $template->warna_aksen }};
    }
    .page2 {
        padding: 14mm 20mm;
        position: relative;
        z-index: 1;
    }
    .page2-title {
        text-align: center;
        font-size: 17px;
        font-weight: bold;
        color: {{ $template->warna_aksen }};
        border-bottom: 2px solid {{ $template->warna_aksen }};
        padding-bottom: 2.5mm;
        margin-bottom: 4mm;
        letter-spacing: 1px;
    }
    .identitas-tbl { width: 100%; font-size: 12px; margin-bottom: 4mm; margin-left: 5mm; }
    .identitas-tbl td { padding: 0.8mm 2mm; vertical-align: top; }
    .identitas-tbl .lbl { width: 48mm; }
    .identitas-tbl .col { width: 4mm; text-align: center; }
    .identitas-tbl .val { font-weight: bold; }

    .nilai-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        margin-top: 2mm;
    }
    .nilai-tbl th {
        background: {{ $template->warna_aksen }};
        color: #fff;
        padding: 2mm 3mm;
        text-align: left;
        font-weight: bold;
        border: 1px solid {{ $template->warna_aksen }};
        font-size: 10.5px;
    }
    .nilai-tbl td { padding: 1.7mm 3mm; border: 1px solid #cbd5e1; }
    .nilai-tbl .center { text-align: center; }
    .nilai-tbl .right { text-align: right; }
    .nilai-tbl .bobot { color: #666; font-size: 10px; }
    .nilai-tbl tr.total-row td {
        background: #f0f4ff;
        font-weight: bold;
        color: {{ $template->warna_aksen }};
        border-top: 2px solid {{ $template->warna_aksen }};
    }
    .nilai-tbl tr.akhir-row td {
        background: #fef3c7;
        font-weight: bold;
        color: #92400e;
        font-size: 13px;
    }
    .predikat-info {
        margin-top: 3mm;
        font-size: 10px;
        color: #444;
        padding: 2.5mm;
        background: #f8fafc;
        border-left: 3px solid {{ $template->warna_aksen }};
    }
    .predikat-info .title {
        font-weight: bold;
        margin-bottom: 1.5mm;
        color: {{ $template->warna_aksen }};
        font-size: 10.5px;
    }
    .predikat-info table { width: 100%; }
    .predikat-info td { padding: 0.6mm 2mm; }

    .catatan-wrap { margin-top: 3.5mm; font-size: 10.5px; }
    .catatan-wrap .item {
        margin-bottom: 2mm;
        padding: 2mm 3mm;
        background: #f8fafc;
        border-left: 3px solid {{ $template->warna_aksen }};
    }
    .catatan-wrap .label {
        font-weight: bold;
        color: {{ $template->warna_aksen }};
        font-size: 9.5px;
        margin-bottom: 0.6mm;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ttd-page2 { margin-top: 5mm; font-size: 11px; text-align: right; padding-right: 8mm; }
    .ttd-page2 .col { display: inline-block; text-align: center; width: 65mm; }
    .ttd-page2 .img-wrap { height: 16mm; margin: 1.5mm 0; }
    .ttd-page2 .img-wrap img { max-height: 16mm; max-width: 32mm; }
    .ttd-page2 .nm { font-weight: bold; text-decoration: underline; }
</style>
</head>
<body>

{{-- ==================== HALAMAN 1 ==================== --}}
<div class="page page-first">

    @if($bgDataUri)
        <img src="{{ $bgDataUri }}" class="background-img" alt="Background">
    @endif

    @if(!$sertifikat->isTerbit())
        <div class="watermark">DRAFT</div>
    @endif

    <div class="overlay">

        <div class="field-nomor">Nomor: {{ $sertifikat->nomor_sertifikat }}</div>

        <div class="biodata-block">
            <div class="biodata-row">
                <span class="biodata-label">Nama Lengkap</span>
                <span class="biodata-colon">:</span>
                <span class="biodata-value">{{ $siswa->nama }}</span>
            </div>
            <div class="biodata-row">
                <span class="biodata-label">NIS / NISN</span>
                <span class="biodata-colon">:</span>
                <span class="biodata-value">{{ $siswa->nis }} / {{ $siswa->nisn }}</span>
            </div>
            <div class="biodata-row">
                <span class="biodata-label">Jurusan</span>
                <span class="biodata-colon">:</span>
                <span class="biodata-value">{{ $siswa->jurusan->nama ?? '-' }}</span>
            </div>
            <div class="biodata-row">
                <span class="biodata-label">Kelas</span>
                <span class="biodata-colon">:</span>
                <span class="biodata-value">{{ $siswa->kelas }}</span>
            </div>
        </div>

        <div class="info-block">
            <div class="info-row">
                <span class="info-label">Tempat PKL</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ $penempatan->dudi->nama_perusahaan ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Periode PKL</span>
                <span class="info-colon">:</span>
                <span class="info-value">{{ $penempatan->tanggal_mulai?->format('d M Y') ?? '-' }} s/d {{ $penempatan->tanggal_selesai?->format('d M Y') ?? '-' }}</span>
            </div>
        </div>

        @if($nilai && $nilai->nilai_akhir)
            <div class="nilai-block">
                <div class="nilai-akhir">Nilai Akhir: {{ number_format($nilai->nilai_akhir, 2) }}</div>
                <div class="nilai-predikat">Predikat: {{ $nilai->predikat ?? '-' }}</div>
            </div>
        @endif

        <div class="ttd-dudi">
            <div class="ttd-jabatan">Pimpinan DUDI</div>
            <div class="ttd-img">
                @if($ttdDudiUri)
                    <img src="{{ $ttdDudiUri }}" alt="TTD DUDI">
                @endif
            </div>
            <div class="ttd-nama">{{ $penempatan->dudi->nama_pimpinan ?? '.........................' }}</div>
        </div>

        <div class="ttd-kepsek">
            <div class="ttd-jabatan">
                Tangerang, {{ $sertifikat->tanggal_terbit?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
            </div>
            <div class="ttd-jabatan">Kepala Sekolah</div>
            <div class="ttd-img">
                @if($ttdKepsekUri)
                    <img src="{{ $ttdKepsekUri }}" alt="TTD Kepsek">
                @endif
            </div>
            <div class="ttd-nama">{{ $setting->nama_kepsek ?? '.........................' }}</div>
            @if($setting->nip_kepsek)
                <div class="ttd-nip">NIP. {{ $setting->nip_kepsek }}</div>
            @endif
        </div>

        <div class="qr-block">
            <img src="{{ $qrDataUri }}" alt="QR Verifikasi">
            <div class="qr-label">Scan untuk verifikasi</div>
        </div>

    </div>
</div>

{{-- ==================== HALAMAN 2: DAFTAR NILAI ==================== --}}
<div class="page page-last">

    <div class="page2-frame"></div>

    <div class="page2">

        <div class="page2-title">DAFTAR NILAI PRAKTIK KERJA LAPANGAN</div>

        <table class="identitas-tbl">
            <tr>
                <td class="lbl">Nama</td>
                <td class="col">:</td>
                <td class="val">{{ $siswa->nama }}</td>
            </tr>
            <tr>
                <td class="lbl">Nomor Induk Siswa (NIS)</td>
                <td class="col">:</td>
                <td class="val">{{ $siswa->nis }}</td>
            </tr>
            <tr>
                <td class="lbl">Program Keahlian</td>
                <td class="col">:</td>
                <td class="val">{{ $siswa->jurusan->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Tempat PKL</td>
                <td class="col">:</td>
                <td class="val">{{ $penempatan->dudi->nama_perusahaan ?? '-' }}</td>
            </tr>
        </table>

        @php
            $bobot = \App\Models\ConfigBobot::getActive();
            $bobotDudi = (float) ($bobot->bobot_dudi ?? 50);
            $bobotGuru = (float) ($bobot->bobot_guru ?? 40);
            $bobotSidang = (float) ($bobot->bobot_sidang ?? 10);
        @endphp

        <table class="nilai-tbl">
            <thead>
                <tr>
                    <th style="width: 8%;" class="center">No</th>
                    <th style="width: 52%;">Komponen Penilaian</th>
                    <th style="width: 15%;" class="center">Bobot</th>
                    <th style="width: 25%;" class="center">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1</td>
                    <td>Kinerja <span style="color: #888; font-size: 10px;">(Penilaian DUDI)</span></td>
                    <td class="center bobot">{{ $bobotDudi }}%</td>
                    <td class="center">{{ $nilai->nilai_kinerja ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="center">2</td>
                    <td>Kompetensi <span style="color: #888; font-size: 10px;">(Penilaian DUDI)</span></td>
                    <td class="center bobot">{{ $bobotDudi }}%</td>
                    <td class="center">{{ $nilai->nilai_kompetensi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="center">3</td>
                    <td>Laporan <span style="color: #888; font-size: 10px;">(Guru Pembimbing)</span></td>
                    <td class="center bobot">{{ $bobotGuru }}%</td>
                    <td class="center">{{ $nilai->nilai_laporan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="center">4</td>
                    <td>Presentasi <span style="color: #888; font-size: 10px;">(Guru Pembimbing)</span></td>
                    <td class="center bobot">{{ $bobotGuru }}%</td>
                    <td class="center">{{ $nilai->nilai_presentasi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="center">5</td>
                    <td>Sidang <span style="color: #888; font-size: 10px;">(Guru Penguji)</span></td>
                    <td class="center bobot">{{ $bobotSidang }}%</td>
                    <td class="center">{{ $nilai->nilai_sidang ?? '-' }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="right">NILAI AKHIR</td>
                    <td class="center" style="font-size: 14px;">{{ $nilai && $nilai->nilai_akhir ? number_format($nilai->nilai_akhir, 2) : '-' }}</td>
                </tr>
                <tr class="akhir-row">
                    <td colspan="3" class="right">PREDIKAT</td>
                    <td class="center">{{ $nilai->predikat ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="predikat-info">
            <div class="title">Keterangan Predikat:</div>
            <table>
                <tr>
                    <td><strong>A</strong> (90-100) = Sangat Baik</td>
                    <td><strong>B</strong> (80-89) = Baik</td>
                    <td><strong>C</strong> (70-79) = Cukup</td>
                    <td><strong>D</strong> (60-69) = Kurang</td>
                    <td><strong>E</strong> (&lt;60) = Sangat Kurang</td>
                </tr>
            </table>
        </div>

        @if($nilai && ($nilai->catatan_dudi || $nilai->catatan_guru || $nilai->catatan_penguji))
            <div class="catatan-wrap">
                @if($nilai->catatan_dudi)
                    <div class="item">
                        <div class="label">Catatan Pembimbing DUDI</div>
                        <div>{{ $nilai->catatan_dudi }}</div>
                    </div>
                @endif
                @if($nilai->catatan_guru)
                    <div class="item">
                        <div class="label">Catatan Guru Pembimbing</div>
                        <div>{{ $nilai->catatan_guru }}</div>
                    </div>
                @endif
                @if($nilai->catatan_penguji)
                    <div class="item">
                        <div class="label">Catatan Guru Penguji</div>
                        <div>{{ $nilai->catatan_penguji }}</div>
                    </div>
                @endif
            </div>
        @endif

        <div class="ttd-page2">
            <div class="col">
                <div>Tangerang, {{ $sertifikat->tanggal_terbit?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold;">Kepala Sekolah</div>
                <div class="img-wrap">
                    @if($ttdKepsekUri)
                        <img src="{{ $ttdKepsekUri }}" alt="TTD Kepsek">
                    @endif
                </div>
                <div class="nm">{{ $setting->nama_kepsek ?? '.........................' }}</div>
                @if($setting->nip_kepsek)
                    <div style="font-size: 10px; color: #555;">NIP. {{ $setting->nip_kepsek }}</div>
                @endif
            </div>
        </div>

    </div>
</div>

</body>
</html>