<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;

class VerifikasiController extends Controller
{
    /**
     * Halaman publik verifikasi sertifikat via QR.
     * Bisa diakses tanpa login.
     */
    public function show(string $token)
    {
        $sertifikat = Sertifikat::with([
            'siswa.jurusan',
            'penempatanPkl.dudi',
        ])->where('qr_token', $token)->first();

        if (! $sertifikat) {
            return view('verifikasi.hasil', [
                'valid' => false,
                'sertifikat' => null,
                'alasan' => 'Token tidak ditemukan di sistem kami.',
            ]);
        }

        if (! $sertifikat->isTerbit()) {
            return view('verifikasi.hasil', [
                'valid' => false,
                'sertifikat' => $sertifikat,
                'alasan' => 'Sertifikat ini belum resmi diterbitkan (status: ' . $sertifikat->statusLabel() . ').',
            ]);
        }

        return view('verifikasi.hasil', [
            'valid' => true,
            'sertifikat' => $sertifikat,
            'alasan' => null,
        ]);
    }
}