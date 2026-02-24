<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche à Collationner — Semestre {{ $semester }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 0; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p  { margin: 4px 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 4px 5px; text-align: center; }
        th { background-color: #d9e1f2; font-weight: bold; font-size: 10px; }
        td.student-name { text-align: left; white-space: nowrap; }
        .footer { margin-top: 20px; font-size: 10px; }
        .signature { display: inline-block; width: 200px; border-top: 1px solid #000; margin-top: 50px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ÉTABLISSEMENT SCOLAIRE</h2>
        <p><strong>Fiche à Collationner — SEMESTRE {{ $semester }}</strong></p>
        <p>Année scolaire : {{ $year->year }} &nbsp;|&nbsp; Classe : {{ $classroom->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="min-width:120px;">Élèves</th>

                @foreach($subjects as $subject)
                    <th colspan="2">{{ $subject->name }}</th>
                @endforeach

                @if($semester == 1)
                    <th rowspan="2">Moy. Gén.</th>
                    <th rowspan="2">Rang</th>
                @else
                    <th rowspan="2">Moy. S1</th>
                    <th rowspan="2">Moy. S2</th>
                    <th rowspan="2">Moy. Ann.</th>
                    <th rowspan="2">Rang S2</th>
                    <th rowspan="2">Rang Ann.</th>
                @endif
            </tr>
            <tr>
                @foreach($subjects as $subject)
                    <th>Moy.</th>
                    <th>Coef.</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td class="student-name">{{ $student->name }} {{ $student->surname }}</td>

                    @foreach($subjects as $subject)
                        @php
                            $note    = $notesData[$student->id][$semester][$subject->id] ?? null;
                            $coef    = $coefficients[$subject->id] ?? '-';
                            $display = $note === 'Dispensé(e)' ? 'Disp.' : ($note !== null ? number_format($note, 2) : '-');
                        @endphp
                        <td>{{ $display }}</td>
                        <td>{{ $coef }}</td>
                    @endforeach

                    @if($semester == 1)
                        <td><strong>{{ isset($moyennesS1[$student->id]) ? number_format($moyennesS1[$student->id], 2) : '-' }}</strong></td>
                        <td>{{ $rangsS1[$student->id] ?? '-' }}</td>
                    @else
                        <td>{{ isset($moyennesS1[$student->id]) ? number_format($moyennesS1[$student->id], 2) : '-' }}</td>
                        <td><strong>{{ isset($moyennesS2[$student->id]) ? number_format($moyennesS2[$student->id], 2) : '-' }}</strong></td>
                        <td><strong>{{ isset($moyennesAnnuelles[$student->id]) ? number_format($moyennesAnnuelles[$student->id], 2) : '-' }}</strong></td>
                        <td>{{ $rangsS2[$student->id] ?? '-' }}</td>
                        <td>{{ $rangsAnnuels[$student->id] ?? '-' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>
            Moy. = Moyenne pondérée par coefficient
            @if($semester == 2)
                &nbsp;|&nbsp; Moy. Ann. = (S2 × 2 + S1) / 3
            @endif
            &nbsp;|&nbsp; Imprimé le {{ $dateImpression }}
        </p>
        <div style="text-align: right; margin-top: 10px;">
            <span class="signature">Le Directeur</span>
        </div>
    </div>
</body>
</html>
