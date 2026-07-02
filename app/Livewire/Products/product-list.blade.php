<div class="space-y-6">
    <!-- Header met titel en acties -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Productbeheer
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Beheer alle producten in uw salon
            </p>
        </div>
        
        <a href="{{ route('products.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nieuw Product
        </a>
    </div>
 
    <!-- Filter en zoek sectie -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 space-y-4">
        <!-- Zoekbalk -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    placeholder="Zoeken op productnaam, EAN-code..."
                    wire:model.live.debounce-300ms="search"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
 
            <!-- Categorie filter -->
            <select 
                wire:model.live="selectedCategory"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Alle categorieën</option>
                @foreach($categories as $category)
                    <option value="{{ $category->Id }}">{{ $category->Naam }}</option>
                @endforeach
            </select>
        </div>
 
        <!-- Filter buttons -->
        <div class="flex flex-wrap gap-2">
            <button 
                wire:click="toggleLowStockFilter"
                @class([
                    'px-4 py-2 rounded-lg transition-colors font-medium',
                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' => $showLowStockOnly,
                    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' => !$showLowStockOnly,
                ])
            >
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Lage Voorraad
            </button>
 
            @if($search || $selectedCategory || $showLowStockOnly)
                <button 
                    wire:click="$reset(['search', 'selectedCategory', 'showLowStockOnly'])"
                    class="px-4 py-2 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                >
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Filters Wissen
                </button>
            @endif
        </div>
    </div>
 
    <!-- Producten tabel -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        @if($products->count() > 0)
            <!-- Desktop tabel -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('Productnaam')" class="flex items-center space-x-2 hover:text-blue-600">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Productnaam</span>
                                    @if($sortBy === 'Productnaam')
                                        @if($sortDirection === 'asc')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3z" /></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3z" /></svg>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('EanCode')" class="flex items-center space-x-2 hover:text-blue-600">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">EAN-Code</span>
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Categorie</span>
                            </th>
                            <th class="px-6 py-3 text-right">
                                <button wire:click="sortBy('Prijs')" class="flex items-center justify-end space-x-2 hover:text-blue-600 ml-auto">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Prijs</span>
                                </button>
                            </th>
                            <th class="px-6 py-3 text-center">
                                <button wire:click="sortBy('Voorraad')" class="flex items-center justify-center space-x-2 hover:text-blue-600 mx-auto">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Voorraad</span>
                                </button>
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
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $product->Productnaam }}
                                            </p>
                                            @if(!empty($product->Opmerking))
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ Str::limit($product->Opmerking, 50) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                        {{ $product->EanCode }}
                                    </span>
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
                                            <span class="text-sm font-bold text-red-600 dark:text-red-400">
                                                {{ $product->Voorraad }}
                                            </span>
                                            <span class="text-xs text-red-500 dark:text-red-400">
                                                ⚠ Laag
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $product->Voorraad }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="inline-flex items-center px-3 py-1 text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Bewerk
                                        </a>
                                        <button wire:click="confirmDelete({{ $product->Id }})" 
                                                class="inline-flex items-center px-3 py-1 text-sm bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 rounded hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Verwijder
                                        </button>
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
                                <h3 class="font-medium text-gray-900 dark:text-white">
                                    {{ $product->Productnaam }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $product->EanCode }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $product->categorie->Naam }}
                            </span>
                        </div>
 
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Prijs:</span>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    €{{ number_format($product->Prijs, 2, ',', '.') }}
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Voorraad:</span>
                                <p class="font-medium @if($product->Voorraad <= $product->MinimumVoorraad) text-red-600 dark:text-red-400 @else text-gray-900 dark:text-white @endif">
                                    {{ $product->Voorraad }}
                                    @if($product->Voorraad <= $product->MinimumVoorraad)
                                        <span class="text-xs">⚠</span>
                                    @endif
                                </p>
                            </div>
                        </div>
 
                        <div class="flex space-x-2 pt-2">
                            <a href="{{ route('products.edit', $product) }}" 
                               class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors"
                            >
                                Bewerk
                            </a>
                            <button wire:click="confirmDelete({{ $product->Id }})" 
                                    class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 rounded hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors"
                            >
                                Verwijder
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
 
            <!-- Paginering -->
            <div class="border-t border-gray-200 dark:border-gray-600 px-6 py-4">
                {{ $products->links() }}
            </div>
        @else
            <!-- Lege staat -->
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
                    <a href="{{ route('products.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        Nieuw Product
                    </a>
                    te klikken.
                </p>
            </div>
        @endif
    </div>
 
    <!-- Delete bevestiging modal -->
    @if($showDeleteConfirm && $productToDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-sm w-full p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
 
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center mb-2">
                    Product verwijderen?
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-center text-sm mb-6">
                    Weet u zeker dat u dit product wilt verwijderen? Dit kan niet ongedaan worden gemaakt.
                </p>
 
                <div class="flex gap-3">
                    <button wire:click="cancelDelete" 
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium"
                    >
                        Annuleren
                    </button>
                    <button wire:click="deleteProduct" 
                            wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium disabled:opacity-50"
                    >
                        <span wire:loading.remove>Verwijder</span>
                        <span wire:loading>
                            <svg class="w-4 h-4 inline animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Bezig...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>