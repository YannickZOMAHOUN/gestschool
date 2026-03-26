@extends('layouts.template')

@section('another_CSS')
<style>
    :root {
        --primary:   #2563eb;
        --primary-dk:#1d4ed8;
        --success:   #16a34a;
        --danger:    #dc2626;
        --border:    #e2e8f0;
        --bg:        #f8fafc;
        --text:      #1e293b;
        --muted:     #64748b;
        --radius:    10px;
        --shadow:    0 2px 16px rgba(0,0,0,.07);
    }

    .page-header {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.5rem;
    }
    .page-header h4 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    .page-header p { font-size: .82rem; color: var(--muted); margin: 0; }

    .breadcrumb-nav {
        font-size: .8rem;
        color: var(--muted);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .breadcrumb-nav a { color: var(--primary); text-decoration: none; }
    .breadcrumb-nav a:hover { text-decoration: underline; }

    .yr-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        max-width: 540px;
    }
    .yr-card-header {
        padding: .9rem 1.25rem;
        border-bottom: 1px solid var(--border);
        font-weight: 600;
        font-size: .95rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .yr-card-body { padding: 1.5rem 1.25rem; }

    .form-label {
        font-weight: 600;
        font-size: .875rem;
        color: var(--text);
        display: block;
        margin-bottom: .4rem;
    }
    .form-control {
        width: 100%;
        height: 44px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 0 12px;
        font-size: .92rem;
        transition: border-color .2s, box-shadow .2s;
        color: var(--text);
        box-sizing: border-box;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37,99,235,.13);
    }
    .form-control.is-invalid { border-color: var(--danger); }
    .invalid-feedback { font-size: .8rem; color: var(--danger); margin-top: .3rem; }

    .status-info {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .55rem .9rem;
        border-radius: 7px;
        font-size: .82rem;
        font-weight: 600;
        margin-top: 1rem;
    }
    .status-info.active   { background: #dcfce7; color: #166534; }
    .status-info.inactive { background: #f1f5f9; color: #64748b; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .active   .status-dot { background: var(--success); }
    .inactive .status-dot { background: #94a3b8; }

    .form-actions {
        display: flex;
        gap: .6rem;
        margin-top: 1.5rem;
        padding-top: 1.1rem;
        border-top: 1px solid var(--border);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem 1.2rem;
        border-radius: 7px;
        font-size: .875rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
        text-decoration: none;
    }
    .btn:hover { opacity: .88; transform: translateY(-1px); text-decoration: none; }
    .btn-primary   { background: var(--primary); color: #fff; }
    .btn-secondary { background: #e2e8f0;         color: var(--text); }

    .alert {
        padding: .75rem 1rem;
        border-radius: 8px;
        font-size: .875rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .6rem;
        max-width: 540px;
    }
    .alert-success { background: #dcfce7; color: #166534; border-left: 4px solid var(--success); }
    .alert-danger  { background: #fee2e2; color: #991b1b; border-left: 4px solid var(--danger);  }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav">
        <a href="{{ route('year.create') }}">Années scolaires</a>
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span>Modifier</span>
    </nav>

    {{-- Header --}}
    <div class="page-header">
        <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="var(--primary)" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.768-6.768a2 2 0 012.828 2.828L11.828 15.828 8 17l1.172-3.828z"/>
        </svg>
        <div>
            <h4>Modifier l'année scolaire</h4>
            <p>Mise à jour de l'intitulé de l'année</p>
        </div>
    </div>

    {{-- Formulaire --}}
    <div class="yr-card">
        <div class="yr-card-header">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15.828 8 17l1.172-3.828 9.586-9.414z"/></svg>
            Informations de l'année
        </div>
        <div class="yr-card-body">
            <form action="{{ route('year.update', $year) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div>
                    <label for="year_input" class="form-label">
                        Intitulé de l'année
                        <span style="color:var(--danger)">*</span>
                    </label>
                    <input
                        type="text"
                        name="year"
                        id="year_input"
                        class="form-control @error('year') is-invalid @enderror"
                        value="{{ old('year', $year->year) }}"
                        placeholder="ex : 2024-2025"
                        autocomplete="off"
                    >
                    @error('year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Statut (lecture seule ici) --}}
                <div class="status-info {{ $year->status ? 'active' : 'inactive' }}">
                    <span class="status-dot"></span>
                    Statut actuel :
                    <strong>{{ $year->status ? 'Active' : 'Inactive' }}</strong>
                    @if($year->status)
                        — Année scolaire en cours
                    @endif
                </div>

                <div class="form-actions">
                    <a href="{{ route('year.create') }}" class="btn btn-secondary">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
