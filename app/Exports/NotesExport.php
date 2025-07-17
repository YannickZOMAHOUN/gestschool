<?php

namespace App\Exports;

use App\Models\Ratio;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class NotesExport implements WithMultipleSheets
{
    protected $classroomId;
    protected $yearId;

    public function __construct($yearId, $classroomId)
    {
        $this->yearId = $yearId;
        $this->classroomId = $classroomId;
    }

    public function sheets(): array
    {
        $ratios = Ratio::with('subject')
            ->where('classroom_id', $this->classroomId)
            ->where('year_id', $this->yearId)
            ->get();

        if ($ratios->isEmpty()) {
            return [new \App\Exports\MatiereNoteSheetExport($this->classroomId, $this->yearId, null)];
        }

        $sheets = [];

        foreach ($ratios as $ratio) {
            $sheets[] = new \App\Exports\MatiereNoteSheetExport($this->classroomId, $this->yearId, $ratio);
        }

        return $sheets;
    }
}
