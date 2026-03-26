@extends('layouts.template')

@section('content')

<div class="imp-wrap">
    <div class="imp-bg"></div>

    <div class="imp-container">

        {{-- Header --}}
        <div class="imp-header">
            <div class="imp-header-text">
                <div class="imp-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Importation de notes
                </div>
                <h1 class="imp-title">Importer des <em>notes</em></h1>
                <p class="imp-sub">Formats acceptés : CSV, XLS, XLSX · Structure identique à l'export</p>
            </div>
            <div class="imp-header-visual">
                <div class="imp-ring imp-ring--1"></div>
                <div class="imp-ring imp-ring--2"></div>
                <div class="imp-core">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <polyline points="12 18 12 12"/><polyline points="9 15 12 12 15 15"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Alertes session --}}
        @if(session('success'))
            <div class="imp-alert imp-alert--success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="imp-alert imp-alert--warning">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                {{ session('warning') }}
            </div>
        @endif
        @if(session('error'))
            <div class="imp-alert imp-alert--error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="imp-card">

            {{-- Breadcrumb --}}
            <div class="imp-bc" id="imp-bc">
                <div class="imp-bc-item imp-bc-year" id="imp-bc-year">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span id="imp-bc-year-lbl">Année</span>
                </div>
                <span class="imp-bc-sep">›</span>
                <div class="imp-bc-item" id="imp-bc-sector"><span id="imp-bc-sector-lbl">Filière</span></div>
                <span class="imp-bc-sep">›</span>
                <div class="imp-bc-item" id="imp-bc-promo"><span id="imp-bc-promo-lbl">Promotion</span></div>
                <span class="imp-bc-sep">›</span>
                <div class="imp-bc-item" id="imp-bc-class"><span id="imp-bc-class-lbl">Classe</span></div>
            </div>

            <form method="POST" action="{{ route('notes.import') }}" enctype="multipart/form-data" id="importForm">
                @csrf

                {{-- Champs cachés contexte --}}
                <input type="hidden" name="year_id"      id="imp-year-id">
                <input type="hidden" name="classroom_id" id="imp-classroom-id">

                {{-- Étape 1 : Filière --}}
                <div class="imp-step" id="imp-step-sector">
                    <div class="imp-step-head">
                        <div class="imp-step-num">1</div>
                        <div class="imp-step-info">
                            <div class="imp-step-title">Filière</div>
                            <div class="imp-step-hint">Choisissez la filière d'enseignement</div>
                        </div>
                        <div class="imp-loader" id="imp-loader-sector" style="display:none;"><div class="imp-spin"></div></div>
                    </div>
                    <div class="imp-step-opts" id="imp-opts-sector">
                        <div class="imp-ph">Chargement…</div>
                    </div>
                </div>

                {{-- Étape 2 : Promotion --}}
                <div class="imp-step imp-step--locked" id="imp-step-promo">
                    <div class="imp-step-head">
                        <div class="imp-step-num">2</div>
                        <div class="imp-step-info">
                            <div class="imp-step-title">Promotion</div>
                            <div class="imp-step-hint">Sélectionnez la promotion</div>
                        </div>
                        <div class="imp-loader" id="imp-loader-promo" style="display:none;"><div class="imp-spin"></div></div>
                    </div>
                    <div class="imp-step-opts" id="imp-opts-promo">
                        <div class="imp-ph">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                            Choisissez d'abord une filière
                        </div>
                    </div>
                </div>

                {{-- Étape 3 : Classe --}}
                <div class="imp-step imp-step--locked" id="imp-step-class">
                    <div class="imp-step-head">
                        <div class="imp-step-num">3</div>
                        <div class="imp-step-info">
                            <div class="imp-step-title">Classe</div>
                            <div class="imp-step-hint">Choisissez la classe cible</div>
                        </div>
                        <div class="imp-loader" id="imp-loader-class" style="display:none;"><div class="imp-spin"></div></div>
                    </div>
                    <div class="imp-step-opts" id="imp-opts-class">
                        <div class="imp-ph">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                            Choisissez d'abord une promotion
                        </div>
                    </div>
                </div>

                {{-- Séparateur --}}
                <div class="imp-sep"><span>Configuration de l'import</span></div>

                {{-- Semestre --}}
                <div class="imp-row">
                    <div class="imp-field">
                        <label class="imp-lbl">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Semestre
                        </label>
                        <div class="imp-sem-group">
                            <label class="imp-sem-card">
                                <input type="radio" name="semester" value="1" required>
                                <div class="imp-sem-inner">
                                    <span class="imp-sem-badge">S1</span>
                                    <span class="imp-sem-name">Semestre 1</span>
                                    <span class="imp-sem-chk">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </span>
                                </div>
                            </label>
                            <label class="imp-sem-card">
                                <input type="radio" name="semester" value="2">
                                <div class="imp-sem-inner">
                                    <span class="imp-sem-badge">S2</span>
                                    <span class="imp-sem-name">Semestre 2</span>
                                    <span class="imp-sem-chk">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </span>
                                </div>
                            </label>
                        </div>
                        <p class="imp-err" id="err-sem" style="display:none;">Veuillez sélectionner un semestre</p>
                    </div>

                    {{-- Matière (requis pour CSV uniquement) --}}
                    <div class="imp-field imp-field--wide" id="field-subject" style="display:none;">
                        <label class="imp-lbl">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            Matière <span class="imp-required">(CSV : obligatoire)</span>
                        </label>
                        <div class="imp-sel-wrap">
                            <select name="subject_id" id="imp-subject-id">
                                <option value="">— Sélectionner une matière —</option>
                            </select>
                            <svg class="imp-sel-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <p class="imp-hint-txt">Pour XLS/XLSX, les matières sont détectées automatiquement via le nom des onglets.</p>
                    </div>
                </div>

                {{-- Zone de dépôt du fichier --}}
                <div class="imp-drop-zone" id="imp-drop-zone">
                    <input type="file" name="file" id="imp-file" accept=".csv,.xls,.xlsx,.txt" required style="display:none;">
                    <div class="imp-drop-content" id="imp-drop-content">
                        <div class="imp-drop-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <div class="imp-drop-text">
                            <strong>Glissez votre fichier ici</strong>
                            <span>ou <button type="button" class="imp-browse-btn" id="imp-browse-btn">parcourir</button></span>
                        </div>
                        <div class="imp-drop-formats">
                            <span class="imp-fmt">CSV</span>
                            <span class="imp-fmt">XLS</span>
                            <span class="imp-fmt">XLSX</span>
                        </div>
                    </div>
                    <div class="imp-file-preview" id="imp-file-preview" style="display:none;">
                        <div class="imp-file-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="imp-file-info">
                            <span class="imp-file-name" id="imp-file-name">—</span>
                            <span class="imp-file-size" id="imp-file-size">—</span>
                        </div>
                        <button type="button" class="imp-file-remove" id="imp-file-remove">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Note structurelle --}}
                <div class="imp-structure-note">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>
                        Structure attendue : colonnes <strong>Matricule · Nom · Prénom(s) · Moy. Interros · Devoir 1 · Devoir 2</strong>.
                        Pour XLS/XLSX, chaque onglet doit correspondre au nom exact d'une matière de la classe.
                        Utilisez l'export comme modèle.
                    </span>
                </div>

                {{-- Actions --}}
                <div class="imp-footer">
                    <a href="{{ route('export_view') }}" class="imp-btn-ghost">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Télécharger un modèle
                    </a>
                    <button type="submit" class="imp-btn-primary" id="imp-btn-submit" disabled>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span class="imp-btn-txt">Importer les notes</span>
                        <div class="imp-btn-spin" id="imp-btn-spin" style="display:none;"></div>
                    </button>
                </div>

            </form>
        </div>

        <p class="imp-footer-note">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Seules les cases vides sont remplies lors d'un import enseignant. Les notes déjà enregistrées ne sont jamais écrasées.
        </p>

    </div>
</div>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --ia: #4f46e5; --ia2: #6366f1; --ia-bg: #eef2ff; --ia-border: #c7d2fe;
    --ib: #e5e7eb; --ic: #f9fafb; --id: #374151; --ie: #6b7280; --if: #9ca3af;
    --ig: #1e1b4b;
    --font: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    --ease: cubic-bezier(.4,0,.2,1);
    --ease-s: cubic-bezier(.34,1.56,.64,1);
}
.imp-wrap { min-height:100vh; background:#f0f4ff; padding:2.5rem 1.25rem 5rem; font-family:var(--font); position:relative; overflow:hidden; }
.imp-bg { position:absolute; inset:0; background:radial-gradient(circle at 15% 15%,rgba(99,102,241,.08),transparent 50%),radial-gradient(circle at 85% 80%,rgba(16,185,129,.06),transparent 50%); pointer-events:none; }
.imp-container { max-width:860px; margin:0 auto; position:relative; z-index:1; }

/* Header */
.imp-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; animation:impUp .5s ease both; }
.imp-pill { display:inline-flex; align-items:center; gap:6px; background:var(--ia-bg); color:var(--ia); border:1.5px solid var(--ia-border); font-size:.72rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:5px 12px; border-radius:50px; margin-bottom:.75rem; }
.imp-title { font-size:2.2rem; font-weight:800; color:var(--ig); letter-spacing:-.02em; margin-bottom:.5rem; }
.imp-title em { font-style:italic; font-weight:300; color:#4f46e5; }
.imp-sub { color:var(--ie); font-size:.88rem; }
.imp-header-visual { position:relative; width:86px; height:86px; flex-shrink:0; }
.imp-ring { position:absolute; border-radius:50%; border:1.5px solid; top:50%; left:50%; transform:translate(-50%,-50%); }
.imp-ring--1 { width:100%; height:100%; border-color:rgba(99,102,241,.15); animation:impSpin 12s linear infinite; }
.imp-ring--2 { width:70%; height:70%; border-color:rgba(99,102,241,.25); animation:impSpin 8s linear infinite reverse; }
@keyframes impSpin { to { transform:translate(-50%,-50%) rotate(360deg); } }
.imp-core { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:46px; height:46px; background:linear-gradient(135deg,var(--ia),var(--ia2)); border-radius:14px; display:flex; align-items:center; justify-content:center; color:#fff; box-shadow:0 8px 24px rgba(99,102,241,.35); }
@keyframes impUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

/* Alertes */
.imp-alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:10px; font-size:.83rem; margin-bottom:12px; border:1px solid; font-weight:500; animation:impUp .3s ease both; }
.imp-alert--success { background:#f0fdf4; border-color:#a7f3d0; color:#065f46; }
.imp-alert--warning { background:#fffbeb; border-color:#fde68a; color:#92400e; }
.imp-alert--error   { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
.imp-alert svg { flex-shrink:0; margin-top:1px; }

/* Card */
.imp-card { background:#fff; border:1px solid var(--ib); border-radius:20px; padding:28px; box-shadow:0 4px 6px rgba(0,0,0,.04),0 20px 50px rgba(99,102,241,.07); animation:impUp .5s .1s ease both; }

/* Breadcrumb */
.imp-bc { display:flex; align-items:center; gap:6px; padding:10px 14px; background:var(--ic); border:1px solid var(--ib); border-radius:10px; margin-bottom:20px; flex-wrap:wrap; }
.imp-bc-item { display:flex; align-items:center; gap:5px; padding:3px 8px; border-radius:6px; color:var(--if); font-size:.74rem; font-weight:600; transition:all .2s; }
.imp-bc-year { color:var(--ia); font-weight:800; }
.imp-bc-item.active { color:var(--id); background:var(--ia-bg); }
.imp-bc-sep { color:#d1d5db; }

/* Étapes */
.imp-step { border:1px solid var(--ib); border-radius:14px; overflow:hidden; margin-bottom:14px; transition:opacity .2s,filter .2s; }
.imp-step--locked { opacity:.5; pointer-events:none; filter:grayscale(.3); }
.imp-step-head { display:flex; align-items:center; gap:12px; padding:12px 16px; background:var(--ic); border-bottom:1px solid #f3f4f6; }
.imp-step-num { width:26px; height:26px; border-radius:8px; background:var(--ia-bg); border:1px solid var(--ia-border); color:var(--ia); font-weight:800; font-size:.78rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.imp-step-info { flex:1; }
.imp-step-title { font-weight:700; font-size:.83rem; color:var(--id); }
.imp-step-hint { font-size:.72rem; color:var(--ie); margin-top:1px; }
.imp-loader { display:flex; align-items:center; }
.imp-spin { width:14px; height:14px; border:2px solid var(--ib); border-top-color:var(--ia); border-radius:50%; animation:impSpinInner .7s linear infinite; }
@keyframes impSpinInner { to { transform:rotate(360deg); } }
.imp-step-opts { padding:12px 16px; display:flex; flex-wrap:wrap; gap:8px; min-height:50px; align-items:center; }
.imp-ph { display:flex; align-items:center; gap:7px; font-size:.79rem; color:var(--if); font-style:italic; }

/* Option cards */
.imp-opt { display:inline-flex; align-items:center; gap:8px; padding:8px 14px; background:#fff; border:1.5px solid var(--ib); border-radius:10px; cursor:pointer; font-family:var(--font); font-size:.83rem; font-weight:600; color:var(--id); transition:all .18s var(--ease); white-space:nowrap; }
.imp-opt:hover { border-color:var(--ia-border); background:var(--ia-bg); color:var(--ia); transform:translateY(-1px); box-shadow:0 2px 8px rgba(0,0,0,.06); }
.imp-opt--active { border-color:var(--ia); background:var(--ia-bg); color:var(--ia); box-shadow:0 0 0 3px rgba(79,70,229,.1); }

/* Séparateur */
.imp-sep { display:flex; align-items:center; gap:12px; margin:20px 0; }
.imp-sep::before,.imp-sep::after { content:''; flex:1; height:1px; background:#f3f4f6; }
.imp-sep span { font-size:.68rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--if); white-space:nowrap; }

/* Ligne config */
.imp-row { display:grid; grid-template-columns:auto 1fr; gap:16px; align-items:start; margin-bottom:20px; }
.imp-field { display:flex; flex-direction:column; gap:8px; }
.imp-field--wide { flex:1; }
.imp-lbl { display:flex; align-items:center; gap:6px; font-size:.78rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--ie); }
.imp-lbl svg { color:var(--ia); opacity:.7; }
.imp-required { font-size:.68rem; font-weight:500; text-transform:none; letter-spacing:0; color:var(--ie); }

/* Semestre */
.imp-sem-group { display:flex; gap:10px; }
.imp-sem-card { cursor:pointer; flex:1; max-width:160px; }
.imp-sem-card input { display:none; }
.imp-sem-inner { display:flex; align-items:center; gap:10px; padding:.7rem 1rem; background:var(--ic); border:1.5px solid var(--ib); border-radius:12px; transition:all .22s; }
.imp-sem-badge { font-size:1rem; font-weight:800; color:var(--if); min-width:24px; }
.imp-sem-name { font-size:.8rem; color:var(--ie); }
.imp-sem-chk { margin-left:auto; width:18px; height:18px; border-radius:50%; background:#e5e7eb; display:flex; align-items:center; justify-content:center; transition:all .22s; }
.imp-sem-chk svg { width:9px; height:9px; color:var(--if); }
.imp-sem-card input:checked + .imp-sem-inner { border-color:var(--ia); background:var(--ia-bg); box-shadow:0 0 0 3px rgba(79,70,229,.1); }
.imp-sem-card input:checked + .imp-sem-inner .imp-sem-badge { color:var(--ia); }
.imp-sem-card input:checked + .imp-sem-inner .imp-sem-chk { background:var(--ia); }
.imp-sem-card input:checked + .imp-sem-inner .imp-sem-chk svg { color:#fff; }
.imp-sem-card:hover .imp-sem-inner { border-color:var(--ia-border); background:var(--ia-bg); }
.imp-err { font-size:.75rem; color:#ef4444; margin-top:4px; }

/* Select matière */
.imp-sel-wrap { position:relative; }
.imp-sel-wrap select { appearance:none; width:100%; padding:.6rem 2.5rem .6rem .9rem; background:#fff; border:1.5px solid var(--ib); border-radius:10px; font-family:var(--font); font-size:.88rem; color:var(--id); outline:none; cursor:pointer; transition:border-color .18s,box-shadow .18s; }
.imp-sel-wrap select:focus { border-color:var(--ia); box-shadow:0 0 0 3px rgba(79,70,229,.12); }
.imp-sel-arrow { position:absolute; right:10px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--if); pointer-events:none; }
.imp-hint-txt { font-size:.72rem; color:var(--if); }

/* Zone de dépôt */
.imp-drop-zone { border:2px dashed var(--ib); border-radius:16px; padding:32px 20px; text-align:center; cursor:pointer; transition:all .2s var(--ease); margin-bottom:16px; background:var(--ic); }
.imp-drop-zone:hover,.imp-drop-zone.dragover { border-color:var(--ia); background:var(--ia-bg); }
.imp-drop-icon { width:56px; height:56px; margin:0 auto 12px; background:var(--ia-bg); border:2px solid var(--ia-border); border-radius:14px; display:flex; align-items:center; justify-content:center; color:var(--ia); }
.imp-drop-text { margin-bottom:12px; }
.imp-drop-text strong { display:block; font-size:.95rem; color:var(--id); margin-bottom:4px; }
.imp-drop-text span { font-size:.83rem; color:var(--ie); }
.imp-browse-btn { background:none; border:none; color:var(--ia); font-weight:700; cursor:pointer; text-decoration:underline; font-family:var(--font); font-size:.83rem; padding:0; }
.imp-drop-formats { display:flex; gap:8px; justify-content:center; }
.imp-fmt { padding:3px 10px; border-radius:6px; background:#fff; border:1.5px solid var(--ib); font-size:.72rem; font-weight:700; color:var(--ie); letter-spacing:.04em; }

/* Prévisualisation fichier */
.imp-file-preview { display:flex; align-items:center; gap:12px; padding:12px 16px; background:#fff; border:1.5px solid var(--ia-border); border-radius:12px; }
.imp-file-icon { width:36px; height:36px; background:var(--ia-bg); border-radius:10px; display:flex; align-items:center; justify-content:center; color:var(--ia); flex-shrink:0; }
.imp-file-info { flex:1; display:flex; flex-direction:column; gap:2px; text-align:left; }
.imp-file-name { font-weight:700; font-size:.85rem; color:var(--id); }
.imp-file-size { font-size:.72rem; color:var(--ie); }
.imp-file-remove { width:28px; height:28px; border:1.5px solid var(--ib); background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--ie); transition:all .15s; flex-shrink:0; }
.imp-file-remove:hover { border-color:#fca5a5; color:#ef4444; background:#fef2f2; }

/* Note structurelle */
.imp-structure-note { display:flex; align-items:flex-start; gap:8px; padding:12px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; font-size:.78rem; color:var(--ie); line-height:1.6; margin-bottom:20px; }
.imp-structure-note svg { flex-shrink:0; margin-top:2px; color:var(--ia); }
.imp-structure-note strong { color:var(--id); }

/* Footer */
.imp-footer { display:flex; align-items:center; justify-content:flex-end; gap:12px; padding-top:20px; border-top:1px solid #f3f4f6; }
.imp-btn-ghost { display:inline-flex; align-items:center; gap:7px; padding:.65rem 1.3rem; background:transparent; border:1.5px solid var(--ib); border-radius:10px; color:var(--ie); font-family:var(--font); font-size:.88rem; font-weight:500; cursor:pointer; text-decoration:none; transition:all .2s; }
.imp-btn-ghost:hover { border-color:#d1d5db; color:var(--id); background:var(--ic); }
.imp-btn-primary { display:inline-flex; align-items:center; gap:8px; padding:.7rem 1.6rem; background:linear-gradient(135deg,var(--ia),var(--ia2)); border:none; border-radius:10px; color:#fff; font-family:var(--font); font-size:.9rem; font-weight:700; cursor:pointer; box-shadow:0 4px 14px rgba(79,70,229,.35); transition:all .25s; }
.imp-btn-primary:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 8px 22px rgba(79,70,229,.45); }
.imp-btn-primary:disabled { opacity:.35; cursor:not-allowed; box-shadow:none; transform:none; }
.imp-btn-spin { width:14px; height:14px; border:2px solid rgba(255,255,255,.3); border-top-color:#fff; border-radius:50%; animation:impSpinInner .7s linear infinite; }

.imp-footer-note { display:flex; align-items:flex-start; gap:7px; margin-top:1.25rem; color:var(--if); font-size:.78rem; animation:impUp .5s .25s ease both; line-height:1.6; }
.imp-footer-note svg { flex-shrink:0; color:var(--ia); margin-top:2px; }

@media(max-width:700px) {
    .imp-card { padding:1.25rem; }
    .imp-row { grid-template-columns:1fr; }
    .imp-footer { flex-direction:column; }
    .imp-btn-ghost,.imp-btn-primary { width:100%; justify-content:center; }
    .imp-header-visual { display:none; }
    .imp-title { font-size:1.75rem; }
}
</style>

@endsection

@section('another_JS')
<script>
/* ═══════════════════════════════════════════════════
   Import — Cascade intelligente + gestion fichier
═══════════════════════════════════════════════════ */
const IMP = {
    baseUrl:    '{{ rtrim(url('/'), '/') }}',
    csrf:       '{{ csrf_token() }}',
    state:      { yearId: null, sectorId: null, promotionId: null, classroomId: null },
    fileExt:    null,

    async get(path) {
        const r = await fetch(this.baseUrl + path, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        if (!r.ok) throw new Error(`Erreur ${r.status}`);
        return r.json();
    },

    async post(path, body) {
        const r = await fetch(this.baseUrl + path, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        });
        return r.json();
    },

    $(id) { return document.getElementById(id); },

    init() {
        // Semestre
        document.querySelectorAll('input[name="semester"]').forEach(r => {
            r.addEventListener('change', () => { this.$('err-sem').style.display = 'none'; this._checkSubmit(); });
        });

        // Fichier
        this.$('imp-browse-btn').addEventListener('click', () => this.$('imp-file').click());
        this.$('imp-drop-zone').addEventListener('click', e => {
            if (e.target !== this.$('imp-browse-btn') && !this.$('imp-file-preview').contains(e.target)) {
                this.$('imp-file').click();
            }
        });
        this.$('imp-file').addEventListener('change', e => this._onFile(e.target.files[0]));
        this.$('imp-file-remove').addEventListener('click', () => this._clearFile());

        // Drag & drop
        const dz = this.$('imp-drop-zone');
        dz.addEventListener('dragover',  e => { e.preventDefault(); dz.classList.add('dragover'); });
        dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
        dz.addEventListener('drop', e => {
            e.preventDefault(); dz.classList.remove('dragover');
            const f = e.dataTransfer.files[0];
            if (f) { this.$('imp-file').files = e.dataTransfer.files; this._onFile(f); }
        });

        // Submit
        this.$('importForm').addEventListener('submit', e => {
            const sem = document.querySelector('input[name="semester"]:checked');
            if (!sem) { e.preventDefault(); this.$('err-sem').style.display = 'block'; return; }
            if (!this.state.classroomId || !this.$('imp-file').files.length) { e.preventDefault(); return; }
            this.$('imp-btn-txt').textContent = 'Import en cours…';
            this.$('imp-btn-spin').style.display = 'block';
            this.$('imp-btn-submit').disabled = true;
        });

        // Démarrer
        this._loadActiveYear();
    },

    async _loadActiveYear() {
        try {
            const d = await this.get('/api/active-year');
            if (d.success && d.year) {
                this.state.yearId = d.year.id;
                this.$('imp-year-id').value = d.year.id;
                this._setBc('year', d.year.year);
                await this._loadSectors();
            }
        } catch(e) { console.error(e); }
    },

    async _loadSectors() {
        this._setLoading('sector', true);
        this.$('imp-step-sector').classList.remove('imp-step--locked');
        try {
            const d = await this.get(`/api/sectors-for-create/${this.state.yearId}`);
            this._setLoading('sector', false);
            this._renderOpts('sector', d.sectors || [], 'Aucune filière.', item => this._onSector(item));
            if (d.auto && d.sectors?.length) await this._onSector(d.sectors[0], true);
        } catch(e) { this._setLoading('sector', false); }
    },

    async _onSector(s, auto = false) {
        this._markActive('sector', s.id);
        this.state.sectorId = s.id;
        this._setBc('sector', s.name);
        this._resetFrom('promo');
        await this._loadPromotions();
    },

    async _loadPromotions() {
        this._setLoading('promo', true);
        this.$('imp-step-promo').classList.remove('imp-step--locked');
        try {
            const d = await this.get(`/api/promotions-for-create/${this.state.yearId}/${this.state.sectorId}`);
            this._setLoading('promo', false);
            this._renderOpts('promo', d.promotions || [], 'Aucune promotion.', item => this._onPromotion(item));
            if (d.auto && d.promotions?.length) await this._onPromotion(d.promotions[0], true);
        } catch(e) { this._setLoading('promo', false); }
    },

    async _onPromotion(p, auto = false) {
        this._markActive('promo', p.id);
        this.state.promotionId = p.id;
        this._setBc('promo', p.name);
        this._resetFrom('class');
        await this._loadClassrooms();
    },

    async _loadClassrooms() {
        this._setLoading('class', true);
        this.$('imp-step-class').classList.remove('imp-step--locked');
        try {
            const d = await this.get(`/api/classrooms-for-create/${this.state.promotionId}?year_id=${this.state.yearId}`);
            this._setLoading('class', false);
            this._renderOpts('class', d.classrooms || [], 'Aucune classe.', item => this._onClassroom(item));
            if (d.auto && d.classrooms?.length) await this._onClassroom(d.classrooms[0], true);
        } catch(e) { this._setLoading('class', false); }
    },

    async _onClassroom(c, auto = false) {
        this._markActive('class', c.id);
        this.state.classroomId = c.id;
        this.$('imp-classroom-id').value = c.id;
        this._setBc('class', c.name);
        // Charger les matières pour le select CSV
        await this._loadSubjects();
        this._checkSubmit();
    },

    async _loadSubjects() {
        const sem = document.querySelector('input[name="semester"]:checked')?.value;
        const sel = this.$('imp-subject-id');
        sel.innerHTML = '<option value="">Chargement…</option>';
        try {
            const d = await this.post('/api/subjects-by-classroom', {
                classroom_id: this.state.classroomId,
                year_id: this.state.yearId,
                semester: sem ? parseInt(sem) : null,
            });
            sel.innerHTML = '<option value="">— Sélectionner une matière —</option>';
            if (d.success && d.subjects?.length) {
                d.subjects.forEach(s => {
                    const o = document.createElement('option');
                    o.value = s.subject_id;
                    o.textContent = `${s.subject_name}  (Coeff. ${s.coefficient})`;
                    sel.appendChild(o);
                });
            }
        } catch(e) {
            sel.innerHTML = '<option value="">— Erreur —</option>';
        }
    },

    _onFile(file) {
        if (!file) return;
        const ext = file.name.split('.').pop().toLowerCase();
        this.fileExt = ext;

        // Afficher le nom + taille
        this.$('imp-file-name').textContent = file.name;
        this.$('imp-file-size').textContent = this._formatSize(file.size);
        this.$('imp-drop-content').style.display = 'none';
        this.$('imp-file-preview').style.display  = 'flex';

        // Afficher le select matière uniquement pour CSV
        const fieldSubject = this.$('field-subject');
        if (ext === 'csv' || ext === 'txt') {
            fieldSubject.style.display = 'flex';
        } else {
            fieldSubject.style.display = 'none';
            this.$('imp-subject-id').value = '';
        }

        this._checkSubmit();
    },

    _clearFile() {
        this.$('imp-file').value = '';
        this.fileExt = null;
        this.$('imp-drop-content').style.display = 'block';
        this.$('imp-file-preview').style.display  = 'none';
        this.$('field-subject').style.display = 'none';
        this._checkSubmit();
    },

    _formatSize(bytes) {
        if (bytes < 1024) return bytes + ' o';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' Ko';
        return (bytes / (1024 * 1024)).toFixed(1) + ' Mo';
    },

    _checkSubmit() {
        const sem       = document.querySelector('input[name="semester"]:checked');
        const hasFile   = this.$('imp-file').files.length > 0;
        const hasClass  = !!this.state.classroomId;
        const needSub   = (this.fileExt === 'csv' || this.fileExt === 'txt');
        const hasSub    = !needSub || !!this.$('imp-subject-id').value;
        this.$('imp-btn-submit').disabled = !(sem && hasFile && hasClass && hasSub);
    },

    _renderOpts(step, items, empty, onClick) {
        const el = this.$(`imp-opts-${step}`);
        if (!items.length) { el.innerHTML = `<div class="imp-ph">${empty}</div>`; return; }
        el.innerHTML = items.map(i => `
            <button type="button" class="imp-opt" data-id="${i.id}">
                <span>${i.name}</span><span style="opacity:.4">›</span>
            </button>
        `).join('');
        el.querySelectorAll('.imp-opt').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = items.find(i => String(i.id) === btn.dataset.id);
                if (item) onClick(item);
            });
        });
    },

    _markActive(step, id) {
        this.$(`imp-opts-${step}`)?.querySelectorAll('.imp-opt').forEach(btn => {
            btn.classList.toggle('imp-opt--active', String(btn.dataset.id) === String(id));
        });
    },

    _setLoading(step, on) {
        const el = this.$(`imp-loader-${step}`);
        if (el) el.style.display = on ? 'flex' : 'none';
    },

    _setBc(part, label) {
        const lbl = this.$(`imp-bc-${part}-lbl`); if (lbl) lbl.textContent = label;
        const item = this.$(`imp-bc-${part}`); if (item) item.classList.add('active');
    },

    _resetFrom(step) {
        const steps = ['sector', 'promo', 'class'];
        const idx = steps.indexOf(step);
        for (let i = idx; i < steps.length; i++) {
            const s = steps[i];
            this.$(`imp-step-${s}`)?.classList.add('imp-step--locked');
            const opts = this.$(`imp-opts-${s}`);
            if (opts) opts.innerHTML = `<div class="imp-ph">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                Choisissez d'abord ${s === 'promo' ? 'une filière' : 'une promotion'}
            </div>`;
            const lbl = this.$(`imp-bc-${s}-lbl`);
            if (lbl) lbl.textContent = s === 'promo' ? 'Promotion' : 'Classe';
            const item = this.$(`imp-bc-${s}`); if (item) item.classList.remove('active');
            if (s === 'promo')  this.state.promotionId = null;
            if (s === 'class')  { this.state.classroomId = null; this.$('imp-classroom-id').value = ''; }
        }
        this._checkSubmit();
    }
};

document.addEventListener('DOMContentLoaded', () => IMP.init());
</script>
@endsection
