<?php

namespace App\Filament\Resources\AssetCategories\RelationManagers;

use App\Filament\Tables\Columns\KeyValuePairColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\KeyValue;
use App\Filament\Support\AssetSchemaValidation;
use Exception;


class AssetSchemaRelationManager extends RelationManager
{
    protected static string $relationship = 'assetSchema';

    public function form(Schema $schema): Schema
    {   

        return $schema
            ->components([
                KeyValue::make('schema')
                    ->rules([
                        fn(): \Closure => function (string $attribute, $values, \Closure $fail) {
                            
                            $validation = AssetSchemaValidation::validateSchema($values);

                            if($validation instanceof Exception){

                                $fail($validation->getMessage());
                            
                            }

                        },
                    ])
                    
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query)=>$query->with('assetCategory'))
            ->recordTitleAttribute('Schema')
            ->columns([
                TextColumn::make('assetCategory.name')
                    ->searchable(),
                KeyValuePairColumn::make('schema'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
