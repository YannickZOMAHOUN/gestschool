<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche à Collationner — Semestre {{ $semester }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5px;
            margin: 0;
            padding: 10px 14px;
            color: #111;
            background: #fff;
        }

        /* ─── EN-TÊTE ─────────────────────────────────────── */
        .header-wrap {
            display: table;
            width: 100%;
            border-bottom: 3px solid #1a3c6e;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header-left  { display: table-cell; width: 20%; vertical-align: middle; }
        .header-center { display: table-cell; text-align: center; vertical-align: middle; }
        .header-right { display: table-cell; width: 20%; text-align: right; vertical-align: middle; font-size: 8px; color: #555; }

        .school-name {
            font-size: 15px;
            font-weight: bold;
            color: #1a3c6e;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-title {
            font-size: 11px;
            font-weight: bold;
            color: #c0392b;
            text-transform: uppercase;
            margin: 3px 0 2px;
            letter-spacing: .5px;
        }
        .meta {
            font-size: 9px;
            color: #333;
        }

        /* ─── TABLEAU ─────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            page-break-inside: auto;
        }
        tr { page-break-inside: avoid; }

        th {
            background-color: #1a3c6e;
            color: #fff;
            font-weight: bold;
            font-size: 8.5px;
            padding: 5px 3px;
            text-align: center;
            border: 1px solid #0d2a52;
        }
        th.th-student {
            text-align: left;
            padding-left: 6px;
        }
        th.th-section {
            background-color: #2e5fa3;
            font-size: 8px;
        }

        td {
            border: 1px solid #bdc8d8;
            padding: 4px 3px;
            text-align: center;
            font-size: 9px;
            vertical-align: middle;
        }
        td.td-student {
            text-align: left;
            padding-left: 6px;
            white-space: nowrap;
            font-weight: 500;
            min-width: 120px;
        }

        /* Zébrage */
        tbody tr:nth-child(even) td { background-color: #f4f7fb; }
        tbody tr:hover td { background-color: #eaf0fb; }

        /* Couleur moyennes */
        .avg-high   { color: #1a7a3a; font-weight: bold; }
        .avg-medium { color: #b35c00; font-weight: bold; }
        .avg-low    { color: #b00020; font-weight: bold; }
        .avg-bold   { font-weight: bold; }

        /* Colonnes synthèse */
        td.td-rang    { font-weight: bold; color: #1a3c6e; font-size: 9px; }
        td.td-moy-gen { font-weight: bold; font-size: 9.5px; }

        /* ─── PIED ─────────────────────────────────────────── */
        .footer {
            margin-top: 18px;
            font-size: 8.5px;
            color: #444;
        }
        .footer-note { margin-bottom: 6px; font-style: italic; }
        .signatures {
            display: table;
            width: 100%;
            margin-top: 24px;
        }
        .sig-cell {
            display: table-cell;
            width: 33%;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
        }
        .sig-line {
            display: inline-block;
            width: 130px;
            border-top: 1px solid #555;
            padding-top: 4px;
            margin-top: 32px;
        }

        /* Badge rang */
        .rang-1 { color: #c8960c; }
        .rang-2 { color: #888; }
        .rang-3 { color: #a0522d; }
    </style>
</head>
<body>

{{-- ── EN-TÊTE ── --}}
<div class="header-wrap">
    <div class="header-left">
        <div style="font-size:8px; color:#555; line-height:1.5;">
            République Centrafricaine<br>
            <strong>MINESUP</strong>
        </div>
    </div>
    <div class="header-center">
        <div class="school-name">Lycée Technique de Bohicon</div>
        <div class="doc-title">Fiche à Collationner — Semestre {{ $semester }}</div>
        <div class="meta">
            Année scolaire : <strong>{{ $year->year }}</strong>
            &nbsp;·&nbsp;
            Classe : <strong>{{ $classroom->name }}</strong>
            &nbsp;·&nbsp;
            @php $nbEleves = count($students); @endphp
            Effectif : <strong>{{ $nbEleves }} élève(s)</strong>
        </div>
    </div>
    <div class="header-right">
        Imprimé le<br><strong>{{ $dateImpression }}</strong>
    </div>
</div>

{{-- ── TABLEAU ── --}}
@php
    /**
     * Couleur selon la note /20
     */
    function ficheAvgClass(?float $v): string {
        if ($v === null) return '';
        if ($v >= 14)   return 'avg-high';
        if ($v >= 10)   return 'avg-medium';
        return 'avg-low';
    }

    /**
     * Formater une moyenne (affiche '—' si null)
     */
    function ficheFormat($v, int $dec = 2): string {
        if ($v === null || $v === '') return '—';
        if ($v === 'Dispensé(e)')     return 'Disp.';
        return number_format((float)$v, $dec);
    }
@endphp

<table>
    {{-- Ligne 1 : en-têtes matières + colonnes bilan --}}
    <thead>
        <tr>
            <th rowspan="2" class="th-student" style="min-width:130px;">Élève</th>

            @foreach($subjects as $subject)
                <th colspan="2" class="th-section">{{ $subject->name }}</th>
            @endforeach

            @if($semester == 1)
                <th rowspan="2" style="background:#c0392b;">Moy. Gén.<br>S1</th>
                <th rowspan="2" style="background:#c0392b;">Rang<br>S1</th>
            @else
                <th rowspan="2" style="background:#2e5fa3;">Moy. S1</th>
                <th rowspan="2" style="background:#c0392b;">Moy. S2</th>
                <th rowspan="2" style="background:#1a7a3a;">Moy. Ann.</th>
                <th rowspan="2" style="background:#c0392b;">Rang S2</th>
                <th rowspan="2" style="background:#1a7a3a;">Rang Ann.</th>
            @endif
        </tr>
        <tr>
            @foreach($subjects as $subject)
                <th>Moy /20</th>
                <th>Coef.</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($students as $student)
            @php
                $sid    = $student->id;
                $myMoyS1 = $moyennesS1[$sid] ?? null;
                $myMoyS2 = $moyennesS2[$sid] ?? null;
                $myMoyAn = $moyennesAnnuelles[$sid] ?? null;
                $rangS1  = $rangsS1[$sid]  ?? '—';
                $rangS2  = $rangsS2[$sid]  ?? '—';
                $rangAn  = $rangsAnnuels[$sid] ?? '—';

                // Mise en forme du rang
                $fmtRang = function($r) {
                    if (!is_numeric($r)) return $r;
                    $cls = $r == 1 ? 'rang-1' : ($r == 2 ? 'rang-2' : ($r == 3 ? 'rang-3' : ''));
                    $suf = $r == 1 ? 'er' : 'ème';
                    return $cls ? "<span class=\"{$cls}\">{$r}{$suf}</span>" : "{$r}{$suf}";
                };
            @endphp
            <tr>
                <td class="td-student">{{ $student->name }} {{ $student->surname }}</td>

                {{-- ── Notes par matière ── --}}
                @foreach($subjects as $subject)
                    @php
                        // $notesData est indexé [$student->id][$semester][$subject->id]
                        $note    = $notesData[$sid][$semester][$subject->id] ?? null;
                        $coef    = $coefficients[$subject->id] ?? '—';
                        $display = ficheFormat($note);
                        $cls     = is_numeric($note) ? ficheAvgClass((float)$note) : '';
                    @endphp
                    <td class="{{ $cls }}">{{ $display }}</td>
                    <td>{{ $coef }}</td>
                @endforeach

                {{-- ── Colonnes bilan ── --}}
                @if($semester == 1)
                    <td class="td-moy-gen {{ ficheAvgClass($myMoyS1) }}">
                        {{ ficheFormat($myMoyS1) }}
                    </td>
                    <td class="td-rang">{!! $fmtRang($rangS1) !!}</td>
                @else
                    <td class="{{ ficheAvgClass($myMoyS1) }}">{{ ficheFormat($myMoyS1) }}</td>
                    <td class="td-moy-gen {{ ficheAvgClass($myMoyS2) }}">{{ ficheFormat($myMoyS2) }}</td>
                    <td class="td-moy-gen {{ ficheAvgClass($myMoyAn) }}">{{ ficheFormat($myMoyAn) }}</td>
                    <td class="td-rang">{!! $fmtRang($rangS2) !!}</td>
                    <td class="td-rang">{!! $fmtRang($rangAn) !!}</td>
                @endif
            </tr>
        @endforeach
    </tbody>

    {{-- ── Ligne stats classe ── --}}
    @php
        $allMoys = ($semester == 1) ? $moyennesS1 : $moyennesS2;
        $validM  = array_filter($allMoys, fn($v) => is_numeric($v));
        $minM    = count($validM) ? min($validM) : null;
        $maxM    = count($validM) ? max($validM) : null;
        $avgM    = count($validM) ? array_sum($validM) / count($validM) : null;
        // floor() comme le contrôleur
        $avgMFmt = $avgM !== null ? number_format(floor($avgM * 100) / 100, 2) : '—';
    @endphp
    <tfoot>
        <tr>
            <td style="text-align:left; font-weight:bold; background:#eaf0fb; padding-left:6px;">
                Statistiques de la classe
            </td>
            @php $colspan = count($subjects) * 2; @endphp
            <td colspan="{{ $colspan }}" style="background:#eaf0fb; font-style:italic; font-size:8.5px; text-align:left; padding-left:6px;">
                Moyenne classe&nbsp;: <strong>{{ $avgMFmt }}</strong>
                &nbsp;·&nbsp; Min&nbsp;: <strong>{{ ficheFormat($minM) }}</strong>
                &nbsp;·&nbsp; Max&nbsp;: <strong>{{ ficheFormat($maxM) }}</strong>
                &nbsp;·&nbsp; Nb ≥ 10 : <strong>{{ count(array_filter($validM, fn($v) => $v >= 10)) }}</strong>
                &nbsp;/&nbsp; {{ count($validM) }}
            </td>
            @if($semester == 1)
                <td colspan="2" style="background:#eaf0fb;"></td>
            @else
                <td colspan="5" style="background:#eaf0fb;"></td>
            @endif
        </tr>
    </tfoot>
</table>

{{-- ── PIED DE PAGE ── --}}
<div class="footer">
    <div class="footer-note">
        Moy. = Moyenne pondérée au coefficient.
        @if($semester == 2)
            Moy. Ann. = Moyenne arithmétique des deux semestres.
        @endif
    </div>
    <div class="signatures">
        <div class="sig-cell">
            <div class="sig-line">Le Professeur Principal</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line">Le Censeur</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line">Le Proviseur</div>
        </div>
    </div>
</div>

</body>
</html>
