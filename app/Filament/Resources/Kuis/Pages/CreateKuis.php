<?php

namespace App\Filament\Resources\Kuis\Pages;

use App\Filament\Resources\Kuis\KuisResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateKuis extends CreateRecord
{
    protected static string $resource = KuisResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }
}
