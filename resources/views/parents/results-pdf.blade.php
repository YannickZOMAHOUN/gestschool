<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bulletin — {{ $student->name }} {{ $student->surname }}</title>
    <style>
        /* ─── DomPDF : DejaVu Sans obligatoire ─── */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            color: #1C2B40;
            background: #fff;
        }

        /* ─── En-tête officiel ─────────────────── */
        .header-bar {
            background: #0B1F3A;
            color: #fff;
            padding: 0;
            margin-bottom: 18px;
        }
        .header-inner {
            padding: 14px 20px;
            border-bottom: 3px solid #C9952A;
        }
        .school-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #E8C06A;
        }
        .header-sub {
            font-size: 8pt;
            color: rgba(255,255,255,.65);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            background: #C9952A;
            color: #0B1F3A;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 0;
        }

        /* ─── Fiche élève ──────────────────────── */
        .student-box {
            border: 1px solid #C9952A;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 14px;
            background: #FAFAF7;
        }
        .student-box table { width: 100%; border-collapse: collapse; }
        .student-box td {
            padding: 3px 6px;
            font-size: 9.5pt;
        }
        .student-box .lbl {
            color: #6B7C93;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: .5px;
            width: 22%;
        }
        .student-box .val { font-weight: bold; color: #0B1F3A; }

        /* ─── Tableau des notes ─────────────────── */
        .notes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9pt;
        }
        .notes-table thead tr {
            background: #0B1F3A;
            color: #E8C06A;
        }
        .notes-table thead th {
            padding: 7px 8px;
            text-align: left;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: .5px;
            border: none;
        }
        .notes-table thead th.center { text-align: center; }

        .notes-table tbody tr:nth-child(even) { background: #F7F3EC; }
        .notes-table tbody tr:nth-child(odd)  { background: #FFFFFF; }

        .notes-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #EDE8DF;
            vertical-align: middle;
        }
        .notes-table td.center { text-align: center; }

        .subject-name { font-weight: bold; }

        .interros-cell { font-size: 8pt; color: #444; }

        /* Pill de moyenne /20 */
        .avg-good { color: #2D9E6B; font-weight: bold; }
        .avg-fail { color: #C84040; font-weight: bold; }
        .avg-null { color: #999; }

        /* ─── Pied de tableau — Moyenne générale ─ */
        .tfoot-row td {
            background: #0B1F3A;
            color: rgba(255,255,255,.75);
            padding: 8px;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: .5px;
            border: none;
        }
        .tfoot-row .avg-value {
            font-size: 14pt;
            font-weight: bold;
            color: #E8C06A;
            text-align: center;
        }

        /* ─── Verdict ──────────────────────────── */
        .verdict {
            margin: 10px 0;
            padding: 10px 14px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .verdict-admis  { background: #E8F5EE; color: #2D9E6B; border: 1px solid #2D9E6B; }
        .verdict-refuse { background: #FDEAEA; color: #C84040; border: 1px solid #C84040; }

        /* ─── Note de bas de page ───────────────── */
        .note-info {
            font-size: 7.5pt;
            color: #6B7C93;
            font-style: italic;
            margin-top: 6px;
        }

        /* ─── Footer du document ────────────────── */
        .doc-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #F7F3EC;
            border-top: 2px solid #C9952A;
            padding: 5px 20px;
            font-size: 7.5pt;
            color: #6B7C93;
            text-align: center;
        }

        .page-body { margin-bottom: 40px; } /* espace pour footer fixe */
    </style>
</head>
<body>

    <div class="page-body">

        {{-- En-tête --}}
        <div class="header-bar">
            <div class="header-inner">
                <div class="school-name">Lycée Technique de Bohicon</div>
                <div class="header-sub">République du Bénin — Excellence &amp; Rigueur</div>
            </div>
            <div class="doc-title">Bulletin de Notes — Semestre {{ $request->semester }}</div>
        </div>

        {{-- Fiche élève --}}
        <div class="student-box">
            <table>
                <tr>
                    <td class="lbl">Élève</td>
                    <td class="val">{{ strtoupper($student->name) }} {{ $student->surname }}</td>
                    <td class="lbl">Matricule</td>
                    <td class="val">{{ $student->matricule }}</td>
                </tr>
                <tr>
                    <td class="lbl">Classe</td>
                    <td class="val">{{ $classroom->name ?? '—' }}</td>
                    <td class="lbl">Année</td>
                    <td class="val">{{ $year->year ?? '—' }}</td>
                </tr>
            </table>
        </div>

        {{-- Tableau des notes --}}
        <table class="notes-table">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th class="center">Coef.</th>
                    <th>Interros</th>
                    <th class="center">Moy. Inter.</th>
                    <th class="center">Devoir 1</th>
                    <th class="center">Devoir 2</th>
                    <th class="center">Moy. /20</th>
                    <th class="center">Moy. Pond.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                <tr>
                    <td><span class="subject-name">{{ $result['subject'] }}</span></td>

                    <td class="center">{{ $result['coefficient'] }}</td>

                    <td class="interros-cell">
                        @if(count($result['interros']) > 0)
                            {{ implode(' / ', array_map(fn($n) => number_format($n, 2), $result['interros'])) }}
                        @else
                            —
                        @endif
                    </td>

                    <td class="center">
                        @if($result['moy_interros'] !== null)
                            {{ number_format($result['moy_interros'], 2) }}
                        @else —
                        @endif
                    </td>

                    <td class="center">
                        @if($result['devoir1'] !== null)
                            {{ number_format($result['devoir1'], 2) }}
                        @else —
                        @endif
                    </td>

                    <td class="center">
                        @if($result['devoir2'] !== null)
                            {{ number_format($result['devoir2'], 2) }}
                        @else —
                        @endif
                    </td>

                    <td class="center">
                        @if($result['subject_average'] !== null)
                            <span class="{{ $result['subject_average'] >= 10 ? 'avg-good' : 'avg-fail' }}">
                                {{ number_format($result['subject_average'], 2) }}
                            </span>
                        @else
                            <span class="avg-null">—</span>
                        @endif
                    </td>

                    <td class="center">
                        @if($result['weighted_average'] !== null)
                            {{ number_format($result['weighted_average'], 2) }}
                        @else —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="tfoot-row">
                    <td colspan="6">Moyenne Générale Pondérée</td>
                    <td colspan="2" class="avg-value">
                        @if($generalAverage !== null)
                            {{ number_format($generalAverage, 2) }} / 20
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Verdict --}}
        @if($request->semester == 2 && $isPassed !== null)
            <div class="verdict {{ $isPassed ? 'verdict-admis' : 'verdict-refuse' }}">
                @if($isPassed)
                    ✓ ADMIS(E) EN CLASSE SUPÉRIEURE — Moyenne : {{ number_format($generalAverage, 2) }}/20
                @else
                    ✗ NON ADMIS(E) — REDOUBLEMENT — Moyenne : {{ number_format($generalAverage, 2) }}/20
                @endif
            </div>
        @endif

        {{-- Note méthodologique --}}
        <p class="note-info">
            * Les notes manquantes (—) sont exclues du calcul. La moyenne générale est pondérée par les coefficients
            de chaque matière. Les valeurs sont tronquées à 2 décimales (sans arrondi).
        </p>

    </div>{{-- /page-body --}}

    {{-- Footer fixe --}}
    <div class="doc-footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} — Lycée Technique de Bohicon — {{ $year->year ?? '' }}
    </div>

</body>
</html>
