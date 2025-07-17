<?php

namespace App\Exports;

use App\Models\Note;
use App\Models\Recording;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MatiereNoteSheetExport implements FromCollection, WithTitle, WithHeadings, WithColumnFormatting
{
    protected $classroomId;
    protected $yearId;
    protected $ratio;

    public function __construct($classroomId, $yearId, $ratio)
    {
        $this->classroomId = $classroomId;
        $this->yearId = $yearId;
        $this->ratio = $ratio;
    }

    public function title(): string
    {
        return $this->ratio ? $this->ratio->subject->name : 'Aucune matière';
    }

    public function headings(): array
    {
        return ['Matricule', 'Nom', 'Prénom', 'Moyenne Interros', 'Devoir 1', 'Devoir 2'];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Colonne A = Matricule
        ];
    }

    public function collection()
    {
        if (!$this->ratio) {
            return collect([
                ['Aucune matière trouvée pour cette classe ou année.']
            ]);
        }

        $recordings = Recording::with('student')
            ->where('classroom_id', $this->classroomId)
            ->where('year_id', $this->yearId)
            ->get();

        return $recordings->map(function ($rec) {
            $note = Note::where('recording_id', $rec->id)
                        ->where('ratio_id', $this->ratio->id)
                        ->latest('semester')
                        ->first();

            $interros = $note && $note->interros ? collect($note->interros) : collect([]);
            $moyenneInterro = $interros->count() ? round($interros->avg(), 2) : null;

            return [
                (string) ($rec->student->matricule ?? ''), // Matricule converti en string
                $rec->student->name,
                $rec->student->surname,
                $moyenneInterro,
                $note->devoir1 ?? null,
                $note->devoir2 ?? null,
            ];
        });
    }
}
