<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page { size: 297mm 210mm; margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Serif', 'Times New Roman', serif;
        color: #1a1a1a;
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

    /* ============ HALAMAN 1: SERTIFIKAT ============ */
    .frame-outer {
        position: absolute;
        top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
        border: 3px solid #1e40af;
    }
    .frame-inner {
        position: absolute;
        top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
        border: 1px solid #1e40af;
    }
    .corner {
        position: absolute;
        width: 18mm; height: 18mm;
        border: 3px solid #f59e0b;
    }
    .corner.tl { top: 5mm; left: 5mm; border-right: none; border-bottom: none; }
    .corner.tr { top: 5mm; right: 5mm; border-left: none; border-bottom: none; }
    .corner.bl { bottom: 5mm; left: 5mm; border-right: none; border-top: none; }
    .corner.br { bottom: 5mm; right: 5mm; border-left: none; border-top: none; }

    .watermark {
        position: absolute; top: 40%; left: 0; right: 0;
        text-align: center; font-size: 110px;
        color: rgba(200, 0, 0, 0.08);
        font-weight: bold; letter-spacing: 18px;
        z-index: 0;
    }

    .content {
        position: relative;
        padding: 14mm 22mm;
        z-index: 1;
    }

    .kop {
        text-align: center;
        border-bottom: 2.5px double #1e40af;
        padding-bottom: 3mm;
        margin-bottom: 4mm;
    }
    .kop .nama { font-size: 23px; font-weight: bold; color: #1e40af; letter-spacing: 1.5px; }
    .kop .sub { font-size: 10.5px; color: #444; margin-top: 1mm; }
    .kop .alamat { font-size: 10px; color: #666; margin-top: 0.8mm; }

    .judul { text-align: center; margin: 4mm 0 3mm; }
    .judul h1 { font-size: 24px; color: #1e40af; letter-spacing: 5px; font-weight: bold; }
    .judul .nomor { font-size: 11.5px; color: #333; margin-top: 2mm; font-style: italic; }

    .pembuka { font-size: 11.5px; line-height: 1.6; text-align: justify; margin: 2.5mm 0; }

    .biodata { width: 100%; font-size: 12.5px; margin: 2mm 0 2mm 12mm; }
    .biodata td { padding: 0.8mm 2mm; vertical-align: top; }
    .biodata .lbl { width: 38mm; }
    .biodata .col { width: 4mm; text-align: center; }
    .biodata .val { font-weight: bold; color: #1e40af; }

    .info-pkl {
        background: #f0f4ff;
        border-left: 4px solid #1e40af;
        padding: 2.5mm 5mm;
        margin: 2.5mm 12mm;
        font-size: 11px;
        line-height: 1.5;
    }
    .info-pkl .row { margin-bottom: 0.4mm; }
    .info-pkl .lbl { display: inline-block; width: 32mm; font-weight: bold; }

    .nilai-box {
        background: #fffbea;
        border: 1.5px solid #f2c94c;
        padding: 2.5mm;
        margin: 3mm auto;
        text-align: center;
        width: 90mm;
    }
    .nilai-box .lbl { font-size: 9px; color: #7a5f00; letter-spacing: 1.5px; }
    .nilai-box .angka { font-size: 22px; font-weight: bold; color: #b78103; margin: 0.4mm 0; }
    .nilai-box .pred { font-size: 10px; color: #7a5f00; }

    .penutup { font-size: 11px; line-height: 1.5; text-align: justify; margin: 2.5mm 0 3mm; }

    .ttd-wrap { margin-top: 3mm; width: 100%; }
    .ttd-col { display: inline-block; width: 32%; text-align: center; vertical-align: top; font-size: 11px; }
    .ttd-col .jab { font-weight: bold; margin-bottom: 1.5mm; }
    .ttd-col .img-wrap { height: 18mm; margin: 1.5mm 0; }
    .ttd-col .img-wrap img { max-height: 18mm; max-width: 38mm; }
    .ttd-col .nm { font-weight: bold; text-decoration: underline; margin-top: 1mm; }
    .ttd-col .nip { font-size: 9.5px; color: #555; }

    .qr-wrap { text-align: center; padding-top: 4mm; }
    .qr-wrap img { width: 20mm; height: 20mm; }
    .qr-wrap .lbl { font-size: 8px; color: #666; margin-top: 0.5mm; }

    /* ============ HALAMAN 2: DAFTAR NILAI ============ */
    .page2-frame {
        position: absolute;
        top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
        border: 2px solid #1e40af;
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
        color: #1e40af;
        border-bottom: 2px solid #1e40af;
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
        background: #1e40af;
        color: #fff;
        padding: 2mm 3mm;
        text-align: left;
        font-weight: bold;
        border: 1px solid #1e40af;
        font-size: 10.5px;
    }
    .nilai-tbl td { padding: 1.7mm 3mm; border: 1px solid #cbd5e1; }
    .nilai-tbl .center { text-align: center; }
    .nilai-tbl .right { text-align: right; }
    .nilai-tbl .bobot { color: #666; font-size: 10px; }
    .nilai-tbl tr.total-row td {
        background: #f0f4ff;
        font-weight: bold;
        color: #1e40af;
        border-top: 2px solid #1e40af;
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
        border-left: 3px solid #1e40af;
    }
    .predikat-info .title { font-weight: bold; margin-bottom: 1.5mm; color: #1e40af; font-size: 10.5px; }
    .predikat-info table { width: 100%; }
    .predikat-info td { padding: 0.6mm 2mm; }

    .catatan-wrap { margin-top: 3.5mm; font-size: 10.5px; }
    .catatan-wrap .item {
        margin-bottom: 2mm;
        padding: 2mm 3mm;
        background: #f8fafc;
        border-left: 3px solid #1e40af;
    }
    .catatan-wrap .label {
        font-weight: bold;
        color: #1e40af;
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

    @if(!$sertifikat->isTerbit())
        <div class="watermark">DRAFT</div>
    @endif

    <div class="frame-outer"></div>
    <div class="frame-inner"></div>
    <div class="corner tl"></div>
    <div class="corner tr"></div>
    <div class="corner bl"></div>
    <div class="corner br"></div>

    <div class="content">

        <div class="kop">
            <div class="nama">{{ $setting->nama_sekolah }}</div>
            <div class="sub">NPSN: {{ $setting->npsn }}</div>
            <div class="alamat">{{ $setting->alamat_sekolah }}</div>
        </div>

        <div class="judul">
            <h1>{{ $setting->judul_sertifikat }}</h1>
            <div class="nomor">Nomor: {{ $sertifikat->nomor_sertifikat }}</div>
        </div>

        <div class="pembuka">{{ $setting->kalimat_pembuka }}</div>

        <table class="biodata">
            <tr>
                <td class="lbl">Nama Lengkap</td>
                <td class="col">:</td>
                <td class="val">{{ $siswa->nama }}</td>
            </tr>
            <tr>
                <td class="lbl">NIS / NISN</td>
                <td class="col">:</td>
                <td class="val">{{ $siswa->nis }} / {{ $siswa->nisn }}</td>
            </tr>
            <tr>
                <td class="lbl">Jurusan</td>
                <td class="col">:</td>
                <td class="val">{{ $siswa->jurusan->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Kelas</td>
                <td class="col">:</td>
                <td class="val">{{ $siswa->kelas }}</td>
            </tr>
        </table>

        <div class="info-pkl">
            <div class="row"><span class="lbl">Tempat PKL</span>: {{ $penempatan->dudi->nama_perusahaan ?? '-' }}</div>
            <div class="row"><span class="lbl">Alamat DUDI</span>: {{ $penempatan->dudi->alamat ?? '-' }}</div>
            <div class="row"><span class="lbl">Periode PKL</span>: {{ $penempatan->tanggal_mulai?->format('d M Y') ?? '-' }} s/d {{ $penempatan->tanggal_selesai?->format('d M Y') ?? '-' }}</div>
        </div>

        @if($nilai && $nilai->nilai_akhir)
            <div class="nilai-box">
                <div class="lbl">NILAI AKHIR</div>
                <div class="angka">{{ number_format($nilai->nilai_akhir, 2) }}</div>
                <div class="pred">Predikat: {{ $nilai->predikat ?? '-' }}</div>
            </div>
        @endif

        <div class="penutup">{{ $setting->kalimat_penutup }}</div>

        <div class="ttd-wrap">
            <div class="ttd-col">
                <div class="jab">Pimpinan DUDI</div>
                <div class="img-wrap">
                    @php $ttdDudi = $sertifikat->ttd_dudi ?: $setting->ttd_dudi; @endphp
                    @if($ttdDudi && file_exists(public_path('storage/' . $ttdDudi)))
                        <img src="{{ public_path('storage/' . $ttdDudi) }}">
                    @endif
                </div>
                <div class="nm">{{ $penempatan->dudi->nama_pimpinan ?? '.........................' }}</div>
            </div>

            <div class="ttd-col">
                <div class="qr-wrap">
                    <img src="{{ $qrDataUri }}">
                    <div class="lbl">Scan untuk verifikasi</div>
                </div>
            </div>

            <div class="ttd-col">
                <div class="jab">Tangerang, {{ $sertifikat->tanggal_terbit?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</div>
                <div class="jab">Kepala Sekolah</div>
                <div class="img-wrap">
                    @php $ttdKepsek = $sertifikat->ttd_kepsek ?: $setting->ttd_kepsek; @endphp
                    @if($ttdKepsek && file_exists(public_path('storage/' . $ttdKepsek)))
                        <img src="{{ public_path('storage/' . $ttdKepsek) }}">
                    @endif
                </div>
                <div class="nm">{{ $setting->nama_kepsek ?? '.........................' }}</div>
                @if($setting->nip_kepsek)
                    <div class="nip">NIP. {{ $setting->nip_kepsek }}</div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ==================== HALAMAN 2 ==================== --}}
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
                    @php $ttdKepsek2 = $sertifikat->ttd_kepsek ?: $setting->ttd_kepsek; @endphp
                    @if($ttdKepsek2 && file_exists(public_path('storage/' . $ttdKepsek2)))
                        <img src="{{ public_path('storage/' . $ttdKepsek2) }}">
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