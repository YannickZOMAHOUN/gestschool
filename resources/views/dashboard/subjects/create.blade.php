@extends('layouts.template')

@section('content')
<div class="container">
    <h4 class="my-3 font-medium text-color-avt">Les Matières</h4>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow rounded p-4">
        <form method="POST" action="{{ route('subject.store') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="year_id">Année scolaire :</label>
                    <select name="year_id" id="year_id" class="form-control" required>
                        <option value="">-- Choisissez une année --</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sector_id">Filière :</label>
                    <select name="sector_id" id="sector_id" class="form-control" disabled required>
                        <option value="">-- Choisissez d'abord une année --</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label>Matières Générales :</label><br>
                @foreach($allSubjects as $subject)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input global-subject" type="checkbox" value="{{ $subject->name }}" id="subject_{{ $subject->id }}">
                        <label class="form-check-label" for="subject_{{ $subject->id }}">{{ $subject->name }}</label>
                    </div>
                @endforeach
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Promotion</th>
                            <th>Matières (séparées par ;)</th>
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
    const globalSubjects = document.querySelectorAll('.global-subject');

    yearSelect.addEventListener('change', () => {
        const yearId = yearSelect.value;
        sectorSelect.innerHTML = `<option>Chargement...</option>`;
        sectorSelect.disabled = true;

        fetch(`/api/subject-sectors-by-year/${yearId}`)
            .then(res => res.json())
            .then(data => {
                let options = `<option value="">-- Choisissez une filière --</option>`;
                data.forEach(sector => {
                    options += `<option value="${sector.id}">${sector.name}</option>`;
                });
                sectorSelect.innerHTML = options;
                sectorSelect.disabled = false;
            });
    });

    sectorSelect.addEventListener('change', () => {
        const yearId = yearSelect.value;
        const sectorId = sectorSelect.value;

        fetch(`/api/subject-promotions/${yearId}/${sectorId}`)
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
                            <td>${promotion.promotion_sector}
                                <input type="hidden" name="promotion_ids[]" value="${promotion.id}">
                            </td>
                            <td>
                                <input type="text" name="subjects_by_promotion[]" class="form-control subject-field" value="${promotion.subjects.join('; ')}">
                            </td>
                        </tr>
                    `;
                });
                promotionBody.innerHTML = rows;
            });
    });

    globalSubjects.forEach(cb => {
        cb.addEventListener('change', () => {
            const selectedSubjects = Array.from(globalSubjects)
                .filter(el => el.checked)
                .map(el => el.value);
            const fields = document.querySelectorAll('.subject-field');
            fields.forEach(field => {
                field.value = selectedSubjects.join('; ');
            });
        });
    });
</script>
@endsection
