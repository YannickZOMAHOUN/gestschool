@extends('layouts.template')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg border-0 rounded-3">

        {{-- EN-TÊTE --}}
        <div class="card-header bg-primary text-white rounded-top-3 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Saisie des Notes
                </h5>
                <div class="badge bg-light text-primary fs-6">
                    <i class="fas fa-calculator me-1"></i>Calculs automatiques activés
                </div>
            </div>
        </div>

        {{-- CORPS --}}
        <div class="card-body">

            {{-- FILTRES --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Année scolaire :</label>
                    <select id="year_id" class="form-select">
                        <option value="">-- Choisir --</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Filière :</label>
                    <select id="sector_id" class="form-select" disabled>
                        <option value="">-- Sélectionner l'année --</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Promotion :</label>
                    <select id="promotion_id" class="form-select" disabled>
                        <option value="">-- Sélectionner la filière --</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Classe :</label>
                    <select id="classroom_id" class="form-select" disabled>
                        <option value="">-- Sélectionner la promotion --</option>
                    </select>
                </div>
            </div>

            {{-- MATIÈRE & PARAMÈTRES --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Matière :</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="fas fa-book"></i></span>
                        <select id="subject_id" class="form-select" disabled>
                            <option value="">-- Sélectionner la classe --</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Coefficient :</label>
                    <input type="text" id="coefficient" class="form-control bg-light" readonly>
                </div>
                <input type="hidden" id="ratio_id">
                <input type="hidden" id="subject_real_id">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Semestre :</label>
                    <select id="semester" class="form-select">
                        <option value="1">Semestre 1</option>
                        <option value="2">Semestre 2</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="btn-load" class="btn btn-primary w-100" disabled>
                        <i class="fas fa-sync-alt me-2"></i>Charger les notes
                    </button>
                </div>
            </div>

            {{-- INSTRUCTIONS --}}
            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-info-circle me-2 fs-5"></i>
                <div>
                    <strong>Navigation rapide :</strong> Utilisez <kbd>TAB</kbd> ou <kbd>ENTRÉE</kbd> pour naviguer.
                    Un champ en-tête de colonne applique la valeur à toute la colonne.
                    Les notes sont sur <strong>/20</strong>.
                </div>
            </div>

            {{-- TABLEAU DES NOTES --}}
            <div class="table-responsive rounded border" id="notes-table-container" style="display: none;">
                <table class="table table-hover table-striped mb-0" id="notes-table">
                    <thead class="table-primary">
                        <tr>
                            <th width="4%">#</th>
                            <th width="16%">Nom</th>
                            <th width="16%">Prénom</th>

                            {{-- 3 champs interro --}}
                            @for($i = 1; $i <= 3; $i++)
                            <th width="9%" class="text-center">
                                <div class="fw-bold mb-1">Interro {{ $i }}</div>
                                <input type="number"
                                       class="form-control form-control-sm text-center header-input interro-header"
                                       data-index="{{ $i - 1 }}"
                                       placeholder="Tous"
                                       min="0" max="20" step="0.01"
                                       style="height:30px;">
                            </th>
                            @endfor

                            <th width="9%" class="text-center">
                                <div class="fw-bold mb-1">Moy Interro</div>
                                <div style="height:30px;line-height:30px;">Auto</div>
                            </th>

                            <th width="8%" class="text-center">
                                <div class="fw-bold mb-1">Devoir 1</div>
                                <input type="number"
                                       class="form-control form-control-sm text-center header-input devoir-header"
                                       data-field="devoir1"
                                       placeholder="Tous"
                                       min="0" max="20" step="0.01"
                                       style="height:30px;">
                            </th>
                            <th width="8%" class="text-center">
                                <div class="fw-bold mb-1">Devoir 2</div>
                                <input type="number"
                                       class="form-control form-control-sm text-center header-input devoir-header"
                                       data-field="devoir2"
                                       placeholder="Tous"
                                       min="0" max="20" step="0.01"
                                       style="height:30px;">
                            </th>

                            <th width="9%" class="text-center bg-success text-white">
                                <div class="fw-bold mb-1">Moy/20</div>
                                <div style="height:30px;line-height:30px;">Auto</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="notes-body"></tbody>
                </table>

                <div class="card-footer bg-light border-top-0 rounded-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="fas fa-users me-1"></i>
                            <span id="student-count">0</span> étudiant(s)
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="btn-reset">
                                <i class="fas fa-eraser me-2"></i>Réinitialiser
                            </button>
                            <button type="button" class="btn btn-success" id="btn-save" disabled>
                                <i class="fas fa-save me-2"></i>Enregistrer toutes les notes
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script>
class NotesManager {
    constructor() {
        this.currentData  = null;
        this.INTERRO_COUNT = 3;
        this.csrfToken    = '{{ csrf_token() }}';
        this.initializeElements();
        this.bindEvents();
        this.setupKeyboardNavigation();
    }

    // ----------------------------------------------------------
    // Troncage à 2 décimales SANS arrondi
    // Ex : 1.6666… → 1.66  (et non 1.67)
    // ----------------------------------------------------------
    trunc2(val) {
        return Math.floor(val * 100) / 100;
    }

    // ----------------------------------------------------------
    // Initialisation
    // ----------------------------------------------------------

    initializeElements() {
        this.el = {
            year:          document.getElementById('year_id'),
            sector:        document.getElementById('sector_id'),
            promotion:     document.getElementById('promotion_id'),
            classroom:     document.getElementById('classroom_id'),
            subject:       document.getElementById('subject_id'),
            coefficient:   document.getElementById('coefficient'),
            ratioId:       document.getElementById('ratio_id'),
            subjectRealId: document.getElementById('subject_real_id'),
            semester:      document.getElementById('semester'),
            btnLoad:       document.getElementById('btn-load'),
            btnSave:       document.getElementById('btn-save'),
            btnReset:      document.getElementById('btn-reset'),
            container:     document.getElementById('notes-table-container'),
            tbody:         document.getElementById('notes-body'),
            count:         document.getElementById('student-count'),
        };
    }

    bindEvents() {
        this.el.year.addEventListener('change',      () => this.loadSectors());
        this.el.sector.addEventListener('change',    () => this.loadPromotions());
        this.el.promotion.addEventListener('change', () => this.loadClassrooms());
        this.el.classroom.addEventListener('change', () => this.loadSubjects());
        this.el.subject.addEventListener('change',   () => { this.updateCoefficient(); this.updateLoadButton(); });
        this.el.semester.addEventListener('change',  () => this.updateLoadButton());
        this.el.btnLoad.addEventListener('click',    () => this.loadNotes());
        this.el.btnSave.addEventListener('click',    () => this.saveNotes());
        this.el.btnReset.addEventListener('click',   () => this.resetForm());
    }

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                const active = document.activeElement;
                if (active.classList.contains('note-input') || active.classList.contains('header-input')) {
                    e.preventDefault();
                    const inputs = [...document.querySelectorAll('.note-input:not(:disabled), .header-input:not(:disabled)')];
                    const idx    = inputs.indexOf(active);
                    if (idx < inputs.length - 1) inputs[idx + 1].focus();
                    else this.el.btnSave.focus();
                }
            }
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                this.saveNotes();
            }
        });
    }

    // ----------------------------------------------------------
    // Dropdowns hiérarchiques
    // ----------------------------------------------------------

    async fetchJson(url) {
        const res = await fetch(url);
        if (!res.ok) throw new Error('Erreur réseau : ' + res.status);
        return res.json();
    }

    async postJson(url, body) {
        const res = await fetch(url, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
            body:    JSON.stringify(body),
        });
        if (!res.ok) throw new Error('Erreur réseau : ' + res.status);
        return res.json();
    }

    async loadSectors() {
        const yearId = this.el.year.value;
        this.resetDownstream('year');
        if (!yearId) return;

        this.el.sector.innerHTML = '<option value="">Chargement…</option>';
        try {
            const data = await this.fetchJson(`/api/sectors-by-year/${yearId}`);
            this.populateSelect(this.el.sector, data, '-- Choisir une filière --');
            this.el.sector.disabled = false;
        } catch (e) {
            this.toast('error', 'Impossible de charger les filières.');
        }
    }

    async loadPromotions() {
        const yearId   = this.el.year.value;
        const sectorId = this.el.sector.value;
        this.resetDownstream('sector');
        if (!yearId || !sectorId) return;

        this.el.promotion.innerHTML = '<option value="">Chargement…</option>';
        try {
            const data = await this.fetchJson(`/api/promotions-by-year-sector/${yearId}/${sectorId}`);
            this.populateSelect(this.el.promotion, data, '-- Choisir une promotion --');
            this.el.promotion.disabled = false;
        } catch (e) {
            this.toast('error', 'Impossible de charger les promotions.');
        }
    }

    async loadClassrooms() {
        const promotionId = this.el.promotion.value;
        this.resetDownstream('promotion');
        if (!promotionId) return;

        this.el.classroom.innerHTML = '<option value="">Chargement…</option>';
        try {
            const data = await this.fetchJson(`/api/classes-by-promotion/${promotionId}`);
            this.populateSelect(this.el.classroom, data, '-- Choisir une classe --');
            this.el.classroom.disabled = false;
        } catch (e) {
            this.toast('error', 'Impossible de charger les classes.');
        }
    }

    async loadSubjects() {
        const classroomId = this.el.classroom.value;
        const yearId      = this.el.year.value;
        this.resetDownstream('classroom');
        if (!classroomId || !yearId) return;

        this.el.subject.innerHTML = '<option value="">Chargement…</option>';
        this.el.subject.disabled  = true;

        try {
            const data = await this.postJson('/api/subjects-by-classroom', {
                classroom_id: classroomId,
                year_id:      yearId,
            });

            this.el.subject.innerHTML = '<option value="">-- Choisir une matière --</option>';

            if (data.success && data.subjects?.length) {
                data.subjects.forEach(s => {
                    const opt               = document.createElement('option');
                    opt.value               = s.ratio_id;
                    opt.textContent         = `${s.subject_name} (Coeff : ${s.coefficient})`;
                    opt.dataset.coefficient = s.coefficient;
                    opt.dataset.subjectId   = s.subject_id;
                    this.el.subject.appendChild(opt);
                });
                this.el.subject.disabled = false;
            } else {
                this.toast('warning', 'Aucune matière disponible pour cette classe.');
            }
        } catch (e) {
            this.toast('error', 'Erreur lors du chargement des matières.');
            this.el.subject.innerHTML = '<option value="">-- Erreur --</option>';
        }

        this.updateLoadButton();
    }

    populateSelect(select, items, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(item => {
            const opt       = document.createElement('option');
            opt.value       = item.id;
            opt.textContent = item.name ?? item.promotion_sector ?? item.name_sector;
            select.appendChild(opt);
        });
    }

    // ----------------------------------------------------------
    // Coefficient & bouton Charger
    // ----------------------------------------------------------

    updateCoefficient() {
        const opt = this.el.subject.options[this.el.subject.selectedIndex];
        if (opt?.value) {
            this.el.coefficient.value   = opt.dataset.coefficient ?? '1';
            this.el.ratioId.value       = opt.value;
            this.el.subjectRealId.value = opt.dataset.subjectId ?? '';
        } else {
            this.el.coefficient.value   = '';
            this.el.ratioId.value       = '';
            this.el.subjectRealId.value = '';
        }
    }

    updateLoadButton() {
        this.el.btnLoad.disabled = !(
            this.el.classroom.value &&
            this.el.subject.value &&
            this.el.semester.value
        );
    }

    // ----------------------------------------------------------
    // Chargement des notes
    // ----------------------------------------------------------

    setLoadingState(loading) {
        if (loading) {
            this.el.btnLoad.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Chargement…';
            this.el.btnLoad.disabled  = true;
        } else {
            this.el.btnLoad.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Charger les notes';
            this.updateLoadButton();
        }
    }

    async loadNotes() {
        const classroomId = this.el.classroom.value;
        const yearId      = this.el.year.value;
        const ratioId     = this.el.ratioId.value;
        const semester    = this.el.semester.value;

        if (!classroomId || !yearId || !ratioId || !semester) {
            this.toast('warning', 'Veuillez remplir tous les champs.');
            return;
        }

        this.setLoadingState(true);
        try {
            const data = await this.postJson('/api/students-with-notes', {
                year_id:      yearId,
                classroom_id: classroomId,
                ratio_id:     ratioId,
                semester:     semester,
            });

            if (data.success) {
                this.currentData = data.students;
                this.renderTable();
                this.toast('success', `${data.students.length} étudiant(s) chargé(s).`);
            } else {
                this.toast('warning', data.message ?? 'Aucun étudiant trouvé.');
            }
        } catch (e) {
            this.toast('error', 'Erreur lors du chargement : ' + e.message);
        } finally {
            this.setLoadingState(false);
        }
    }

    // ----------------------------------------------------------
    // Rendu du tableau
    // ----------------------------------------------------------

    renderTable() {
        if (!this.currentData?.length) {
            this.el.tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="fas fa-users-slash fa-2x mb-3 d-block"></i>
                        Aucun étudiant trouvé pour cette classe.
                    </td>
                </tr>`;
            this.el.container.style.display = 'none';
            this.el.btnSave.disabled = true;
            return;
        }

        let html = '';
        this.currentData.forEach((student, idx) => {
            const disabled = student.is_disabled ? 'disabled' : '';
            const interros = student.interros ?? [];

            html += `
            <tr data-index="${idx}">
                <td class="text-center fw-bold">${idx + 1}</td>
                <td>${this.esc(student.name)}</td>
                <td>${this.esc(student.surname)}</td>`;

            // 3 champs interro
            for (let i = 0; i < this.INTERRO_COUNT; i++) {
                const val = interros[i] !== undefined
                    ? this.trunc2(parseFloat(interros[i])).toFixed(2)
                    : '';
                html += `
                <td class="text-center p-1">
                    <input type="number"
                           class="form-control form-control-sm text-center note-input interro-input"
                           data-index="${idx}" data-interro-index="${i}"
                           value="${val}" min="0" max="20" step="0.01"
                           placeholder="—" ${disabled}>
                </td>`;
            }

            // Moyenne interros
            const moyI = student.moy_interros ?? 0;
            html += `
                <td class="text-center bg-light fw-bold">
                    <span class="moy-interro" data-index="${idx}">${moyI > 0 || interros.length > 0 ? this.trunc2(parseFloat(moyI)).toFixed(2) : '—'}</span>
                </td>`;

            // Devoir 1 & 2
            ['devoir1', 'devoir2'].forEach(field => {
                const val = student[field] !== null && student[field] !== undefined
                    ? this.trunc2(parseFloat(student[field])).toFixed(2) : '';
                html += `
                <td class="text-center p-1">
                    <input type="number"
                           class="form-control form-control-sm text-center note-input devoir-input"
                           data-field="${field}" data-index="${idx}"
                           value="${val}" min="0" max="20" step="0.01"
                           placeholder="—" ${disabled}>
                </td>`;
            });

            // Moyenne /20
            const moy20 = student.moy_20;
            const colorClass = moy20 !== null ? (moy20 >= 10 ? 'bg-success' : 'bg-danger') : 'bg-secondary';
            const moy20Display = moy20 !== null ? this.trunc2(parseFloat(moy20)).toFixed(2) : '—';
            html += `
                <td class="text-center ${colorClass} text-white fw-bold">
                    <span class="moy-20" data-index="${idx}">${moy20Display}</span>
                </td>
            </tr>`;
        });

        this.el.tbody.innerHTML         = html;
        this.el.container.style.display = 'block';
        this.el.count.textContent       = this.currentData.length;
        this.el.btnSave.disabled        = false;

        this.bindInputEvents();
        this.bindHeaderEvents();
    }

    // ----------------------------------------------------------
    // Événements inputs
    // ----------------------------------------------------------

    bindInputEvents() {
        document.querySelectorAll('.interro-input, .devoir-input').forEach(input => {
            input.addEventListener('input', () => this.recalculate(input));
            input.addEventListener('blur',  () => this.formatInput(input));
        });
    }

    bindHeaderEvents() {
        document.querySelectorAll('.interro-header').forEach(h =>
            h.addEventListener('blur', () => this.applyHeader(h))
        );
        document.querySelectorAll('.devoir-header').forEach(h =>
            h.addEventListener('blur', () => this.applyHeader(h))
        );
    }

    applyHeader(header) {
        const raw = header.value.trim();
        if (raw === '') return;
        const value = parseFloat(raw);
        if (isNaN(value) || value < 0 || value > 20) return;

        if (header.classList.contains('interro-header')) {
            const i = parseInt(header.dataset.index);
            document.querySelectorAll(`.interro-input[data-interro-index="${i}"]:not(:disabled)`)
                .forEach(inp => { inp.value = this.trunc2(value).toFixed(2); this.recalculate(inp); });
        } else {
            const field = header.dataset.field;
            document.querySelectorAll(`.devoir-input[data-field="${field}"]:not(:disabled)`)
                .forEach(inp => { inp.value = this.trunc2(value).toFixed(2); this.recalculate(inp); });
        }
    }

    // ----------------------------------------------------------
    // Calcul des moyennes — troncage à 2 décimales (miroir du PHP)
    // ----------------------------------------------------------

    recalculate(changedInput) {
        const idx = parseInt(changedInput.dataset.index);
        const row = document.querySelector(`tr[data-index="${idx}"]`);
        if (!row) return;

        // Collecter les interros saisies (valeurs brutes pour les calculs)
        const interros = [];
        for (let i = 0; i < this.INTERRO_COUNT; i++) {
            const inp = row.querySelector(`.interro-input[data-interro-index="${i}"]`);
            if (inp && inp.value !== '' && !inp.disabled) {
                const v = parseFloat(inp.value);
                if (!isNaN(v)) interros.push(v);
            }
        }

        const getDevoir = (field) => {
            const inp = row.querySelector(`.devoir-input[data-field="${field}"]`);
            return (inp && inp.value !== '' && !inp.disabled) ? parseFloat(inp.value) : null;
        };

        const devoir1 = getDevoir('devoir1');
        const devoir2 = getDevoir('devoir2');

        // Moyenne interros — valeur brute (pas tronquée) pour les calculs intermédiaires
        const moyInterrosBrut = interros.length > 0
            ? interros.reduce((a, b) => a + b, 0) / interros.length
            : null;

        // Moyenne /20 : calcul sur valeurs brutes, troncage uniquement à la fin
        const composantes = [];
        if (moyInterrosBrut !== null) composantes.push(moyInterrosBrut);
        if (devoir1         !== null) composantes.push(devoir1);
        if (devoir2         !== null) composantes.push(devoir2);

        const moy20Brut = composantes.length > 0
            ? composantes.reduce((a, b) => a + b, 0) / composantes.length
            : null;

        // Troncage uniquement pour l'affichage
        const moyInterrosTronc = moyInterrosBrut !== null ? this.trunc2(moyInterrosBrut) : null;
        const moy20Tronc       = moy20Brut       !== null ? this.trunc2(moy20Brut)       : null;

        // Mise à jour de l'affichage
        row.querySelector('.moy-interro').textContent =
            moyInterrosTronc !== null ? moyInterrosTronc.toFixed(2) : '—';

        const moy20El = row.querySelector('.moy-20');
        moy20El.textContent = moy20Tronc !== null ? moy20Tronc.toFixed(2) : '—';

        const cell = moy20El.closest('td');
        cell.classList.remove('bg-success', 'bg-danger', 'bg-secondary');
        if (moy20Tronc !== null) cell.classList.add(moy20Tronc >= 10 ? 'bg-success' : 'bg-danger');
        else cell.classList.add('bg-secondary');

        // Mise à jour en mémoire (valeurs tronquées pour cohérence avec PHP)
        if (this.currentData[idx]) {
            this.currentData[idx].interros     = interros;
            this.currentData[idx].devoir1      = devoir1;
            this.currentData[idx].devoir2      = devoir2;
            this.currentData[idx].moy_interros = moyInterrosTronc;
            this.currentData[idx].moy_20       = moy20Tronc;
        }
    }

    formatInput(input) {
        if (input.value !== '' && !input.disabled) {
            const v = parseFloat(input.value);
            if (!isNaN(v)) {
                // Clamp entre 0 et 20, puis troncage
                input.value = this.trunc2(Math.min(Math.max(v, 0), 20)).toFixed(2);
            }
        }
    }

    // ----------------------------------------------------------
    // Sauvegarde — puis vidage des champs
    // ----------------------------------------------------------

    async saveNotes() {
        if (!this.currentData?.length) {
            this.toast('warning', 'Aucune donnée à sauvegarder.');
            return;
        }

        const payload = {
            year_id:      this.el.year.value,
            classroom_id: this.el.classroom.value,
            ratio_id:     this.el.ratioId.value,
            semester:     this.el.semester.value,
            notes: this.currentData.map(s => ({
                recording_id: s.recording_id,
                interros:     s.interros ?? [],
                devoir1:      s.devoir1 ?? null,
                devoir2:      s.devoir2 ?? null,
            })),
        };

        const orig = this.el.btnSave.innerHTML;
        this.el.btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sauvegarde…';
        this.el.btnSave.disabled  = true;

        try {
            const data = await this.postJson('/api/notes/bulk', payload);
            if (data.success) {
                this.toast('success', data.message);
                this.clearInputsAfterSave();
                setTimeout(() => this.loadNotes(), 1000);
            } else {
                throw new Error(data.message);
            }
        } catch (e) {
            this.toast('error', 'Erreur lors de la sauvegarde : ' + e.message);
        } finally {
            this.el.btnSave.innerHTML = orig;
            this.el.btnSave.disabled  = false;
        }
    }

    clearInputsAfterSave() {
        document.querySelectorAll('.note-input:not(:disabled)').forEach(inp => {
            inp.value = '';
        });
        document.querySelectorAll('.header-input').forEach(inp => {
            inp.value = '';
        });
        document.querySelectorAll('.moy-interro').forEach(el => {
            el.textContent = '—';
        });
        document.querySelectorAll('.moy-20').forEach(el => {
            el.textContent = '—';
            const cell = el.closest('td');
            cell.classList.remove('bg-success', 'bg-danger');
            cell.classList.add('bg-secondary');
        });

        if (this.currentData) {
            this.currentData.forEach(s => {
                s.interros     = [];
                s.devoir1      = null;
                s.devoir2      = null;
                s.moy_interros = null;
                s.moy_20       = null;
            });
        }
    }

    // ----------------------------------------------------------
    // Réinitialisation
    // ----------------------------------------------------------

    resetDownstream(from) {
        const chain = {
            year:      ['sector', 'promotion', 'classroom', 'subject'],
            sector:    ['promotion', 'classroom', 'subject'],
            promotion: ['classroom', 'subject'],
            classroom: ['subject'],
        };
        (chain[from] ?? []).forEach(key => {
            this.el[key].innerHTML = '<option value="">—</option>';
            this.el[key].disabled  = true;
        });
        this.clearTable();
        this.updateLoadButton();
    }

    clearTable() {
        this.currentData                = null;
        this.el.tbody.innerHTML         = '';
        this.el.container.style.display = 'none';
        this.el.count.textContent       = '0';
        this.el.btnSave.disabled        = true;
        this.el.coefficient.value       = '';
        this.el.ratioId.value           = '';
        this.el.subjectRealId.value     = '';
        document.querySelectorAll('.header-input').forEach(h => h.value = '');
    }

    resetForm() {
        this.el.year.value     = '';
        this.el.semester.value = '1';
        this.resetDownstream('year');
        this.toast('info', 'Formulaire réinitialisé.');
    }

    // ----------------------------------------------------------
    // Utilitaires
    // ----------------------------------------------------------

    esc(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    toast(icon, message) {
        if (typeof Swal !== 'undefined') {
            Swal.mixin({
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 3500, timerProgressBar: true,
            }).fire({ icon, title: message });
        } else {
            alert(`[${icon.toUpperCase()}] ${message}`);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.notesManager = new NotesManager();
});
</script>

<style>
.note-input, .header-input {
    text-align: center;
    font-weight: 500;
}
.note-input:focus, .header-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 .2rem rgba(13,110,253,.25);
}
.note-input:disabled { background-color: #f8f9fa; cursor: not-allowed; }
.header-input { background-color: rgba(255,255,255,.9); }
.table-primary th { background-color: #0d6efd; color: white; vertical-align: middle; }
kbd { background-color: #6c757d; color: white; padding: .2rem .4rem; border-radius: .25rem; font-size:.875em; }
</style>
@endsection
