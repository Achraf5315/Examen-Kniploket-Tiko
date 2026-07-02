{{-- Compatibiliteitspagina voor het verwijderen van een afspraak. De primaire flow gebruikt de modal op het overzicht. --}}
@extends('layouts.kniploket')

@section('titel', 'Afspraak verwijderen - Kniploket Tiko')

@section('inhoud')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-danger shadow">
                <div class="card-header bg-danger text-white">
                    <h1 class="h5 mb-0">Afspraak verwijderen</h1>
                </div>

                <div class="card-body">
                    <p class="fw-semibold mb-1">Weet je het zeker?</p>
                    <p class="text-secondary">Deze actie kan niet ongedaan worden gemaakt.</p>

                    <ul class="list-unstyled border rounded bg-light p-3">
                        <li><strong>Klant:</strong> {{ $afspraak->KlantNaam }}</li>
                        <li><strong>Medewerker:</strong> {{ $afspraak->MedewerkerNaam }}</li>
                        <li><strong>Behandeling:</strong> {{ $afspraak->BehandelingNaam }}</li>
                        <li>
                            <strong>Datum:</strong>
                            {{ \Illuminate\Support\Carbon::parse($afspraak->Datum)->format('d-m-Y') }}
                            om {{ substr($afspraak->Starttijd, 0, 5) }}
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('afspraken.destroy', $afspraak->Id) }}">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label for="Bevestiging" class="form-label">Typ VERWIJDEREN om het te verwijderen</label>
                            <input type="text" name="Bevestiging" id="Bevestiging" autocomplete="off"
                                   class="form-control @error('Bevestiging') is-invalid @enderror"
                                   placeholder="VERWIJDEREN"
                                   value="{{ old('Bevestiging') }}">
                            @error('Bevestiging')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">Verwijderen</button>
                            <a href="{{ route('afspraken.index') }}" class="btn btn-outline-secondary">Annuleren</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection