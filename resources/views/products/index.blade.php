@extends('layouts.app')

@section('title', 'Productbeheer')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="js-flash-notification bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg transition-opacity duration-300">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="js-flash-notification bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg transition-opacity duration-300">
                {{ session('error') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="js-flash-notification bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-lg transition-opacity duration-300">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Productbeheer</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Beheer alle producten in uw salon</p>
            </div>
            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nieuw Product
            </a>
        </div>

        <!-- Filter en zoek sectie -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 space-y-4">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Zoeken op productnaam, EAN-code..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <select name="category" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Alle categorieën</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->Id }}" @selected($selectedCategory == $category->Id)>
                                {{ $category->Naam }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Zoeken
                    </button>
                </div>

                <!-- Preserve sort state across search submits -->
                <input type="hidden" name="sort" value="{{ $sortBy }}">
                <input type="hidden" name="direction" value="{{ $sortDirection }}">

                <div class="flex flex-wrap gap-2">
                    @if($showLowStockOnly)
                        {{-- Currently on: link removes the filter --}}
                        <a href="{{ route('products.index', array_filter(['search' => $search, 'category' => $selectedCategory, 'sort' => $sortBy, 'direction' => $sortDirection])) }}"
                           class="px-4 py-2 rounded-lg transition-colors font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                    @else
                        <a href="{{ route('products.index', array_filter(['search' => $search, 'category' => $selectedCategory, 'low_stock' => 1, 'sort' => $sortBy, 'direction' => $sortDirection])) }}"
                           class="px-4 py-2 rounded-lg transition-colors font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200">
                    @endif
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        Voorraad te laag
                    </a>

                    @if($search || $selectedCategory || $showLowStockOnly)
                        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Filters Wissen
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Producten tabel -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            @if($products->count() > 0)
                @php
                    // Helper to build a sort link, preserving current filters
                    $sortLink = function (string $column) use ($search, $selectedCategory, $showLowStockOnly, $sortBy, $sortDirection) {
                        $direction = ($sortBy === $column && $sortDirection === 'asc') ? 'desc' : 'asc';
                        return route('products.index', array_filter([
                            'search' => $search,
                            'category' => $selectedCategory,
                            'low_stock' => $showLowStockOnly ? 1 : null,
                            'sort' => $column,
                            'direction' => $direction,
                        ]));
                    };
                @endphp

                <!-- Desktop tabel -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <a href="{{ $sortLink('Productnaam') }}" class="flex items-center space-x-2 hover:text-blue-600">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Productnaam</span>
                                        @if($sortBy === 'Productnaam')
                                            @if($sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3z" /></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3z" /></svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left">
                                    <a href="{{ $sortLink('EanCode') }}" class="flex items-center space-x-2 hover:text-blue-600">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">EAN-Code</span>
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Categorie</span>
                                </th>
                                <th class="px-6 py-3 text-right">
                                    <a href="{{ $sortLink('Prijs') }}" class="flex items-center justify-end space-x-2 hover:text-blue-600 ml-auto">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Prijs</span>
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-center">
                                    <a href="{{ $sortLink('Voorraad') }}" class="flex items-center justify-center space-x-2 hover:text-blue-600 mx-auto">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Voorraad</span>
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-center">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Acties</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->Productnaam }}</p>
                                        @if(!empty($product->Opmerking))
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($product->Opmerking, 50) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">{{ $product->EanCode }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $product->categorie->Naam }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            €{{ number_format($product->Prijs, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($product->Voorraad <= $product->MinimumVoorraad)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ $product->Voorraad }}</span>
                                                <span class="inline-flex items-center text-xs text-red-500 dark:text-red-400">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                                    </svg>
                                                    Te laag
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $product->Voorraad }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-3 py-1 text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Bewerk
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                  onsubmit="return confirm('Weet u zeker dat u \'{{ addslashes($product->Productnaam) }}\' wilt verwijderen? Dit kan niet ongedaan worden gemaakt.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1 text-sm bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 rounded hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Verwijder
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile kaarten weergave -->
                <div class="md:hidden space-y-4 p-4">
                    @foreach($products as $product)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $product->Productnaam }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $product->EanCode }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $product->categorie->Naam }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Prijs:</span>
                                    <p class="font-medium text-gray-900 dark:text-white">€{{ number_format($product->Prijs, 2, ',', '.') }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Voorraad:</span>
                                    @if($product->Voorraad <= $product->MinimumVoorraad)
                                        <p class="flex items-center gap-1 font-medium text-red-600 dark:text-red-400">
                                            <span>{{ $product->Voorraad }}</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                            </svg>
                                        </p>
                                    @else
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $product->Voorraad }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex space-x-2 pt-2">
                                <a href="{{ route('products.edit', $product) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                    Bewerk
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1"
                                      onsubmit="return confirm('Weet u zeker dat u \'{{ addslashes($product->Productnaam) }}\' wilt verwijderen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 text-sm bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 rounded hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                                        Verwijder
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-200 dark:border-gray-600 px-6 py-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Geen producten gevonden</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-center">
                        @if($search || $selectedCategory || $showLowStockOnly)
                            Pas uw zoekcriteria aan of
                        @else
                            Er zijn nog geen producten. Maak er een aan door op
                        @endif
                        <a href="{{ route('products.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Nieuw Product</a>
                        te klikken.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notifications = document.querySelectorAll('.js-flash-notification');

            notifications.forEach(function (notification) {
                // Bepaal timeout op basis van type (warning/error = langer, success = korter)
                let timeout = 3000;
                if (notification.classList.contains('bg-yellow-50') || notification.classList.contains('bg-red-50')) {
                    timeout = 8000; // Waarschuwingen en fouten blijven langer zichtbaar
                }

                window.setTimeout(function () {
                    notification.classList.add('opacity-0');

                    window.setTimeout(function () {
                        notification.remove();
                    }, 300);
                }, timeout);
            });
        });
    </script>
    @endpush