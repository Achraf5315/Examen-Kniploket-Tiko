<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">Bestellingen Overzicht</div>
                    <a href="{{ route('bestellingen.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition">
                        Nieuwe bestelling toevoegen
                    </a>
                </div>

                @if(session('success'))
                    <div
                        class="mx-2 p-3 text-sm text-green-800 bg-green-100 border border-green-300 rounded-lg dark:bg-green-900 dark:text-green-100 dark:border-green-700">
                        {{ session('success') }}
                        <meta http-equiv="refresh" content="5;url={{ route('bestellingen.index') }}">
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mx-2 p-3 text-sm text-red-800 bg-red-100 border border-red-300 rounded-lg dark:bg-red-900 dark:text-red-100 dark:border-red-700">
                        {{ session('error') }}
                        <meta http-equiv="refresh" content="5;url={{ route('bestellingen.index') }}">
                    </div>
                @endif

                <table class="w-full border border-gray-200 border-separate font-semibold mb-0 align-middle">
                    <thead>
                        <tr>
                            <th
                                class="bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                Product
                            </th>
                            <th
                                class="bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                Klant
                            </th>
                            <th
                                class="bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                Orderdatum
                            </th>
                            <th
                                class="bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                Verwachte Leverdatum
                            </th>
                            <th
                                class="bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                Status
                            </th>
                            <th
                                class="bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 text-center">
                                Wijzigen
                            </th>
                            <th
                                class="bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 text-center">
                                Verwijderen
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bestellingen as $bestelling)
                            @php
                                $productNaam = $bestelling->ProductNaam ?? $bestelling->Productnaam ?? '';
                                $klantNaam = $bestelling->KlantNaam ?? $bestelling->Klantnaam ?? '';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td
                                    class="px-4 py-3 text-sm text-gray-900 border-t border-gray-100 align-middle whitespace-nowrap">
                                    {{ $productNaam }}
                                </td>
                                <td
                                    class="px-4 py-3 text-sm text-gray-900 border-t border-gray-100 align-middle whitespace-nowrap">
                                    {{ $klantNaam }}
                                </td>
                                <td
                                    class="px-4 py-3 text-sm text-gray-900 border-t border-gray-100 align-middle whitespace-nowrap">
                                    {{ $bestelling->Orderdatum }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 border-t border-gray-100 align-middle">
                                    {{ $bestelling->VerwachteLeverdatum }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 border-t border-gray-100 align-middle">
                                    @php
                                        if ($bestelling->Status == 'In behandeling') {
                                            echo '<span class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900">In behandeling</span>';
                                        } elseif ($bestelling->Status == 'Verzonden') {
                                            echo '<span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">Verzonden</span>';
                                        } elseif ($bestelling->Status == 'Geleverd') {
                                            echo '<span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">Geleverd</span>';
                                        } elseif ($bestelling->Status == 'Nieuw') {
                                            echo '<span class="bg-purple-100 text-purple-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-purple-200 dark:text-purple-900">Nieuw</span>';
                                        } elseif ($bestelling->Status == 'Geannuleerd') {
                                            echo '<span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900">Geannuleerd</span>';
                                        }
                                    @endphp
                                </td>
                                <td
                                    class="px-4 py-3 text-sm text-gray-500 border-t border-gray-100 align-middle text-center">
                                    <a href="{{ route('bestellingen.edit', $bestelling->Id) }}"
                                        class="inline-flex items-center justify-center text-blue-600 hover:text-blue-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 border-t border-gray-100 align-middle text-center"
                                    x-data="{ openDeleteModal: false }">
                                    <button type="button"
                                        class="inline-flex items-center justify-center text-red-600 hover:text-red-900"
                                        @click="openDeleteModal = true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>

                                    <div x-show="openDeleteModal" x-transition.opacity
                                        class="fixed inset-0 z-40 bg-black/40" @click="openDeleteModal = false"></div>

                                    <div x-show="openDeleteModal" x-transition
                                        class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                        <div class="w-full max-w-sm rounded-lg bg-white p-6 shadow-xl"
                                            @click.away="openDeleteModal = false">
                                            <h3 class="text-lg font-semibold text-gray-900">Bestelling verwijderen?</h3>
                                            <p class="mt-2 text-sm text-gray-600">
                                                Deze actie kan je niet ongedaan maken.
                                            </p>

                                            <div class="mt-5 flex items-center justify-end gap-3">
                                                <button type="button"
                                                    class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                    @click="openDeleteModal = false">
                                                    Annuleren
                                                </button>

                                                <form action="{{ route('bestellingen.destroy', $bestelling->Id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                                        VERWIJDEREN
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="bg-amber-50 text-amber-700 text-center p-3">
                                        Er zijn geen bestellingen om te tonen.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>