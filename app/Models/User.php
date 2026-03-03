<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'surname',
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
        'email_verified_at'    => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────────────

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /** Affectations matière/classe de cet enseignant */
    public function classSubjectTeachers()
    {
        return $this->hasMany(ClassSubjectTeacher::class);
    }

    /** Classes dont cet enseignant est PP */
    public function principalClasses()
    {
        return $this->hasMany(PrincipalClassTeacher::class);
    }

    // ── Helpers rôles ────────────────────────────────────────────────────

    public function hasRole($roleName): bool
    {
        return $this->roles->contains('name', $roleName);
    }

    public function assignRole($roleName): void
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function isEnseignant(): bool
    {
        return $this->hasRole('Enseignant');
    }

    public function isCenseur(): bool
    {
        return $this->hasRole('Censeur');
    }

    // ── Helpers PP ───────────────────────────────────────────────────────

    /**
     * Vérifie si l'enseignant est PP d'une classe donnée pour une année donnée.
     */
    public function isPrincipalOf(int $classroomId, int $yearId): bool
    {
        return $this->principalClasses()
            ->where('classroom_id', $classroomId)
            ->where('year_id', $yearId)
            ->exists();
    }

    /**
     * Retourne les IDs des classes où l'utilisateur enseigne pour une année.
     */
    public function teachingClassroomIds(int $yearId): array
    {
        return $this->classSubjectTeachers()
            ->where('year_id', $yearId)
            ->pluck('classroom_id')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Retourne les IDs des matières que l'utilisateur enseigne dans une classe.
     */
    public function subjectIdsInClassroom(int $classroomId, int $yearId): array
    {
        return $this->classSubjectTeachers()
            ->where('classroom_id', $classroomId)
            ->where('year_id', $yearId)
            ->pluck('subject_id')
            ->toArray();
    }
}
