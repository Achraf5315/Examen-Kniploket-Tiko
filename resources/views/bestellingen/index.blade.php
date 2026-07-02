<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Bestellingen Overzicht") }}
                </div>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bestellingen as $bestelling)
                            <tr class="hover:bg-gray-50">
                                <td
                                    class="px-4 py-3 text-sm text-gray-900 border-t border-gray-100 align-middle whitespace-nowrap">
                                    {{ $bestelling->ProductNaam }}
                                </td>
                                <td
                                    class="px-4 py-3 text-sm text-gray-900 border-t border-gray-100 align-middle whitespace-nowrap">
                                    {{ $bestelling->KlantNaam }}
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
                                        if($bestelling->Status == 'In behandeling')
                                        {
                                            echo '<span class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900">In behandeling</span>';
                                        }
                                        elseif($bestelling->Status == 'Verzonden')
                                        {
                                            echo '<span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">Verzonden</span>';
                                        }
                                        elseif($bestelling->Status == 'Geleverd')
                                        {
                                            echo '<span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">Geleverd</span>';
                                        }
                                        elseif($bestelling->Status == 'Nieuw')
                                        {
                                            echo '<span class="bg-purple-100 text-purple-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-purple-200 dark:text-purple-900">Nieuw</span>';
                                        }
                                        elseif($bestelling->Status == 'Geannuleerd')
                                        {
                                            echo '<span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900">Geannuleerd</span>';
                                        }
                                    @endphp
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