<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin - {{ $classroom->name }} - {{ $year->year }} - S{{ $semester }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
            color: #333;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1e3c72;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .header h3 {
            margin: 3px 0;
            font-size: 11px;
            font-weight: normal;
        }

        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .info-item {
            flex: 1;
            padding: 5px;
        }

        .info-item strong {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #1e3c72;
            color: white;
            font-weight: bold;
        }

        .subject-name {
            text-align: left;
            font-weight: bold;
        }

        .moyenne-high { color: #2e7d32; font-weight: bold; }
        .moyenne-medium { color: #f57f17; font-weight: bold; }
        .moyenne-low { color: #c62828; font-weight: bold; }

        .summary {
            margin-top: 10px;
        }

        .summary table {
            width: 100%;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .signature {
            width: 30%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 9px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @php
    if (!function_exists('appreciation')) {
        function appreciation($moy) {
            return match (true) {
                $moy === null => '',
                $moy < 5 => 'Insuffisant',
                $moy < 10 => 'Faible',
                $moy < 12 => 'Passable',
                $moy < 14 => 'Assez Bien',
                $moy < 16 => 'Bien',
                $moy < 18 => 'Très Bien',
                default => 'Excellent',
            };
        }
    }
@endphp

    @foreach ($students as $student)
        <div class="header">
            <h2>LYCÉE TECHNIQUE D'EXCELLENCE DE BANGUI</h2>
            <h3>Année scolaire : {{ $year->year }} | Semestre : {{ $semester }} | Classe : {{ $classroom->name }}</h3>
            <h2>BULLETIN DE NOTES</h2>
        </div>

        <div class="student-info">
            <div class="info-item">
                <strong>Élève :</strong> {{ $student->name }} {{ $student->surname }}
            </div>
            <div class="info-item">
                <strong>Né(e) le :</strong> {{ $student->birthday ? \Illuminate\Support\Carbon::parse($student->birthday)->format('d/m/Y') : '-' }}
            </div>
            <div class="info-item">
                <strong>Matricule :</strong> {{ $student->matricule ?? '-' }}
            </div>
        </div>

        <table>
           <thead>
    <tr>
        <th>Matière</th>
        <th>Coef</th>
        <th>Moy/20</th>
        <th>Moy Coef</th>
        <th>Rang</th>
        <th>Appréciation</th>
    </tr>
</thead>
<tbody>
@foreach ($subjects as $subject)
    @php
        $coef = $coefficients[$subject->id] ?? 1;
        $note = $notesData[$student->id][$semester][$subject->id] ?? null;
        $isDispensed = $note === 'Dispensé(e)';
        $rank = $subjectRanks[$subject->id][$student->id] ?? '-';
    @endphp
    <tr>
        <td class="subject-name">{{ $subject->name }}</td>
        <td>{{ $coef }}</td>
        @if ($isDispensed)
            <td colspan="4" style="font-style: italic;">Dispensé(e)</td>
        @else
            <td>{{ number_format($note, 2) }}</td>
            <td>{{ number_format($note * $coef, 2) }}</td>
            <td>{{ $rank }}</td>
            <td>{{ appreciation($note) }}</td>
        @endif
    </tr>
@endforeach
</tbody>


        </table>

        <div class="summary">
           @php
    $allMoys = $semester == 1 ? $moyennesS1 : $moyennesS2;
    $minMoy = collect($allMoys)->min();
    $maxMoy = collect($allMoys)->max();
@endphp

<table>
    <tr>
        <th>Moyenne</th>
        <td>{{ number_format($allMoys[$student->id] ?? 0, 2) }}</td>
        <th>Rang</th>
        <td>{{ ($semester == 1 ? $rangsS1 : $rangsS2)[$student->id] ?? '-' }}</td>
    </tr>
    <tr>
        <th>Plus faible moyenne</th>
        <td>{{ number_format($minMoy, 2) }}</td>
        <th>Plus forte moyenne</th>
        <td>{{ number_format($maxMoy, 2) }}</td>
    </tr>
    <tr>
        <th colspan="2">Date d'impression</th>
        <td colspan="2">{{ $dateImpression }}</td>
    </tr>
</table>

        </div>

        <div class="footer">
            <div class="signature">Le Professeur Principal</div>

            <div class="signature">Le Chef d'Établissement</div>

        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
