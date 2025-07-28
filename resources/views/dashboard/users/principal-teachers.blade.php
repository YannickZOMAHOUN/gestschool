@extends('layouts.template')

@section('content')
  <div class="container">
    <h4 class="my-3 font-medium text-color-avt">Liste des Professeurs Principals par filière</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow rounded p-4">
        <form method="POST" action="">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Année scolaire :</label>
                    <select name="year_id" id="year_id" class="form-control" required>
                        <option value="">-- Choisissez une année --</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Filière :</label>
                    <select name="sector_id" id="sector_id" class="form-control " disabled required>
                        <option value="">-- Sélectionnez d'abord l'année --</option>
                    </select>
                        <div class="invalid-feedback">Veuillez sélectionner une filière</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Classe</th>
                            <th>Enseignant</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="">
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
        const subjectSelect = document.getElementById('subject_id');
        const classroomSelect= document.getElementById('classroom_id');

        yearSelect.addEventListener('change', () => {
            const yearId = yearSelect.value;
            sectorSelect.innerHTML = `<option>Chargement...</option>`;
            sectorSelect.disabled = true;

            fetch(`/teacher-assignments/get-sectors/${yearId}`)
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


    </script>
@endsection

