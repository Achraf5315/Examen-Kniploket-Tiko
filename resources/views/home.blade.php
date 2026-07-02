{{-- Homepagina van Kniploket Tiko, opgebouwd volgens de wireframe --}}
@extends('layouts.kniploket')

@section('titel', 'Kniploket Tiko - Welkom')

@section('inhoud')
<div class="container py-5">

    {{-- Hero-gedeelte: welkomsttekst met knoppen en een afbeeldingsplaceholder --}}
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <h1 class="display-5 fw-bold">Welkom bij Kniploket Tiko</h1>
            <p class="fs-5 text-secondary mb-1">Boek eenvoudig online een afspraak bij jouw specialist.</p>
            <p class="fs-5 text-secondary">Of bestel je haarproducten en haal ze op in de salon.</p>

            <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="{{ route('afspraken.create') }}" class="btn btn-dark btn-lg">Afspraak maken</a>
                <a href="#" class="btn btn-outline-dark btn-lg">Bekijk behandelingen</a>
            </div>
        </div>

        <div class="col-lg-6">
            {{-- Afbeeldingsplaceholder zoals in de wireframe --}}
            <div class="border rounded bg-light d-flex align-items-center justify-content-center" style="height: 320px;">
                <span class="text-secondary">Afbeelding (salon)</span>
            </div>
        </div>
    </div>

    <hr class="my-5">

    {{-- Snel naar: drie kaarten conform de wireframe --}}
    <h2 class="h4 mb-4">Snel naar</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="border rounded bg-light d-flex align-items-center justify-content-center mb-3" style="height: 140px;">
                        <span class="text-secondary">Afbeelding</span>
                    </div>
                    <h3 class="h5 card-title">Behandelingen</h3>
                    <p class="card-text text-secondary">Bekijk knippen, kleuren, stylen en meer.</p>
                    <a href="#" class="fw-semibold text-decoration-none text-dark">Bekijk aanbod &rsaquo;</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="border rounded bg-light d-flex align-items-center justify-content-center mb-3" style="height: 140px;">
                        <span class="text-secondary">Afbeelding</span>
                    </div>
                    <h3 class="h5 card-title">Producten bestellen</h3>
                    <p class="card-text text-secondary">Bestel online en haal op in de salon.</p>
                    <a href="#" class="fw-semibold text-decoration-none text-dark">Naar de shop &rsaquo;</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="border rounded bg-light d-flex align-items-center justify-content-center mb-3" style="height: 140px;">
                        <span class="text-secondary">Afbeelding</span>
                    </div>
                    <h3 class="h5 card-title">Mijn afspraken</h3>
                    <p class="card-text text-secondary">Bekijk, wijzig of annuleer je afspraken.</p>
                    <a href="{{ route('dashboard') }}" class="fw-semibold text-decoration-none text-dark">Mijn account &rsaquo;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
