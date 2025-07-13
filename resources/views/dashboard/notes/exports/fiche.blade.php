<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulletin de notes - Semestre {{ $semester }}</title>
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
        <p>BULLETIN DE NOTES - SEMESTRE {{ $semester }}</p>
        <p>Année scolaire: {{ $year->year }} | Classe: {{ $classroom->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 15%;">Élèves</th>
                @foreach($subjects as $subject)
                    <th colspan="2">{{ $subject->name }}</th>
                @endforeach
                <th rowspan="2">Moy. Gén.</th>
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
                        <td>{{ $notesData[$student->id][$subject->id] ?? '-' }}</td>
                        <td>{{ $coefficients[$subject->id] }}</td>
                    @endforeach
                    <td><strong>{{ $moyennesGenerales[$student->id] }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Moy. Gén. = Moyenne Générale (pondérée par les coefficients)</p>
        <div style="text-align: right;">
            <div class="signature">Le Directeur</div>
        </div>
    </div>
</body>
</html>
