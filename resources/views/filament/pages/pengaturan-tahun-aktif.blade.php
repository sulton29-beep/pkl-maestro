<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ============================================ --}}
        {{-- CARD: TAHUN AJARAN AKTIF SEKARANG              --}}
        {{-- ============================================ --}}
        <x-filament::section>
            <x-slot name="heading">
                🟢 Tahun Ajaran Aktif Sekarang
            </x-slot>

            @if($tahunAktif)
                <div class="flex flex-col gap-4 rounded-xl border-2 border-success-500 bg-success-50 dark:bg-success-950 p-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <div class="text-3xl font-bold text-success-700 dark:text-success-400">
                                {{ $tahunAktif->nama }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                📅 {{ $tahunAktif->tanggal_mulai->format('d M Y') }}
                                — {{ $tahunAktif->tanggal_selesai->format('d M Y') }}
                            </div>
                            @if($tahunAktif->keterangan)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 italic">
                                    {{ $tahunAktif->keterangan }}
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <x-filament::badge color="success" size="lg">
                                ✅ AKTIF
                            </x-filament::badge>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $tahunAktif->penempatanPkls->count() }} siswa terdaftar
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-xl border-2 border-danger-500 bg-danger-50 dark:bg-danger-950 p-6 text-center">
                    <div class="text-lg font-bold text-danger-700 dark:text-danger-400">
                        ⚠️ Belum ada tahun ajaran aktif
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                        Pilih tahun ajaran di bawah untuk dijadikan aktif.
                    </div>
                </div>
            @endif
        </x-filament::section>

        {{-- ============================================ --}}
        {{-- CARD: SEMUA TAHUN AJARAN                       --}}
        {{-- ============================================ --}}
        <x-filament::section>
            <x-slot name="heading">
                📋 Semua Tahun Ajaran ({{ $semuaTahun->count() }})
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($semuaTahun as $tahun)
                    @php
                        $isAktif = $tahun->is_active;
                        $isArsip = $tahun->is_arsip;
                        $border = $isAktif ? 'border-success-500 bg-success-50 dark:bg-success-950'
                                 : ($isArsip ? 'border-gray-400 bg-gray-100 dark:bg-gray-800 opacity-70'
                                            : 'border-gray-300 dark:border-gray-600');
                    @endphp

                    <div class="rounded-xl border-2 {{ $border }} p-4 flex flex-col gap-3">

                        {{-- Header: nama + badge --}}
                        <div class="flex items-start justify-between">
                            <div class="text-xl font-bold">
                                {{ $tahun->nama }}
                            </div>
                            <div class="flex flex-col gap-1 items-end">
                                @if($isAktif)
                                    <x-filament::badge color="success">✅ Aktif</x-filament::badge>
                                @endif
                                @if($isArsip)
                                    <x-filament::badge color="gray">🔒 Arsip</x-filament::badge>
                                @endif
                                @if(! $isAktif && ! $isArsip)
                                    <x-filament::badge color="info">📅 Mendatang</x-filament::badge>
                                @endif
                            </div>
                        </div>

                        {{-- Periode --}}
                        <div class="text-xs text-gray-600 dark:text-gray-300">
                            📅 {{ $tahun->tanggal_mulai->format('d M Y') }}
                            — {{ $tahun->tanggal_selesai->format('d M Y') }}
                        </div>

                        {{-- Jumlah siswa --}}
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            👥 {{ $tahun->penempatanPkls->count() }} siswa terdaftar
                        </div>

                        {{-- Keterangan --}}
                        @if($tahun->keterangan)
                            <div class="text-xs italic text-gray-500 dark:text-gray-400">
                                {{ $tahun->keterangan }}
                            </div>
                        @endif

                        {{-- Action buttons --}}
                        <div class="flex gap-2 mt-auto pt-2 flex-wrap">
                            @if(! $isAktif)
                                <x-filament::button
                                    wire:click="jadikanAktif({{ $tahun->id }})"
                                    color="success"
                                    size="sm"
                                    icon="heroicon-o-check-circle"
                                    wire:confirm="Jadikan {{ $tahun->nama }} sebagai tahun ajaran aktif?">
                                    Jadikan Aktif
                                </x-filament::button>
                            @endif

                            @if($isAktif)
                                <x-filament::button
                                    disabled
                                    color="success"
                                    size="sm"
                                    icon="heroicon-o-check">
                                    Sedang Aktif
                                </x-filament::button>
                            @endif

                            @if(! $isAktif)
                                <x-filament::button
                                    wire:click="toggleArsip({{ $tahun->id }})"
                                    color="{{ $isArsip ? 'warning' : 'gray' }}"
                                    size="sm"
                                    icon="{{ $isArsip ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed' }}">
                                    {{ $isArsip ? 'Buka Arsip' : 'Arsipkan' }}
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>