<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Résultats Scolaires - {{ $student->name }} {{ $student->surname }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .student-info { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; font-size: 0.8em; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Résultats Scolaires</h2>
        <h3>{{ $year->year }} - Semestre {{ $request->semester }}</h3>
    </div>

    <div class="student-info">
        <p><strong>Élève:</strong> {{ $student->name }} {{ $student->surname }}</p>
        <p><strong>Matricule:</strong> {{ $student->matricule }}</p>
        <p><strong>Classe:</strong> {{ $classroom->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Matières</th>
                <th>Coefficient</th>
                <th>Moyenne</th>
                <th>Moyenne Pondérée</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
            <tr>
                <td>{{ $result['subject'] }}</td>
                <td>{{ $result['coefficient'] }}</td>
                <td>{{ number_format($result['subject_average'], 2) }}</td>
                <td>{{ number_format($result['weighted_average'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">MOYENNE GENERALE</th>
                <th>{{ number_format($generalAverage, 2) }}/20</th>
            </tr>
            @if($request->semester == 2)
            <tr>
                <td colspan="4" style="text-align: center; font-weight: bold;">
                    {{ $isPassed ? 'ADMIS(E)' : 'NON ADMIS(E)' }}
                </td>
            </tr>
            @endif
        </tfoot>
    </table>

    <div class="footer">
        <p>Généré le {{ now()->format('d/m/Y à H:i') }} - © {{ $year->year }} École Excellence</p>
    </div>
</body>
</html>
