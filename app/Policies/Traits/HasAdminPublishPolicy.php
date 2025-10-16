<?php


namespace App\Policies\Traits;
use Illuminate\Support\Facades\Auth;


trait HasAdminPublishPolicy {

    protected function setIsPublishedAttribute($value)
    {
        
        if ($value && Auth::check() && !Auth::user()->isAdmin()) {
            
            $this->attributes['is_published'] = false;
        
        } else {

            $this->attributes['is_published'] = $value;
        }
    }
}