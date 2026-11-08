<?php

namespace App\Filament\Resources\SertifikatResource\Pages;

use App\Filament\Resources\SertifikatResource;
use App\Models\PenempatanPkl;
use App\Services\SertifikatService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSertifikat extends CreateRecord
{
    protected static string $resource = SertifikatResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $penempatan = PenempatanPkl::find($data['penempatan_pkl_id']);
        $service = app(SertifikatService::class);

        $data['siswa_id'] = $penempatan->siswa_id;
        $data['nomor_sertifikat'] = $service->generateNomorSertifikat();
        $data['qr_token'] = Str::random(40);
        $data['status'] = $data['status'] ?? 'draft';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Sertifikat berhasil dibuat';
    }
}