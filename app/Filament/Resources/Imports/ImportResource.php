<?php

namespace App\Filament\Resources\Imports;

use App\Filament\Resources\Imports\Pages\ListImports;
use App\Filament\Resources\Imports\Pages\ViewImport;
use App\Filament\Resources\Imports\Schemas\ImportForm;
use App\Filament\Resources\Imports\Schemas\ImportInfolist;
use App\Filament\Resources\Imports\Tables\ImportsTable;
use App\Models\Import;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ImportResource extends Resource
{
    protected static ?string $model = Import::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowUpOnSquareStack;

    protected static ?int $navigationSort = 100;

    public static function form(Schema $schema): Schema
    {
        return ImportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ImportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListImports::route('/'),
            'view' => ViewImport::route('/{record}'),
        ];
    }
}
