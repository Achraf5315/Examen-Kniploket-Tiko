<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Bestellingen Wijzigen
                </div>

                @if (session('error'))
                    <div
                        class="mx-2 p-3 text-sm text-red-800 bg-red-100 border border-red-300 rounded-lg dark:bg-red-900 dark:text-red-100 dark:border-red-700">
                        {{ session('error') }}
                        <meta http-equiv="refresh" content="5;url={{ route('bestellingen.edit', $bestelling->Id) }}">
                    </div>
                @endif

                <form method="POST" action="{{ route('bestellingen.update', $bestelling->Id) }}" class="p-6 space-y-6">
                    @csrf
                    @php
                        $selectedProductId = old('ProductNaam', $bestelling->ProductId ?? '');
                        $selectedKlantId = old('KlantNaam', $bestelling->KlantId ?? '');
                        $selectedStatus = old('Status', $bestelling->Status ?? '');
                        $statuses = ['Nieuw', 'In behandeling', 'Verzonden', 'Geleverd', 'Geannuleerd'];

                        if ($selectedStatus !== '' && !in_array($selectedStatus, $statuses, true)) {
                            array_unshift($statuses, $selectedStatus);
                        }
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-200 rounded-lg">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Product</label>
                            <select name="ProductNaam"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecteer product</option>
                                @if(isset($producten) && count($producten))
                                    @foreach($producten as $product)
                                        <option value="{{ $product->Id }}" @selected((string) $selectedProductId === (string) $product->Id)>
                                            {{ $product->Productnaam }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="product-1">Geen producten gevonden</option>
                                @endif
                            </select>
                            @error('ProductNaam')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Klant</label>
                            <select name="KlantNaam"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecteer klant</option>
                                @if(isset($klanten) && count($klanten))
                                    @foreach($klanten as $klant)
                                        <option value="{{ $klant->Id }}" @selected((string) $selectedKlantId === (string) $klant->Id)>
                                            {{ $klant->Naam}}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="klant-1">Geen klanten gevonden</option>
                                @endif
                            </select>
                            @error('KlantNaam')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Orderdatum</label>
                            <input type="date" name="Orderdatum" min="{{ date('Y-m-d') }}"
                                value="{{ old('Orderdatum', $bestelling->Orderdatum) }}"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                            @error('Orderdatum')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Verwachte leverdatum</label>
                            <input type="date" name="VerwachteLeverdatum" min="{{ date('Y-m-d') }}"
                                value="{{ old('VerwachteLeverdatum', $bestelling->VerwachteLeverdatum) }}"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                            @error('VerwachteLeverdatum')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Status</label>
                            <select name="Status"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecteer status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected((string) $selectedStatus === (string) $status)>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('Status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Opslaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>