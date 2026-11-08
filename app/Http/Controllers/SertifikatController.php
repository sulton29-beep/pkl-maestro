<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;
use App\Services\SertifikatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SertifikatController extends Controller
{
    /**
     * Download PDF sertifikat (on-demand).
     * Akses: admin, kepsek, waka, kaprodi, atau siswa pemilik sertifikat.
     */
    public function download(Request $request, Sertifikat $sertifikat)
    {
        $user = Auth::user();
        abort_if(! $user, 403);

        $isStaff = $user->hasAnyRole(['admin', 'kepala_sekolah', 'waka_kurikulum', 'kaprodi']);
        $isOwner = $user->hasRole('siswa') && $sertifikat->siswa?->user_id === $user->id;

        abort_unless($isStaff || $isOwner, 403);

        // Siswa cuma bisa download kalau statusnya sudah terbit
        if ($user->hasRole('siswa') && ! $sertifikat->isTerbit()) {
            abort(403, 'Sertifikat belum diterbitkan.');
        }

        $service = app(SertifikatService::class);
        $pdfContent = $service->renderPdf($sertifikat);
        $filename = $service->namaFile($sertifikat);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}