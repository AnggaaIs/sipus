<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section
            heading="Cetak laporan perpustakaan"
            description="Pilih jenis laporan dan periode data. Halaman ini hanya membaca data dari sistem, jadi aman untuk dipakai saat rekap."
            icon="heroicon-m-document-chart-bar"
            icon-color="primary"
        >
            <div class="grid gap-4 lg:grid-cols-[1fr_1fr_1fr_auto] lg:items-end">
                <label class="space-y-2">
                    <span class="text-sm font-medium text-gray-950">Jenis laporan</span>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="reportType">
                            @foreach ($this->reportTypeOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-gray-950">Dari tanggal</span>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="startDate" />
                    </x-filament::input.wrapper>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-gray-950">Sampai tanggal</span>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="endDate" />
                    </x-filament::input.wrapper>
                </label>

                <x-filament::button
                    tag="a"
                    :href="$this->printUrl()"
                    target="_blank"
                    icon="heroicon-m-printer"
                    class="justify-center"
                >
                    Cetak
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section
            :heading="$this->reportTitle()"
            :description="'Periode '.$this->dateRangeLabel()"
            icon="heroicon-m-document-text"
        >
            <div class="mb-6 flex flex-col gap-2 border-b border-gray-200 pb-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <x-filament::badge color="primary">SIPUS SMA Semen Padang</x-filament::badge>
                </div>
                <div class="text-sm text-gray-500 sm:text-right">
                    Preview laporan sebelum dicetak
                </div>
            </div>

            @include('filament.pages.partials.report-content', ['report' => $this->reportData()])
        </x-filament::section>
    </div>
</x-filament-panels::page>
