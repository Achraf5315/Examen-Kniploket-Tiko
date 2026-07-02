<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Behandeling wijzigen
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                {{--
                    Rode foutmelding bij een mislukte opslag (bijv. een databasefout in de
                    stored procedure). Zonder dit blok zou de pagina stil terugkeren en lijkt
                    het alsof er "niets gebeurt". session('error') wordt door de controller gezet.
                --}}
                @if (session('error'))
                    <div class="mb-5 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                {{--
                    Rood validatie-blok bovenaan het formulier (unhappy path).
                    Toont álle server-side validatiefouten in het Nederlands, zodat de gebruiker
                    in één oogopslag ziet wat er mis is. $errors is altijd beschikbaar in Blade.
                --}}
                @if ($errors->any())
                    <div class="mb-5 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                        <p class="font-semibold">Er ging iets mis. Controleer de volgende punten:</p>
                        <ul class="mt-2 list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- POST-formulier met @method('PUT') zodat de resource-route update() aanroept. --}}
                <form method="POST" action="{{ route('behandelingen.update', $behandeling->Id) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="Naam" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Naam</label>
                        <input
                            id="Naam"
                            name="Naam"
                            type="text"
                            required
                            minlength="2"
                            maxlength="100"
                            value="{{ old('Naam', $behandeling->Naam) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('Naam')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="Prijs" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Prijs (EUR)</label>
                        <input
                            id="Prijs"
                            name="Prijs"
                            type="number"
                            required
                            min="0"
                            {{-- Maximaal 3 cijfers voor de komma: 999.99. HTML5-limiet naast de server-side check. --}}
                            max="999.99"
                            step="0.01"
                            value="{{ old('Prijs', $behandeling->Prijs) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('Prijs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="DuurMinuten" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Duur (minuten)</label>
                        <input
                            id="DuurMinuten"
                            name="DuurMinuten"
                            type="number"
                            required
                            min="1"
                            {{-- Maximaal 3 cijfers in totaal: 999 minuten. HTML5-limiet naast de server-side check. --}}
                            max="999"
                            step="1"
                            value="{{ old('DuurMinuten', $behandeling->DuurMinuten) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('DuurMinuten')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <p class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Producten koppelen</p>
                        <div class="grid grid-cols-1 gap-2 rounded-md border border-gray-200 p-3">
                            @php
                                $oudeProducten = old('Producten');
                                $huidigeSelectie = is_array($oudeProducten)
                                    ? array_map('intval', $oudeProducten)
                                    : $geselecteerdeProducten;
                            @endphp
                            @forelse ($producten as $product)
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        type="checkbox"
                                        name="Producten[]"
                                        value="{{ $product->Id }}"
                                        {{ in_array((int) $product->Id, $huidigeSelectie, true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    >
                                    <span>{{ $product->Productnaam }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">Er zijn geen actieve producten beschikbaar.</p>
                            @endforelse
                        </div>
                        @error('Producten')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('Producten.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="Opmerking" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Opmerking</label>
                        <textarea
                            id="Opmerking"
                            name="Opmerking"
                            maxlength="255"
                            rows="4"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('Opmerking', $behandeling->Opmerking) }}</textarea>
                        @error('Opmerking')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <a
                            href="{{ route('behandelingen.index') }}"
                            class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300"
                        >
                            Annuleren
                        </a>
                        <button
                            type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500"
                        >
                            Opslaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
