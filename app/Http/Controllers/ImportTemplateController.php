<?php

namespace App\Http\Controllers;

use App\Models\PengaturanUmum;
use Illuminate\Support\Facades\Auth;

class ImportTemplateController extends Controller
{
    public function download(string $type)
    {
        $templates = [
            'siswa' => [
                'filename' => 'template-import-siswa-' . now()->format('Ymd-His'),
                'judul'    => 'TEMPLATE IMPORT DATA SISWA',
                'petunjuk' => [
                    'Isi data siswa pada tabel di bawah mulai baris ke-4.',
                    'Kolom bertanda (*) WAJIB diisi. Kolom lain opsional.',
                    'Kolom "jenis_kelamin" isi dengan L (Laki-laki) atau P (Perempuan).',
                    'Pastikan "email" sama dengan email akun user yang sudah dibuat di menu Users (role: siswa).',
                    'Kalau akun user belum ada, kosongkan kolom email dulu — nanti bisa di-link manual dari menu Siswa.',
                    'Simpan file dengan format XLSX atau CSV sebelum di-upload.',
                ],
                'columns' => [
                    ['label' => 'nama',          'required' => true,  'width' => 180],
                    ['label' => 'nis',           'required' => true,  'width' => 100],
                    ['label' => 'nisn',          'required' => false, 'width' => 120],
                    ['label' => 'jenis_kelamin', 'required' => true,  'width' => 100],
                    ['label' => 'kelas',         'required' => false, 'width' => 100],
                    ['label' => 'email',         'required' => false, 'width' => 200],
                    ['label' => 'no_hp',         'required' => false, 'width' => 130],
                ],
                'example' => ['Budi Santoso', '2024001', '0099887766', 'L', 'XII-TBSM-1', 'budi@siswa.com', '081234567890'],
            ],
            'guru' => [
                'filename' => 'template-import-guru-' . now()->format('Ymd-His'),
                'judul'    => 'TEMPLATE IMPORT DATA GURU',
                'petunjuk' => [
                    'Isi data guru pada tabel di bawah mulai baris ke-4.',
                    'Kolom bertanda (*) WAJIB diisi. Kolom lain opsional.',
                    'Kolom "jenis_kelamin" isi dengan L (Laki-laki) atau P (Perempuan).',
                    'Pastikan "email" sama dengan email akun user yang sudah dibuat di menu Users (role: guru_pembimbing / guru_penguji).',
                    'Kolom "nip" harus UNIQUE — tidak boleh ada 2 guru dengan NIP sama.',
                ],
                'columns' => [
                    ['label' => 'nama',          'required' => true,  'width' => 180],
                    ['label' => 'nip',           'required' => true,  'width' => 120],
                    ['label' => 'jenis_kelamin', 'required' => true,  'width' => 100],
                    ['label' => 'email',         'required' => false, 'width' => 200],
                    ['label' => 'no_hp',         'required' => false, 'width' => 130],
                ],
                'example' => ['Ahmad Subarjo', '1987654321', 'L', 'ahmad@sekolah.com', '081234567890'],
            ],
            'dudi' => [
                'filename' => 'template-import-dudi-' . now()->format('Ymd-His'),
                'judul'    => 'TEMPLATE IMPORT DATA DUDI (MITRA PKL)',
                'petunjuk' => [
                    'Isi data DUDI pada tabel di bawah mulai baris ke-4.',
                    'Kolom bertanda (*) WAJIB diisi. Kolom lain opsional.',
                    'Kolom "kuota" isi angka (jumlah max siswa PKL di perusahaan).',
                    'Kolom "nama_perusahaan" harus UNIQUE — tidak boleh ada 2 DUDI dengan nama sama.',
                    'Latitude & Longitude bisa di-set setelah import lewat menu edit DUDI (map picker).',
                ],
                'columns' => [
                    ['label' => 'nama_perusahaan', 'required' => true,  'width' => 200],
                    ['label' => 'bidang_usaha',    'required' => false, 'width' => 130],
                    ['label' => 'alamat',          'required' => false, 'width' => 220],
                    ['label' => 'kota',            'required' => false, 'width' => 120],
                    ['label' => 'kontak_person',   'required' => false, 'width' => 130],
                    ['label' => 'no_hp',           'required' => false, 'width' => 130],
                    ['label' => 'email',           'required' => false, 'width' => 200],
                    ['label' => 'kuota',           'required' => false, 'width' => 80],
                ],
                'example' => ['PT Contoh Jaya', 'Otomotif', 'Jl. Merdeka No 1', 'Tangerang', 'Budi', '081234567890', 'budi@contoh.com', '5'],
            ],
        ];

        abort_unless(isset($templates[$type]), 404);

        $tpl = $templates[$type];
        $setting = PengaturanUmum::first();
        $user = Auth::user();

        $sekolah = [
            'nama'   => $setting->nama_sekolah ?? 'SMKS Maestro Sepatan',
            'npsn'   => $setting->npsn ?? '69876678',
            'alamat' => $setting->alamat_sekolah ?? 'Jl. Raya Kramat Pakuhaji Km 3,5, Kayu Agung, Kec. Sepatan, Tangerang',
        ];

        $html = view('exports.template-import', [
            'judul'    => $tpl['judul'],
            'petunjuk' => $tpl['petunjuk'],
            'columns'  => $tpl['columns'],
            'example'  => $tpl['example'],
            'sekolah'  => $sekolah,
            'exporter' => $user->name ?? 'Admin Sekolah',
            'waktu'    => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ])->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $tpl['filename'] . '.xls"')
            ->header('Cache-Control', 'max-age=0')
            ->header('Pragma', 'public');
    }
}