@extends('layouts.template')

@section('another_CSS')
    <link rel="stylesheet" href="{{ asset('css/datatable/dataTables.checkboxes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}">
@endsection

@section('content')
<div class="row col-12 pb-5">

    <div class="d-flex justify-content-between my-2 flex-wrap">
        <div class="text-color-avt fs-22 font-medium">Filière de l'année</div>
        <div>
            <a class="btn btn-success fs-14" href="">
                <i class="fas fa-plus"></i> Promotion(s)
            </a>
        </div>
    </div>

    <div class="my-3">
        <h4 class="font-medium text-color-avt">Enregistrement d'une nouvelle filière</h4>
    </div>

    <div class="card p-4">
        <form action="{{ route('sector.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <label for="name_sector" class="form-label text-black">Nom de la filière</label>
                    <input type="text" name="name_sector" id="name_sector" class="form-control bg-form" required placeholder="Ex: IMI, BTP...">
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="reset" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between my-4">
    <h5 class="text-color-avt">Liste des Filières</h5>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered w-100">
            <thead class="text-center">
                <tr>
                    <th>Nom de la filière</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sectors as $sector)
                <tr>
                    <td>{{ $sector->name_sector }}</td>
                    <td class="text-center">
                        <a href="{{ route('sector.show', $sector) }}" class="text-secondary me-2" title="Détails">
                            <i class="bi bi-printer"></i>
                        </a>
                        <a href="{{ route('sector.edit', $sector) }}" class="text-primary me-2" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </a>
                        <span data-bs-toggle="modal" data-bs-target="#delete_sector{{ $sector->id }}" class="text-danger me-2" style="cursor: pointer;" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </span>
                        @if ($sector->status)
                            <span data-bs-toggle="modal" data-bs-target="#toggle_sector{{ $sector->id }}" class="text-danger" title="Désactiver">
                                <i class="fas fa-times"></i>
                            </span>
                        @else
                            <span data-bs-toggle="modal" data-bs-target="#toggle_sector{{ $sector->id }}" class="text-success" title="Activer">
                                <i class="fas fa-check"></i>
                            </span>
                        @endif
                    </td>
                </tr>

                {{-- Modal Supprimer --}}
                <div class="modal fade" id="delete_sector{{ $sector->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment supprimer cette filière ?
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('sector.destroy', $sector) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Confirmer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Activation/Désactivation --}}
                <div class="modal fade" id="toggle_sector{{ $sector->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Voulez-vous vraiment {{ $sector->status ? 'désactiver' : 'activer' }} cette filière ?
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <a class="btn btn-success" href="{{ $sector->status ? route('disable_sector', $sector) : route('activate_sector', $sector) }}">Confirmer</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('another_Js')
<script src="{{ asset('js/datatable/jquery-3.5.1.js') }}"></script>
<script src="{{ asset('js/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/datatable/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#example').DataTable({
            language: {
                url: "{{ asset('js/datatable/French.json') }}"
            },
            responsive: true,
            columnDefs: [{
                targets: -1,
                orderable: false
            }]
        });
    });
</script>
@endsection
