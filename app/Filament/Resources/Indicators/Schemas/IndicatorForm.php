<?php

namespace App\Filament\Resources\Indicators\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Filament\Support\UIPermissions;
use Filament\Forms\Components\RichEditor;

class IndicatorForm
{   

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Textarea::make('definition')
                    ->columnSpanFull(),
                RichEditor::make('source')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript','link'],
                        ['alignStart', 'alignCenter', 'alignEnd'],
                        ['bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ]),
                RichEditor::make('note')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript','link'],
                        ['alignStart', 'alignCenter', 'alignEnd'],
                        ['bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ]),
                TextArea::make('data_flag')
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->disabled(fn()=>!UIPermissions::canPublish())
                    ->required(),
            ]);
    }
}
