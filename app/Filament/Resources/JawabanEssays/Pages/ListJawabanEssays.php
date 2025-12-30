<?php

namespace App\Filament\Resources\JawabanEssays\Pages;

use App\Filament\Resources\JawabanEssays\JawabanEssayResource;
use App\Services\GeminiService;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListJawabanEssays extends ListRecords
{
    protected static string $resource = JawabanEssayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Action untuk menilai ulang jawaban yang error
            Action::make('nilaiUlangError')
                ->label('Nilai Ulang Error')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (GeminiService $gemini) {
                    $errorJawaban = \App\Models\JawabanEssay::where('status_penilaian', 'error')->get();
                    
                    $success = 0;
                    $failed = 0;
                    
                    foreach ($errorJawaban as $jawaban) {
                        $result = $gemini->nilaiJawabanEssay($jawaban);
                        if ($result['success']) {
                            $success++;
                        } else {
                            $failed++;
                        }
                    }
                    
                    Notification::make()
                        ->title('Penilaian Selesai')
                        ->body("Berhasil: {$success}, Gagal: {$failed}")
                        ->success()
                        ->send();
                }),
                
            CreateAction::make(),
        ];
    }
}