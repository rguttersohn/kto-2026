<?php


namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Attributes\Boot;


trait HasAdminPublishPolicy {

   #[Boot]
    protected static function HasAdminPublishPolicy()
    {   
        if(app()->runningInConsole()){
            if(!app()->runningUnitTests()){
                return;
            }
        } 
        
        static::saving(function ($model) {
            // Skip the check if this is a new model being created
            if (!$model->exists) {
                return;
            }
            
            if ($model->isDirty('is_published') && !static::userCanPublish()) {
                $model->is_published = $model->getOriginal('is_published');
            }
        });
    }
    
    protected static function userCanPublish(): bool {
            return Auth::user()->isAdmin();
    }

    
}