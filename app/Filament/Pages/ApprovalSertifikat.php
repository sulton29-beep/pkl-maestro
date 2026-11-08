<?php

namespace App\Filament\Pages;

use App\Models\Sertifikat;
use App\Services\SertifikatService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApprovalSertifikat extends Page implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Sertifikat PKL';
    protected static ?string $navigationLabel = 'Approval Sertifikat';
    protected static ?string $title = 'Approval Sertifikat';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.approval-sertifikat';

    public static function canAccess(): bool
    {
        return Auth::user()?->hasAnyRole(['kaprodi', 'waka_kurikulum', 'kepala_sekolah']) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasAnyRole(['kaprodi', 'waka_kurikulum', 'kepala_sekolah']) ?? false;
    }

    public function getHeading(): string
    {
        $user = Auth::user();
        if ($user->hasRole('kaprodi')) {
            return 'Validasi Sertifikat (Kaprodi)';
        }
        if ($user->hasRole('waka_kurikulum')) {
            return 'Persetujuan Sertifikat (Waka Kurikulum)';
        }
        if ($user->hasRole('kepala_sekolah')) {
            return 'Penerbitan Sertifikat (Kepala Sekolah)';
        }
        return 'Approval Sertifikat';
    }

    public function getSubheading(): ?string
    {
        $user = Auth::user();
        if ($user->hasRole('kaprodi')) {
            return 'Sertifikat draft yang perlu Anda validasi sebelum diteruskan ke Waka Kurikulum.';
        }
        if ($user->hasRole('waka_kurikulum')) {
            return 'Sertifikat yang sudah divalidasi Kaprodi, menunggu persetujuan Anda.';
        }
        if ($user->hasRole('kepala_sekolah')) {
            return 'Sertifikat yang sudah disetujui Waka Kurikulum, siap Anda TTD & terbitkan.';
        }
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('nomor_sertifikat')
                    ->label('No. Sertifikat')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Siswa')
                    ->searchable()
                    ->description(fn ($record) => ($record->siswa->nis ?? '-') . ' — ' . ($record->siswa->kelas ?? '-')),

                Tables\Columns\TextColumn::make('penempatanPkl.dudi.nama')
                    ->label('DUDI')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => Sertifikat::statusList()[$state] ?? $state)
                    ->colors([
                        'gray' => Sertifikat::STATUS_DRAFT,
                        'warning' => Sertifikat::STATUS_VALIDASI_KAPRODI,
                        'info' => Sertifikat::STATUS_DISETUJUI_WAKA,
                        'success' => Sertifikat::STATUS_TERBIT,
                        'danger' => Sertifikat::STATUS_DITOLAK,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Preview PDF')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn ($record) => route('sertifikat.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('approve')
                    ->label(fn () => Auth::user()->hasRole('kepala_sekolah') ? 'TTD & Terbitkan' : 'Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn () => in_array(Auth::user()->getRoleNames()->first(), ['kaprodi', 'waka_kurikulum', 'kepala_sekolah']))
                    ->form([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan (opsional)')
                            ->rows(2),
                    ])
                    ->action(function ($record, array $data) {
                        $user = Auth::user();

                        if ($user->hasRole('kaprodi')) {
                            $record->update([
                                'status' => Sertifikat::STATUS_VALIDASI_KAPRODI,
                                'catatan_revisi' => $data['catatan'] ?? null,
                            ]);
                            $msg = 'Sertifikat divalidasi. Diteruskan ke Waka Kurikulum.';
                        } elseif ($user->hasRole('waka_kurikulum')) {
                            $record->update([
                                'status' => Sertifikat::STATUS_DISETUJUI_WAKA,
                                'catatan_revisi' => $data['catatan'] ?? null,
                            ]);
                            $msg = 'Sertifikat disetujui. Diteruskan ke Kepala Sekolah.';
                        } elseif ($user->hasRole('kepala_sekolah')) {
                            $record->update([
                                'status' => Sertifikat::STATUS_TERBIT,
                                'tanggal_terbit' => now(),
                                'catatan_revisi' => $data['catatan'] ?? null,
                            ]);
                            $msg = 'Sertifikat resmi diterbitkan.';
                        } else {
                            $msg = 'Aksi tidak dikenali.';
                        }

                        Notification::make()
                            ->title($msg)
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn () => in_array(Auth::user()->getRoleNames()->first(), ['kaprodi', 'waka_kurikulum', 'kepala_sekolah']))
                    ->form([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => Sertifikat::STATUS_DITOLAK,
                            'catatan_revisi' => $data['catatan'],
                        ]);

                        Notification::make()
                            ->title('Sertifikat ditolak')
                            ->body($data['catatan'])
                            ->danger()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'asc')
            ->emptyStateHeading('Tidak ada sertifikat yang menunggu')
            ->emptyStateDescription('Semua sertifikat pada tahap ini sudah diproses.')
            ->emptyStateIcon('heroicon-o-check-badge');
    }

    protected function getQuery(): Builder
    {
        $user = Auth::user();
        $query = Sertifikat::query()->with(['siswa', 'penempatanPkl.dudi']);

        if ($user->hasRole('kaprodi')) {
            $query->where('status', Sertifikat::STATUS_DRAFT);
        } elseif ($user->hasRole('waka_kurikulum')) {
            $query->where('status', Sertifikat::STATUS_VALIDASI_KAPRODI);
        } elseif ($user->hasRole('kepala_sekolah')) {
            $query->where('status', Sertifikat::STATUS_DISETUJUI_WAKA);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }
}