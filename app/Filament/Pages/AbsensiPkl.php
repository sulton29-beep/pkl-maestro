<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiPkl extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Absensi PKL';
    protected static ?string $title = 'Absensi PKL';
    protected static ?string $navigationGroup = 'PKL';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.absensi-pkl';

    public $hari_ini;
    public $absensi_hari_ini;
    public $dudi;
    public $siswa;
    public $radius = 100;

    public function mount(): void
    {
        $this->hari_ini = Carbon::today()->format('Y-m-d');

        $user = Auth::user();
        $this->siswa = Siswa::where('user_id', $user->id)->first();

        if ($this->siswa) {
            $penempatan = $this->siswa->penempatanPkl()
                ->where('status', 'berjalan')
                ->latest()
                ->first();

            if ($penempatan) {
                $this->dudi = Dudi::find($penempatan->dudi_id);
            }

            $this->absensi_hari_ini = Absensi::where('siswa_id', $this->siswa->id)
                ->where('tanggal', $this->hari_ini)
                ->first();
        }
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('siswa') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('siswa') ?? false;
    }

    /**
     * Helper: ambil siswa + dudi fresh dari DB (dipanggil di method absen).
     */
    private function ambilSiswaDanDudi(): array
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return [null, null];
        }

        $penempatan = $siswa->penempatanPkl()
            ->where('status', 'berjalan')
            ->latest()
            ->first();

        $dudi = $penempatan ? Dudi::find($penempatan->dudi_id) : null;

        return [$siswa, $dudi];
    }

    public function absenMasuk(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string',
        ]);

        // Ambil data fresh dari DB (bukan dari $this->siswa)
        [$siswa, $dudi] = $this->ambilSiswaDanDudi();

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
        }
        if (!$dudi) {
            return response()->json(['success' => false, 'message' => 'Data DUDI tidak ditemukan. Hubungi admin.']);
        }

        $hari_ini = Carbon::today()->format('Y-m-d');

        $absensi_hari_ini = Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $hari_ini)
            ->first();

        if ($absensi_hari_ini) {
            return response()->json(['success' => false, 'message' => 'Kamu sudah absen masuk hari ini.']);
        }

        $jarak = $this->hitungJarak(
            $validated['latitude'], $validated['longitude'],
            $dudi->latitude, $dudi->longitude
        );

        if ($jarak > $this->radius) {
            return response()->json([
                'success' => false,
                'message' => "Kamu berada {$jarak}m dari lokasi DUDI. Maksimal {$this->radius}m.",
            ]);
        }

        $fotoPath = $this->simpanFoto($siswa->id, $validated['foto'], 'masuk');

        Absensi::create([
            'siswa_id' => $siswa->id,
            'dudi_id' => $dudi->id,
            'tanggal' => $hari_ini,
            'jam_masuk' => now()->format('H:i:s'),
            'foto_masuk' => $fotoPath,
            'latitude_masuk' => $validated['latitude'],
            'longitude_masuk' => $validated['longitude'],
            'jarak_masuk' => $jarak,
            'status_masuk' => 'valid',
        ]);

        return response()->json(['success' => true, 'message' => 'Absen masuk berhasil!']);
    }

    public function absenKeluar(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string',
        ]);

        [$siswa, $dudi] = $this->ambilSiswaDanDudi();

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
        }
        if (!$dudi) {
            return response()->json(['success' => false, 'message' => 'Data DUDI tidak ditemukan. Hubungi admin.']);
        }

        $hari_ini = Carbon::today()->format('Y-m-d');

        $absensi_hari_ini = Absensi::where('siswa_id', $siswa->id)
            ->where('tanggal', $hari_ini)
            ->first();

        if (!$absensi_hari_ini) {
            return response()->json(['success' => false, 'message' => 'Kamu belum absen masuk.']);
        }

        if ($absensi_hari_ini->jam_keluar) {
            return response()->json(['success' => false, 'message' => 'Kamu sudah absen keluar hari ini.']);
        }

        $jarak = $this->hitungJarak(
            $validated['latitude'], $validated['longitude'],
            $dudi->latitude, $dudi->longitude
        );

        if ($jarak > $this->radius) {
            return response()->json([
                'success' => false,
                'message' => "Kamu berada {$jarak}m dari lokasi DUDI. Maksimal {$this->radius}m.",
            ]);
        }

        $fotoPath = $this->simpanFoto($siswa->id, $validated['foto'], 'keluar');

        $absensi_hari_ini->update([
            'jam_keluar' => now()->format('H:i:s'),
            'foto_keluar' => $fotoPath,
            'latitude_keluar' => $validated['latitude'],
            'longitude_keluar' => $validated['longitude'],
            'jarak_keluar' => $jarak,
            'status_keluar' => 'valid',
        ]);

        return response()->json(['success' => true, 'message' => 'Absen keluar berhasil!']);
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2): int
    {
        if (!$lat2 || !$lon2) return 0;

        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return (int) round($earthRadius * $c);
    }

    private function simpanFoto(int $siswaId, string $base64, string $tipe): string
    {
        $image = preg_replace('#^data:image/\w+;base64,#i', '', $base64);
        $image = str_replace(' ', '+', $image);
        $filename = 'absensi/' . $siswaId . '_' . $tipe . '_' . now()->format('Ymd_His') . '.jpg';
        \Storage::disk('public')->put($filename, base64_decode($image));
        return $filename;
    }
}