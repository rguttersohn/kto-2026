<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
   public function viewAny(User $user):bool {

        return $user->isAdmin();

   }

   public function delete(User $user){
        
        return $user->isAdmin();
   }

   public function deleteAny(User $user):bool{

        return $user->isAdmin();
   }

}
