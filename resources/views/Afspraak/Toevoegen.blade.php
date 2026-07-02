{{-- Formulier voor het toevoegen van een afspraak, conform de wireframe (vijf velden) --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Afspraak Toevoegen
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @if (session('fout'))
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ session('fout') }}
                </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <form method="POST" action="{{ route('afspraken.store') }}" class="space-y-5">
                    @csrf

                    {{-- Veld 1: klant --}}
                    <div>
                        <label for="KlantId" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Klant</label>
                        <select name="KlantId" id="KlantId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Kies een klant</option>
                            @foreach ($klanten as $klant)
                                <option value="{{ $klant->Id }}" @selected(old('KlantId') == $klant->Id)>{{ $klant->Naam }}</option>
                            @endforeach
                        </select>
                        @error('KlantId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Veld 2: medewerker --}}
                    <div>
                        <label for="MedewerkerId" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Medewerker</label>
                        <select name="MedewerkerId" id="MedewerkerId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Kies een medewerker</option>
                            @foreach ($medewerkers as $medewerker)
                                <option value="{{ $medewerker->Id }}" @selected(old('MedewerkerId') == $medewerker->Id)>{{ $medewerker->Naam }}</option>
                            @endforeach
                        </select>
                        @error('MedewerkerId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Veld 3: behandeling (met duur, zodat de overlapcontrole logisch is voor de gebruiker) --}}
                    <div>
                        <label for="BehandelingId" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Behandeling</label>
                        <select name="BehandelingId" id="BehandelingId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Kies een behandeling</option>
                            @foreach ($behandelingen as $behandeling)
                                <option value="{{ $behandeling->Id }}" @selected(old('BehandelingId') == $behandeling->Id)>
                                    {{ $behandeling->Naam }} ({{ $behandeling->DuurMinuten }} min)
                                </option>
                            @endforeach
                        </select>
                        @error('BehandelingId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Veld 4: datum --}}
                    <div>
                        <label for="Datum" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Datum</label>
                        <input type="date" name="Datum" id="Datum" value="{{ old('Datum') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('Datum')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Veld 5: starttijd --}}
                    <div>
                        <label for="Starttijd" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Starttijd</label>
                        <input type="time" name="Starttijd" id="Starttijd" value="{{ old('Starttijd') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('Starttijd')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Knoppen conform de wireframe: Toevoegen en Annuleren --}}
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('afspraken.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300">
                            Annuleren
                        </a>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                            Toevoegen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
