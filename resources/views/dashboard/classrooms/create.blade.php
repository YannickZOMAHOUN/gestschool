@extends('layouts.template')

@section('content')
<div class="container">
    <h3 class="mb-4">Créer les classes</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow rounded p-4">
        <form method="POST" action="{{ route('promotion-classrooms.store') }}">
            @csrf
            <div class="row">
                <div class="col-12 col-md-4 mb-3">
                    <label for="year_id">Année scolaire:</label>
                    <select name="year_id" id="year_id" class="form-control" required>
                        <option value="">-- Choisissez une année --</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <label for="sector_id">Filière:</label>
                    <select name="sector_id" id="sector_id" class="form-control" required disabled>
                        <option value="">-- Choisir l’année d’abord --</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <label for="general_count">Nombre général de classes :</label>
                    <input type="number" id="general_count" class="form-control" placeholder="Ex: 2">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Promotion</th>
                            <th>Nombre de classes</th>
                        </tr>
                    </thead>
                    <tbody id="promotionBody">
                        <tr>
                            <td colspan="2" class="text-center text-muted">Sélectionnez une année et une filière</td>
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
    const promotionBody = document.getElementById('promotionBody');
    const generalCount = document.getElementById('general_count');

    yearSelect.addEventListener('change', () => {
        const yearId = yearSelect.value;
        if (!yearId) return;

        sectorSelect.innerHTML = `<option>Chargement...</option>`;
        sectorSelect.disabled = true;

        fetch(`/api/classroom-sectors-by-year/${yearId}`)
            .then(res => res.json())
            .then(data => {
                let options = `<option value="">-- Choisir une filière --</option>`;
                data.forEach(sector => {
                    options += `<option value="${sector.id}">${sector.name}</option>`;
                });
                sectorSelect.innerHTML = options;
                sectorSelect.disabled = false;
                promotionBody.innerHTML = `<tr><td colspan="2" class="text-center text-muted">Sélectionnez une filière</td></tr>`;
            });
    });

    sectorSelect.addEventListener('change', () => {
        const yearId = yearSelect.value;
        const sectorId = sectorSelect.value;
        if (!yearId || !sectorId) return;

        fetch(`/api/classroom-promotions/${yearId}/${sectorId}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    promotionBody.innerHTML = `<tr><td colspan="2" class="text-center text-muted">Aucune promotion trouvée</td></tr>`;
                    return;
                }

                let rows = '';
                data.forEach(promotion => {
                    rows += `
                        <tr>
                            <td>
                                ${promotion.name}
                                <input type="hidden" name="promotions[]" value="${promotion.id}">
                            </td>
                            <td>
                                <input type="number" name="counts[]" class="form-control count-input" required>
                            </td>
                        </tr>
                    `;
                });

                promotionBody.innerHTML = rows;
            });
    });

    generalCount.addEventListener('input', () => {
        document.querySelectorAll('.count-input').forEach(input => {
            input.value = generalCount.value;
        });
    });
</script>
@endsection
