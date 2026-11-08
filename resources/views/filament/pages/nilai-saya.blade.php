<x-filament-panels::page>

@if (!$siswaData)
    <div class="rounded-xl bg-white dark:bg-gray-900 p-8 text-center shadow">
        <x-heroicon-o-user-circle class="mx-auto h-16 w-16 text-gray-400" />
        <h2 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
            Data Siswa Tidak Ditemukan
        </h2>
        <p class="mt-2 text-sm text-gray-500">
            Akun Anda belum terhubung dengan data siswa. Hubungi admin sekolah.
        </p>
    </div>
@elseif (!$nilaiData)

    {{-- Kartu info siswa --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 p-6 shadow">
        <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                <x-heroicon-o-user class="h-7 w-7 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $siswaData['nama'] }}</h2>
                <p class="text-sm text-gray-500">
                    NIS {{ $siswaData['nis'] ?? '-' }} · Kelas {{ $siswaData['kelas'] ?? '-' }} · {{ $siswaData['jurusan'] }}
                </p>
            </div>
        </div>
    </div>

    {{-- SERTIFIKAT (inline, bukan @include) --}}
    @if ($sertifikatData)
        <div class="rounded-xl bg-white dark:bg-gray-900 p-6 shadow border-l-4
            @if ($sertifikatData['is_terbit']) border-emerald-500
            @elseif ($sertifikatData['is_ditolak']) border-rose-500
            @else border-amber-500
            @endif">

            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-document-check class="h-5 w-5 text-primary-600" />
                        Sertifikat PKL
                    </h3>
                    <div class="mt-3 space-y-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Nomor Sertifikat</p>
                            <p class="mt-0.5 font-mono font-bold text-gray-900 dark:text-white">
                                {{ $sertifikatData['nomor'] }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                            <div class="mt-1">
                                @if ($sertifikatData['is_terbit'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                                        <x-heroicon-o-check-circle class="h-4 w-4" />
                                        Terbit — {{ $sertifikatData['tanggal_terbit'] }}
                                    </span>
                                @elseif ($sertifikatData['is_ditolak'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 dark:bg-rose-900/30 px-3 py-1 text-xs font-semibold text-rose-700 dark:text-rose-300">
                                        <x-heroicon-o-x-circle class="h-4 w-4" />
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-900/30 px-3 py-1 text-xs font-semibold text-amber-700 dark:text-amber-300">
                                        <x-heroicon-o-clock class="h-4 w-4" />
                                        Menunggu Persetujuan — {{ $sertifikatData['status_label'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    @if ($sertifikatData['is_terbit'])
                        <a href="{{ $sertifikatData['download_url'] }}" target="_blank"
                           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow hover:bg-emerald-700 transition">
                            <x-heroicon-o-arrow-down-tray class="h-5 w-5" />
                            Download Sertifikat PDF
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-lg bg-gray-200 dark:bg-gray-700 px-5 py-3 text-sm font-bold text-gray-500 dark:text-gray-400 cursor-not-allowed">
                            <x-heroicon-o-lock-closed class="h-5 w-5" />
                            Belum Bisa Diunduh
                        </span>
                    @endif
                </div>
            </div>

            @if ($sertifikatData['is_ditolak'] && $sertifikatData['catatan_revisi'])
                <div class="mt-4 rounded-lg border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20 p-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-rose-700 dark:text-rose-300">Alasan Penolakan</p>
                    <p class="mt-1 text-sm text-rose-700 dark:text-rose-300">{{ $sertifikatData['catatan_revisi'] }}</p>
                </div>
            @endif
        </div>
    @endif

    <div class="rounded-xl bg-white dark:bg-gray-900 p-8 text-center shadow">
        <x-heroicon-o-clock class="mx-auto h-16 w-16 text-gray-400" />
        <h2 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
            Nilai Belum Tersedia
        </h2>
        <p class="mt-2 text-sm text-gray-500">
            Nilai PKL Anda akan muncul di sini setelah pembimbing DUDI, guru pembimbing, dan guru penguji selesai memberikan penilaian.
        </p>
    </div>
@else

    {{-- Info Siswa --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 p-6 shadow">
        <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                <x-heroicon-o-user class="h-7 w-7 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $siswaData['nama'] }}</h2>
                <p class="text-sm text-gray-500">
                    NIS {{ $siswaData['nis'] ?? '-' }} · Kelas {{ $siswaData['kelas'] ?? '-' }} · {{ $siswaData['jurusan'] }}
                </p>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Perusahaan (DUDI)</p>
                <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $dudiData['nama'] }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Guru Pembimbing</p>
                <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $dudiData['pembimbing'] }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Guru Penguji</p>
                <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $dudiData['penguji'] }}</p>
            </div>
        </div>
    </div>

    {{-- Nilai Akhir Highlight --}}
    @php
        $angka = $nilaiData['nilai_akhir'];
        $warna = match(true) {
            $angka === null => 'gray',
            $angka >= 90    => 'success',
            $angka >= 80    => 'info',
            $angka >= 70    => 'warning',
            default         => 'danger',
        };
        $bgColor = match($warna) {
            'success' => 'from-emerald-500 to-emerald-600',
            'info'    => 'from-sky-500 to-sky-600',
            'warning' => 'from-amber-500 to-amber-600',
            'danger'  => 'from-rose-500 to-rose-600',
            default   => 'from-gray-400 to-gray-500',
        };
    @endphp

    <div class="rounded-xl bg-gradient-to-br {{ $bgColor }} p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-wider opacity-90">Nilai Akhir PKL</p>
                <p class="mt-1 text-5xl font-extrabold">
                    {{ $angka !== null ? number_format((float) $angka, 2) : '—' }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm uppercase tracking-wider opacity-90">Predikat</p>
                <p class="mt-1 text-5xl font-extrabold">{{ $nilaiData['predikat'] }}</p>
            </div>
        </div>
    </div>

    {{-- Rincian Nilai --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        <div class="rounded-xl bg-white dark:bg-gray-900 p-5 shadow">
            <h3 class="border-b border-gray-200 dark:border-gray-700 pb-2 text-sm font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                Penilaian DUDI
            </h3>
            <div class="mt-3 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Kinerja</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $nilaiData['nilai_kinerja'] ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Kompetensi</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $nilaiData['nilai_kompetensi'] ?? '—' }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Rata-rata</span>
                    <span class="font-bold text-primary-600 dark:text-primary-400">
                        {{ $nilaiData['rata_dudi'] !== null ? number_format($nilaiData['rata_dudi'], 2) : '—' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 p-5 shadow">
            <h3 class="border-b border-gray-200 dark:border-gray-700 pb-2 text-sm font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                Penilaian Guru
            </h3>
            <div class="mt-3 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Laporan</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $nilaiData['nilai_laporan'] ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Presentasi</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $nilaiData['nilai_presentasi'] ?? '—' }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Rata-rata</span>
                    <span class="font-bold text-primary-600 dark:text-primary-400">
                        {{ $nilaiData['rata_guru'] !== null ? number_format($nilaiData['rata_guru'], 2) : '—' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 p-5 shadow">
            <h3 class="border-b border-gray-200 dark:border-gray-700 pb-2 text-sm font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                Penilaian Sidang
            </h3>
            <div class="mt-3 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Nilai Sidang</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $nilaiData['nilai_sidang'] ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Catatan --}}
    @if ($nilaiData['catatan_dudi'] || $nilaiData['catatan_guru'] || $nilaiData['catatan_penguji'])
        <div class="rounded-xl bg-white dark:bg-gray-900 p-6 shadow">
            <h3 class="border-b border-gray-200 dark:border-gray-700 pb-3 text-base font-bold text-gray-900 dark:text-white">
                Catatan & Masukan
            </h3>
            <div class="mt-4 space-y-4">
                @if ($nilaiData['catatan_dudi'])
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Dari DUDI</p>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $nilaiData['catatan_dudi'] }}</p>
                    </div>
                @endif
                @if ($nilaiData['catatan_guru'])
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Dari Guru Pembimbing</p>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $nilaiData['catatan_guru'] }}</p>
                    </div>
                @endif
                @if ($nilaiData['catatan_penguji'])
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Dari Guru Penguji</p>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $nilaiData['catatan_penguji'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- SERTIFIKAT PKL --}}
    @if ($sertifikatData)
        <div class="rounded-xl bg-white dark:bg-gray-900 p-6 shadow border-l-4
            @if ($sertifikatData['is_terbit']) border-emerald-500
            @elseif ($sertifikatData['is_ditolak']) border-rose-500
            @else border-amber-500
            @endif">

            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-document-check class="h-5 w-5 text-primary-600" />
                        Sertifikat PKL
                    </h3>
                    <div class="mt-3 space-y-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Nomor Sertifikat</p>
                            <p class="mt-0.5 font-mono font-bold text-gray-900 dark:text-white">
                                {{ $sertifikatData['nomor'] }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                            <div class="mt-1">
                                @if ($sertifikatData['is_terbit'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                                        <x-heroicon-o-check-circle class="h-4 w-4" />
                                        Terbit — {{ $sertifikatData['tanggal_terbit'] }}
                                    </span>
                                @elseif ($sertifikatData['is_ditolak'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 dark:bg-rose-900/30 px-3 py-1 text-xs font-semibold text-rose-700 dark:text-rose-300">
                                        <x-heroicon-o-x-circle class="h-4 w-4" />
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-900/30 px-3 py-1 text-xs font-semibold text-amber-700 dark:text-amber-300">
                                        <x-heroicon-o-clock class="h-4 w-4" />
                                        Menunggu Persetujuan — {{ $sertifikatData['status_label'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    @if ($sertifikatData['is_terbit'])
                        <a href="{{ $sertifikatData['download_url'] }}" target="_blank"
                           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow hover:bg-emerald-700 transition">
                            <x-heroicon-o-arrow-down-tray class="h-5 w-5" />
                            Download Sertifikat PDF
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-lg bg-gray-200 dark:bg-gray-700 px-5 py-3 text-sm font-bold text-gray-500 dark:text-gray-400 cursor-not-allowed">
                            <x-heroicon-o-lock-closed class="h-5 w-5" />
                            Belum Bisa Diunduh
                        </span>
                    @endif
                </div>
            </div>

            @if ($sertifikatData['is_ditolak'] && $sertifikatData['catatan_revisi'])
                <div class="mt-4 rounded-lg border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20 p-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-rose-700 dark:text-rose-300">Alasan Penolakan</p>
                    <p class="mt-1 text-sm text-rose-700 dark:text-rose-300">{{ $sertifikatData['catatan_revisi'] }}</p>
                </div>
            @endif
        </div>
    @endif

@endif

</x-filament-panels::page>