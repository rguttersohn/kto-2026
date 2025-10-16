<?php


namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Attributes\Boot;


trait HasAdminPublishPolicy {

    #[Boot]
    protected static function HasAdminPublishPolicy()
    {

        static::saving(function ($model) {
            
            if ($model->isDirty('is_published') && !static::userCanPublish()) {
                $model->is_published = $model->getOriginal('is_published');
            }
        });
    }
    


    protected static function userCanPublish(): bool {
            return Auth::user()->isAdmin();
    }
    
}