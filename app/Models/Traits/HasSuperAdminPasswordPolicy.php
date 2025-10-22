<?php

namespace App\Models\Traits;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Illuminate\Support\Facades\Auth;


trait HasAdminPasswordPolicy{

    #[Boot]
    protected static function hasAdminPasswordPolicy(){


        if(app()->runningInConsole()){
            
            if(!app()->runningUnitTests()){
                return;
            }

        }
        
        
        static::saving(function ($model) {
            
            if (!$model->exists) {
                return;
            }
            
            if ($model->isDirty('password') && !static::userCanUpdatePassword()) {
                $model->password = $model->getOriginal('password');
            }
        
        });

    }

     protected static function userCanUpdatePassword():bool {
            
            return Auth::user()->isAdmin();
        
    }

}