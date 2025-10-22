<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use App\Policies\UserPolicy;
use App\Models\Traits\HasSuperAdminPasswordPolicy;

#[UsePolicy(UserPolicy::class)]

class User extends Authenticatable implements FilamentUser
{
    
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasSuperAdminPasswordPolicy;

    protected $table = 'users.users';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
                    ->orderBy('created_at', 'desc');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role_id !== 1;
    }

    public function isAdmin():bool{

        return  Auth::check() && $this->role_id > 2;
    
    }

    public function isSuperAdmin():bool{

        return  Auth::check() && $this->role_id === 4;
    
    }

    public function role(){

        return $this->belongsTo(Role::class);

    }

    
}
