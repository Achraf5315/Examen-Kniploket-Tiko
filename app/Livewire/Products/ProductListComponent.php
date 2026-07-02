<?php
 
namespace App\Livewire\Products;
 
use App\Models\Product;
use App\Models\Categorie;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
 
/**
 * ProductListComponent
 * 
 * Livewire component voor het weergeven en beheren van producten.
 * Ondersteunt zoeken, filteren, sorteren en real-time updates.
 * 
 * @package App\Livewire\Products
 */
class ProductListComponent extends Component
{
    use WithPagination;
 
    /**
     * Search en filter properties
     */
    public string $search = '';
    public string $selectedCategory = '';
    public string $sortBy = 'Productnaam';
    public string $sortDirection = 'asc';
    public bool $showLowStockOnly = false;
 
    /**
     * Modal en acties
     */
    public bool $showDeleteConfirm = false;
    public ?int $productToDelete = null;
    public bool $showImportModal = false;
 
    /**
     * Pagination
     */
    public int $perPage = 15;
 
    /**
     * Listeners voor real-time updates
     */
    protected $listeners = [
        'productCreated' => 'handleProductCreated',
        'productUpdated' => 'handleProductUpdated',
        'refreshProducts' => '$refresh',
    ];
 
    /**
     * Geeft de producten weer die voldoen aan de filters
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getProductsProperty()
    {
        try {
            $query = Product::query()
                ->where('IsActief', true)
                ->with(['categorie', 'leveranciers']);
 
            // Zoekfilter
            if (!empty($this->search)) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('Productnaam', 'LIKE', $searchTerm)
                        ->orWhere('EanCode', 'LIKE', $searchTerm)
                        ->orWhere('Opmerking', 'LIKE', $searchTerm);
                });
            }
 
            // Categorie filter
            if (!empty($this->selectedCategory)) {
                $query->where('CategorieId', $this->selectedCategory);
            }
 
            // Low stock filter
            if ($this->showLowStockOnly) {
                $query->whereRaw('Voorraad <= MinimumVoorraad');
            }
 
            // Sortering
            $query->orderBy($this->sortBy, $this->sortDirection);
 
            return $query->paginate($this->perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching products: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'search' => $this->search,
                'category' => $this->selectedCategory,
            ]);
 
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Er is een fout opgetreden bij het ophalen van producten.',
            ]);
 
            return collect();
        }
    }
 
    /**
     * Geeft alle categorieën op
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesProperty()
    {
        return Categorie::where('IsActief', true)
            ->orderBy('Naam')
            ->get();
    }
 
    /**
     * Hanteert search input met debouncing
     * 
     * @return void
     */
    #[\Livewire\Attributes\On('input')]
    public function updateSearch($value)
    {
        $this->search = trim($value);
        $this->resetPage();
    }
 
    /**
     * Wijzigt de geselecteerde categorie
     * 
     * @param string $categoryId
     * @return void
     */
    public function changeCategory(string $categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }
 
    /**
     * Wijzigt sorteervolgorde
     * 
     * @param string $column
     * @return void
     */
    public function sortBy(string $column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }
 
    /**
     * Toggle low stock filter
     * 
     * @return void
     */
    public function toggleLowStockFilter()
    {
        $this->showLowStockOnly = !$this->showLowStockOnly;
        $this->resetPage();
    }
 
    /**
     * Opent delete bevestigingsmodal
     * 
     * @param int $productId
     * @return void
     */
    public function confirmDelete(int $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            
            // Controleer of product aan behandelingen gekoppeld is
            if ($product->behandelingen()->count() > 0) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'Dit product kan niet worden verwijderd omdat het aan behandelingen gekoppeld is.',
                ]);
                return;
            }
 
            $this->productToDelete = $productId;
            $this->showDeleteConfirm = true;
        } catch (\Exception $e) {
            Log::error('Error in confirmDelete: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Er is een fout opgetreden.',
            ]);
        }
    }
 
    /**
     * Verwijdert een product
     * 
     * @return void
     */
    public function deleteProduct()
    {
        try {
            if (!$this->productToDelete) {
                return;
            }
 
            $product = Product::findOrFail($this->productToDelete);
            $productName = $product->Productnaam;
 
            // Soft delete
            $product->update(['IsActief' => false]);
            $product->leveranciers()->detach();
 
            Log::info('Product verwijderd', [
                'product_id' => $product->Id,
                'product_name' => $productName,
                'user_id' => auth()->id(),
            ]);
 
            $this->showDeleteConfirm = false;
            $this->productToDelete = null;
            $this->resetPage();
 
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Product '{$productName}' is succesvol verwijderd.",
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage(), [
                'product_id' => $this->productToDelete,
                'user_id' => auth()->id(),
            ]);
 
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Er is een fout opgetreden bij het verwijderen van het product.',
            ]);
        }
    }
 
    /**
     * Sluit delete modal
     * 
     * @return void
     */
    public function cancelDelete()
    {
        $this->showDeleteConfirm = false;
        $this->productToDelete = null;
    }
 
    /**
     * Hanteert product created event
     * 
     * @return void
     */
    public function handleProductCreated()
    {
        $this->resetPage();
        $this->search = '';
        $this->selectedCategory = '';
    }
 
    /**
     * Hanteert product updated event
     * 
     * @return void
     */
    public function handleProductUpdated()
    {
        $this->resetPage();
    }
 
    /**
     * Rendert de component
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.products.product-list', [
            'products' => $this->products,
            'categories' => $this->categories,
        ]);
    }
}