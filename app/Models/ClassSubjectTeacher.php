<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSubjectTeacher extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'subject_id',
        'year_id',
        'is_principal'
    ];

    protected $casts = [
        'is_principal' => 'boolean'
    ];

    /**
     * Relation avec l'utilisateur (enseignant)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la classe
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(PromotionClassroom::class, 'classroom_id');
    }

    /**
     * Relation avec la matière
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relation avec l'année scolaire
     */
    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }

    /**
     * Scope pour les professeurs principaux
     */
    public function scopePrincipal($query)
    {
        return $query->where('is_principal', true);
    }

    /**
     * Scope pour une année spécifique
     */
    public function scopeForYear($query, $yearId)
    {
        return $query->where('year_id', $yearId);
    }

    /**
     * Scope pour une classe spécifique
     */
    public function scopeForClassroom($query, $classroomId)
    {
        return $query->where('classroom_id', $classroomId);
    }

    /**
     * Vérifie si l'enseignant est déjà assigné à cette combinaison
     */
    public static function isAlreadyAssigned($userId, $classroomId, $subjectId, $yearId): bool
    {
        return self::where('user_id', $userId)
            ->where('classroom_id', $classroomId)
            ->where('subject_id', $subjectId)
            ->where('year_id', $yearId)
            ->exists();
    }

    /**
     * Récupère le professeur principal d'une classe
     */
    public static function getPrincipalForClassroom($classroomId)
    {
        return self::where('classroom_id', $classroomId)
            ->where('is_principal', true)
            ->first();
    }
}
