<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">Bestellingen OToevoegen</div>
                </div>
                <form method="POST" action="{{ route('bestellingen.store') }}" class="p-6 space-y-6">
                    @csrf
                    @php $statuses = ['Nieuw', 'In behandeling', 'Verzonden', 'Geleverd', 'Geannuleerd']; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-200 rounded-lg">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Product</label>
                            <input type="text" name="ProductNaam"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Klant</label>
                            <input type="text" name="KlantNaam]"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Orderdatum</label>
                            <input type="date" name="Orderdatum]"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Verwachte leverdatum</label>
                            <input type="date" name="VerwachteLeverdatum]"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Status</label>
                            <select name="Status]"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                option value="">Selecteer status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
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