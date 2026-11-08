<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Guru;
use App\Models\Logbook;
use App\Models\PengaturanUmum;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportExcelController extends Controller
{
    /* ============================================================
     * 5 METHOD EXPORT
     * ============================================================ */

    public function dudi()
    {
        $rows = Dudi::orderBy('nama_perusahaan')->get()->map(function ($d) {
            return [
                $d->id,
                $d->nama_perusahaan,
                $d->bidang_usaha,
                $d->kota,
                $d->kontak_person,
                $d->no_hp,
                $d->email,
                $d->kuota,
                $d->is_active ? 'Aktif' : 'Nonaktif',
            ];
        })->toArray();

        return $this->generateExcel(
            judul: 'DATA DUDI (MITRA PKL)',
            columns: ['ID', 'Nama Perusahaan', 'Bidang Usaha', 'Kota', 'Kontak Person', 'No HP', 'Email', 'Kuota', 'Status'],
            rows: $rows,
            filename: 'data-dudi-' . now()->format('Ymd-His'),
        );
    }

    public function guru()
    {
        $rows = Guru::orderBy('nama')->get()->map(function ($g) {
            return [
                $g->id,
                $g->nip,
                $g->nama,
                $g->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                $g->email,
                $g->no_hp,
            ];
        })->toArray();

        return $this->generateExcel(
            judul: 'DATA GURU',
            columns: ['ID', 'NIP', 'Nama', 'Jenis Kelamin', 'Email', 'No HP'],
            rows: $rows,
            filename: 'data-guru-' . now()->format('Ymd-His'),
        );
    }

    public function siswa()
    {
        $rows = Siswa::with('jurusan')->orderBy('nama')->get()->map(function ($s) {
            return [
                $s->id,
                $s->nis,
                $s->nisn,
                $s->nama,
                $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                $s->jurusan->nama ?? '-',
                $s->kelas,
                $s->email,
                $s->no_hp,
            ];
        })->toArray();

        return $this->generateExcel(
            judul: 'DATA SISWA PKL',
            columns: ['ID', 'NIS', 'NISN', 'Nama', 'Jenis Kelamin', 'Jurusan', 'Kelas', 'Email', 'No HP'],
            rows: $rows,
            filename: 'data-siswa-' . now()->format('Ymd-His'),
        );
    }

    public function logbook()
    {
        $rows = Logbook::with('siswa')->orderBy('tanggal', 'desc')->get()->map(function ($l) {
            return [
                $l->id,
                $l->tanggal ? date('d/m/Y', strtotime($l->tanggal)) : '-',
                $l->siswa->nama ?? '-',
                $l->kegiatan,
                ucfirst($l->status ?? 'pending'),
                $l->catatan_dudi ?? '-',
            ];
        })->toArray();

        return $this->generateExcel(
            judul: 'DATA LOGBOOK PKL',
            columns: ['ID', 'Tanggal', 'Nama Siswa', 'Kegiatan', 'Status', 'Catatan DUDI'],
            rows: $rows,
            filename: 'data-logbook-' . now()->format('Ymd-His'),
        );
    }

    public function absensi()
    {
        $rows = Absensi::with(['siswa', 'dudi'])->orderBy('tanggal', 'desc')->get()->map(function ($a) {
            return [
                $a->id,
                $a->tanggal ? date('d/m/Y', strtotime($a->tanggal)) : '-',
                $a->siswa->nama ?? '-',
                $a->dudi->nama_perusahaan ?? '-',
                $a->jam_masuk ? date('H:i', strtotime($a->jam_masuk)) : '-',
                $a->jam_keluar ? date('H:i', strtotime($a->jam_keluar)) : '-',
                $a->jarak_masuk ?? '-',
                ucfirst($a->status_masuk ?? '-'),
                ucfirst($a->status_keluar ?? '-'),
            ];
        })->toArray();

        return $this->generateExcel(
            judul: 'REKAP ABSENSI PKL',
            columns: ['ID', 'Tanggal', 'Nama Siswa', 'DUDI', 'Jam Masuk', 'Jam Keluar', 'Jarak (m)', 'Status Masuk', 'Status Keluar'],
            rows: $rows,
            filename: 'rekap-absensi-' . now()->format('Ymd-His'),
        );
    }

    /* ============================================================
     * GENERATOR UTAMA
     * ============================================================ */

    protected function generateExcel(string $judul, array $columns, array $rows, string $filename)
    {
        $setting = PengaturanUmum::first();
        $user = Auth::user();

        $sekolah = [
            'nama'  => $setting->nama_sekolah ?? 'SMKS Maestro Sepatan',
            'npsn'  => $setting->npsn ?? '69876678',
            'alamat' => $setting->alamat_sekolah ?? 'Jl. Raya Kramat Pakuhaji Km 3,5, Kayu Agung, Kec. Sepatan, Tangerang',
        ];

        $html = view('exports.excel', [
            'judul'   => $judul,
            'sekolah' => $sekolah,
            'columns' => $columns,
            'rows'    => $rows,
            'exporter' => $user->name ?? 'Admin Sekolah',
            'waktu'   => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ])->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.xls"')
            ->header('Cache-Control', 'max-age=0')
            ->header('Pragma', 'public');
    }
}