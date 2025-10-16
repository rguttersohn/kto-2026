<?php

namespace App\Policies\Traits;

use App\Models\User;

trait HasAdminDeletePolicy{


    public function delete(User $user):bool{

        return $user->isAdmin();
    }

    public function deleteAny(User $user):bool{

    
        return $user->isAdmin();

    }

}