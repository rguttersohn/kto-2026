<?php

namespace App\Filament\Support;

use Illuminate\Support\Facades\Auth;


class UIPermissions {
    
    public static function canPublish(){

        return Auth::user()->isAdmin();
    }
}