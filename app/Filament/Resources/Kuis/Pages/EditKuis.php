<?php

namespace App\Filament\Resources\Kuis\Pages;

use App\Filament\Resources\Kuis\KuisResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKuis extends EditRecord
{
    protected static string $resource = KuisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
