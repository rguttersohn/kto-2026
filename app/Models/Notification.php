<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    
    protected $connection = 'supabase';
    
    protected $table = 'app.notifications';

    protected $casts = [
        'data' => 'array'
    ];

}
