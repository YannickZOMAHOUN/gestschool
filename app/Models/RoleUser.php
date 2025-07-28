<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_user';
   protected $fillable = ['user_id', 'role_id'];

    // Un rôle appartient à plusieurs utilisateurs
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
