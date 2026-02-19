<?php

namespace App\Exports;

use App\Models\Ratio;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class NotesExport implements WithMultipleSheets
{
    protected $yearId;
    protected $classroomId;

    public function __construct($yearId, $classroomId)
    {
        $this->yearId      = $yearId;
        $this->classroomId = $classroomId;
    }

    public function sheets(): array
    {
        $ratios = Ratio::with('subject')
            ->where('classroom_id', $this->classroomId)
            ->where('year_id', $this->yearId)
            ->get();

        if ($ratios->isEmpty()) {
            // Retourne une feuille vide plutôt que de planter
            return [new MatiereNoteSheetExport($this->classroomId, $this->yearId, null)];
        }

        return $ratios->map(fn($ratio) =>
            new MatiereNoteSheetExport($this->classroomId, $this->yearId, $ratio)
        )->all();
    }
}
