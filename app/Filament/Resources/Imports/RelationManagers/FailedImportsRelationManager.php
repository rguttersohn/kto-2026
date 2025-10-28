<?php

namespace App\Filament\Resources\Imports\RelationManagers;

use Filament\Actions\BulkActionGroup;
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
use Filament\Actions\Action;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Models\FailedImport;
use League\Csv\Writer;

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
                Action::make('export_failed_imports')
                    ->label('Export Failed Imports')
                    ->action(function () {
                        
                        $import_id = $this->ownerRecord->id;

                        $failed_imports = FailedImport::where('import_id', $import_id)->get();

                        $csv = Writer::createFromString();

                        $first_record = $failed_imports->first();

                        $data = array_keys($first_record->data);

                        $headers = [
                            ...$data,
                            'validation_errors'
                        ];

                        $csv->insertOne($headers);

                        $failed_imports->each(function($import)use(&$csv){

                                $data = $import->data;
                                
                                $csv->insertOne([
                                    ...$data,
                                    $import->validation_error
                                ]);
                        });

                        return response()->streamDownload(function()use($csv){

                            echo $csv;
                        },  'failed-imports-' . $import_id . '.csv', 
                        [
                            'Content-Type' => 'text/csv',
                        ]
                        );

                    }),

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
