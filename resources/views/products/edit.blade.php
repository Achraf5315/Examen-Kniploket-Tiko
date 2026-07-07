@extends('layouts.app')
 
@section('title', 'Bewerk Product: ' . $product->Productnaam)
 
@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('products.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Terug naar producten
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Bewerk Product
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                {{ $product->Productnaam }}
            </p>
        </div>
 
        <!-- Formulier -->
        <form action="{{ route('products.update', $product->Id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
 
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 space-y-6">
                <!-- Productnaam -->
                <div>
                    <label for="Productnaam" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Productnaam <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="Productnaam"
                        name="Productnaam" 
                        value="{{ old('Productnaam', $product->Productnaam) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                        placeholder="bijv. Keune Care Shampoo"
                        required
                    />
                    @error('Productnaam')
                        <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
 
                <!-- EAN Code -->
                <div>
                    <label for="EanCode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        EAN-Code <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="EanCode"
                        name="EanCode" 
                        value="{{ old('EanCode', $product->EanCode) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                        placeholder="bijv. 8717185223412"
                        pattern="[0-9]{8,14}"
                        required
                    />
                    <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                        EAN-codes moeten uit 8 tot 14 cijfers bestaan
                    </p>
                    @error('EanCode')
                        <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
 
                <!-- Twee kolommen layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Categorie -->
                    <div>
                        <label for="CategorieId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Categorie <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="CategorieId"
                            name="CategorieId" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                            required
                        >
                            <option value="">Selecteer een categorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->Id }}" @selected(old('CategorieId', $product->CategorieId) == $category->Id)>
                                    {{ $category->Naam }}
                                </option>
                            @endforeach
                        </select>
                        @error('CategorieId')
                            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Prijs -->
                    <div>
                        <label for="Prijs" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Prijs (€) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="Prijs"
                            name="Prijs" 
                            value="{{ old('Prijs', number_format($product->Prijs, 2, '.', '')) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                            placeholder="0.00"
                            step="0.01"
                            min="0.01"
                            max="9999.99"
                            required
                        />
                        @error('Prijs')
                            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
 
                <!-- Voorraad informatie -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Voorraadbeheer
                    </h3>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900/50 rounded-lg p-4 mb-4">
                        <p class="text-sm text-blue-800 dark:text-blue-300">
                            <strong>Huidige voorraad:</strong> {{ $product->Voorraad }} stuks
                            @if($product->Voorraad <= $product->MinimumVoorraad)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                    ⚠ Voorraad te laag
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Huidige voorraad -->
                        <div>
                            <label for="Voorraad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Huidige Voorraad <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="Voorraad"
                                name="Voorraad" 
                                value="{{ old('Voorraad', $product->Voorraad) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                                placeholder="0"
                                min="0"
                                max="99999"
                                required
                            />
                            @error('Voorraad')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
 
                        <!-- Minimumvoorraad -->
                        <div>
                            <label for="MinimumVoorraad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Minimumvoorraad <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="MinimumVoorraad"
                                name="MinimumVoorraad" 
                                value="{{ old('MinimumVoorraad', $product->MinimumVoorraad) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                                placeholder="5"
                                min="0"
                                max="99999"
                                required
                            />
                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                                Bij een voorraad onder dit aantal krijg je een waarschuwing
                            </p>
                            @error('MinimumVoorraad')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
 
                <!-- Leveranciers -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Leveranciers
                    </h3>
                    
                    <div class="space-y-2">
                        @forelse($suppliers as $supplier)
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="leveranciers[]" 
                                    value="{{ $supplier->Id }}"
                                    @checked(in_array($supplier->Id, old('leveranciers', $selectedSuppliers)))
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700"
                                />
                                <span class="ml-3 text-gray-700 dark:text-gray-300">
                                    {{ $supplier->Naam }}
                                </span>
                            </label>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">
                                Geen actieve leveranciers beschikbaar
                            </p>
                        @endforelse
                    </div>
                </div>
 
                <!-- Opmerking -->
                <div>
                    <label for="Opmerking" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Opmerking
                    </label>
                    <textarea 
                        id="Opmerking"
                        name="Opmerking" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
                        placeholder="Eventuele opmerkingen over dit product..."
                        maxlength="255"
                    >{{ old('Opmerking', $product->Opmerking) }}</textarea>
                    <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                        Max. 255 karakters
                    </p>
                    @error('Opmerking')
                        <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
 
                <!-- Systeeminformatie -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Aangemaakt:</p>
                            <p class="text-gray-900 dark:text-white font-medium">
                                {{ \Carbon\Carbon::parse($product->DatumAangemaakt)->format('d-m-Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Laatst bijgewerkt:</p>
                            <p class="text-gray-900 dark:text-white font-medium">
                                {{ \Carbon\Carbon::parse($product->DatumGewijzigd)->format('d-m-Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
 
            <!-- Form Actions -->
            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Wijzigingen Opslaan
                </button>
                
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium"
                >
                    Annuleren
                </a>
            </div>
        </form>
    </div>
</div>
 
<script>
    // Voorraad validatie
    document.getElementById('MinimumVoorraad').addEventListener('change', function() {
        const voorraad = parseInt(document.getElementById('Voorraad').value);
        const minimum = parseInt(this.value);
        
        if (minimum > voorraad) {
            this.classList.add('border-yellow-500');
            alert('Let op: voorraad is te laag, vul het aan.');
        } else {
            this.classList.remove('border-yellow-500');
        }
    });
</script>
@endsection