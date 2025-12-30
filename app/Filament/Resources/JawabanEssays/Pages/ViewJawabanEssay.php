<?php

namespace App\Filament\Resources\JawabanEssays\Pages;

use App\Filament\Resources\JawabanEssays\JawabanEssayResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewJawabanEssay extends ViewRecord
{
    protected static string $resource = JawabanEssayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
