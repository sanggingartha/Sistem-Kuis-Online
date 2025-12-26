<?php

namespace App\Filament\Resources\Kuis\Pages;

use App\Filament\Resources\Kuis\KuisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKuis extends ListRecords
{
    protected static string $resource = KuisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Kuis Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
}
