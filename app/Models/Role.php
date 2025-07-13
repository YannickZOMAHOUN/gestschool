<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    // Un rôle appartient à plusieurs utilisateurs
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
