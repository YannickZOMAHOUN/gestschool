<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bulletins — {{ $classroom->name ?? '' }} — S{{ $semester }}</title>
    <style>
        @page { margin: 18px 18px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color:#111827; }

        .sheet { page-break-after: always; }
        .sheet:last-child { page-break-after: auto; }

        .head {
            background: #1e1b4b;
            color: #fff;
            padding: 12px 14px;
            border-radius: 10px;
        }
        .head .title { margin:0; font-size: 16px; letter-spacing: -.2px; }
        .head .sub { margin-top: 4px; opacity:.86; font-size: 11px; }

        .box {
            margin-top: 10px;
            border: 1px solid #e3e8f0;
            background: #f8f9fc;
            border-radius: 10px;
            padding: 10px 12px;
        }

        .row { width:100%; display: table; }
        .col { display: table-cell; vertical-align: top; }
        .right { text-align:right; }

        .pill {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #4f46e5;
            font-weight: 700;
            font-size: 10px;
        }
        .mono { font-family: DejaVu Sans Mono, Consolas, monospace; }

        table { width:100%; border-collapse: collapse; margin-top: 10px; }
        thead th {
            background: #eef2ff;
            color: #3730a3;
            border: 1px solid #c7d2fe;
            padding: 7px 7px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        tbody td {
            border: 1px solid #e3e8f0;
            padding: 7px 7px;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) td { background:#fafbff; }

        .t-center { text-align:center; }
        .t-right { text-align:right; }

        .mark {
            display:inline-block;
            min-width: 54px;
            text-align:center;
            padding: 3px 8px;
            border-radius: 7px;
            border: 1px solid #e3e8f0;
            background: #ffffff;
            font-weight: 800;
        }
        .ok { background: #ecfdf5; border-color: #a7f3d0; color:#059669; }
        .bad { background: #fef2f2; border-color: #fecaca; color:#dc2626; }
        .na { color:#9ca3af; }

        .resume {
            margin-top: 10px;
            border: 1px solid #e3e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        .resume th, .resume td { border: 1px solid #e3e8f0; padding: 9px 10px; }
        .resume th { background:#f8f9fc; text-align:left; width:55%; }
        .foot {
            margin-top: 10px;
            font-size: 9px;
            color:#6b7280;
        }
    </style>
</head>
<body>

@php
    $classLabel = trim(($classroom->promotionSector->promotion_sector ?? '') . ' · ' . ($classroom->name ?? ''), ' · ');
    $yearLabel  = $year->year ?? '';
@endphp

@foreach($students as $student)
@php
    $sid = $student->id;
    $full = trim(($student->name ?? '').' '.($student->surname ?? ''));
    $mat  = $student->matricule ?? '—';
    $sex  = $student->sex ?? null;

    $moySem = ((int)$semester === 1) ? ($moyennesS1[$sid] ?? null) : ($moyennesS2[$sid] ?? null);
    $rangSem = ((int)$semester === 1) ? ($rangsS1[$sid] ?? '—') : ($rangsS2[$sid] ?? '—');

    $moyAnn = $moyennesAnnuelles[$sid] ?? null;
    $rangAnn = $rangsAnnuels[$sid] ?? '—';

    $mClass = $moySem === null ? 'na' : ($moySem >= 10 ? 'ok' : 'bad');
    $mAnnClass = $moyAnn === null ? 'na' : ($moyAnn >= 10 ? 'ok' : 'bad');
@endphp

<div class="sheet">
    <div class="head">
        <h1 class="title">Bulletin de notes — <span style="font-weight:900;">S{{ $semester }}</span></h1>
        <div class="sub">
            Classe : <b>{{ $classLabel }}</b> · Année : <b>{{ $yearLabel }}</b> · Date : <b>{{ $dateImpression ?? '' }}</b>
        </div>
    </div>

    <div class="box">
        <div class="row">
            <div class="col">
                <div><b>Élève :</b> {{ $full }}</div>
                <div style="margin-top:4px;">
                    <b>Matricule :</b> <span class="mono">{{ $mat }}</span>
                    @if($sex) · <b>Sexe :</b> {{ $sex }} @endif
                </div>
                <div style="margin-top:4px;">
                    <b>Filière :</b> {{ $classroom->promotionSector->sectorYear->sector->name_sector ?? '—' }}
                    · <b>Promotion :</b> {{ $classroom->promotionSector->promotion_sector ?? '—' }}
                </div>
            </div>
            <div class="col right">
                <span class="pill">Document officiel</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th class="t-center">Coeff</th>
                <th class="t-center">Moy /20</th>
                <th class="t-center">Rang matière</th>
            </tr>
        </thead>
        <tbody>
        @foreach($subjects as $subject)
            @php
                $coef = $coefficients[$subject->id] ?? 1;
                $moy  = $notesData[$sid][$semester][$subject->id] ?? null;
                $rankSub = $subjectRanks[$subject->id][$sid] ?? '—';
                $cls = $moy === null ? 'na' : ($moy >= 10 ? 'ok' : 'bad');
            @endphp
            <tr>
                <td><b>{{ $subject->name ?? '—' }}</b></td>
                <td class="t-center mono">{{ $coef }}</td>
                <td class="t-center">
                    @if($moy === null)
                        <span class="na">—</span>
                    @else
                        <span class="mark {{ $cls }}">{{ number_format($moy, 2) }}</span>
                    @endif
                </td>
                <td class="t-center mono">{{ $rankSub }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="resume" style="width:100%; border-collapse:collapse;">
        <tr>
            <th>Moyenne générale S{{ $semester }}</th>
            <td class="t-right">
                @if($moySem === null) <span class="na">—</span>
                @else <span class="mark {{ $mClass }}">{{ number_format($moySem, 2) }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Rang S{{ $semester }}</th>
            <td class="t-right mono">{{ $rangSem }}</td>
        </tr>

        @if((int)$semester === 2)
        <tr>
            <th>Moyenne annuelle</th>
            <td class="t-right">
                @if($moyAnn === null) <span class="na">—</span>
                @else <span class="mark {{ $mAnnClass }}">{{ number_format($moyAnn, 2) }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Rang annuel</th>
            <td class="t-right mono">{{ $rangAnn }}</td>
        </tr>
        @endif
    </table>

    <div class="foot">
        NB : Les moyennes sont sur 20. Les valeurs “—” indiquent une absence de note.
    </div>
</div>
@endforeach

</body>
</html>
