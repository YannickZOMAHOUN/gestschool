@extends('layouts.template')

@section('another_CSS')
<link rel="stylesheet" href="{{ asset('css/datatable/dataTables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}">
@endsection

@section('content')
<div class="row col-12 pb-5">
    <div class="my-3">
        <h4 class="font-medium text-color-avt">Enregistrement d'un nouvel utilisateur</h4>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card py-5">
        <form action="{{ route('user.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="name" class="font-medium fs-16 text-black form-label">Nom</label>
                        <input type="text" name="name" id="name" class="form-control bg-form"
                            value="{{ old('name') }}" placeholder="">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="surname" class="font-medium fs-16 text-black form-label">Prénoms</label>
                        <input type="text" name="surname" id="surname" class="form-control bg-form"
                            value="{{ old('surname') }}" placeholder="">
                        @error('surname')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="email" class="font-medium fs-16 text-black form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control bg-form"
                            value="{{ old('email') }}" placeholder="">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="phone" class="font-medium fs-16 text-black form-label">Contact</label>
                        <input type="text" name="phone" id="phone" class="form-control bg-form"
                            value="{{ old('phone') }}" placeholder="">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-12 mb-3">
                        <label class="form-label fw-bold">Fonction(s) :</label>
                        <select name="role_id" id="role_id" class="form-select shadow-sm" required>
                            <option value="">-- Choisissez une fonction --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row d-flex justify-content-center mt-2">
                    <button type="reset" class="btn bg-secondary w-auto me-2 text-white">Annuler</button>
                    <button type="submit" class="btn btn-success w-auto">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('another_JS')
<!-- Tu peux ajouter ici les scripts Select2 si nécessaire -->
@endsection
