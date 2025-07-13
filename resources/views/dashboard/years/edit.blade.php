@extends('layouts.template')

@section('another_CSS')
<link rel="stylesheet" href="{{asset('css/datatable/dataTables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('css/select2-bootstrap-5-theme.min.css')}}">
@endsection

@section('content')
<div class="row col-12 pb-5">
    <div class="my-3">
        <h4 class="font-medium text-color-avt">Modification  d'une année scolaire</h4>
    </div>

    <div class="card py-5">
        <form action="{{route('year.update', $year)}}" method="POST">
             @csrf
            @method('put')
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12 mb-3">
                        <label for="year" class="font-medium fs-16 text-black form-label">Année Scolaire</label>
                        <input type="text" name="year" id="year" class="form-control bg-form" placeholder=""  value="{{$year->year}}">
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
@endsection
