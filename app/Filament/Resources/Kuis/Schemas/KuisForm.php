<?php

namespace App\Filament\Resources\Kuis\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Str;

class KuisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Wizard::make([
                Step::make('informasi')
                    ->label('Informasi Kuis')
                    ->description('Isi data dasar kuis')
                    ->components([

                        Section::make('Data Kuis')
                            ->schema([
                                TextInput::make('nama_kuis')
                                    ->label('Nama Kuis')
                                    ->placeholder('Contoh: UTS Matematika Kelas 10')
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->placeholder('Jelaskan tentang kuis ini')
                                    ->rows(2),
                            ]),

                        Section::make('Waktu dan Jadwal')
                            ->schema([
                                TextInput::make('waktu_pengerjaan')
                                    ->label('Durasi Pengerjaan')
                                    ->numeric()
                                    ->default(30)
                                    ->suffix('menit')
                                    ->required()
                                    ->helperText('Waktu maksimal mengerjakan'),

                                DateTimePicker::make('mulai_dari')
                                    ->label('Mulai Tanggal'),

                                DateTimePicker::make('berakhir_pada')
                                    ->label('Selesai Tanggal'),
                            ]),

                        Section::make('Pengaturan')
                            ->schema([
                                Toggle::make('acak_soal')
                                    ->label('Acak Urutan Soal')
                                    ->helperText('Soal akan muncul acak untuk setiap peserta'),

                                Toggle::make('acak_opsi')
                                    ->label('Acak Pilihan Jawaban')
                                    ->helperText('Pilihan jawaban diacak untuk setiap soal'),

                                Toggle::make('tampilkan_hasil')
                                    ->label('Tampilkan Nilai')
                                    ->default(true)
                                    ->helperText('Tampilkan hasil setelah selesai'),

                                Select::make('status')
                                    ->label('Status')
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

                Step::make('soal_pg')
                    ->label('Soal Pilihan Ganda')
                    ->description('Tambah soal pilihan ganda')
                    ->components([

                        Placeholder::make('info')
                            ->label('')
                            ->content('Setiap soal harus memiliki minimal 2 pilihan jawaban'),

                        Repeater::make('soalPilihanGanda')
                            ->relationship()
                            ->label('')
                            ->schema([
                                TextInput::make('poin')
                                    ->label('Poin Soal')
                                    ->numeric()
                                    ->default(10)
                                    ->required(),

                                RichEditor::make('pertanyaan')
                                    ->label('Pertanyaan')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'bulletList',
                                    ]),

                                FileUpload::make('gambar_url')
                                    ->label('Gambar Pendukung')
                                    ->image()
                                    ->directory('soal-images')
                                    ->maxSize(1024),

                                Repeater::make('opsi')
                                    ->relationship()
                                    ->label('Pilihan Jawaban')
                                    ->schema([
                                        TextInput::make('teks_opsi')
                                            ->label('Teks Opsi')
                                            ->placeholder('Isi pilihan jawaban')
                                            ->required(),
                                        Toggle::make('opsi_benar')
                                            ->label('Benar'),
                                    ])
                                    ->minItems(2)
                                    ->maxItems(6)
                                    ->defaultItems(4),
                            ])
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(
                                fn($state) =>
                                Str::limit(strip_tags($state['pertanyaan'] ?? 'Soal baru'), 50)
                            ),
                    ]),

                Step::make('soal_essay')
                    ->label('Soal Essay')
                    ->description('Tambah soal essay')
                    ->components([

                        Placeholder::make('info')
                            ->label('')
                            ->content('Soal essay akan dinilai secara manual'),

                        Repeater::make('soalEssay')
                            ->relationship()
                            ->label('')
                            ->schema([
                                TextInput::make('poin_maksimal')
                                    ->label('Poin Maksimal')
                                    ->numeric()
                                    ->default(20)
                                    ->required(),

                                RichEditor::make('pertanyaan')
                                    ->label('Pertanyaan')
                                    ->required(),

                                FileUpload::make('gambar_url')
                                    ->label('Gambar Pendukung')
                                    ->image()
                                    ->directory('essay-images'),

                                Textarea::make('jawaban_acuan')
                                    ->label('Jawaban Contoh')
                                    ->rows(2),

                                Textarea::make('rubrik_penilaian')
                                    ->label('Pedoman Penilaian')
                                    ->rows(2),
                            ])
                            ->collapsible()
                            ->cloneable(),
                    ]),

                Step::make('ringkasan')
                    ->label('Ringkasan')
                    ->description('Periksa data sebelum simpan')
                    ->components([

                        Section::make('Detail Kuis')
                            ->schema([
                                Placeholder::make('total')
                                    ->content(
                                        fn($get) =>
                                        "Jumlah Soal Pilihan Ganda: " . count($get('soalPilihanGanda') ?? []) . "\n" .
                                            "Jumlah Soal Essay: " . count($get('soalEssay') ?? []) . "\n" .
                                            "Total Soal: " . (count($get('soalPilihanGanda') ?? []) + count($get('soalEssay') ?? []))
                                    ),

                                Placeholder::make('waktu')
                                    ->content(
                                        fn($get) =>
                                        "Durasi: " . ($get('waktu_pengerjaan') ?? '30') . " menit"
                                    ),

                                Placeholder::make('pengaturan')
                                    ->content(
                                        fn($get) =>
                                        "Acak soal: " . ($get('acak_soal') ? 'Ya' : 'Tidak') . "\n" .
                                            "Acak opsi: " . ($get('acak_opsi') ? 'Ya' : 'Tidak') . "\n" .
                                            "Tampilkan nilai: " . ($get('tampilkan_hasil') ? 'Ya' : 'Tidak')
                                    ),
                            ]),
                    ]),
            ])
                ->columnSpanFull()
                ->persistStepInQueryString()
                ->skippable(),
        ]);
    }
}
