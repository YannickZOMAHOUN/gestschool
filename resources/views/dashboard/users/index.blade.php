@extends('layouts.template')

@section("another_CSS")
    <link rel="stylesheet" href="{{asset('css/datatable/bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('css/datatable/dataTables.bootstrap4.min.css')}}">
@endsection

@section("content")

    <div class="d-flex justify-content-between my-2 flex-wrap">
        <div class="text-color-avt fs-22 font-medium">Liste des utilisateurs</div>
        <div>
            <a class="btn btn-success fs-14" href="{{ route('user.create') }}">
                <i class="fas fa-plus"></i> Nouvelle Utilisateur
            </a>
        </div>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                <tr class="text-center">
                    <th>Nom et Prénoms</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Actions </th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $key=>$user)
                    <tr>
                        <td>{{ $user->name}} {{ $user->name}}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td class="text-center" style="cursor: pointer">
                            <a class="text-decoration-none text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Détails de l'utilisateur" href=> <i class="fas fa-eye"></i> </a>
                            &nbsp;
                            <a class="text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="Editer " href="{{route('user.edit', $user)}}"> <i class="fas fa-pen"></i> </a>
                            &nbsp;
                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer le secteur" class="">
                                    <i data-bs-toggle="modal" data-bs-target="#delete_user{{$user->id }}" class="fas fa-trash-alt text-danger" ></i>
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade" id="delete_user{{$user->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-color-avt">Confirmer suppression de la parcelle </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{route('user.destroy',$user) }}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <div class="d-flex justify-content-center">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-danger ms-2">Confirmer </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
 <!-- End Basic Modal-->

@endsection

@section("another_Js")

    <script src="{{asset('js/datatable/jquery-3.5.1.js')}}"></script>
    <script src="{{asset('js/datatable/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/datatable/dataTables.bootstrap4.min.js')}}"></script>

    <script>
        $(document).ready(function(){
            $('#example').DataTable(
                {
                    "language": {
                        "url": "{{asset('js/datatable/French.json')}}"
                    },
                    responsive: true,
                    "columnDefs": [ {
                        "targets": -1,
                        "orderable": false
                    } ]
                }
            );
            $('.alert').alert('close')
        });
    </script>
@endsection
