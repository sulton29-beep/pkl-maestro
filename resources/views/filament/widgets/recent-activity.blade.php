<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📋 Aktivitas Terbaru
        </x-slot>

        <x-slot name="description">
            Log aktivitas sistem — auto-refresh tiap 30 detik
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::button
                :href="$this->getSeeAllUrl()"
                color="info"
                size="sm"
                icon="heroicon-o-arrow-right"
                icon-position="after"
                tag="a">
                Lihat Semua
            </x-filament::button>
        </x-slot>

        @php
            $activities = $this->getActivities();
        @endphp

        @if($activities->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <x-heroicon-o-clipboard-document-list class="w-12 h-12 mx-auto mb-2 opacity-50" />
                <div class="text-sm">Belum ada aktivitas tercatat.</div>
            </div>
        @else
            <div class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach($activities as $log)
                    @php
                        $aksiColor = match($log->aksi) {
                            'created' => 'success',
                            'updated' => 'warning',
                            'deleted' => 'danger',
                            'login'   => 'info',
                            'logout'  => 'gray',
                            default   => 'primary',
                        };
                        $aksiIcon = match($log->aksi) {
                            'created' => 'heroicon-o-plus-circle',
                            'updated' => 'heroicon-o-pencil-square',
                            'deleted' => 'heroicon-o-trash',
                            'login'   => 'heroicon-o-key',
                            'logout'  => 'heroicon-o-arrow-right-on-rectangle',
                            default   => 'heroicon-o-bolt',
                        };
                    @endphp

                    <div class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 mt-1">
                            <x-filament::icon
                                :icon="$aksiIcon"
                                class="w-5 h-5 text-{{ $aksiColor }}-500"
                            />
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-sm text-gray-900 dark:text-white">
                                    {{ $log->user_name }}
                                </span>
                                <x-filament::badge :color="$aksiColor" size="sm">
                                    {{ strtoupper($log->aksi) }}
                                </x-filament::badge>
                                <x-filament::badge color="gray" size="sm">
                                    {{ $log->model_short_name }}
                                </x-filament::badge>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ $log->deskripsi ?? '-' }}
                            </div>
                        </div>

                        {{-- Waktu --}}
                        <div class="flex-shrink-0 text-right">
                            <div class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $log->created_at?->diffForHumans() }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ $log->created_at?->format('H:i:s') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>