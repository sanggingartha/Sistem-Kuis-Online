<?php

namespace App\Filament\Resources\Kuis;

use App\Models\Kuis;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

use App\Filament\Resources\Kuis\Pages\ListKuis;
use App\Filament\Resources\Kuis\Pages\CreateKuis;
use App\Filament\Resources\Kuis\Pages\EditKuis;
use App\Filament\Resources\Kuis\Pages\ViewKuis;

use App\Filament\Resources\Kuis\Schemas\KuisForm;
use App\Filament\Resources\Kuis\Schemas\KuisInfolist;
use App\Filament\Resources\Kuis\Tables\KuisTable;
use UnitEnum;

class KuisResource extends Resource
{
    protected static ?string $model = Kuis::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Kuis';

    protected static ?string $pluralLabel = 'Kuis';

    protected static string|UnitEnum|null $navigationGroup = 'Kuis Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama_kuis';

    public static function form(Schema $schema): Schema
    {
        return KuisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KuisTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KuisInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListKuis::route('/'),
            'create' => CreateKuis::route('/create'),
            'edit'   => EditKuis::route('/{record}/edit'),
            'view'   => ViewKuis::route('/{record}'),
        ];
    }
}
