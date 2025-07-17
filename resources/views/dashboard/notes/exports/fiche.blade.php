<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche à Collationner - Semestre {{ $semester }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .student-name { text-align: left; }
        .footer { margin-top: 20px; font-size: 11px; }
        .signature { display: inline-block; width: 200px; border-top: 1px solid #000; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ÉTABLISSEMENT SCOLAIRE</h2>
        <p>Fiche à Collationner - SEMESTRE {{ $semester }}</p>
        <p>Année scolaire: {{ $year->year }} | Classe: {{ $classroom->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Élèves</th>
                @foreach($subjects as $subject)
                    <th colspan="2">{{ $subject->name }}</th>
                @endforeach
                @if ($semester == 1)
                    <th rowspan="2">Moy. Gén.</th>
                    <th rowspan="2">Rang</th>
                @else
                    <th rowspan="2">Moy. S1</th>
                    <th rowspan="2">Moy. S2</th>
                    <th rowspan="2">Moy. Annuelle</th>
                    <th rowspan="2">Rang S2</th>
                    <th rowspan="2">Rang Annuel</th>
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
                    <td class="student-name">{{ $student->surname }} {{ $student->name }}</td>

                    @foreach($subjects as $subject)
                        @php
                            $note = $notesData[$student->id][$semester][$subject->id] ?? '-';
                            $coef = $coefficients[$subject->id] ?? '-';
                        @endphp
                        <td>{{ $note }}</td>
                        <td>{{ $coef }}</td>
                    @endforeach

                    @if ($semester == 1)
                        <td><strong>{{ $moyennesS1[$student->id] ?? '-' }}</strong></td>
                        <td>{{ $rangsS1[$student->id] ?? '-' }}</td>
                    @else
                        <td>{{ $moyennesS1[$student->id] ?? '-' }}</td>
                        <td><strong>{{ $moyennesS2[$student->id] ?? '-' }}</strong></td>
                        <td><strong>{{ $moyennesAnnuelles[$student->id] ?? '-' }}</strong></td>
                        <td>{{ $rangsS2[$student->id] ?? '-' }}</td>
                        <td>{{ $rangsAnnuels[$student->id] ?? '-' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>
            Moy. = Moyenne pondérée <br>
            @if($semester == 2)
                Moy. Annuelle = (Moy. S1 + Moy. S2) / 2
            @endif
        </p>
        <div style="text-align: right;">
            <div class="signature">Le Directeur</div>
        </div>
    </div>
</body>
</html>
