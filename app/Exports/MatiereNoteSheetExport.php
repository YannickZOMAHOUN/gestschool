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
        $this->yearId      = $yearId;
        $this->ratio       = $ratio;
    }

    public function title(): string
    {
        // Excel limite les noms d'onglets à 31 caractères
        $name = $this->ratio ? $this->ratio->subject->name : 'Aucune matière';
        return mb_substr($name, 0, 31);
    }

    public function headings(): array
    {
        return ['Matricule', 'Nom', 'Prénom', 'Moy. Interros', 'Devoir 1', 'Devoir 2', 'Moy./20'];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Matricule en texte pour éviter la troncature
        ];
    }

    public function collection()
    {
        if (!$this->ratio) {
            return collect([['Aucune matière trouvée pour cette classe ou année.']]);
        }

        $recordings = Recording::with('student')
            ->where('classroom_id', $this->classroomId)
            ->where('year_id', $this->yearId)
            ->get()
            ->sortBy('student.name');

        return $recordings->map(function ($rec) {
            // On prend la note du semestre le plus récent pour cette matière
            $note = Note::where('recording_id', $rec->id)
                ->where('ratio_id', $this->ratio->id)
                ->latest('semester')
                ->first();

            // Sécurité : interros peut être null si aucune note
            $interrosRaw    = $note?->interros ?? null;
            $interrosArr    = is_array($interrosRaw)
                ? $interrosRaw
                : (json_decode($interrosRaw ?? '[]', true) ?? []);
            $interrosCol    = collect(array_filter($interrosArr, fn($v) => $v !== null));
            $moyenneInterro = $interrosCol->count() ? round($interrosCol->avg(), 2) : null;

            // Calcul moy/20 (même formule que le contrôleur)
            $composantes = [];
            if ($moyenneInterro !== null) $composantes[] = $moyenneInterro;
            if ($note?->devoir1 !== null) $composantes[] = floatval($note->devoir1);
            if ($note?->devoir2 !== null) $composantes[] = floatval($note->devoir2);
            $moy20 = count($composantes) > 0
                ? round(array_sum($composantes) / count($composantes), 2)
                : null;

            return [
                (string) ($rec->student->matricule ?? ''),
                $rec->student->name    ?? '',
                $rec->student->surname ?? '',
                $moyenneInterro,
                $note?->devoir1 ?? null,
                $note?->devoir2 ?? null,
                $moy20,
            ];
        });
    }
}
