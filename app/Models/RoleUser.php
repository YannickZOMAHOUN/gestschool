<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
   protected $fillable = ['user_id', 'role_id'];

    // Un rôle appartient à plusieurs utilisateurs
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
