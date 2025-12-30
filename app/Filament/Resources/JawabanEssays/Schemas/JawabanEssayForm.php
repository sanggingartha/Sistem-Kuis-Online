<?php

namespace App\Filament\Resources\JawabanEssays\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class JawabanEssayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hasil_kuis_id')
                    ->required()
                    ->numeric(),
                TextInput::make('soal_id')
                    ->required()
                    ->numeric(),
                Textarea::make('jawaban_siswa')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('poin_maksimal')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('feedback_ai')
                    ->columnSpanFull(),
                TextInput::make('skor_ai')
                    ->numeric(),
                TextInput::make('poin_diperoleh')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status_penilaian')
                    ->options([
            'belum_dinilai' => 'Belum dinilai',
            'sedang_proses' => 'Sedang proses',
            'sudah_dinilai' => 'Sudah dinilai',
            'error' => 'Error',
        ])
                    ->default('belum_dinilai')
                    ->required(),
                Select::make('nilai_oleh')
                    ->options(['AI' => 'A i', 'pengajar' => 'Pengajar', 'sistem' => 'Sistem'])
                    ->default('AI')
                    ->required(),
                DateTimePicker::make('dijawab_pada')
                    ->required(),
                DateTimePicker::make('dinilai_pada'),
            ]);
    }
}
