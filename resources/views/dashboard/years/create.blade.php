@extends('layouts.template')

@section('another_CSS')
<link rel="stylesheet" href="{{asset('css/datatable/dataTables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('css/select2-bootstrap-5-theme.min.css')}}">
@endsection

@section('content')
<div class="row col-12 pb-5">
    <div class="my-3">
        <h4 class="font-medium text-color-avt">Enregistrement d'une nouvelle année scolaire</h4>
    </div>

    <div class="card py-5">
        <form action="{{route('year.store')}}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12 mb-3">
                        <label for="year" class="font-medium fs-16 text-black form-label">Année Scolaire</label>
                        <input type="text" name="year" id="year" class="form-control bg-form" placeholder="">
                    </div>

                    <div class="row d-flex justify-content-center mt-2">
                        <button type="reset" class="btn bg-secondary w-auto me-2 text-white">Annuler</button>
                        <button type="submit" class="btn btn-success w-auto">Enregistrer</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between my-2 flex-wrap">
    <div class="text-color-avt fs-22">Années Scolaires</div>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr class="text-center">
                    <th>Année Scolaire</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($years as $year)
                <tr>
                    <td>{{ $year->year }}</td>
                    <td class="text-center" style="cursor: pointer">
                        <a class="text-decoration-none text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Détails de l'année scolaire" href="{{route('year.show', $year)}}"><i class="bi bi-printer"></i></a>
                        &nbsp;
                        <a class="text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="Éditer l'année scolaire" href="{{route('year.edit', $year)}}"><i class="fas fa-pen"></i></a>
                        &nbsp;
                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer cette année scolaire" class="">
                            <i data-bs-toggle="modal" data-bs-target="#delete_year{{$year->id}}" class="fas fa-trash-alt text-danger"></i>
                        </a>
                        &nbsp;
                        @if ($year->status)
                        <span class="text-decoration-none text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Désactiver l'année scolaire">
                            <i data-bs-toggle="modal" data-bs-target="#exampleModal{{$year->id}}" class="fas fa-times"></i>
                        </span>
                        &nbsp;
                        @else
                        <span class="text-decoration-none text-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Activer l'année scolaire">
                            <i data-bs-toggle="modal" data-bs-target="#exampleModal{{$year->id}}" class="fas fa-check"></i>
                        </span>
                        &nbsp;
                        @endif
                    </td>
                </tr>

                <!-- Modal Supprimer -->
                <div class="modal fade" id="delete_year{{$year->id}}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-color-avt">Confirmer la suppression</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('year.destroy', $year)}}" method="POST">
                                    @csrf
                                    @method('delete')
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-danger ms-2">Confirmer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Activer/Désactiver -->
                <div class="modal fade" id="exampleModal{{$year->id}}" tabindex="-1" aria-labelledby="exampleModalLabel{{$year->id}}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel{{$year->id}}">Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body mx-auto justify-content-center">
                                Voulez-vous vraiment @if($year->status) désactiver @else activer @endif cette année scolaire ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                @if($year->status)
                                <a type="button" class="btn btn-success" href="{{route('disable_year',$year)}}">Confirmer</a>
                                @else
                                <a type="button" class="btn btn-success" href="{{route('activate_year',$year)}}">Confirmer</a>
                                @endif
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

@section("another_Js")
<script src="{{asset('js/datatable/jquery-3.5.1.js')}}"></script>
<script src="{{asset('js/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/datatable/dataTables.bootstrap4.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#example').DataTable({
            "language": {
                "url": "{{asset('js/datatable/French.json')}}"
            },
            responsive: true,
            "columnDefs": [{
                "targets": -1,
                "orderable": false
            }]
        });
        $('.alert').alert('close');
    });
</script>
@endsection
