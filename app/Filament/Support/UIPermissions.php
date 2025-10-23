<?php

namespace App\Filament\Support;

use Illuminate\Support\Facades\Auth;
use App\Models\User;


class UIPermissions {
    
    public static function canPublish(){

        return Auth::user()->isAdmin();
    }

    public static function moreThanOneAdminExists(){
        $admin_count = User::where('role_id', 3)->count();

        return $admin_count > 1;
    }

    public static function currentRecordIsAdmin(User | null $record):bool {
        
        if(!$record){
            
            return false;

        }

        return $record->role_id === 3;
    }
}