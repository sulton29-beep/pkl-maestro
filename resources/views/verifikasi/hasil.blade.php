<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi Sertifikat PKL — SMKS Maestro Sepatan</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body { font-family: 'Inter', system-ui, sans-serif; }
</style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4">

<div class="max-w-2xl mx-auto">

    {{-- HEADER --}}
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-blue-700 text-white text-2xl font-bold mb-3">
            SM
        </div>
        <h1 class="text-xl font-bold text-slate-800">SMKS MAESTRO SEPATAN</h1>
        <p class="text-sm text-slate-500">Sistem Verifikasi Sertifikat PKL</p>
    </div>

    {{-- HASIL VERIFIKASI --}}
    @if ($valid)

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 px-6 py-8 text-white text-center">
                <svg class="h-16 w-16 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <h2 class="text-2xl font-bold">SERTIFIKAT VALID</h2>
                <p class="mt-1 text-emerald-50 text-sm">Dokumen ini terdaftar resmi di sistem kami.</p>
            </div>

            <div class="p-6">
                <div class="space-y-4">

                    <div class="border-b border-slate-200 pb-3">
                        <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Nomor Sertifikat</div>
                        <div class="mt-1 font-mono font-bold text-lg text-slate-800">{{ $sertifikat->nomor_sertifikat }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Nama Siswa</div>
                            <div class="mt-1 font-bold text-slate-800">{{ $sertifikat->siswa->nama ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">NIS / NISN</div>
                            <div class="mt-1 font-semibold text-slate-800">
                                {{ $sertifikat->siswa->nis ?? '-' }} / {{ $sertifikat->siswa->nisn ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Jurusan</div>
                            <div class="mt-1 font-semibold text-slate-800">{{ $sertifikat->siswa->jurusan->nama ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Kelas</div>
                            <div class="mt-1 font-semibold text-slate-800">{{ $sertifikat->siswa->kelas ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-3">
                        <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Tempat PKL</div>
                        <div class="mt-1 font-bold text-slate-800">{{ $sertifikat->penempatanPkl->dudi->nama_perusahaan ?? '-' }}</div>
                        <div class="text-sm text-slate-500">{{ $sertifikat->penempatanPkl->dudi->alamat ?? '' }}</div>
                    </div>

                    <div class="border-t border-slate-200 pt-3">
                        <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Tanggal Terbit</div>
                        <div class="mt-1 font-semibold text-slate-800">
                            {{ $sertifikat->tanggal_terbit?->translatedFormat('d F Y') ?? '-' }}
                        </div>
                    </div>

                </div>

                <div class="mt-6 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-700 text-center">
                    Sertifikat ini dinyatakan <strong>asli dan sah</strong>. Verifikasi dilakukan pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.
                </div>
            </div>
        </div>

    @else

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-br from-rose-500 to-rose-600 px-6 py-8 text-white text-center">
                <svg class="h-16 w-16 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-2xl font-bold">TIDAK VALID</h2>
                <p class="mt-1 text-rose-50 text-sm">Dokumen ini tidak dapat diverifikasi.</p>
            </div>

            <div class="p-6">
                <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">
                    <strong>Alasan:</strong> {{ $alasan }}
                </div>

                @if ($sertifikat)
                    <div class="mt-4 border-t border-slate-200 pt-4 space-y-2">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Nomor Sertifikat</div>
                            <div class="font-mono font-bold text-slate-800">{{ $sertifikat->nomor_sertifikat }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Status</div>
                            <div class="font-semibold text-slate-800">{{ $sertifikat->statusLabel() }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    @endif

    {{-- FOOTER --}}
    <div class="text-center mt-6 text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} SMKS Maestro Sepatan — Yayasan Medina Heritage</p>
        <p class="mt-1">Jl. Raya Kramat Pakuhaji Km 3,5, Kayu Agung, Kec. Sepatan, Tangerang</p>
    </div>

</div>

</body>
</html>