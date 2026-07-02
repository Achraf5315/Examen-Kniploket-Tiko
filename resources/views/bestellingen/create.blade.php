<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Bestellingen Toevoegen
                </div>

                @if (session('error'))
                    <div
                        class="mx-2 p-3 text-sm text-red-800 bg-red-100 border border-red-300 rounded-lg dark:bg-red-900/30 dark:text-red-200 dark:border-red-700">
                        {{ session('error') }}
                        <meta http-equiv="refresh" content="5;url={{ route('bestellingen.create') }}">
                    </div>
                @endif

                <form method="POST" action="{{ route('bestellingen.store') }}" class="p-6 space-y-6">
                    @csrf
                    @php $statuses = ['Nieuw', 'In behandeling', 'Verzonden', 'Geleverd', 'Geannuleerd']; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                            <select name="ProductNaam"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="">Selecteer product</option>
                                @if(isset($producten) && count($producten))
                                    @foreach($producten as $product)
                                        <option value="{{ $product->Id }}" @selected(old('ProductNaam') == $product->Id)>
                                            {{ $product->Productnaam }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="product-1">Geen producten gevonden</option>
                                @endif
                            </select>
                            @error('ProductNaam')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Klant</label>
                            <select name="KlantNaam"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="">Selecteer klant</option>
                                @if(isset($klanten) && count($klanten))
                                    @foreach($klanten as $klant)
                                        <option value="{{ $klant->Id }}" @selected(old('KlantNaam') == $klant->Id)>
                                            {{ $klant->Naam}}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="klant-1">Geen klanten gevonden</option>
                                @endif
                            </select>
                            @error('KlantNaam')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Orderdatum</label>
                            <input type="date" name="Orderdatum" min="{{ date('Y-m-d') }}"
                                value="{{ old('Orderdatum') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400" />
                            @error('Orderdatum')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Verwachte leverdatum</label>
                            <input type="date" name="VerwachteLeverdatum" min="{{ date('Y-m-d') }}"
                                value="{{ old('VerwachteLeverdatum') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400" />
                            @error('VerwachteLeverdatum')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select name="Status"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400">
                                <option value="">Selecteer status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('Status') == $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('Status')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 transition">
                            Opslaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>