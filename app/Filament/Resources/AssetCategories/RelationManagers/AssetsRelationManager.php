<?php

namespace App\Filament\Resources\AssetCategories\RelationManagers;

use App\Filament\Imports\AssetImporter;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Toggle;
use App\Filament\Support\UIPermissions;
use Filament\Forms\Components\KeyValue;


class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    // public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    // {
        
    //     return $ownerRecord->parent_id === 0 && $ownerRecord->parent_id;
    // }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                KeyValue::make('data')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('geometry')
                    ->required(),
                Toggle::make('is_published')
                    ->required()
                    ->columnSpanFull()
                    ->disabled(fn()=>!UIPermissions::canPublish())
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('data')
                    ->label("Properties")
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->expandableLimitedList(),
                TextColumn::make('assetCategory.name'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_published')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
                ImportAction::make()
                    ->importer(AssetImporter::class)
                    ->options([
                        'asset_category_id' => $this->getOwnerRecord()->getKey()
                    ])
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
