<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin — {{ $classroom->name }} — S{{ $semester }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5px;
            color: #1a1a2e;
            background: #fff;
            padding: 0;
            margin: 0;
        }

        /* ═══════════════════════════════════════════════
           BULLETIN INDIVIDUEL (1 par page)
        ═══════════════════════════════════════════════ */
        .bulletin-page {
            width: 100%;
            min-height: 270mm;
            padding: 12px 16px 10px;
            position: relative;
        }

        /* ── Filigrane ── */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 62px;
            font-weight: 900;
            color: rgba(26, 60, 110, 0.04);
            white-space: nowrap;
            pointer-events: none;
            letter-spacing: 4px;
            text-transform: uppercase;
            z-index: 0;
        }

        /* ── En-tête ── */
        .bulletin-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .bh-left, .bh-center, .bh-right {
            display: table-cell;
            vertical-align: middle;
        }
        .bh-left  { width: 22%; }
        .bh-right { width: 22%; text-align: right; }
        .bh-center { text-align: center; }

        .republic-line {
            font-size: 7.5px;
            color: #555;
            line-height: 1.55;
        }
        .republic-line strong { color: #1a3c6e; }

        .school-logo-circle {
            display: inline-block;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a3c6e, #c0392b);
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
            padding-top: 11px;
            letter-spacing: .3px;
        }

        .school-main-name {
            font-size: 13px;
            font-weight: bold;
            color: #1a3c6e;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            line-height: 1.2;
        }
        .school-sub {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
            letter-spacing: .4px;
        }
        .bulletin-doc-title {
            display: inline-block;
            margin-top: 5px;
            padding: 3px 14px;
            background: linear-gradient(135deg, #1a3c6e, #2e5fa3);
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 2px;
        }
        .school-year-info {
            font-size: 8px;
            color: #444;
            margin-top: 5px;
        }

        /* ── Séparateur décoratif ── */
        .divider {
            height: 3px;
            background: linear-gradient(90deg, #1a3c6e 0%, #c0392b 50%, #1a7a3a 100%);
            border-radius: 2px;
            margin-bottom: 9px;
        }
        .divider-thin {
            height: 1px;
            background: linear-gradient(90deg, #1a3c6e 0%, #c0392b 50%, transparent 100%);
            margin: 6px 0;
        }

        /* ── Infos élève ── */
        .student-card {
            display: table;
            width: 100%;
            background: linear-gradient(135deg, #f0f4fc, #fafbfe);
            border: 1px solid #c8d4ea;
            border-radius: 4px;
            padding: 7px 10px;
            margin-bottom: 9px;
        }
        .sc-col { display: table-cell; vertical-align: middle; }
        .sc-col:not(:last-child) {
            border-right: 1px dashed #c8d4ea;
            padding-right: 12px;
        }
        .sc-col:not(:first-child) { padding-left: 12px; }
        .sc-label {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #2e5fa3;
            margin-bottom: 2px;
        }
        .sc-value {
            font-size: 10px;
            font-weight: bold;
            color: #1a1a2e;
        }
        .sc-value.name { font-size: 11px; color: #1a3c6e; }

        /* ── Tableau des notes ── */
        .notes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 9px;
            font-size: 9px;
        }
        .notes-table thead th {
            background: linear-gradient(180deg, #1a3c6e, #16325e);
            color: #fff;
            font-size: 8.5px;
            font-weight: bold;
            padding: 5px 5px;
            text-align: center;
            border: 1px solid #0d2a52;
            letter-spacing: .2px;
        }
        .notes-table thead th.th-left { text-align: left; padding-left: 8px; }
        .notes-table tbody td {
            border: 1px solid #c8d4ea;
            padding: 4.5px 5px;
            text-align: center;
            vertical-align: middle;
        }
        .notes-table tbody td.td-subject {
            text-align: left;
            padding-left: 8px;
            font-weight: 500;
            white-space: nowrap;
        }
        .notes-table tbody tr:nth-child(even) td {
            background: #f4f7fb;
        }
        .notes-table tbody tr:nth-child(odd) td {
            background: #fff;
        }

        /* Couleurs notes */
        .note-excellent { color: #0a5c2e; font-weight: bold; background: #d4edda !important; }
        .note-bien      { color: #155724; font-weight: bold; }
        .note-assez-bien{ color: #2e7d32; font-weight: bold; }
        .note-passable  { color: #856404; }
        .note-faible    { color: #cc6600; font-weight: bold; }
        .note-insuffisant { color: #c0392b; font-weight: bold; background: #fde8e8 !important; }
        .dispensed-cell { font-style: italic; color: #888; }

        /* Rang */
        .rang-cell { font-weight: bold; color: #1a3c6e; }
        .rang-1    { color: #c8960c; }
        .rang-2    { color: #757575; }
        .rang-3    { color: #8d6e63; }

        /* ── Synthèse ── */
        .summary-block {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .sum-main {
            display: table-cell;
            width: 58%;
            vertical-align: top;
        }
        .sum-aside {
            display: table-cell;
            width: 42%;
            vertical-align: top;
        }
        .sum-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sum-table td, .sum-table th {
            border: 1px solid #c8d4ea;
            padding: 5px 7px;
            font-size: 9px;
        }
        .sum-table th {
            background: #1a3c6e;
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            text-align: left;
            letter-spacing: .2px;
        }
        .sum-table .highlight {
            font-size: 13px;
            font-weight: bold;
        }

        /* Mention */
        .mention-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: .4px;
        }
        .mention-excellent  { background: #d4edda; color: #0a5c2e; border: 1px solid #a3d9b1; }
        .mention-tb         { background: #cfe2ff; color: #0a3d91; border: 1px solid #a6c8f8; }
        .mention-bien       { background: #d4edda; color: #155724; border: 1px solid #9acda8; }
        .mention-assez-bien { background: #fff3cd; color: #664d03; border: 1px solid #ffe09a; }
        .mention-passable   { background: #fff8e1; color: #795548; border: 1px solid #ffe0b2; }
        .mention-faible     { background: #fde8e8; color: #8b0000; border: 1px solid #f5b7b1; }
        .mention-insuf      { background: #fde8e8; color: #c0392b; border: 1px solid #f5b7b1; }

        /* ── Observation / décision ── */
        .obs-section {
            border: 1px solid #c8d4ea;
            border-radius: 3px;
            padding: 5px 8px;
            margin-bottom: 8px;
            min-height: 22px;
            display: table;
            width: 100%;
        }
        .obs-label {
            display: table-cell;
            font-size: 8px;
            font-weight: bold;
            color: #2e5fa3;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
            padding-right: 10px;
            vertical-align: middle;
            width: 130px;
        }
        .obs-line {
            display: table-cell;
            border-bottom: 1px dotted #bbb;
            width: 100%;
            min-height: 16px;
            vertical-align: bottom;
        }

        /* ── Signatures ── */
        .signatures-row {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        .sig-col {
            display: table-cell;
            text-align: center;
            width: 33%;
            font-size: 8px;
        }
        .sig-title {
            font-weight: bold;
            font-size: 8.5px;
            color: #1a3c6e;
            margin-bottom: 30px;
        }
        .sig-underline {
            display: inline-block;
            width: 120px;
            border-top: 1px solid #555;
            padding-top: 4px;
        }

        /* ── Saut de page ── */
        .page-break { page-break-after: always; }

        /* ── Mini bar couleur en bas ── */
        .footer-bar {
            height: 4px;
            background: linear-gradient(90deg, #1a3c6e 0%, #c0392b 50%, #1a7a3a 100%);
            border-radius: 2px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

@php
    /* ── Helpers ── */
    function bApprec(?float $m): string {
        return match(true) {
            $m === null => '—',
            $m < 5     => 'Insuffisant',
            $m < 8     => 'Très Faible',
            $m < 10    => 'Faible',
            $m < 12    => 'Passable',
            $m < 14    => 'Assez Bien',
            $m < 16    => 'Bien',
            $m < 18    => 'Très Bien',
            default    => 'Excellent',
        };
    }

    function bNoteCls(?float $m): string {
        if ($m === null) return '';
        return match(true) {
            $m < 5     => 'note-insuffisant',
            $m < 10    => 'note-faible',
            $m < 12    => 'note-passable',
            $m < 14    => 'note-assez-bien',
            $m < 16    => 'note-bien',
            $m < 18    => 'note-bien',
            default    => 'note-excellent',
        };
    }

    function bMentionCls(?float $m): string {
        if ($m === null) return '';
        return match(true) {
            $m < 5     => 'mention-insuf',
            $m < 8     => 'mention-faible',
            $m < 10    => 'mention-faible',
            $m < 12    => 'mention-passable',
            $m < 14    => 'mention-assez-bien',
            $m < 16    => 'mention-bien',
            $m < 18    => 'mention-tb',
            default    => 'mention-excellent',
        };
    }

    function bRangFmt($r): string {
        if (!is_numeric($r)) return (string)$r;
        $suf = ($r == 1) ? 'er' : 'ème';
        $cls = ($r == 1) ? 'rang-1' : (($r == 2) ? 'rang-2' : (($r == 3) ? 'rang-3' : 'rang-cell'));
        return "<span class=\"{$cls}\">{$r}{$suf}</span>";
    }
@endphp

@foreach($students as $student)
@php
    $sid    = $student->id;
    $myMoyS1  = $moyennesS1[$sid]       ?? null;
    $myMoyS2  = $moyennesS2[$sid]       ?? null;
    $myMoyAn  = $moyennesAnnuelles[$sid] ?? null;
    $myRangS1 = $rangsS1[$sid]          ?? '—';
    $myRangS2 = $rangsS2[$sid]          ?? '—';
    $myRangAn = $rangsAnnuels[$sid]     ?? '—';

    $allMoys  = ($semester == 1) ? $moyennesS1 : $moyennesS2;
    $validMoys = collect($allMoys)->filter(fn($v) => $v !== null);
    $minMoy   = $validMoys->min();
    $maxMoy   = $validMoys->max();
    $myMoy    = ($semester == 1) ? $myMoyS1 : $myMoyS2;
    $myRang   = ($semester == 1) ? $myRangS1 : $myRangS2;
@endphp

<div class="bulletin-page">
    <div class="watermark">LTB</div>

    {{-- ── EN-TÊTE ── --}}
    <div class="bulletin-header">
        <div class="bh-left">
            <div class="republic-line">
                République du Bénin<br>
                Ministère des Enseignements<br>
                Secondaire et de la Formation<br>
                <strong>Technique et Professionnelle</strong>
            </div>
        </div>
        <div class="bh-center">
            <div class="school-logo-circle">LTB<br>BOHICON</div><br>
            <div class="school-main-name">Lycée Technique de Bohicon</div>
            <div class="school-sub">Excellence · Rigueur · Développement</div>
            <div class="bulletin-doc-title">Bulletin de Notes</div>
            <div class="school-year-info">
                Année scolaire : <strong>{{ $year->year }}</strong>
                &nbsp;·&nbsp; <strong>Semestre {{ $semester }}</strong>
                &nbsp;·&nbsp; Classe : <strong>{{ $classroom->name }}</strong>
            </div>
        </div>
        <div class="bh-right" style="font-size:8px; color:#666; line-height:1.6;">
            N° d'ordre :<br>
            <span style="border-bottom:1px solid #999; display:inline-block; width:80px; margin-top:2px;"></span><br><br>
            Imprimé le :<br><strong>{{ $dateImpression }}</strong>
        </div>
    </div>

    <div class="divider"></div>

    {{-- ── IDENTITÉ ÉLÈVE ── --}}
    <div class="student-card">
        <div class="sc-col" style="width:38%">
            <div class="sc-label">Nom &amp; Prénom</div>
            <div class="sc-value name">{{ strtoupper($student->name) }} {{ $student->surname }}</div>
        </div>
        <div class="sc-col" style="width:22%">
            <div class="sc-label">Matricule</div>
            <div class="sc-value">{{ $student->matricule ?? '—' }}</div>
        </div>
        <div class="sc-col" style="width:22%">
            <div class="sc-label">Date de naissance</div>
            <div class="sc-value">
                {{ $student->birthday
                    ? \Illuminate\Support\Carbon::parse($student->birthday)->format('d/m/Y')
                    : '—' }}
            </div>
        </div>
        <div class="sc-col" style="width:18%">
            <div class="sc-label">Sexe</div>
            <div class="sc-value">{{ $student->gender ?? '—' }}</div>
        </div>
    </div>

    {{-- ── TABLEAU DES NOTES ── --}}
    <table class="notes-table">
        <thead>
            <tr>
                <th class="th-left" style="min-width:110px;">Matière</th>
                <th style="width:35px;">Coef.</th>
                <th style="width:55px;">Moy /20</th>
                <th style="width:60px;">Moy × Coef</th>
                <th style="width:50px;">Rang</th>
                <th>Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $subject)
                @php
                    $coef        = $coefficients[$subject->id] ?? 1;
                    $note        = $notesData[$sid][$semester][$subject->id] ?? null;
                    $isDispensed = ($note === 'Dispensé(e)');
                    $noteVal     = (!$isDispensed && $note !== null) ? (float)$note : null;
                    $rank        = $subjectRanks[$subject->id][$sid] ?? '—';
                    $noteCls     = bNoteCls($noteVal);
                @endphp
                <tr>
                    <td class="td-subject">{{ $subject->name }}</td>
                    <td>{{ $coef }}</td>
                    @if($isDispensed)
                        <td colspan="4" class="dispensed-cell">Dispensé(e)</td>
                    @elseif($noteVal !== null)
                        <td class="{{ $noteCls }}">{{ number_format($noteVal, 2) }}</td>
                        <td>{{ number_format($noteVal * $coef, 2) }}</td>
                        <td class="rang-cell">{!! bRangFmt($rank) !!}</td>
                        <td style="font-style:italic; font-size:8.5px;">{{ bApprec($noteVal) }}</td>
                    @else
                        <td>—</td><td>—</td><td>—</td><td>—</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── BLOC SYNTHÈSE ── --}}
    <div class="summary-block">
        <div class="sum-main">
            <table class="sum-table">
                <tr>
                    <th style="width:50%">Moyenne générale du semestre</th>
                    <th style="width:50%">Rang dans la classe</th>
                </tr>
                <tr>
                    <td class="highlight {{ bNoteCls($myMoy) }}" style="text-align:center;">
                        {{ $myMoy !== null ? number_format($myMoy, 2) . ' /20' : '—' }}
                    </td>
                    <td style="text-align:center; font-weight:bold; font-size:12px;">
                        {!! bRangFmt($myRang) !!}
                    </td>
                </tr>
                <tr>
                    <th>Mention</th>
                    <th>Effectif de la classe</th>
                </tr>
                <tr>
                    <td style="text-align:center;">
                        @if($myMoy !== null)
                            <span class="mention-badge {{ bMentionCls($myMoy) }}">
                                {{ bApprec($myMoy) }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td style="text-align:center; font-weight:bold;">{{ count($students) }} élève(s)</td>
                </tr>
                @if($semester == 2)
                <tr>
                    <th>Moy. S1</th>
                    <th>Moy. S2</th>
                </tr>
                <tr>
                    <td style="text-align:center; {{ $myMoyS1 !== null && $myMoyS1 >= 10 ? 'color:#1a7a3a;' : ($myMoyS1 !== null ? 'color:#c0392b;' : '') }} font-weight:bold;">
                        {{ $myMoyS1 !== null ? number_format($myMoyS1, 2) : '—' }}
                    </td>
                    <td style="text-align:center; {{ $myMoyS2 !== null && $myMoyS2 >= 10 ? 'color:#1a7a3a;' : ($myMoyS2 !== null ? 'color:#c0392b;' : '') }} font-weight:bold;">
                        {{ $myMoyS2 !== null ? number_format($myMoyS2, 2) : '—' }}
                    </td>
                </tr>
                <tr>
                    <th colspan="2">Moyenne Annuelle</th>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" class="highlight {{ bNoteCls($myMoyAn) }}">
                        {{ $myMoyAn !== null ? number_format($myMoyAn, 2) . ' /20' : '—' }}
                        @if(is_numeric($myRangAn))
                            &nbsp; — &nbsp; Rang annuel : {!! bRangFmt($myRangAn) !!}
                        @endif
                    </td>
                </tr>
                @endif
            </table>
        </div>

        <div class="sum-aside">
            <table class="sum-table">
                <tr>
                    <th colspan="2" style="text-align:center; background:#2e5fa3;">Statistiques de la classe</th>
                </tr>
                <tr>
                    <td>Moy. la plus basse</td>
                    <td style="text-align:center; font-weight:bold; color:#c0392b;">
                        {{ $minMoy !== null ? number_format($minMoy, 2) : '—' }}
                    </td>
                </tr>
                <tr>
                    <td>Moy. la plus haute</td>
                    <td style="text-align:center; font-weight:bold; color:#1a7a3a;">
                        {{ $maxMoy !== null ? number_format($maxMoy, 2) : '—' }}
                    </td>
                </tr>
                <tr>
                    <td>Nb élèves ≥ 10</td>
                    <td style="text-align:center; font-weight:bold; color:#1a7a3a;">
                        @php
                            $nbPass = collect($allMoys)->filter(fn($v) => $v !== null && $v >= 10)->count();
                            $nbTotal = collect($allMoys)->filter(fn($v) => $v !== null)->count();
                        @endphp
                        {{ $nbPass }} / {{ $nbTotal }}
                    </td>
                </tr>
                <tr>
                    <td>Taux de réussite</td>
                    <td style="text-align:center; font-weight:bold;">
                        {{ $nbTotal > 0 ? number_format($nbPass / $nbTotal * 100, 1) . '%' : '—' }}
                    </td>
                </tr>
                <tr>
                    <td>Année scolaire</td>
                    <td style="text-align:center; font-weight:bold; color:#1a3c6e;">{{ $year->year }}</td>
                </tr>
                <tr>
                    <td>Semestre</td>
                    <td style="text-align:center; font-weight:bold; color:#c0392b;">S{{ $semester }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── OBSERVATION ── --}}
    <div class="obs-section">
        <div class="obs-label">Observation du Prof. Principal :</div>
        <div class="obs-line"></div>
    </div>
    <div class="obs-section">
        <div class="obs-label">Décision du Conseil de Classe :</div>
        <div class="obs-line">
            <span style="font-size:8px; color:#bbb;">Admis(e) · Ajourné(e) · Redoublement · Orientation</span>
        </div>
    </div>

    {{-- ── SIGNATURES ── --}}
    <div class="signatures-row">
        <div class="sig-col">
            <div class="sig-title">Le Professeur Principal</div>
            <div><span class="sig-underline"></span></div>
        </div>
        <div class="sig-col">
            <div class="sig-title">Le Censeur</div>
            <div><span class="sig-underline"></span></div>
        </div>
        <div class="sig-col">
            <div class="sig-title">Visa des Parents / Tuteurs</div>
            <div><span class="sig-underline"></span></div>
        </div>
    </div>

    <div class="footer-bar"></div>
</div>

@if(!$loop->last)
    <div class="page-break"></div>
@endif

@endforeach
</body>
</html>
