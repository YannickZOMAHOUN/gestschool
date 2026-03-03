@extends('layouts.template')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --sy-primary: #4F46E5;
        --sy-primary-hover: #4338CA;
        --sy-bg: #F8FAFC;
        --sy-card-bg: #FFFFFF;
        --sy-text-main: #1E293B;
        --sy-text-muted: #64748B;
        --sy-border: #E2E8F0;
        --sy-success: #10B981;
        --sy-danger: #EF4444;
        --sy-warning: #F59E0B;
        --sy-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .sy-wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--sy-bg);
        min-height: 100vh;
        padding: 2rem;
        color: var(--sy-text-main);
    }

    /* Header */
    .sy-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
    }

    .sy-title { font-size: 1.875rem; font-weight: 700; letter-spacing: -0.025em; margin-bottom: 0.25rem; }
    .sy-subtitle { color: var(--sy-text-muted); font-size: 0.95rem; }

    /* Cards */
    .sy-card {
        background: var(--sy-card-bg);
        border-radius: 1rem;
        border: 1px solid var(--sy-border);
        box-shadow: var(--sy-shadow);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    /* Grid & Items */
    .sy-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-top: 1.5rem;
    }

    .sy-sector-card {
        position: relative;
        border: 2px solid var(--sy-border);
        border-radius: 0.75rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .sy-sector-card:hover:not(.is-locked) {
        border-color: var(--sy-primary);
        background: #F5F3FF;
        transform: translateY(-2px);
    }

    .sy-sector-card.is-active {
        border-color: var(--sy-primary);
        background: #EEF2FF;
    }

    .sy-sector-card.is-locked {
        background: #F1F5F9;
        cursor: not-allowed;
        opacity: 0.8;
    }

    /* Custom Checkbox */
    .sy-checkbox-custom {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        border: 2px solid var(--sy-border);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .is-active .sy-checkbox-custom {
        background: var(--sy-primary);
        border-color: var(--sy-primary);
    }

    .is-active .sy-checkbox-custom::after {
        content: "\f00c";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: white;
        font-size: 10px;
    }

    .is-locked .sy-checkbox-custom {
        background: var(--sy-text-muted);
        border-color: var(--sy-text-muted);
    }

    .is-locked .sy-checkbox-custom::after {
        content: "\f023";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: white;
        font-size: 10px;
    }

    /* Toolbar & Search */
    .sy-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid var(--sy-border);
        margin-bottom: 1rem;
    }

    .sy-search-box {
        position: relative;
        flex: 1;
        min-width: 250px;
    }

    .sy-search-box input {
        width: 100%;
        padding: 0.6rem 1rem 0.6rem 2.5rem;
        border-radius: 0.5rem;
        border: 1px solid var(--sy-border);
        outline: none;
    }

    .sy-search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--sy-text-muted);
    }

    /* Buttons */
    .sy-btn-primary {
        background: var(--sy-primary);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .sy-btn-primary:disabled {
        background: var(--sy-border);
        cursor: not-allowed;
    }

    .sy-btn-primary:not(:disabled):hover {
        background: var(--sy-primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    /* Badge */
    .sy-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 1rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .badge-locked { background: #E2E8F0; color: #475569; }
    .badge-new { background: #DCFCE7; color: #166534; }
    .badge-existing { background: #DBEAFE; color: #1E40AF; }

    /* Animations */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .sy-sector-card { animation: fadeIn 0.3s ease forwards; }
</style>

<div class="sy-wrapper">
    <div class="sy-header">
        <div>
            <h1 class="sy-title">Configuration des Filières</h1>
            <p class="sy-subtitle">Associez vos programmes aux années scolaires en un clic.</p>
        </div>
        <a href="{{ route('sector.create') }}" class="sy-btn-primary" style="background: white; color: var(--sy-primary); border: 1px solid var(--sy-primary);">
            <i class="fas fa-plus"></i> Créer une filière
        </a>
    </div>

    <form action="{{ route('sectorbyyear.store') }}" method="POST" id="sectorForm">
        @csrf

        <div class="sy-card">
            <label class="sy-label" style="font-weight: 600; margin-bottom: 0.5rem; display: block;">
                <i class="fas fa-calendar-alt text-primary"></i> Sélectionner l'année scolaire
            </label>
            <select name="year" id="year" class="form-control form-select-lg" required style="border-radius: 0.5rem;">
                <option value="">— Choisir une année —</option>
                @foreach ($years as $year)
                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                @endforeach
            </select>
        </div>

        <div class="sy-card" id="sectorsContainer" style="display:none; min-height: 400px;">
            <div class="sy-toolbar">
                <div class="sy-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Rechercher une filière (ex: Informatique)...">
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAll">Tout cocher</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnUnselectAll">Tout décocher</button>
                </div>
            </div>

            <div id="sectorsGrid" class="sy-grid">
                </div>

            <div id="gridLoader" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Synchronisation des données...</p>
            </div>
        </div>

        <div class="sy-card mt-4 d-flex justify-content-between align-items-center" id="actionBar" style="display: none !important;">
            <div id="selectionSummary" class="text-muted small">
                <strong>0</strong> filière(s) sélectionnée(s)
            </div>
            <button type="submit" class="sy-btn-primary" id="submitBtn" disabled>
                <i class="fas fa-save"></i>
                <span id="btnText">Enregistrer les modifications</span>
            </button>
        </div>
    </form>
</div>

@endsection

@section('another_JS')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const yearSelect = document.getElementById('year');
    const sectorsContainer = document.getElementById('sectorsContainer');
    const sectorsGrid = document.getElementById('sectorsGrid');
    const gridLoader = document.getElementById('gridLoader');
    const submitBtn = document.getElementById('submitBtn');
    const actionBar = document.getElementById('actionBar');
    const searchInput = document.getElementById('searchInput');

    let allSectors = [];

    // 1. Chargement des données
    yearSelect.addEventListener('change', function() {
        const yearId = this.value;
        if (!yearId) {
            sectorsContainer.style.display = 'none';
            actionBar.style.setProperty('display', 'none', 'important');
            return;
        }

        sectorsContainer.style.display = 'block';
        sectorsGrid.style.display = 'none';
        gridLoader.style.display = 'block';
        actionBar.style.setProperty('display', 'none', 'important');

        fetch(`/sectors/by-year/${yearId}`)
            .then(res => res.json())
            .then(data => {
                allSectors = data.sectors.map(s => ({
                    ...s,
                    isSelected: data.selected.includes(s.id) || data.locked.includes(s.id),
                    isLocked: data.locked.includes(s.id),
                    isExisting: data.selected.includes(s.id) && !data.locked.includes(s.id)
                }));
                renderGrid();
            })
            .finally(() => {
                gridLoader.style.display = 'none';
                sectorsGrid.style.display = 'grid';
                actionBar.style.setProperty('display', 'flex', 'important');
            });
    });

    // 2. Rendu de la grille
    function renderGrid(filter = '') {
        sectorsGrid.innerHTML = '';
        const filtered = allSectors.filter(s => s.name_sector.toLowerCase().includes(filter.toLowerCase()));

        filtered.forEach(s => {
            const card = document.createElement('div');
            card.className = `sy-sector-card ${s.isSelected ? 'is-active' : ''} ${s.isLocked ? 'is-locked' : ''}`;

            let badge = s.isLocked ? '<span class="sy-badge badge-locked">Verrouillée</span>' :
                        (s.isExisting ? '<span class="sy-badge badge-existing">Active</span>' : '<span class="sy-badge badge-new">Libre</span>');

            card.innerHTML = `
                <div class="sy-checkbox-custom"></div>
                <div style="flex:1">
                    <div style="font-weight:600; font-size:0.95rem">${s.name_sector}</div>
                    ${badge}
                </div>
                <input type="checkbox" name="sectors[]" value="${s.id}"
                       ${s.isSelected ? 'checked' : ''}
                       ${s.isLocked ? 'disabled' : ''}
                       style="display:none" class="sector-checkbox">
            `;

            if (!s.isLocked) {
                card.onclick = () => toggleSector(s.id);
            }
            sectorsGrid.appendChild(card);
        });
        updateUI();
    }

    // 3. Logique de sélection
    function toggleSector(id) {
        const sector = allSectors.find(s => s.id === id);
        if (sector && !sector.isLocked) {
            sector.isSelected = !sector.isSelected;
            renderGrid(searchInput.value);
        }
    }

    function updateUI() {
        const selectedCount = allSectors.filter(s => s.isSelected).length;
        document.getElementById('selectionSummary').innerHTML = `<strong>${selectedCount}</strong> filière(s) sélectionnée(s)`;
        submitBtn.disabled = selectedCount === 0;
    }

    // 4. Recherche & Actions groupées
    searchInput.oninput = (e) => renderGrid(e.target.value);

    document.getElementById('btnSelectAll').onclick = () => {
        allSectors.forEach(s => s.isSelected = true);
        renderGrid();
    };

    document.getElementById('btnUnselectAll').onclick = () => {
        allSectors.forEach(s => { if(!s.isLocked) s.isSelected = false; });
        renderGrid();
    };

    // 5. Envoi du formulaire (Gestion des champs Disabled)
    document.getElementById('sectorForm').onsubmit = function() {
        // Pour chaque filière verrouillée, on ajoute un champ hidden car les inputs disabled ne partent pas en POST
        allSectors.filter(s => s.isLocked).forEach(s => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'sectors[]';
            hidden.value = s.id;
            this.appendChild(hidden);
        });

        submitBtn.disabled = true;
        document.getElementById('btnText').innerText = "Enregistrement...";
    };
});
</script>
@endsection
