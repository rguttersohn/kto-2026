<?php

namespace App\Filament\Resources\Imports\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ImportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('file_name'),
                TextEntry::make('importer'),
                TextEntry::make('processed_rows')
                    ->numeric(),
                TextEntry::make('total_rows')
                    ->numeric(),
                TextEntry::make('successful_rows')
                    ->numeric(),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
