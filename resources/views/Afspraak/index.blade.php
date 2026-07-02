{{-- Afspraakoverzicht conform de wireframe: titel, knop "Nieuwe afspraak", filterbalk en tabel --}}
@extends('layouts.kniploket')

@section('titel', 'Afspraken - Kniploket Tiko')

@section('inhoud')
<div class="container py-4">

    {{-- Kop met de titel en de knop "Nieuwe afspraak" (rechtsboven, conform wireframe) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Afspraken</h1>

        @if (Route::has('afspraken.create'))
            <a href="{{ route('afspraken.create') }}" class="btn btn-dark">Nieuwe afspraak</a>
        @endif
    </div>

    {{-- Foutmelding wanneer het overzicht niet uit de database geladen kan worden --}}
    @isset($foutmelding)
        <div class="alert alert-danger" role="alert">{{ $foutmelding }}</div>
    @endisset

    {{-- Filterbalk: filtert op klant, medewerker, behandeling, datum of status --}}
    <form method="GET" action="{{ route('afspraken.index') }}" class="row g-2 mb-4" role="search">
        <div class="col-12 col-sm-auto">
            <input type="text" name="zoek" value="{{ $zoekterm }}" class="form-control" placeholder="Filter afspraken...">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">Filter</button>
        </div>
    </form>

    {{-- Tabel met alle afspraken uit de stored procedure spAfspraakOverzicht --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Klant</th>
                    <th scope="col">Medewerker</th>
                    <th scope="col">Behandeling</th>
                    <th scope="col">Datum</th>
                    <th scope="col">Tijd</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end">Acties</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($afspraken as $afspraak)
                    <tr>
                        <td>{{ $afspraak->KlantNaam }}</td>
                        <td>{{ $afspraak->MedewerkerNaam }}</td>
                        <td>{{ $afspraak->BehandelingNaam }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($afspraak->Datum)->format('d-m-Y') }}</td>
                        {{-- Starttijd en de in de stored procedure berekende eindtijd --}}
                        <td>{{ substr($afspraak->Starttijd, 0, 5) }} - {{ substr($afspraak->Eindtijd, 0, 5) }}</td>
                        <td>{{ $afspraak->Status }}</td>
                        <td class="text-end">
                            @if (Route::has('afspraken.edit'))
                                <a href="{{ route('afspraken.edit', $afspraak->Id) }}" class="btn btn-sm btn-outline-dark">Wijzigen</a>
                            @endif
                            @if (Route::has('afspraken.destroy'))
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#verwijderAfspraakModal-{{ $afspraak->Id }}">
                                    Verwijderen
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">Geen afspraken gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach ($afspraken as $afspraak)
    <div class="modal fade" id="verwijderAfspraakModal-{{ $afspraak->Id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 bg-dark text-white">
                    <div>
                        <p class="text-uppercase small mb-1 text-white-50">Bevestigen</p>
                        <h2 class="modal-title fs-5 mb-0">Afspraak verwijderen</h2>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="mb-2 fw-semibold">Weet je zeker dat je deze afspraak wilt verwijderen?</p>
                    <p class="text-secondary mb-3">Deze actie kan niet ongedaan worden gemaakt.</p>

                    <div class="border rounded-3 bg-light p-3 mb-3">
                        <div><strong>Klant:</strong> {{ $afspraak->KlantNaam }}</div>
                        <div><strong>Medewerker:</strong> {{ $afspraak->MedewerkerNaam }}</div>
                        <div><strong>Behandeling:</strong> {{ $afspraak->BehandelingNaam }}</div>
                        <div><strong>Datum:</strong> {{ \Illuminate\Support\Carbon::parse($afspraak->Datum)->format('d-m-Y') }} om {{ substr($afspraak->Starttijd, 0, 5) }}</div>
                    </div>

                    <form method="POST" action="{{ route('afspraken.destroy', $afspraak->Id) }}">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label for="Bevestiging-{{ $afspraak->Id }}" class="form-label">Typ VERWIJDEREN om door te gaan</label>
                            <input type="text"
                                   name="Bevestiging"
                                   id="Bevestiging-{{ $afspraak->Id }}"
                                   class="form-control @error('Bevestiging') is-invalid @enderror"
                                   placeholder="VERWIJDEREN"
                                   autocomplete="off"
                                   value="{{ old('Bevestiging') }}">
                            @error('Bevestiging')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                            <button type="submit" class="btn btn-danger">Verwijderen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if (request()->filled('verwijder'))
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('verwijderAfspraakModal-{{ request('verwijder') }}');
            if (modal) {
                bootstrap.Modal.getOrCreateInstance(modal).show();
            }
        });
    </script>
@endif
@endsection
