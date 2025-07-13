<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'must_change_password' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // Un utilisateur a plusieurs rôles (many to many)
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Si l'utilisateur est enseignant, ses classes/matières
    public function classSubjectTeachers()
    {
        return $this->hasMany(ClassSubjectTeacher::class);
    }

    // Méthode pratique pour vérifier un rôle
    public function hasRole($roleName)
    {
        return $this->roles->contains('name', $roleName);
    }
}
