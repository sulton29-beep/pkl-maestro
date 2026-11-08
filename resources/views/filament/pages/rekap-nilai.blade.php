<x-filament-panels::page>

@php $data = $this->getDataProperty(); @endphp

{{-- Filter --}}
<div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow">
    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
        <div>
            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Jurusan</label>
            <select wire:model.live="filterJurusan"
                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm">
                <option value="">Semua Jurusan</option>
                @foreach ($this->getJurusanOptionsProperty() as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">DUDI</label>
            <select wire:model.live="filterDudi"
                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm">
                <option value="">Semua DUDI</option>
                @foreach ($this->getDudiOptionsProperty() as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-600 dark:text-gray-300">Kelas</label>
            <select wire:model.live="filterKelas"
                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm">
                <option value="">Semua Kelas</option>
                @foreach ($this->getKelasOptionsProperty() as $kelas => $label)
                    <option value="{{ $kelas }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <x-filament::button wire:click="resetFilter" color="gray" size="sm">
                Reset
            </x-filament::button>

            <x-filament::button
                tag="a"
                :href="route('rekap-nilai.export', array_filter([
                    'jurusan' => $filterJurusan,
                    'dudi'    => $filterDudi,
                    'kelas'   => $filterKelas,
                ]))"
                icon="heroicon-o-arrow-down-tray"
                size="sm"
                color="success">
                Export Excel
            </x-filament::button>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 gap-4 md:grid-cols-4">
    <div class="rounded-xl bg-white dark:bg-gray-900 p-5 shadow border-l-4 border-primary-500">
        <p class="text-xs uppercase tracking-wide text-gray-500">Total Siswa Dinilai</p>
        <p class="mt-1 text-3xl font-extrabold text-gray-900 dark:text-white">{{ $data['total'] }}</p>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-900 p-5 shadow border-l-4 border-sky-500">
        <p class="text-xs uppercase tracking-wide text-gray-500">Rata-rata Nilai Akhir</p>
        <p class="mt-1 text-3xl font-extrabold text-gray-900 dark:text-white">
            {{ $data['rataAkhir'] ? number_format($data['rataAkhir'], 2) : '—' }}
        </p>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-900 p-5 shadow border-l-4 border-emerald-500">
        <p class="text-xs uppercase tracking-wide text-gray-500">Predikat A</p>
        <p class="mt-1 text-3xl font-extrabold text-emerald-600">{{ $data['predikatCount']['A'] }}</p>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-900 p-5 shadow border-l-4 border-amber-500">
        <p class="text-xs uppercase tracking-wide text-gray-500">Predikat B</p>
        <p class="mt-1 text-3xl font-extrabold text-amber-600">{{ $data['predikatCount']['B'] }}</p>
    </div>
</div>

{{-- Distribusi Predikat --}}
<div class="rounded-xl bg-white dark:bg-gray-900 p-5 shadow">
    <h3 class="text-sm font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2">
        Distribusi Predikat
    </h3>
    <div class="mt-3 grid grid-cols-3 gap-3 md:grid-cols-6">
        @foreach (['A', 'B', 'C', 'D', 'E', '-'] as $p)
            @php
                $warna = match($p) {
                    'A' => 'text-emerald-600',
                    'B' => 'text-sky-600',
                    'C' => 'text-amber-600',
                    'D', 'E' => 'text-rose-600',
                    default => 'text-gray-500',
                };
            @endphp
            <div class="text-center">
                <p class="text-xs text-gray-500">{{ $p }}</p>
                <p class="text-2xl font-extrabold {{ $warna }}">{{ $data['predikatCount'][$p] }}</p>
            </div>
        @endforeach
    </div>
</div>

{{-- Tabel Rekap --}}
<div class="rounded-xl bg-white dark:bg-gray-900 shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase">
                <tr>
                    <th class="px-3 py-2 text-left">No</th>
                    <th class="px-3 py-2 text-left">Nama</th>
                    <th class="px-3 py-2 text-left">Kelas</th>
                    <th class="px-3 py-2 text-left">Jurusan</th>
                    <th class="px-3 py-2 text-left">DUDI</th>
                    <th class="px-3 py-2 text-center">Kinerja</th>
                    <th class="px-3 py-2 text-center">Kompetensi</th>
                    <th class="px-3 py-2 text-center">Laporan</th>
                    <th class="px-3 py-2 text-center">Presentasi</th>
                    <th class="px-3 py-2 text-center">Sidang</th>
                    <th class="px-3 py-2 text-center">Nilai Akhir</th>
                    <th class="px-3 py-2 text-center">Predikat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($data['rows'] as $i => $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                        <td class="px-3 py-2 font-semibold text-gray-900 dark:text-white">{{ $row['nama'] }}</td>
                        <td class="px-3 py-2">{{ $row['kelas'] }}</td>
                        <td class="px-3 py-2">{{ $row['jurusan'] }}</td>
                        <td class="px-3 py-2 max-w-xs truncate" title="{{ $row['dudi'] }}">{{ $row['dudi'] }}</td>
                        <td class="px-3 py-2 text-center">{{ $row['kinerja'] ?? '—' }}</td>
                        <td class="px-3 py-2 text-center">{{ $row['kompetensi'] ?? '—' }}</td>
                        <td class="px-3 py-2 text-center">{{ $row['laporan'] ?? '—' }}</td>
                        <td class="px-3 py-2 text-center">{{ $row['presentasi'] ?? '—' }}</td>
                        <td class="px-3 py-2 text-center">{{ $row['sidang'] ?? '—' }}</td>
                        <td class="px-3 py-2 text-center font-bold text-primary-600 dark:text-primary-400">
                            {{ $row['akhir'] !== null ? number_format((float) $row['akhir'], 2) : '—' }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-block rounded-full px-2 py-0.5 text-xs font-bold
                                @if ($row['predikat'] === 'A') bg-emerald-100 text-emerald-700
                                @elseif ($row['predikat'] === 'B') bg-sky-100 text-sky-700
                                @elseif ($row['predikat'] === 'C') bg-amber-100 text-amber-700
                                @elseif (in_array($row['predikat'], ['D', 'E'])) bg-rose-100 text-rose-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ $row['predikat'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-3 py-12 text-center text-gray-500">
                            Belum ada data nilai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</x-filament-panels::page>