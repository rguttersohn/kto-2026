<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
   
    public function view(User $user, User $model):bool {

        return $user->isAdmin();
    }  
      
    public function viewAny(User $user):bool {

        return $user->isAdmin();

   }

   public function delete(User $user, User $user_target){
        
        if($user->isAdmin() === false){
               
               return false;

        }

        if($user_target->isAdmin()){

               $admin_count = User::where('role_id','>==', '3')->count();

               if($admin_count <= 1){

                      return false;

               }
        }

        return true;
   }

   public function deleteAny(User $user):bool{

        return $user->isAdmin();
   }

   public function update(User $user):bool{

        return $user->isAdmin();
   }

}
