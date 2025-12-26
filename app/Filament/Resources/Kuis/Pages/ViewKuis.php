<?php

namespace App\Filament\Resources\Kuis\Pages;

use App\Filament\Resources\Kuis\KuisResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKuis extends ViewRecord
{
    protected static string $resource = KuisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
