<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Support\UIPermissions;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Select::make('role_id')
                    ->relationship('role', 'name')
                    ->disabled(fn($record)=>!UIPermissions::moreThanOneAdminExists() && UIPermissions::currentRecordIsAdmin($record))
                    ->helperText(fn($record)=>!UIPermissions::moreThanOneAdminExists() && UIPermissions::currentRecordIsAdmin($record) ? 'At least one Admin user is required.' : null)
                    ->label('Role'),
                TextInput::make('password')
                    ->password()

            ]);
    }
}
