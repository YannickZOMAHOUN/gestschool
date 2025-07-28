
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Résultats Scolaires -
                            {{ $student->name }} {{ $student->surname }} ({{ $student->matricule }})
                        </h4>
                        <div>
                            <a href="{{ route('parents.dashboard') }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                            <a href="{{ route('parents.export.pdf', $request->all()) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-file-pdf me-1"></i> Exporter PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Nom:</strong> {{ $student->name }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Prénom:</strong> {{ $student->surname }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Matricule:</strong> {{ $student->matricule }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Semestre:</strong> {{ $request->semester == 1 ? 'Premier' : 'Deuxième' }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="30%">Matières</th>
                                    <th width="10%">Coef</th>
                                    <th width="20%">Interros</th>
                                    <th width="15%">Moy. Interros</th>
                                    <th width="15%">Moy. Matière</th>
                                    <th width="10%">Moy. Coef</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                <tr>
                                    <td>{{ $result['subject'] }}</td>
                                    <td class="text-center">{{ $result['coefficient'] }}</td>
                                    <td>
                                        @if(count($result['interros']) > 0)
                                            {{ implode(', ', $result['interros']) }}
                                        @else
                                            <span class="text-muted">Aucune note</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($result['interros_average'], 2) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($result['subject_average'], 2) }}</td>
                                    <td class="text-center">{{ number_format($result['weighted_average'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">MOYENNE GENERALE</td>
                                    <td colspan="2" class="text-center fw-bold text-primary" style="font-size: 1.1em">
                                        {{ number_format($generalAverage, 2) }}/20
                                    </td>
                                </tr>
                                @if($request->semester == 2)
                                <tr>
                                    <td colspan="6" class="text-center fw-bold {{ $isPassed ? 'text-success' : 'text-danger' }}">
                                        <i class="fas fa-{{ $isPassed ? 'check-circle' : 'times-circle' }} me-2"></i>
                                        {{ $isPassed ? 'ADMIS(E)' : 'NON ADMIS(E)' }}
                                    </td>
                                </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> La moyenne générale est calculée en tenant compte des coefficients.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

