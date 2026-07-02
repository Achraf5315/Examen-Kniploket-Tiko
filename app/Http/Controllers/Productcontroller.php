<?php
 
namespace App\Http\Controllers;
 
use App\Models\Product;
use App\Models\Categorie;
use App\Models\Leverancier;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
 
/**
 * ProductController
 * 
 * Beheert CRUD-operaties voor producten met ingebouwde validatie,
 * error handling en logging conform PSR-12 standaarden.
 * 
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /**
     * Geeft de index pagina met alle producten
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            return view('products.index');
        } catch (\Exception $e) {
            Log::error('Error loading product index: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->route('dashboard')
                ->with('error', 'Er is een fout opgetreden bij het laden van de producten.');
        }
    }
 
    /**
     * Geeft het formulier voor het aanmaken van een nieuw product
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $categories = Categorie::where('IsActief', true)
                ->orderBy('Naam')
                ->get();
            
            $suppliers = Leverancier::where('IsActief', true)
                ->orderBy('Naam')
                ->get();
 
            return view('products.create', compact('categories', 'suppliers'));
        } catch (\Exception $e) {
            Log::error('Error loading create product form: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->route('products.index')
                ->with('error', 'Er is een fout opgetreden bij het laden van het formulier.');
        }
    }
 
    /**
     * Slaat een nieuw product op in de database
     * 
     * @param \App\Http\Requests\StoreProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Validatie is al gedaan in StoreProductRequest
            $validatedData = $request->validated();
 
            // Maak het product aan
            $product = Product::create([
                'Productnaam' => $validatedData['Productnaam'],
                'EanCode' => $validatedData['EanCode'],
                'CategorieId' => $validatedData['CategorieId'],
                'Prijs' => $validatedData['Prijs'],
                'Voorraad' => $validatedData['Voorraad'],
                'MinimumVoorraad' => $validatedData['MinimumVoorraad'],
                'IsActief' => true,
                'Opmerking' => $validatedData['Opmerking'] ?? null,
            ]);
 
            // Voeg leveranciers toe als deze geselecteerd zijn
            if (!empty($validatedData['leveranciers'])) {
                $product->leveranciers()->attach($validatedData['leveranciers']);
            }
 
            DB::commit();
 
            Log::info('Product aangemaakt', [
                'product_id' => $product->Id,
                'product_name' => $product->Productnaam,
                'user_id' => auth()->id(),
            ]);
 
            return redirect()->route('products.index')
                ->with('success', "Product '{$product->Productnaam}' is succesvol aangemaakt.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating product: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
            ]);
 
            return redirect()->back()
                ->with('error', 'Er is een fout opgetreden bij het aanmaken van het product.')
                ->withInput();
        }
    }
 
    /**
     * Geeft het formulier voor het bewerken van een product
     * 
     * @param \App\Models\Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        try {
            $categories = Categorie::where('IsActief', true)
                ->orderBy('Naam')
                ->get();
            
            $suppliers = Leverancier::where('IsActief', true)
                ->orderBy('Naam')
                ->get();
            
            $selectedSuppliers = $product->leveranciers()
                ->pluck('LeverancierId')
                ->toArray();
 
            return view('products.edit', compact('product', 'categories', 'suppliers', 'selectedSuppliers'));
        } catch (ModelNotFoundException $e) {
            Log::warning('Product not found for edit: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('products.index')
                ->with('error', 'Het product is niet gevonden.');
        } catch (\Exception $e) {
            Log::error('Error loading edit product form: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->route('products.index')
                ->with('error', 'Er is een fout opgetreden bij het laden van het formulier.');
        }
    }
 
    /**
     * Werkt een bestaand product bij
     * 
     * @param \App\Http\Requests\UpdateProductRequest $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::beginTransaction();
        
        try {
            $validatedData = $request->validated();
 
            // Update productgegevens
            $product->update([
                'Productnaam' => $validatedData['Productnaam'],
                'EanCode' => $validatedData['EanCode'],
                'CategorieId' => $validatedData['CategorieId'],
                'Prijs' => $validatedData['Prijs'],
                'Voorraad' => $validatedData['Voorraad'],
                'MinimumVoorraad' => $validatedData['MinimumVoorraad'],
                'Opmerking' => $validatedData['Opmerking'] ?? null,
            ]);
 
            // Werk leveranciers bij
            if (isset($validatedData['leveranciers'])) {
                $product->leveranciers()->sync($validatedData['leveranciers']);
            }
 
            DB::commit();
 
            Log::info('Product bijgewerkt', [
                'product_id' => $product->Id,
                'product_name' => $product->Productnaam,
                'user_id' => auth()->id(),
            ]);
 
            return redirect()->route('products.index')
                ->with('success', "Product '{$product->Productnaam}' is succesvol bijgewerkt.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating product: ' . $e->getMessage(), [
                'product_id' => $product->Id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
            ]);
 
            return redirect()->back()
                ->with('error', 'Er is een fout opgetreden bij het bijwerken van het product.')
                ->withInput();
        }
    }
 
    /**
     * Verwijdert een product (soft delete via IsActief)
     * 
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        
        try {
            $productName = $product->Productnaam;
            
            // Controleer of product gekoppeld is aan behandelingen
            $hasBehandelingen = $product->behandelingen()->count() > 0;
            
            if ($hasBehandelingen) {
                return redirect()->route('products.index')
                    ->with('warning', "Product '{$productName}' kan niet worden verwijderd omdat het gekoppeld is aan behandelingen.");
            }
 
            // Soft delete door IsActief op 0 te zetten
            $product->update(['IsActief' => false]);
            
            // Verwijder leverancier koppelingen
            $product->leveranciers()->detach();
 
            DB::commit();
 
            Log::info('Product deactivated', [
                'product_id' => $product->Id,
                'product_name' => $productName,
                'user_id' => auth()->id(),
            ]);
 
            return redirect()->route('products.index')
                ->with('success', "Product '{$productName}' is succesvol verwijderd.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting product: ' . $e->getMessage(), [
                'product_id' => $product->Id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
            ]);
 
            return redirect()->back()
                ->with('error', 'Er is een fout opgetreden bij het verwijderen van het product.');
        }
    }
 
    /**
     * Geeft JSON response met low stock producten
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLowStockProducts()
    {
        try {
            $lowStockProducts = Product::where('IsActief', true)
                ->whereRaw('Voorraad <= MinimumVoorraad')
                ->with('categorie')
                ->orderBy('Voorraad')
                ->get();
 
            return response()->json([
                'success' => true,
                'data' => $lowStockProducts,
                'count' => $lowStockProducts->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching low stock products: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het ophalen van producten met lage voorraad.',
            ], 500);
        }
    }
 
    /**
     * Controleert of EAN code uniek is
     * 
     * @param string $eanCode
     * @param int|null $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEanCode($eanCode, $productId = null)
    {
        try {
            $query = Product::where('EanCode', $eanCode)->where('IsActief', true);
            
            if ($productId) {
                $query->where('Id', '!=', $productId);
            }
 
            $exists = $query->exists();
 
            return response()->json([
                'success' => true,
                'available' => !$exists,
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking EAN code: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het controleren van de EAN code.',
            ], 500);
        }
    }
}