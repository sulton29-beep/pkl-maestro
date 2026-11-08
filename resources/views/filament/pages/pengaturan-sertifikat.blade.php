<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-check" color="success">
                Simpan Pengaturan
            </x-filament::button>
        </div>
    </form>

    <x-filament::section class="mt-6">
        <x-slot name="heading">Preview Format Nomor</x-slot>
        <x-slot name="description">
            Nomor sertifikat berikutnya yang bakal digenerate otomatis pakai format di atas.
        </x-slot>
        @php
            $format = $this->data['nomor_format'] ?? '{no}/PKL/SMKS-MST/{tahun}';
            $contoh = str_replace(
                ['{no}', '{tahun}', '{bulan}'],
                ['001', date('Y'), date('m')],
                $format
            );
        @endphp
        <div class="text-lg font-bold text-primary-600">
            {{ $contoh }}
        </div>
    </x-filament::section>
</x-filament-panels::page>