{{-- Dashboard voor ingelogde gebruikers: snelkoppelingen naar de beheeronderdelen --}}
@extends('layouts.kniploket')

@section('titel', 'Dashboard - Kniploket Tiko')

@section('inhoud')
<div class="container py-4">
    <h1 class="h3 mb-4">Dashboard</h1>

    <div class="row g-4">
        {{-- Afspraken: onderdeel van de Afspraak user stories --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 card-title">Afspraken</h2>
                    <p class="card-text text-secondary">Bekijk, voeg toe, wijzig of verwijder afspraken.</p>
                    <a href="{{ route('afspraken.index') }}" class="btn btn-dark">Afspraken</a>
                </div>
            </div>
        </div>

        {{-- Behandelingen en Producten worden door teamgenoten gebouwd --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 card-title">Behandelingen</h2>
                    <p class="card-text text-secondary">Beheer het aanbod van behandelingen.</p>
                    <a href="#" class="btn btn-outline-secondary disabled" aria-disabled="true">Behandelingen</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 card-title">Producten</h2>
                    <p class="card-text text-secondary">Beheer de producten en de voorraad.</p>
                    <a href="#" class="btn btn-outline-secondary disabled" aria-disabled="true">Producten</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
