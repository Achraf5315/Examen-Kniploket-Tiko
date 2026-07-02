{{-- Formulier voor het wijzigen van een afspraak, conform de wireframe (vijf velden, vooraf ingevuld) --}}
@extends('layouts.kniploket')

@section('titel', 'Afspraak wijzigen - Kniploket Tiko')

@section('inhoud')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <h1 class="h3 mb-4">Afspraak Wijzigen</h1>

            <form method="POST" action="{{ route('afspraken.update', $afspraak->Id) }}">
                @csrf
                {{-- Wijzigen gebeurt met een PUT-verzoek volgens de REST-conventies --}}
                @method('PUT')

                {{-- Veld 1: klant (vooraf ingevuld met de huidige klant) --}}
                <div class="mb-3">
                    <label for="KlantId" class="form-label">Klant</label>
                    <select name="KlantId" id="KlantId" class="form-select @error('KlantId') is-invalid @enderror">
                        <option value="">Kies een klant</option>
                        @foreach ($klanten as $klant)
                            <option value="{{ $klant->Id }}" @selected(old('KlantId', $afspraak->KlantId) == $klant->Id)>{{ $klant->Naam }}</option>
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
                            <option value="{{ $medewerker->Id }}" @selected(old('MedewerkerId', $afspraak->MedewerkerId) == $medewerker->Id)>{{ $medewerker->Naam }}</option>
                        @endforeach
                    </select>
                    @error('MedewerkerId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Veld 3: behandeling --}}
                <div class="mb-3">
                    <label for="BehandelingId" class="form-label">Behandeling</label>
                    <select name="BehandelingId" id="BehandelingId" class="form-select @error('BehandelingId') is-invalid @enderror">
                        <option value="">Kies een behandeling</option>
                        @foreach ($behandelingen as $behandeling)
                            <option value="{{ $behandeling->Id }}" @selected(old('BehandelingId', $afspraak->BehandelingId) == $behandeling->Id)>
                                {{ $behandeling->Naam }} ({{ $behandeling->DuurMinuten }} min)
                            </option>
                        @endforeach
                    </select>
                    @error('BehandelingId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Veld 4: datum --}}
                <div class="mb-3">
                    <label for="Datum" class="form-label">Datum</label>
                    <input type="date" name="Datum" id="Datum" value="{{ old('Datum', $afspraak->Datum) }}"
                           class="form-control @error('Datum') is-invalid @enderror">
                    @error('Datum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Veld 5: starttijd --}}
                <div class="mb-3">
                    <label for="Starttijd" class="form-label">Starttijd</label>
                    <input type="time" name="Starttijd" id="Starttijd" value="{{ old('Starttijd', substr($afspraak->Starttijd, 0, 5)) }}"
                           class="form-control @error('Starttijd') is-invalid @enderror">
                    @error('Starttijd')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Knoppen conform de wireframe: Opslaan en Annuleren --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-dark">Opslaan</button>
                    <a href="{{ route('afspraken.index') }}" class="btn btn-outline-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
