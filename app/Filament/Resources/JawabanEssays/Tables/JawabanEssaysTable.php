<?php

namespace App\Filament\Resources\JawabanEssays\Tables;

use App\Services\GeminiService;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class JawabanEssaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hasilKuis.user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('soal.pertanyaan')
                    ->label('Pertanyaan')
                    ->limit(50)
                    ->html(),
                TextColumn::make('poin_maksimal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('skor_ai')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                TextColumn::make('poin_diperoleh')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status_penilaian')
                    ->badge()
                    ->colors([
                        'warning' => 'belum_dinilai',
                        'info' => 'sedang_proses',
                        'success' => 'sudah_dinilai',
                        'danger' => 'error',
                    ]),
                TextColumn::make('nilai_oleh')
                    ->badge(),
                TextColumn::make('dijawab_pada')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('dinilai_pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('nilaiUlang')
                    ->label('Nilai Ulang AI')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->visible(fn($record) => in_array($record->status_penilaian, ['belum_dinilai', 'error']))
                    ->requiresConfirmation()
                    ->action(function ($record, GeminiService $gemini) {
                        $result = $gemini->nilaiJawabanEssay($record);

                        if ($result['success']) {
                            Notification::make()
                                ->title('Penilaian Berhasil')
                                ->body("Skor: {$result['skor']}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Penilaian Gagal')
                                ->body($result['error'])
                                ->danger()
                                ->send();
                        }
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
