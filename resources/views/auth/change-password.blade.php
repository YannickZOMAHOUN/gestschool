@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-4">Changer votre mot de passe</h3>
                </div>

                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.change') }}">
                        @csrf

                        <div class="form-floating mb-4">
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password" required autocomplete="new-password" autofocus
                                placeholder="Nouveau mot de passe">
                            <label for="password">Nouveau mot de passe</label>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <input id="password_confirmation" type="password"
                                class="form-control"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirmer le mot de passe">
                            <label for="password_confirmation">Confirmer le mot de passe</label>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3">
                                <i class="fas fa-save me-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center py-3 bg-light">
                    <small class="text-muted">Assurez-vous de choisir un mot de passe sécurisé</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    body {
        background-color: #f8f9fa;
    }
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    .form-control {
        border-radius: 10px;
        padding: 15px;
    }
    .btn-primary {
        background-color: #4e73df;
        border: none;
        border-radius: 10px;
        transition: all 0.3s;
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endsection
