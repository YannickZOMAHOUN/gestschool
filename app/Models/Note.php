<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester',
        'interros',
        'devoir1',
        'devoir2',
        'ratio_id',
        'subject_id',
        'recording_id',
        'is_locked',
    ];

    protected $casts = [
        'interros'   => 'array',
        'is_locked'  => 'boolean',
        'devoir1'    => 'float',
        'devoir2'    => 'float',
        'semester'   => 'integer',
    ];

    public function recording()
    {
        return $this->belongsTo(Recording::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function ratio()
    {
        return $this->belongsTo(Ratio::class);
    }

    // Scopes utiles
    public function scopeForContext($q, int $yearId, int $classroomId)
    {
        return $q->whereHas('recording', fn($r) => $r->where('year_id', $yearId)->where('classroom_id', $classroomId));
    }

    public function scopeSemester($q, int $semester)
    {
        return $q->where('semester', $semester);
    }
}
