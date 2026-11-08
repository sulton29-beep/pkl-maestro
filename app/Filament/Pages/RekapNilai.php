<?php

namespace App\Filament\Pages;

use App\Models\Dudi;
use App\Models\Jurusan;
use App\Models\Nilai;
use App\Services\HitungNilaiService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Response;

class RekapNilai extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Rekap Nilai';
    protected static ?string $title = 'Rekap Nilai PKL';
    protected static ?string $navigationGroup = 'Penilaian PKL';
    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.pages.rekap-nilai';

    public ?int $filterJurusan = null;
    public ?int $filterDudi = null;
    public ?string $filterKelas = null;

    public function mount(): void
    {
        //
    }

    public function getDataProperty(): array
    {
        $query = Nilai::query()
            ->with([
                'siswa.jurusan',
                'penempatanPkl.dudi',
                'penempatanPkl.guruPembimbing',
                'penempatanPkl.guruPenguji',
            ]);

        if ($this->filterJurusan) {
            $query->whereHas('siswa', fn ($q) => $q->where('jurusan_id', $this->filterJurusan));
        }

        if ($this->filterDudi) {
            $query->whereHas('penempatanPkl', fn ($q) => $q->where('dudi_id', $this->filterDudi));
        }

        if ($this->filterKelas) {
            $query->whereHas('siswa', fn ($q) => $q->where('kelas', $this->filterKelas));
        }

        $nilais = $query->orderBy('nilai_akhir', 'desc')->get();

        $rows = $nilais->map(function ($n) {
            return [
                'nama'      => $n->siswa?->nama ?? '-',
                'nis'       => $n->siswa?->nis ?? '-',
                'kelas'     => $n->siswa?->kelas ?? '-',
                'jurusan'   => $n->siswa?->jurusan?->nama ?? '-',
                'dudi'      => $n->penempatanPkl?->dudi?->nama_perusahaan ?? '-',
                'pembimbing' => $n->penempatanPkl?->guruPembimbing?->nama ?? '-',
                'penguji'   => $n->penempatanPkl?->guruPenguji?->nama ?? '-',
                'kinerja'   => $n->nilai_kinerja,
                'kompetensi' => $n->nilai_kompetensi,
                'laporan'   => $n->nilai_laporan,
                'presentasi' => $n->nilai_presentasi,
                'sidang'    => $n->nilai_sidang,
                'akhir'     => $n->nilai_akhir,
                'predikat'  => HitungNilaiService::predikat(
                    $n->nilai_akhir !== null ? (float) $n->nilai_akhir : null
                ),
            ];
        });

        $total = $rows->count();
        $rataAkhir = $total > 0 ? round($rows->avg('akhir'), 2) : 0;

        $predikatCount = [
            'A' => $rows->where('predikat', 'A')->count(),
            'B' => $rows->where('predikat', 'B')->count(),
            'C' => $rows->where('predikat', 'C')->count(),
            'D' => $rows->where('predikat', 'D')->count(),
            'E' => $rows->where('predikat', 'E')->count(),
            '-' => $rows->where('predikat', '-')->count(),
        ];

        return [
            'rows'          => $rows,
            'total'         => $total,
            'rataAkhir'     => $rataAkhir,
            'predikatCount' => $predikatCount,
        ];
    }

    public function getJurusanOptionsProperty()
    {
        return Jurusan::orderBy('nama')->pluck('nama', 'id')->toArray();
    }

    public function getDudiOptionsProperty()
    {
        return Dudi::orderBy('nama_perusahaan')->pluck('nama_perusahaan', 'id')->toArray();
    }

    public function getKelasOptionsProperty()
    {
        return Nilai::query()
            ->join('siswas', 'siswas.id', '=', 'nilais.siswa_id')
            ->whereNotNull('siswas.kelas')
            ->distinct()
            ->orderBy('siswas.kelas')
            ->pluck('siswas.kelas', 'siswas.kelas')
            ->toArray();
    }

    public function resetFilter(): void
    {
        $this->filterJurusan = null;
        $this->filterDudi = null;
        $this->filterKelas = null;
    }

    /**
     * Export rekap nilai sebagai file Excel (.xls) dengan warna & format.
     * Pakai trik HTML table + CSS inline biar Excel render warna tanpa library.
     */
    public function exportExcel()
    {
        $data = $this->getDataProperty();

        $filename = 'rekap-nilai-pkl-' . now()->format('Y-m-d-His') . '.xls';

        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta charset="UTF-8">';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        $html .= '<x:Name>Rekap Nilai PKL</x:Name><x:WorksheetOptions><x:DisplayGridlines/>';
        $html .= '</x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '<style>
            table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11pt; }
            td, th { border: 1px solid #9ca3af; padding: 6px 8px; vertical-align: middle; }
            .title { font-size: 18pt; font-weight: bold; text-align: center; color: #1e3a8a; border: none; padding: 12px 8px 4px 8px; letter-spacing: 1px; }
            .subtitle { font-size: 12pt; font-weight: bold; text-align: center; color: #374151; border: none; padding: 2px 8px; }
            .subtitle-small { font-size: 10pt; text-align: center; color: #6b7280; border: none; padding: 2px 8px 8px 8px; }
            .meta { font-size: 10pt; border: none; text-align: left; padding: 4px 8px; color: #374151; }
            .head { background: #1e3a8a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #1e3a8a; font-size: 11pt; }
            .head-left { text-align: left; }
            .row-odd { background: #f9fafb; }
            .row-even { background: #ffffff; }
            .center { text-align: center; }
            .nilai-akhir { background: #fef3c7; font-weight: bold; text-align: center; color: #92400e; font-size: 12pt; }
            .pred-a { background: #d1fae5; color: #065f46; font-weight: bold; text-align: center; font-size: 12pt; }
            .pred-b { background: #dbeafe; color: #1e40af; font-weight: bold; text-align: center; font-size: 12pt; }
            .pred-c { background: #fef3c7; color: #92400e; font-weight: bold; text-align: center; font-size: 12pt; }
            .pred-de { background: #fee2e2; color: #991b1b; font-weight: bold; text-align: center; font-size: 12pt; }
            .pred-dash { background: #f3f4f6; color: #6b7280; text-align: center; font-size: 12pt; }
            .summary-spacer { border: none; padding: 8px; }
            .summary-label { background: #e5e7eb; font-weight: bold; text-align: right; color: #1f2937; padding: 8px 12px; border: 1px solid #9ca3af; }
            .summary-value { background: #fef3c7; font-weight: bold; text-align: center; color: #92400e; font-size: 12pt; padding: 8px 12px; border: 1px solid #9ca3af; }
            .empty-row { text-align: center; padding: 20px; color: #6b7280; font-style: italic; }
        </style></head><body>';

        $html .= '<table>';

        // ============= HEADER / JUDUL =============
        $html .= '<tr><td colspan="15" class="title">REKAP NILAI PKL</td></tr>';
        $html .= '<tr><td colspan="15" class="subtitle">SMKS MAESTRO SEPATAN</td></tr>';
        $html .= '<tr><td colspan="15" class="subtitle-small">Jl. Raya Kramat Pakuhaji Km 3,5, Kayu Agung, Sepatan, Tangerang &middot; NPSN: 69876678</td></tr>';
        $html .= '<tr><td colspan="15" class="meta">&nbsp;</td></tr>';
        $html .= '<tr><td colspan="15" class="meta"><b>Diekspor:</b> ' . e(now()->translatedFormat('d F Y, H:i')) . ' WIB &nbsp;&middot;&nbsp; <b>Oleh:</b> ' . e(auth()->user()?->name ?? '-') . '</td></tr>';

        $filterInfo = [];
        if ($this->filterJurusan) {
            $filterInfo[] = 'Jurusan: ' . ($this->getJurusanOptionsProperty()[$this->filterJurusan] ?? '-');
        }
        if ($this->filterDudi) {
            $filterInfo[] = 'DUDI: ' . ($this->getDudiOptionsProperty()[$this->filterDudi] ?? '-');
        }
        if ($this->filterKelas) {
            $filterInfo[] = 'Kelas: ' . $this->filterKelas;
        }
        if (!empty($filterInfo)) {
            $html .= '<tr><td colspan="15" class="meta"><b>Filter Aktif:</b> ' . e(implode(' | ', $filterInfo)) . '</td></tr>';
        }

        $html .= '<tr><td colspan="15" class="meta">&nbsp;</td></tr>';

        // ============= HEADER TABEL =============
        $headers = ['No', 'Nama Siswa', 'NIS', 'Kelas', 'Jurusan', 'DUDI', 'Guru Pembimbing', 'Guru Penguji', 'Kinerja', 'Kompetensi', 'Laporan', 'Presentasi', 'Sidang', 'Nilai Akhir', 'Predikat'];
        $html .= '<tr>';
        foreach ($headers as $i => $h) {
            $cls = 'head' . (in_array($i, [1, 5, 6, 7]) ? ' head-left' : '');
            $html .= '<td class="' . $cls . '">' . e($h) . '</td>';
        }
        $html .= '</tr>';

        // ============= DATA =============
        if ($data['rows']->isEmpty()) {
            $html .= '<tr><td colspan="15" class="empty-row">Belum ada data nilai.</td></tr>';
        } else {
            $no = 1;
            foreach ($data['rows'] as $row) {
                $rowClass = ($no % 2 === 0) ? 'row-even' : 'row-odd';

                $html .= '<tr>';
                $html .= '<td class="' . $rowClass . ' center">' . $no++ . '</td>';
                $html .= '<td class="' . $rowClass . '">' . e($row['nama']) . '</td>';
                $html .= '<td class="' . $rowClass . ' center">' . e($row['nis']) . '</td>';
                $html .= '<td class="' . $rowClass . ' center">' . e($row['kelas']) . '</td>';
                $html .= '<td class="' . $rowClass . '">' . e($row['jurusan']) . '</td>';
                $html .= '<td class="' . $rowClass . '">' . e($row['dudi']) . '</td>';
                $html .= '<td class="' . $rowClass . '">' . e($row['pembimbing']) . '</td>';
                $html .= '<td class="' . $rowClass . '">' . e($row['penguji']) . '</td>';
                $html .= '<td class="' . $rowClass . ' center">' . self::fmt($row['kinerja']) . '</td>';
                $html .= '<td class="' . $rowClass . ' center">' . self::fmt($row['kompetensi']) . '</td>';
                $html .= '<td class="' . $rowClass . ' center">' . self::fmt($row['laporan']) . '</td>';
                $html .= '<td class="' . $rowClass . ' center">' . self::fmt($row['presentasi']) . '</td>';
                $html .= '<td class="' . $rowClass . ' center">' . self::fmt($row['sidang']) . '</td>';
                $html .= '<td class="nilai-akhir">' . self::fmt($row['akhir']) . '</td>';

                $predClass = match ($row['predikat']) {
                    'A' => 'pred-a',
                    'B' => 'pred-b',
                    'C' => 'pred-c',
                    'D', 'E' => 'pred-de',
                    default => 'pred-dash',
                };
                $html .= '<td class="' . $predClass . '">' . e($row['predikat']) . '</td>';
                $html .= '</tr>';
            }
        }

        // ============= SUMMARY =============
        $html .= '<tr><td colspan="15" class="summary-spacer">&nbsp;</td></tr>';
        $html .= '<tr><td colspan="12" class="summary-label">TOTAL SISWA DINILAI</td><td colspan="3" class="summary-value">' . $data['total'] . '</td></tr>';
        $html .= '<tr><td colspan="12" class="summary-label">RATA-RATA NILAI AKHIR</td><td colspan="3" class="summary-value">' . self::fmt($data['rataAkhir']) . '</td></tr>';
        $html .= '<tr><td colspan="12" class="summary-label">JUMLAH PREDIKAT A</td><td colspan="3" class="summary-value">' . $data['predikatCount']['A'] . '</td></tr>';
        $html .= '<tr><td colspan="12" class="summary-label">JUMLAH PREDIKAT B</td><td colspan="3" class="summary-value">' . $data['predikatCount']['B'] . '</td></tr>';

        $html .= '</table></body></html>';

        return Response::make($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Format angka: 2 desimal, desimal koma, ribuan titik.
     * Kalau null/kosong → tampil "-".
     */
    protected static function fmt($v): string
    {
        if ($v === null || $v === '') {
            return '-';
        }
        return number_format((float) $v, 2, ',', '.');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'kepala_sekolah', 'waka_kurikulum', 'kaprodi']) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'kepala_sekolah', 'waka_kurikulum', 'kaprodi']) ?? false;
    }
}