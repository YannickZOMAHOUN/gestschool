<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de notes - {{ $classroom->name }} - {{ $year->year }} - Semestre {{ $semester }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Playfair+Display:wght@400;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #f9f9f9;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding: 15px 0;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .school-info {
            margin-bottom: 10px;
        }

        .school-info h2, .school-info h3 {
            margin: 5px 0;
            font-family: 'Playfair Display', serif;
        }

        .school-info h2 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .school-info h3 {
            font-size: 14px;
            font-weight: 400;
        }

        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }

        .info-box {
            border: 1px solid #ddd;
            padding: 10px 15px;
            flex: 1;
            border-radius: 6px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .info-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .info-box strong {
            color: #1e3c72;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 11px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #e0e0e0;
            padding: 8px 10px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #1e3c72;
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        tr:hover {
            background-color: #f1f5f9;
        }

        .summary table {
            width: 80%;
            margin: 25px auto;
            background-color: white;
        }

        .summary th {
            background-color: #2a5298;
        }

        .summary td {
            font-weight: 500;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .signature {
            text-align: center;
            width: 30%;
            border-top: 2px solid #1e3c72;
            margin-top: 50px;
            padding-top: 10px;
            font-style: italic;
            color: #555;
        }

        .page-break {
            page-break-after: always;
        }

        .interros-list {
            font-size: 10px;
            color: #666;
        }

        .subject-name {
            font-weight: 500;
            color: #1a365d;
        }

        .moyenne-high {
            color: #2e7d32;
            font-weight: 700;
        }

        .moyenne-medium {
            color: #f57f17;
            font-weight: 700;
        }

        .moyenne-low {
            color: #c62828;
            font-weight: 700;
        }

        .header-decoration {
            height: 4px;
            background: linear-gradient(90deg, #1e3c72, #2a5298, #7b4397, #dc2430);
            margin: 10px auto;
            width: 80%;
            border-radius: 2px;
        }

        .watermark {
            position: fixed;
            bottom: 10px;
            right: 10px;
            opacity: 0.1;
            font-size: 80px;
            font-weight: bold;
            color: #1e3c72;
            pointer-events: none;
            z-index: -1;
            transform: rotate(-15deg);
        }

        .student-photo {
            width: 80px;
            height: 80px;
            border: 2px solid #1e3c72;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 10px;
            display: block;
            background-color: #f0f4f8;
        }
    </style>
</head>
<body>
    <div class="watermark">LYTEB</div>

    @foreach ($students as $student)
        <div class="header">
            <div class="school-info">
                <h2>REPUBLIQUE DU [PAYS]</h2>
                <h3>Ministère de l'Éducation Nationale</h3>
                <h2>LYCÉE TECHNIQUE D'EXCELLENCE DE BANGUI</h2>
                <div class="header-decoration"></div>
            </div>
            <h2>BULLETIN DE NOTES</h2>
            <h3>Année scolaire : {{ $year->year }}</h3>
            <h3>Semestre : {{ $semester }}</h3>
            <h3>Classe : {{ $classroom->name }}</h3>
        </div>



        <div class="student-info">
            <div class="info-box">
                <strong>Nom et Prénom(s) :</strong><br>
                <span style="font-size:14px; font-weight:500;">{{ $student->name }} {{ $student->surname }}</span>
            </div>
            <div class="info-box">
                <strong>Date de naissance :</strong><br>
                <span>{{ $student->birthday ? \Illuminate\Support\Carbon::parse($student->birthday)->format('d/m/Y') : 'Non renseignée' }}</span>
            </div>
            <div class="info-box">
                <strong>Matricule :</strong><br>
                <span style="font-family: monospace; font-size:14px;">{{ $student->matricule ?? '-' }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width:25%">Matières</th>
                    <th rowspan="2" style="width:5%">Coef</th>
                    <th colspan="3">Notes</th>
                    <th rowspan="2" style="width:10%">Moyenne</th>
                </tr>
                <tr>
                    <th style="width:20%">Interros</th>
                    <th style="width:15%">Devoir 1</th>
                    <th style="width:15%">Devoir 2</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subjects as $subject)
                    @php
                        $notesForSubj = $notesData[$student->id][$semester][$subject->id] ?? null;
                        $isDispensed = is_string($notesForSubj) && $notesForSubj === 'Dispensé(e)';
                        $moyenneClass = '';

                        if (is_numeric($notesForSubj)) {
                            if ($notesForSubj >= 16) {
                                $moyenneClass = 'moyenne-high';
                            } elseif ($notesForSubj >= 10) {
                                $moyenneClass = 'moyenne-medium';
                            } else {
                                $moyenneClass = 'moyenne-low';
                            }
                        }
                    @endphp
                    <tr>
                        <td class="subject-name">{{ $subject->name }}</td>
                        <td>{{ $coefficients[$subject->id] ?? 1 }}</td>
                        @if ($isDispensed)
                            <td colspan="3" style="color:#9e9e9e; font-style:italic;">Dispensé(e)</td>
                            <td style="color:#9e9e9e; font-style:italic;">Disp.</td>
                        @else
                            <td class="interros-list">-</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="{{ $moyenneClass }}">
                                {{ is_numeric($notesForSubj) ? number_format($notesForSubj, 2) : '-' }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <table>
                <tr>
                    <th>Moyenne 1er Semestre</th>
                    <td style="font-size:14px; font-weight:bold;">
                        {{ isset($moyennesS1[$student->id]) ? number_format($moyennesS1[$student->id], 2) : '-' }}
                    </td>
                    <th>Rang 1er Semestre</th>
                    <td style="font-size:14px; font-weight:bold;">
                        {{ $rangsS1[$student->id] ?? '-' }}
                    </td>
                </tr>
                @if ($semester == 2)
                    <tr>
                        <th>Moyenne 2ème Semestre</th>
                        <td style="font-size:14px; font-weight:bold;">
                            {{ isset($moyennesS2[$student->id]) ? number_format($moyennesS2[$student->id], 2) : '-' }}
                        </td>
                        <th>Rang 2ème Semestre</th>
                        <td style="font-size:14px; font-weight:bold;">
                            {{ $rangsS2[$student->id] ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Moyenne Annuelle</th>
                        <td style="font-size:16px; font-weight:bold; color:#1e3c72;">
                            {{ isset($moyennesAnnuelles[$student->id]) ? number_format($moyennesAnnuelles[$student->id], 2) : '-' }}
                        </td>
                        <th>Rang Annuel</th>
                        <td style="font-size:16px; font-weight:bold; color:#1e3c72;">
                            {{ $rangsAnnuels[$student->id] ?? '-' }}
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="footer">
            <div class="signature">Le Professeur Principal<br><span style="font-size:10px;">Nom et signature</span></div>
            <div class="signature">Le Chef d'Établissement<br><span style="font-size:10px;">Nom, titre et signature</span></div>
            <div class="signature">Les Parents<br><span style="font-size:10px;">Nom et signature</span></div>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>
