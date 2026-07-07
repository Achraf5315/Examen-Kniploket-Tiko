<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center m-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white m-0">
                        Bestellingen Overzicht
                    </h2>

                    <a href="{{ route('bestellingen.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 active:bg-blue-700 disabled:opacity-25 transition">
                        Nieuwe bestelling toevoegen
                    </a>
                </div>

                @if(session('success'))
                    <div
                        class="mx-2 p-3 text-sm text-green-800 bg-green-100 border border-green-300 rounded-lg dark:bg-green-900/30 dark:text-green-200 dark:border-green-700">
                        {{ session('success') }}
                        <meta http-equiv="refresh" content="5;url={{ route('bestellingen.index') }}">
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mx-2 p-3 text-sm text-red-800 bg-red-100 border border-red-300 rounded-lg dark:bg-red-900/30 dark:text-red-200 dark:border-red-700">
                        {{ session('error') }}
                        <meta http-equiv="refresh" content="5;url={{ route('bestellingen.index') }}">
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table
                        class="w-full border border-gray-200 dark:border-gray-700 border-separate border-spacing-0 font-semibold mb-0 align-middle bg-white dark:bg-gray-800">
                        <thead>
                            <tr>
                                <th
                                    class="bg-gray-50 dark:bg-gray-700 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">
                                    Product
                                </th>
                                <th
                                    class="bg-gray-50 dark:bg-gray-700 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">
                                    Klant
                                </th>
                                <th
                                    class="bg-gray-50 dark:bg-gray-700 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">
                                    Orderdatum
                                </th>
                                <th
                                    class="bg-gray-50 dark:bg-gray-700 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">
                                    Verwachte Leverdatum
                                </th>
                                <th
                                    class="bg-gray-50 dark:bg-gray-700 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600">
                                    Status
                                </th>
                                <th
                                    class="bg-gray-50 dark:bg-gray-700 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600 text-center">
                                    Acties
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bestellingen as $bestelling)
                                @php
                                    $productNaam = $bestelling->ProductNaam ?? $bestelling->Productnaam ?? '';
                                    $klantNaam = $bestelling->KlantNaam ?? $bestelling->Klantnaam ?? '';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td
                                        class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 border-t border-gray-100 dark:border-gray-700 align-middle whitespace-nowrap">
                                        {{ $productNaam }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 border-t border-gray-100 dark:border-gray-700 align-middle whitespace-nowrap">
                                        {{ $klantNaam }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 border-t border-gray-100 dark:border-gray-700 align-middle whitespace-nowrap">
                                        {{ $bestelling->Orderdatum }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-t border-gray-100 dark:border-gray-700 align-middle">
                                        {{ $bestelling->VerwachteLeverdatum }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-t border-gray-100 dark:border-gray-700 align-middle">
                                        @php
                                            if ($bestelling->Status == 'In behandeling') {
                                                echo '<span class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-400/20 dark:text-yellow-300">In behandeling</span>';
                                            } elseif ($bestelling->Status == 'Verzonden') {
                                                echo '<span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-400/20 dark:text-blue-300">Verzonden</span>';
                                            } elseif ($bestelling->Status == 'Geleverd') {
                                                echo '<span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-400/20 dark:text-green-300">Geleverd</span>';
                                            } elseif ($bestelling->Status == 'Nieuw') {
                                                echo '<span class="bg-purple-100 text-purple-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-purple-400/20 dark:text-purple-300">Nieuw</span>';
                                            } elseif ($bestelling->Status == 'Geannuleerd') {
                                                echo '<span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-red-400/20 dark:text-red-300">Geannuleerd</span>';
                                            }
                                        @endphp
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-t border-gray-100 dark:border-gray-700 align-middle"
                                        x-data="{ openDeleteModal: false }">
                                        <div class="flex items-center justify-center gap-4">

                                            <a href="{{ route('bestellingen.edit', $bestelling->Id) }}"
                                                class="inline-flex items-center justify-center text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                    </path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                    </path>
                                                </svg>
                                            </a>

                                            <button type="button"
                                                class="inline-flex items-center justify-center text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                                @click="openDeleteModal = true">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path
                                                        d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                    </path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                            </button>

                                        </div>

                                        <div x-show="openDeleteModal" x-transition.opacity
                                            class="fixed inset-0 z-40 bg-black/40 dark:bg-black/60"
                                            @click="openDeleteModal = false"></div>

                                        <div x-show="openDeleteModal" x-transition
                                            class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                            <div class="w-full max-w-sm rounded-lg bg-white dark:bg-gray-800 p-6 shadow-xl border border-gray-200 dark:border-gray-700"
                                                @click.away="openDeleteModal = false">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                    Bestelling verwijderen?
                                                </h3>
                                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                                    Deze actie kan je niet ongedaan maken.
                                                </p>

                                                <div class="mt-5 flex items-center justify-end gap-3">
                                                    <button type="button"
                                                        class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700"
                                                        @click="openDeleteModal = false">
                                                        Annuleren
                                                    </button>

                                                    <form action="{{ route('bestellingen.destroy', $bestelling->Id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800">
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
                                    <td colspan="8" class="border-t border-gray-100 dark:border-gray-700">
                                        <div
                                            class="bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-200 text-center p-3">
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
    </div>
</x-app-layout>