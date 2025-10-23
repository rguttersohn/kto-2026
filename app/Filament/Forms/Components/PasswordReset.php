<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\Hash;

class PasswordReset extends Field
{
    protected string $view = 'filament.forms.components.password-reset';

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function ($component, $state) {
           
            $component->state(null); 

        });

        $this->dehydrated(fn ($state) => filled($state));

        $this->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null);
        
    }

}
