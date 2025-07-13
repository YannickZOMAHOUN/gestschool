@extends('layouts.template')

@section('content')
<div class="container">
    <h4 class="my-3 font-medium text-color-avt">Le(s) Coefficient(s)</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow rounded p-4">
        <form method="POST" action="{{ route('ratio.store') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Année scolaire :</label>
                    <select name="year_id" id="year_id" class="form-control" required>
                        <option value="">-- Choisissez une année --</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Filière :</label>
                    <select id="sector_id" class="form-control" disabled required></select>
                </div>
                <div class="col-md-4">
                    <label>Promotion :</label>
                    <select name="promotion_id" id="promotion_id" class="form-control" disabled required></select>
                </div>
            </div>

            <div class="mb-3">
                <label>Coefficient commun :</label>
                <input type="number" min="1" class="form-control" id="globalCoefficient">
            </div>

            <div class="mb-3 text-end">
                <button type="button" class="btn btn-sm btn-outline-success me-2" onclick="selectAll()">Tout sélectionner</button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deselectAll()">Tout désélectionner</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Classes</th>
                            <th>Coefficient</th>
                        </tr>
                    </thead>
                    <tbody id="promotionBody">
                        <tr>
                            <td colspan="3" class="text-center text-muted">Sélectionnez une promotion</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Enregistrer</button>
        </form>
    </div>
</div>
@endsection

@section('another_JS')
<script>
const yearSelect = document.getElementById('year_id');
const sectorSelect = document.getElementById('sector_id');
const promotionSelect = document.getElementById('promotion_id');
const promotionBody = document.getElementById('promotionBody');
const globalCoefficient = document.getElementById('globalCoefficient');

// 🎯 Chargement des filières
yearSelect.addEventListener('change', () => {
    const yearId = yearSelect.value;
    sectorSelect.innerHTML = '<option>Chargement...</option>';
    sectorSelect.disabled = true;
    promotionSelect.innerHTML = '<option>-- Choisissez une promotion --</option>';
    promotionSelect.disabled = true;
    promotionBody.innerHTML = '<tr><td colspan="3" class="text-muted text-center">Sélectionnez une promotion</td></tr>';

    fetch(`/api/ratios/sectors/${yearId}`)
        .then(res => res.json())
        .then(data => {
            sectorSelect.innerHTML = '<option value="">-- Choisissez une filière --</option>';
            data.forEach(s => {
                sectorSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
            });
            sectorSelect.disabled = false;
        });
});

// 🎯 Chargement des promotions
sectorSelect.addEventListener('change', () => {
    const yearId = yearSelect.value;
    const sectorId = sectorSelect.value;
    promotionSelect.innerHTML = '<option>Chargement...</option>';
    promotionSelect.disabled = true;
    promotionBody.innerHTML = '<tr><td colspan="3" class="text-muted text-center">Sélectionnez une promotion</td></tr>';

    fetch(`/api/ratios/promotions/${yearId}/${sectorId}`)
        .then(res => res.json())
        .then(data => {
            promotionSelect.innerHTML = '<option value="">-- Choisissez une promotion --</option>';
            data.forEach(p => {
                promotionSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
            });
            promotionSelect.disabled = false;
        });
});

// 🎯 Chargement matières + classes + coefficients
promotionSelect.addEventListener('change', () => {
    const yearId = yearSelect.value;
    const promotionId = promotionSelect.value;
    promotionBody.innerHTML = '<tr><td colspan="3">Chargement...</td></tr>';

    fetch(`/api/ratios/data/${promotionId}/${yearId}`)
        .then(res => res.json())
        .then(data => {
            const ratios = data.ratios;
            let html = '';

            data.subjects.forEach((subject, index) => {
                const ratioPerSubject = ratios.filter(r => r.subject_id === subject.id);
                const classroomMap = {};
                ratioPerSubject.forEach(r => {
                    classroomMap[r.classroom_id] = r.coefficient;
                });

                html += `<tr>
                    <td>
                        ${subject.name}
                        <input type="hidden" name="ratios[${index}][subject_id]" value="${subject.id}">
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            ${data.classes.map(c => {
                                const isChecked = classroomMap[c.id] !== undefined ? 'checked' : '';
                                return `<label class="me-2">
                                    <input type="checkbox" class="checkbox-global" name="ratios[${index}][classroom_ids][]" value="${c.id}" ${isChecked}>
                                    ${c.name}
                                </label>`;
                            }).join('')}
                        </div>
                    </td>
                    <td>
                        <input type="number" name="ratios[${index}][coefficient]" class="form-control coeff-field" min="1"
                            value="${ratioPerSubject[0]?.coefficient || ''}">
                    </td>
                </tr>`;
            });

            promotionBody.innerHTML = html;
        });
});

// 🎯 Coefficient commun
globalCoefficient.addEventListener('input', () => {
    const value = globalCoefficient.value;
    document.querySelectorAll('.coeff-field').forEach(input => input.value = value);
});

// ✅ Tout sélectionner ou désélectionner globalement
function selectAll() {
    document.querySelectorAll('.checkbox-global').forEach(cb => cb.checked = true);
}

function deselectAll() {
    document.querySelectorAll('.checkbox-global').forEach(cb => cb.checked = false);
}
</script>
@endsection
