@extends('layouts.template')

@section('another_CSS')
<link rel="stylesheet" href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}">
@endsection

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center pb-5">
        <div class="col-12">
            <h4 class="my-3 font-medium text-color-avt">Promotions(s) disponibles</h4>
            <div class="card shadow rounded p-4">
                <form action="{{ route('promotionbysector.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="year" class="form-label fs-6 text-label">Année Scolaire</label>
                        <select class="form-select bg-form" name="year" id="year" required>
                            <option value="" selected disabled>Choisissez l'année scolaire</option>
                            @foreach ($years as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-flex gap-2">
                        <button type="button" id="selectAll" class="btn btn-sm btn-outline-primary d-none">Tout Sélectionner</button>
                        <button type="button" id="unselectAll" class="btn btn-sm btn-outline-danger d-none">Tout Décocher</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Filière</th>
                                    <th>Promotions</th>
                                </tr>
                            </thead>
                            <tbody id="sectorTableBody">
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Veuillez sélectionner une année scolaire.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('another_JS')
<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log('JS chargé');
    const yearSelect = document.getElementById('year');
    const tableBody = document.getElementById('sectorTableBody');
    const selectAll = document.getElementById('selectAll');
    const unselectAll = document.getElementById('unselectAll');
    const submitBtn = document.getElementById('submitBtn');

    function slugify(text) {
        return text.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, '-');
    }

    function toggleSubmit() {
        submitBtn.disabled = document.querySelectorAll('.promotion-checkbox:checked').length === 0;
    }

    yearSelect.addEventListener('change', () => {
        const yearId = yearSelect.value;
        console.log("Année choisie :", yearId);
        if (!yearId) return;

        tableBody.innerHTML = `<tr><td colspan="2" class="text-center text-info">Chargement...</td></tr>`;

        fetch(`/promotion-sectors/${yearId}`)
            .then(res => res.json())
            .then(data => {
                const sectors = data.sectors;
                const selected = data.registeredPromotions;

                if (!sectors.length) {
                    tableBody.innerHTML = `<tr><td colspan="2" class="text-center text-warning">Aucune filière pour cette année.</td></tr>`;
                    return;
                }

                tableBody.innerHTML = '';
                sectors.forEach(sector => {
                    const sectorName = sector.name_sector;
                    const sectorId = sector.id;
                    const row = document.createElement('tr');

                    const promotionsHtml = ['2nde', '1ère', 'Tle'].map(promotion => {
                        const value = `${promotion} ${sectorName}`;
                        const id = slugify(value + '-' + sectorId);
                        const isChecked = selected.includes(value) ? 'checked' : '';
                        return `
                            <div class="form-check form-check-inline">
                                <input class="form-check-input promotion-checkbox"
                                       type="checkbox"
                                       name="sector_year_ids[${sectorId}][]"
                                       id="${id}"
                                       value="${value}"
                                       ${isChecked}>
                                <label class="form-check-label" for="${id}">${promotion}</label>
                            </div>
                        `;
                    }).join('');

                    row.innerHTML = `
                        <td>${sectorName}</td>
                        <td>${promotionsHtml}</td>
                    `;

                    tableBody.appendChild(row);
                });

                selectAll.classList.remove('d-none');
                unselectAll.classList.remove('d-none');
                toggleSubmit();
            });
    });

    selectAll.addEventListener('click', () => {
        document.querySelectorAll('.promotion-checkbox').forEach(cb => cb.checked = true);
        toggleSubmit();
    });

    unselectAll.addEventListener('click', () => {
        document.querySelectorAll('.promotion-checkbox').forEach(cb => cb.checked = false);
        toggleSubmit();
    });

    document.addEventListener('change', e => {
        if (e.target.classList.contains('promotion-checkbox')) {
            toggleSubmit();
        }
    });
});
</script>
@endsection
