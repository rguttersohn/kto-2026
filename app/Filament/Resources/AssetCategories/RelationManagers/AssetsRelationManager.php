<?php

namespace App\Filament\Resources\AssetCategories\RelationManagers;

use App\Filament\Imports\AssetImporter;
use App\Filament\Support\AssetSchemaValidation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Toggle;
use App\Filament\Support\UIPermissions;
use Filament\Forms\Components\KeyValue;
use App\Filament\Tables\Columns\KeyValuePairColumn;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Grouping\Group;
use App\Models\AssetSchema;
use Exception;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                KeyValue::make('data')
                    ->required()
                    ->columnSpanFull()
                    ->rule(fn(): \Closure => function (string $attribute, $values, \Closure $fail) {
                            $asset_category = $this->ownerRecord;

                            $schema = AssetSchema::where('asset_category_id', $asset_category->id)->first();

                                $key_value_pairs = array_column($values, 'value', 'key');

                                $validation = AssetSchemaValidation::validateData($schema, $key_value_pairs);

                                if($validation instanceof Exception){

                                    $fail($validation->getMessage());

                                }

                        },),
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
                KeyValuePairColumn::make('data'),
                TextColumn::make('assetCategory.name')
                    ->label('Category'),
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
                ImportAction::make()
                    ->importer(AssetImporter::class)
                    ->options([
                        'asset_category_id' => $this->getOwnerRecord()->getKey()
                    ])
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('set_published')
                        ->label('Publish')
                        ->action(fn ($records) => $records->each->update(['is_published' => true]))
                        ->requiresConfirmation()
                        ->color('success')
                        ->visible(fn()=>UIPermissions::canPublish()),
                    BulkAction::make('set_unpublished')
                        ->label('Publish')
                        ->action(fn ($records) => $records->each->update(['is_published' => false]))
                        ->requiresConfirmation()
                        ->color('success')
                        ->visible(fn()=>UIPermissions::canPublish()),
                ])
                ->visible(fn()=>UIPermissions::canPublish()),
            ])
            ->groups([
                Group::make('updated_at')
                    ->label('Updated Date'),
                Group::make('created_at')
                    ->label('Created Date'),
                Group::make('import_id')
                    ->label('Import Group')
                    ->getTitleFromRecordUsing(fn ($record): string => "{$record->import->file_name}_{$record->import->created_at}"),
            ]);
    }
}
