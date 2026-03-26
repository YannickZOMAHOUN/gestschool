@extends('layouts.template')

@section('another_CSS')
<style>
    :root {
        --primary:   #2563eb;
        --primary-dk:#1d4ed8;
        --success:   #16a34a;
        --danger:    #dc2626;
        --warning:   #d97706;
        --border:    #e2e8f0;
        --bg:        #f8fafc;
        --text:      #1e293b;
        --muted:     #64748b;
        --radius:    10px;
        --shadow:    0 2px 16px rgba(0,0,0,.07);
    }

    /* ── Layout ── */
    .page-header {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.5rem;
    }
    .page-header h4 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    .page-header p {
        font-size: .82rem;
        color: var(--muted);
        margin: 0;
    }

    /* ── Card ── */
    .yr-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.75rem;
    }
    .yr-card-header {
        padding: .9rem 1.25rem;
        border-bottom: 1px solid var(--border);
        font-weight: 600;
        font-size: .95rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .yr-card-body { padding: 1.5rem 1.25rem; }

    /* ── Form ── */
    .form-label {
        font-weight: 600;
        font-size: .875rem;
        color: var(--text);
        display: block;
        margin-bottom: .4rem;
    }
    .form-control {
        width: 100%;
        height: 42px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 0 12px;
        font-size: .92rem;
        transition: border-color .2s, box-shadow .2s;
        color: var(--text);
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37,99,235,.13);
    }
    .form-control.is-invalid { border-color: var(--danger); }
    .invalid-feedback { font-size: .8rem; color: var(--danger); margin-top: .3rem; }

    /* ── Buttons ── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem 1.1rem;
        border-radius: 7px;
        font-size: .875rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
        text-decoration: none;
    }
    .btn:hover { opacity: .88; transform: translateY(-1px); }
    .btn:active { transform: none; }
    .btn-primary   { background: var(--primary);   color: #fff; }
    .btn-secondary { background: #94a3b8;           color: #fff; }
    .btn-danger    { background: var(--danger);     color: #fff; }
    .btn-success   { background: var(--success);    color: #fff; }
    .btn-sm { padding: .3rem .75rem; font-size: .8rem; }

    /* ── Alerts ── */
    .alert {
        padding: .75rem 1rem;
        border-radius: 8px;
        font-size: .875rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .alert-success { background: #dcfce7; color: #166534; border-left: 4px solid var(--success); }
    .alert-danger  { background: #fee2e2; color: #991b1b; border-left: 4px solid var(--danger);  }

    /* ── Table ── */
    .yr-table-wrapper {
        border-radius: var(--radius);
        overflow: hidden;
        border: 1px solid var(--border);
    }
    .yr-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .9rem;
    }
    .yr-table thead th {
        background: var(--primary);
        color: #fff;
        padding: 12px 16px;
        font-weight: 600;
        font-size: .82rem;
        letter-spacing: .03em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .yr-table tbody tr { transition: background .12s; }
    .yr-table tbody tr:hover { background: var(--bg); }
    .yr-table tbody td {
        padding: 12px 16px;
        border-top: 1px solid var(--border);
        color: var(--text);
        vertical-align: middle;
    }

    /* ── Year name ── */
    .year-name {
        font-weight: 600;
        font-size: .95rem;
    }

    /* ── Badge statut ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .28rem .75rem;
        border-radius: 20px;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .02em;
    }
    .status-active   { background: #dcfce7; color: #166534; }
    .status-inactive { background: #f1f5f9; color: #64748b; }
    .status-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .status-active   .status-dot { background: var(--success); }
    .status-inactive .status-dot { background: #94a3b8; }

    /* ── Action icons ── */
    .action-group { display: flex; gap: 6px; justify-content: center; }
    .icon-btn {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: transform .15s, opacity .15s;
        background: transparent;
    }
    .icon-btn:hover { transform: scale(1.12); opacity: .85; }
    .icon-btn.ib-edit    { background: #eff6ff; color: var(--primary); }
    .icon-btn.ib-delete  { background: #fef2f2; color: var(--danger);  }
    .icon-btn.ib-activate { background: #f0fdf4; color: var(--success); }
    .icon-btn.ib-disable  { background: #fff7ed; color: var(--warning); }

    /* ── Search bar ── */
    .table-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .75rem 1rem;
        border-bottom: 1px solid var(--border);
        gap: 1rem;
        flex-wrap: wrap;
    }
    .search-box {
        position: relative;
        max-width: 260px;
        width: 100%;
    }
    .search-box input {
        height: 36px;
        width: 100%;
        border: 1.5px solid var(--border);
        border-radius: 7px;
        padding: 0 12px 0 34px;
        font-size: .85rem;
        color: var(--text);
    }
    .search-box input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }
    .search-icon {
        position: absolute;
        left: 10px; top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        pointer-events: none;
    }
    .table-count { font-size: .82rem; color: var(--muted); }

    /* ── Pagination ── */
    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .6rem 1rem;
        border-top: 1px solid var(--border);
        font-size: .82rem;
        color: var(--muted);
        flex-wrap: wrap;
        gap: .5rem;
    }
    .pag-btns { display: flex; gap: 4px; }
    .pag-btn {
        min-width: 30px; height: 30px;
        border: 1.5px solid var(--border);
        border-radius: 6px;
        background: #fff;
        font-size: .8rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background .12s;
        padding: 0 6px;
    }
    .pag-btn:hover { background: var(--bg); }
    .pag-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }
    .pag-btn:disabled { opacity: .4; cursor: not-allowed; }

    /* ── Modal ── */
    .modal-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff;
        border-radius: 12px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: fadeUp .2s ease;
        overflow: hidden;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .modal-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 700;
        font-size: .95rem;
        color: var(--text);
    }
    .modal-close-btn {
        background: none; border: none; cursor: pointer;
        color: var(--muted); font-size: 1.1rem; padding: 2px;
    }
    .modal-body  { padding: 1.25rem; font-size: .9rem; color: var(--text); line-height: 1.6; }
    .modal-body .highlight { font-weight: 700; color: var(--primary); }
    .modal-body .warn-note {
        margin-top: .75rem;
        padding: .5rem .75rem;
        background: #fffbeb;
        border-left: 3px solid var(--warning);
        border-radius: 6px;
        font-size: .82rem;
        color: #92400e;
    }
    .modal-foot {
        padding: .9rem 1.25rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: .6rem;
    }

    /* ── Responsive ── */
    @media (max-width: 600px) {
        .yr-table thead { display: none; }
        .yr-table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            border-top: none;
            font-size: .85rem;
        }
        .yr-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--muted);
            font-size: .78rem;
        }
        .yr-table tbody tr { border-top: 1px solid var(--border); }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width:900px;">

    {{-- ── Alerts ── --}}
    @if(session('success'))
    <div class="alert alert-success">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Page header ── --}}
    <div class="page-header">
        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <div>
            <h4>Années Scolaires</h4>
            <p>Gestion et configuration des années scolaires</p>
        </div>
    </div>

    {{-- ── Formulaire de création ── --}}
    <div class="yr-card">
        <div class="yr-card-header">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nouvelle année scolaire
        </div>
        <div class="yr-card-body">
            <form action="{{ route('year.store') }}" method="POST" novalidate>
                @csrf
                <div style="display:flex; gap:1rem; align-items:flex-end; flex-wrap:wrap;">
                    <div style="flex:1; min-width:200px;">
                        <label for="year" class="form-label">
                            Intitulé de l'année
                            <span style="color:var(--danger)">*</span>
                        </label>
                        <input
                            type="text"
                            name="year"
                            id="year"
                            class="form-control @error('year') is-invalid @enderror"
                            placeholder="ex : 2024-2025"
                            value="{{ old('year') }}"
                            autocomplete="off"
                        >
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="display:flex; gap:.6rem; padding-bottom: @error('year') 1.4rem @else 0 @enderror;">
                        <button type="reset" class="btn btn-secondary">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tableau des années ── --}}
    <div class="yr-card">
        <div class="table-toolbar">
            <div class="search-box">
                <svg class="search-icon" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Rechercher une année…">
            </div>
            <span class="table-count" id="tableCount"></span>
        </div>

        <div class="yr-table-wrapper">
            <table class="yr-table" id="yearsTable">
                <thead>
                    <tr>
                        <th>Année scolaire</th>
                        <th style="text-align:center;">Statut</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="yearsBody">
                @forelse($years as $year)
                <tr data-year="{{ strtolower($year->year) }}">
                    <td data-label="Année">
                        <span class="year-name">{{ $year->year }}</span>
                    </td>
                    <td data-label="Statut" style="text-align:center;">
                        @if($year->status)
                            <span class="status-badge status-active">
                                <span class="status-dot"></span> Active
                            </span>
                        @else
                            <span class="status-badge status-inactive">
                                <span class="status-dot"></span> Inactive
                            </span>
                        @endif
                    </td>
                    <td data-label="Actions">
                        <div class="action-group">
                            {{-- Éditer --}}
                            <a href="{{ route('year.edit', $year) }}" class="icon-btn ib-edit" title="Modifier">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.768-6.768a2 2 0 012.828 2.828L11.828 15.828 8 17l1.172-3.828z"/></svg>
                            </a>

                            {{-- Activer / Désactiver --}}
                            @if($year->status)
                                <button
                                    class="icon-btn ib-disable"
                                    title="Désactiver"
                                    onclick="openToggleModal({{ $year->id }}, '{{ $year->year }}', false)"
                                >
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                </button>
                            @else
                                <button
                                    class="icon-btn ib-activate"
                                    title="Activer"
                                    onclick="openToggleModal({{ $year->id }}, '{{ $year->year }}', true)"
                                >
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                            @endif

                            {{-- Supprimer --}}
                            @if(!$year->status)
                                <button
                                    class="icon-btn ib-delete"
                                    title="Supprimer"
                                    onclick="openDeleteModal({{ $year->id }}, '{{ $year->year }}')"
                                >
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            @else
                                {{-- Icône suppression désactivée pour l'année active --}}
                                <span class="icon-btn" style="opacity:.3;cursor:not-allowed;" title="Impossible de supprimer l'année active">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="3" style="text-align:center; padding:2.5rem; color:var(--muted);">
                        Aucune année scolaire enregistrée.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap" id="paginationWrap"></div>
    </div>

</div>

{{-- ══ Modal : Activer / Désactiver ══ --}}
<div class="modal-overlay" id="toggleModal">
    <div class="modal-box">
        <div class="modal-head">
            <span id="toggleModalTitle">Confirmation</span>
            <button class="modal-close-btn" onclick="closeModal('toggleModal')">&#10005;</button>
        </div>
        <div class="modal-body">
            <span id="toggleModalMsg"></span>
            <div class="warn-note" id="toggleWarnNote" style="display:none;">
                ⚠️ Toutes les autres années scolaires seront automatiquement <strong>désactivées</strong>.
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="closeModal('toggleModal')">Annuler</button>
            <a id="toggleConfirmBtn" href="#" class="btn btn-success">Confirmer</a>
        </div>
    </div>
</div>

{{-- ══ Modal : Supprimer ══ --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-head" style="color:var(--danger);">
            <span>Confirmer la suppression</span>
            <button class="modal-close-btn" onclick="closeModal('deleteModal')">&#10005;</button>
        </div>
        <div class="modal-body">
            Voulez-vous vraiment supprimer l'année <span class="highlight" id="deleteYearName"></span> ?
            <div class="warn-note">⚠️ Cette action est irréversible.</div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="closeModal('deleteModal')">Annuler</button>
            <form id="deleteForm" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
        </div>
    </div>
</div>

{{-- Routes JSON pour JS (évite les interpolations dans les boucles) --}}
<script>
    const ROUTES = {
        activate: '/years/__ID__/activate',   // adapter selon web.php
        disable:  '/years/__ID__/disable',
        destroy:  '/years/__ID__',
    };
    // Surcharger avec les vraies routes Laravel si les URLs diffèrent
    ROUTES.activate = '{{ route("activate_year", "__ID__") }}'.replace('%2F__ID__%2F','/__ID__/');
    ROUTES.disable  = '{{ route("disable_year",  "__ID__") }}'.replace('%2F__ID__%2F','/__ID__/');
    ROUTES.destroy  = '{{ url("years/__ID__") }}';
</script>
@endsection

@section('another_JS')
<script>
(function () {
    /* ── Modal helpers ── */
    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    window.closeModal = closeModal;

    // Fermer en cliquant sur l'overlay
    document.querySelectorAll('.modal-overlay').forEach(function(el) {
        el.addEventListener('click', function(e) {
            if (e.target === el) closeModal(el.id);
        });
    });

    /* ── Toggle modal (activer / désactiver) ── */
    window.openToggleModal = function(id, yearName, activate) {
        var msg = document.getElementById('toggleModalMsg');
        var note = document.getElementById('toggleWarnNote');
        var title = document.getElementById('toggleModalTitle');
        var btn = document.getElementById('toggleConfirmBtn');

        title.textContent = activate ? 'Activer l\'année scolaire' : 'Désactiver l\'année scolaire';
        msg.innerHTML = 'Voulez-vous vraiment ' +
            (activate ? '<strong>activer</strong>' : '<strong>désactiver</strong>') +
            ' l\'année <strong>' + yearName + '</strong> ?';

        note.style.display = activate ? '' : 'none';
        btn.href = (activate ? ROUTES.activate : ROUTES.disable).replace('__ID__', id);
        openModal('toggleModal');
    };

    /* ── Delete modal ── */
    window.openDeleteModal = function(id, yearName) {
        document.getElementById('deleteYearName').textContent = yearName;
        document.getElementById('deleteForm').action = ROUTES.destroy.replace('__ID__', id);
        openModal('deleteModal');
    };

    /* ── Search + pagination ── */
    var rows = Array.from(document.querySelectorAll('#yearsBody tr[data-year]'));
    var perPage = 10;
    var currentPage = 1;
    var filtered = rows;

    function render() {
        // Hide all
        rows.forEach(function(r) { r.style.display = 'none'; });

        var start = (currentPage - 1) * perPage;
        var pageRows = filtered.slice(start, start + perPage);
        pageRows.forEach(function(r) { r.style.display = ''; });

        // Count
        document.getElementById('tableCount').textContent =
            filtered.length + ' année' + (filtered.length !== 1 ? 's' : '');

        // Pagination
        renderPagination();
    }

    function renderPagination() {
        var wrap = document.getElementById('paginationWrap');
        var total = Math.ceil(filtered.length / perPage);
        if (total <= 1) { wrap.innerHTML = ''; return; }

        var html = '<span>' + ((currentPage-1)*perPage+1) + '–' +
            Math.min(currentPage*perPage, filtered.length) + ' sur ' + filtered.length + '</span>';
        html += '<div class="pag-btns">';
        html += '<button class="pag-btn" onclick="goPage(' + (currentPage-1) + ')"' +
            (currentPage===1 ? ' disabled' : '') + '>&#8249;</button>';
        for (var i = 1; i <= total; i++) {
            html += '<button class="pag-btn' + (i===currentPage ? ' active' : '') +
                '" onclick="goPage(' + i + ')">' + i + '</button>';
        }
        html += '<button class="pag-btn" onclick="goPage(' + (currentPage+1) + ')"' +
            (currentPage===total ? ' disabled' : '') + '>&#8250;</button>';
        html += '</div>';
        wrap.innerHTML = html;
    }

    window.goPage = function(p) {
        var total = Math.ceil(filtered.length / perPage);
        if (p < 1 || p > total) return;
        currentPage = p;
        render();
    };

    document.getElementById('searchInput').addEventListener('input', function() {
        var q = this.value.toLowerCase().trim();
        filtered = rows.filter(function(r) {
            return r.getAttribute('data-year').includes(q);
        });
        currentPage = 1;
        render();
    });

    // Init
    render();
})();
</script>
@endsection
