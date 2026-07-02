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
                            @if (Route::has('afspraken.delete'))
                                <a href="{{ route('afspraken.delete', $afspraak->Id) }}" class="btn btn-sm btn-outline-danger">Verwijderen</a>
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
@endsection
