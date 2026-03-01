<?php

namespace App\Filament\Resources\Breakdowns;

use App\Filament\Resources\Breakdowns\Pages\CreateBreakdown;
use App\Filament\Resources\Breakdowns\Pages\EditBreakdown;
use App\Filament\Resources\Breakdowns\Pages\ListBreakdowns;
use App\Filament\Resources\Breakdowns\RelationManagers\SubBreakdownsRelationManager;
use App\Filament\Resources\Breakdowns\Schemas\BreakdownForm;
use App\Filament\Resources\Breakdowns\Tables\BreakdownsTable;
use App\Models\Breakdown;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BreakdownResource extends Resource
{
    protected static ?string $model = Breakdown::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Indicators';
    
    protected static ?string $modelLabel = "Breakdown Categories";

    public static function getEloquentQuery():Builder{
        
        return parent::getEloquentQuery()
            ->whereNull('parent_id');
    }

    public static function form(Schema $schema): Schema
    {
        return BreakdownForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BreakdownsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SubBreakdownsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBreakdowns::route('/'),
            'create' => CreateBreakdown::route('/create'),
            'edit' => EditBreakdown::route('/{record}/edit'),
        ];
    }
}
