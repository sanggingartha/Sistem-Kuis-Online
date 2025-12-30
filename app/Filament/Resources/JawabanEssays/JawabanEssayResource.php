<?php

namespace App\Filament\Resources\JawabanEssays;

use App\Models\JawabanEssay;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

use App\Filament\Resources\JawabanEssays\Pages\ListJawabanEssays;
use App\Filament\Resources\JawabanEssays\Pages\ViewJawabanEssay;
use App\Filament\Resources\JawabanEssays\Schemas\JawabanEssayForm;
use App\Filament\Resources\JawabanEssays\Tables\JawabanEssaysTable;

class JawabanEssayResource extends Resource
{
    protected static ?string $model = JawabanEssay::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Jawaban Essay';

    protected static ?string $pluralLabel = 'Jawaban Essay';

    protected static string|UnitEnum|null $navigationGroup = 'Penilaian';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'jawaban_siswa';

    public static function form(Schema $schema): Schema
    {
        return JawabanEssayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JawabanEssaysTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJawabanEssays::route('/'),
            'view'  => ViewJawabanEssay::route('/{record}'),
        ];
    }
}