<?php

namespace App\Filament\Resources\Kuis\Tables;

use Filament\Actions\Action as ActionsAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KuisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kuis')
                    ->label('Nama Kuis')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->deskripsi ? \Illuminate\Support\Str::limit($record->deskripsi, 30) : null),

                TextColumn::make('kode_kuis')
                    ->label('Kode Kuis')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode kuis berhasil disalin!')
                    ->copyMessageDuration(1500)
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-key'),

                TextColumn::make('waktu_pengerjaan')
                    ->label('Durasi')
                    ->suffix(' menit')
                    ->sortable()
                    ->alignCenter()
                    ->icon('heroicon-o-clock'),

                TextColumn::make('total_poin')
                    ->label('Total Poin')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->getStateUsing(fn($record) => $record->fresh()->total_poin),

                IconColumn::make('acak_soal')
                    ->label('Acak')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn($record) => $record->acak_soal ? 'Soal diacak' : 'Soal tidak diacak')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'aktif' => 'success',
                        'selesai' => 'warning',
                        'arsip' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionsAction::make('showQR')
                    ->label('Lihat QR')
                    ->tooltip('Lihat QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->url(fn($record) => route('kuis.qr-code-preview', $record))
                    ->openUrlInNewTab(),

                ViewAction::make()
                    ->tooltip('Lihat kuis'),
                EditAction::make()
                    ->tooltip('Edit kuis'),

                ActionGroup::make([
                    ActionsAction::make('setDraft')
                        ->label('Set ke Draft')
                        ->icon('heroicon-o-pencil')
                        ->color('gray')
                        ->visible(fn($record) => $record->status !== 'draft')
                        ->action(fn($record) => $record->update(['status' => 'draft'])),

                    ActionsAction::make('setAktif')
                        ->label('Aktifkan Kuis')
                        ->icon('heroicon-o-play-circle')
                        ->color('success')
                        ->visible(fn($record) => $record->status !== 'aktif')
                        ->action(fn($record) => $record->update(['status' => 'aktif'])),

                    ActionsAction::make('setSelesai')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('warning')
                        ->visible(fn($record) => $record->status !== 'selesai')
                        ->action(fn($record) => $record->update(['status' => 'selesai'])),

                    ActionsAction::make('setArsip')
                        ->label('Arsipkan')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->visible(fn($record) => $record->status !== 'arsip')
                        ->action(fn($record) => $record->update(['status' => 'arsip'])),
                ])
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->dropdownPlacement('bottom-end')
                    ->tooltip('Ubah status kuis'),


            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
