<?php

namespace App\Filament\Resources\Imports\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ExportAction;
use App\Filament\Exports\FailedImportExporter;
use Filament\Actions\Exports\Enums\ExportFormat;

class FailedImportsRelationManager extends RelationManager
{
    protected static string $relationship = 'failedImports';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Failed Imports')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Failed Imports')
            ->columns([
                TextColumn::make('data')
                    ->state(function($record){

                        return $record->data;

                    }),
                TextColumn::make('validation_error')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(FailedImportExporter::class)
                    ->modifyQueryUsing(fn($query)=>$query->where('import_id', $this->ownerRecord->id))
                    ->formats([
                        ExportFormat::Csv,
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
