{{-- Formulier voor het toevoegen van een afspraak, conform de wireframe (vijf velden) --}}
@extends('layouts.kniploket')

@section('titel', 'Afspraak toevoegen - Kniploket Tiko')

@section('inhoud')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <h1 class="h3 mb-4">Afspraak Toevoegen</h1>

            <form method="POST" action="{{ route('afspraken.store') }}">
                @csrf

                {{-- Veld 1: klant --}}
                <div class="mb-3">
                    <label for="KlantId" class="form-label">Klant</label>
                    <select name="KlantId" id="KlantId" class="form-select @error('KlantId') is-invalid @enderror">
                        <option value="">Kies een klant</option>
                        @foreach ($klanten as $klant)
                            <option value="{{ $klant->Id }}" @selected(old('KlantId') == $klant->Id)>{{ $klant->Naam }}</option>
                        @endforeach
                    </select>
                    @error('KlantId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Veld 2: medewerker --}}
                <div class="mb-3">
                    <label for="MedewerkerId" class="form-label">Medewerker</label>
                    <select name="MedewerkerId" id="MedewerkerId" class="form-select @error('MedewerkerId') is-invalid @enderror">
                        <option value="">Kies een medewerker</option>
                        @foreach ($medewerkers as $medewerker)
                            <option value="{{ $medewerker->Id }}" @selected(old('MedewerkerId') == $medewerker->Id)>{{ $medewerker->Naam }}</option>
                        @endforeach
                    </select>
                    @error('MedewerkerId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Veld 3: behandeling (met duur, zodat de overlapcontrole logisch is voor de gebruiker) --}}
                <div class="mb-3">
                    <label for="BehandelingId" class="form-label">Behandeling</label>
                    <select name="BehandelingId" id="BehandelingId" class="form-select @error('BehandelingId') is-invalid @enderror">
                        <option value="">Kies een behandeling</option>
                        @foreach ($behandelingen as $behandeling)
                            <option value="{{ $behandeling->Id }}" @selected(old('BehandelingId') == $behandeling->Id)>
                                {{ $behandeling->Naam }} ({{ $behandeling->DuurMinuten }} min)
                            </option>
                        @endforeach
                    </select>
                    @error('BehandelingId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Veld 4: datum --}}
                <div class="mb-3">
                    <label for="Datum" class="form-label">Datum</label>
                    <input type="date" name="Datum" id="Datum" value="{{ old('Datum') }}"
                           class="form-control @error('Datum') is-invalid @enderror">
                    @error('Datum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Veld 5: starttijd --}}
                <div class="mb-3">
                    <label for="Starttijd" class="form-label">Starttijd</label>
                    <input type="time" name="Starttijd" id="Starttijd" value="{{ old('Starttijd') }}"
                           class="form-control @error('Starttijd') is-invalid @enderror">
                    @error('Starttijd')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Knoppen conform de wireframe: Toevoegen en Annuleren --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-dark">Toevoegen</button>
                    <a href="{{ route('afspraken.index') }}" class="btn btn-outline-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
