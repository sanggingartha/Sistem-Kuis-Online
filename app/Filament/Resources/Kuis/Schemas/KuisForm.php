<?php

namespace App\Filament\Resources\Kuis\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder as ComponentsPlaceholder;
use Filament\Forms\Get;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class KuisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Wizard::make([
                Step::make('informasi')
                    ->label('Informasi Kuis')
                    ->icon(Heroicon::InformationCircle)
                    ->components([

                        Section::make('Informasi Dasar')
                            ->components([
                                TextInput::make('nama_kuis')
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('deskripsi')
                                    ->rows(3),
                            ]),

                        Section::make('Waktu')
                            ->components([
                                Grid::make(3)->components([
                                    TextInput::make('waktu_pengerjaan')
                                        ->numeric()
                                        ->default(30)
                                        ->suffix('menit')
                                        ->required(),
                                ]),

                                Grid::make(2)->components([
                                    DateTimePicker::make('mulai_dari'),
                                    DateTimePicker::make('berakhir_pada')
                                        ->after('mulai_dari'),
                                ]),
                            ]),

                        Section::make('Pengaturan')
                            ->collapsible()
                            ->components([
                                Toggle::make('acak_soal'),
                                Toggle::make('acak_opsi'),
                                Toggle::make('tampilkan_hasil')
                                    ->default(true),

                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'aktif' => 'Aktif',
                                        'selesai' => 'Selesai',
                                        'arsip' => 'Arsip',
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ]),
                    ]),

                Step::make('pilihan_ganda')
                    ->label('Pilihan Ganda')
                    ->icon(Heroicon::ListBullet)
                    ->components([

                        Repeater::make('soalPilihanGanda')
                            ->relationship()
                            ->schema([
                                TextInput::make('poin')
                                    ->numeric()
                                    ->default(10)
                                    ->required(),

                                RichEditor::make('pertanyaan')
                                    ->required(),

                                FileUpload::make('gambar_url')
                                    ->image()
                                    ->directory('soal-images'),

                                Repeater::make('opsi')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('teks_opsi')->required(),
                                        Toggle::make('opsi_benar'),
                                    ])
                                    ->minItems(2)
                                    ->maxItems(6),
                            ])
                            ->collapsible()
                            ->itemLabel(
                                fn($state) =>
                                Str::limit(strip_tags($state['pertanyaan'] ?? ''), 40)
                            ),
                    ]),

                Step::make('essay')
                    ->label('Essay')
                    ->icon(Heroicon::PencilSquare)
                    ->components([

                        Repeater::make('soalEssay')
                            ->relationship()
                            ->schema([
                                TextInput::make('poin_maksimal')
                                    ->numeric()
                                    ->default(20)
                                    ->required(),

                                RichEditor::make('pertanyaan')
                                    ->required(),

                                FileUpload::make('gambar_url')
                                    ->image()
                                    ->directory('essay-images'),

                                Textarea::make('jawaban_acuan'),
                                Textarea::make('rubrik_penilaian'),
                            ])
                            ->collapsible(),
                    ]),
                Step::make('review')
                    ->label('Review')
                    ->icon(Heroicon::CheckBadge)
                    ->components([
                        ComponentsPlaceholder::make('ringkasan')
                            ->content(
                                fn(UtilitiesGet $get) =>
                                "Total PG: " . count($get('soalPilihanGanda') ?? []) .
                                    " | Essay: " . count($get('soalEssay') ?? [])
                            ),
                    ]),
            ])
                ->columnSpanFull()
                ->persistStepInQueryString()
                ->skippable(),
        ]);
    }
}
