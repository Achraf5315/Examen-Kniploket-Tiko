<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

/**
 * ProductController
 *
 * Beheert alle acties rondom producten.
 * De controller gebruikt Stored Procedures voor databasebewerkingen
 * en verzorgt de communicatie tussen de database en de webpagina's.
 */
class ProductController extends Controller
{
    /**
     * Toont het overzicht van alle producten.
     *
     * De gebruiker kan producten zoeken, filteren, sorteren
     * en bekijken via meerdere pagina's.
     */
    public function index(Request $request)
    {
        try {

            // Haalt de zoekterm en filterinstellingen van de gebruiker op.
            $search = trim((string) $request->query('search', ''));
            $selectedCategory = $request->query('category', '');
            $showLowStockOnly = $request->boolean('low_stock');
            // Standaard tonen we de nieuwst toegevoegde producten bovenaan (Id DESC).
            $sortBy = $request->query('sort', 'Id');
            $sortDirection = $request->query('direction', 'desc');


            /*
             * Alleen toegestane kolommen mogen gebruikt worden
             * voor sortering. Dit voorkomt ongewenste invoer via de URL.
             */
            $allowedSorts = [
                'Id',
                'Productnaam',
                'EanCode',
                'Prijs',
                'Voorraad'
            ];

            if (!in_array($sortBy, $allowedSorts, true)) {
                $sortBy = 'Id';
            }

            if (!in_array($sortDirection, ['asc', 'desc'], true)) {
                $sortDirection = 'desc';
            }


            /*
             * Haalt de producten op via een Stored Procedure.
             * De database verzorgt hierbij ook de koppeling
             * met categorieën en leveranciers.
             */
            $productsData = DB::select('CALL sp_GetProducts(?, ?, ?)', [
                $search ?: null,
                $selectedCategory ?: null,
                $showLowStockOnly ? 1 : 0,
            ]);


            /*
             * Sorteert de resultaten op basis van de gekozen optie.
             * De standaardvolgorde is op productnaam.
             */
            $productsData = collect($productsData)
                ->sortBy(
                    fn ($product) => $product->$sortBy ?? $product->Productnaam,
                    SORT_REGULAR,
                    $sortDirection === 'desc'
                )
                ->values()
                ->all();


            /*
             * Verdeelt de resultaten over meerdere pagina's.
             * Hierdoor blijft het overzicht overzichtelijk bij veel producten.
             */
            $currentPage = (int) $request->query('page', 1);
            $perPage = 15;

            $offset = ($currentPage - 1) * $perPage;

            $pageItems = array_slice(
                $productsData,
                $offset,
                $perPage
            );


            $products = new LengthAwarePaginator(
                $pageItems,
                count($productsData),
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'query' => $request->query()
                ]
            );


            /*
             * Haalt de actieve categorieën op voor het filtermenu.
             */
            $categories = DB::select(
                'CALL sp_GetCategories()'
            );


            /*
             * Stuurt alle gegevens naar de productpagina.
             */
            return view('products.index', [
                'products' => $products,
                'categories' => $categories,
                'search' => $search,
                'selectedCategory' => $selectedCategory,
                'showLowStockOnly' => $showLowStockOnly,
                'sortBy' => $sortBy,
                'sortDirection' => $sortDirection,
            ]);

        } catch (\Exception $e) {

            /*
             * Fouten worden opgeslagen in het logbestand.
             * Hierdoor zijn problemen later terug te vinden.
             */
            Log::error('Fout bij laden producten', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);


            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Er is een fout opgetreden bij het laden van producten.'
                );
        }
    }


    /**
     * Toont het formulier om een nieuw product toe te voegen.
     */
    public function create()
    {
        try {

            /*
             * Haalt de beschikbare categorieën en leveranciers op
             * voor de keuzelijsten in het formulier.
             */
            $categories = DB::select(
                'CALL sp_GetCategories()'
            );

            $suppliers = DB::select(
                'CALL sp_GetLeveranciers()'
            );


            return view('products.create', [
                'categories' => $categories,
                'suppliers' => $suppliers,
            ]);


        } catch (\Exception $e) {

            Log::error('Fout bij openen productformulier', [
                'message' => $e->getMessage(),
            ]);


            return redirect()
                ->route('products.index')
                ->with(
                    'error',
                    'Het productformulier kon niet worden geladen.'
                );
        }
    }

    /**
     * Slaat een nieuw product op in de database.
     *
     * De invoer wordt eerst gecontroleerd.
     * Daarna wordt het product via een Stored Procedure toegevoegd.
     */
    public function store(StoreProductRequest $request)
    {
        try {

            /*
             * Haalt alleen gegevens op die de validatie hebben doorstaan.
             */
            $validatedData = $request->validated();


            /*
             * De geselecteerde leveranciers worden samengevoegd,
             * zodat de Stored Procedure deze gegevens kan verwerken.
             */
            $supplierIds = !empty($validatedData['leveranciers'])
                ? implode(',', $validatedData['leveranciers'])
                : '';


            /*
             * Het product wordt opgeslagen via de databaseprocedure.
             * De waarden worden veilig meegegeven via parameters.
             */
            DB::statement('CALL sp_CreateProduct(?, ?, ?, ?, ?, ?, ?, ?)', [
                $validatedData['Productnaam'],
                $validatedData['EanCode'],
                $validatedData['CategorieId'],
                $validatedData['Prijs'],
                $validatedData['Voorraad'],
                $validatedData['MinimumVoorraad'],
                $validatedData['Opmerking'] ?? null,
                $supplierIds,
            ]);


            /*
             * Zoekt het aangemaakte product op om het ID
             * te kunnen gebruiken in de logging.
             */
            $newProduct = DB::select(
                'SELECT Id FROM Product WHERE EanCode = ? ORDER BY Id DESC LIMIT 1',
                [
                    $validatedData['EanCode']
                ]
            );


            Log::info('Product aangemaakt', [
                'product_id' => $newProduct[0]->Id ?? null,
                'productnaam' => $validatedData['Productnaam'],
                'user_id' => auth()->id(),
            ]);


            return redirect()
                ->route('products.index')
                ->with(
                    'success',
                    "Product '{$validatedData['Productnaam']}' is succesvol toegevoegd."
                );


        } catch (\Exception $e) {


            Log::error('Fout bij aanmaken product', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);


            /*
             * Databasefouten worden omgezet naar een leesbare melding.
             */
            $message = str_contains($e->getMessage(), 'SQLSTATE[45000]')
                ? preg_replace('/^.*45000\]:\s*/', '', $e->getMessage())
                : 'Het product kon niet worden opgeslagen.';


            return redirect()
                ->back()
                ->with('error', $message)
                ->withInput();
        }
    }


    /**
     * Toont het formulier om een bestaand product te wijzigen.
     */
    public function edit(int $product)
    {
        try {


            /*
             * Haalt het gekozen product op inclusief gekoppelde gegevens.
             */
            $productResult = DB::select(
                'CALL sp_GetProductById(?)',
                [
                    $product
                ]
            );


            if (empty($productResult)) {

                return redirect()
                    ->route('products.index')
                    ->with(
                        'error',
                        'Het product is niet gevonden.'
                    );
            }


            $productData = $productResult[0];


            /*
             * De opgeslagen leveranciers worden omgezet naar een lijst,
             * zodat de juiste keuzes zichtbaar zijn in het formulier.
             */
            $selectedSuppliers = $productData->leveranciers_ids
                ? explode(',', $productData->leveranciers_ids)
                : [];


            $categories = DB::select(
                'CALL sp_GetCategories()'
            );

            $suppliers = DB::select(
                'CALL sp_GetLeveranciers()'
            );


            return view('products.edit', [
                'product' => $productData,
                'categories' => $categories,
                'suppliers' => $suppliers,
                'selectedSuppliers' => $selectedSuppliers,
            ]);


        } catch (\Exception $e) {


            Log::error('Fout bij openen wijzigformulier', [
                'message' => $e->getMessage(),
                'product_id' => $product,
            ]);


            return redirect()
                ->route('products.index')
                ->with(
                    'error',
                    'Het product kon niet worden geladen.'
                );
        }
    }


    /**
     * Werkt een bestaand product bij.
     *
     * De gewijzigde gegevens worden gecontroleerd
     * en daarna opgeslagen via een Stored Procedure.
     */
    public function update(UpdateProductRequest $request, int $product)
    {
        try {


            $validatedData = $request->validated();


            /*
             * Zet de gekozen leveranciers om naar een formaat
             * dat door de databaseprocedure gelezen wordt.
             */
            $supplierIds = !empty($validatedData['leveranciers'])
                ? implode(',', $validatedData['leveranciers'])
                : '';


            DB::statement('CALL sp_UpdateProduct(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $product,
                $validatedData['Productnaam'],
                $validatedData['EanCode'],
                $validatedData['CategorieId'],
                $validatedData['Prijs'],
                $validatedData['Voorraad'],
                $validatedData['MinimumVoorraad'],
                $validatedData['Opmerking'] ?? null,
                $supplierIds,
            ]);


            Log::info('Product bijgewerkt', [
                'product_id' => $product,
                'user_id' => auth()->id(),
            ]);


            return redirect()
                ->route('products.index')
                ->with(
                    'success',
                    "Product '{$validatedData['Productnaam']}' is succesvol bijgewerkt."
                );


        } catch (\Exception $e) {


            Log::error('Fout bij wijzigen product', [
                'message' => $e->getMessage(),
                'product_id' => $product,
                'user_id' => auth()->id(),
            ]);


            $message = str_contains($e->getMessage(), 'SQLSTATE[45000]')
                ? preg_replace('/^.*45000\]:\s*/', '', $e->getMessage())
                : 'De wijzigingen konden niet worden opgeslagen.';


            return redirect()
                ->back()
                ->with('error', $message)
                ->withInput();
        }
    }

    /**
     * Maakt een product inactief.
     *
     * Het product wordt niet volledig verwijderd,
     * maar blijft behouden voor historische gegevens.
     */
    public function destroy(int $product)
    {
        try {


            /*
             * Haalt eerst de productnaam op.
             * Deze wordt gebruikt voor de melding naar de gebruiker.
             */
            $productResult = DB::select(
                'SELECT Productnaam FROM Product WHERE Id = ? AND IsActief = 1',
                [
                    $product
                ]
            );


            if (empty($productResult)) {

                return redirect()
                    ->route('products.index')
                    ->with(
                        'error',
                        'Het product is niet gevonden.'
                    );
            }


            $productName = $productResult[0]->Productnaam;


            /*
             * De databaseprocedure controleert of het product verwijderd mag worden
             * en maakt het product daarna inactief.
             */
            DB::statement(
                'CALL sp_DeleteProduct(?)',
                [
                    $product
                ]
            );


            Log::info('Product verwijderd', [
                'product_id' => $product,
                'productnaam' => $productName,
                'user_id' => auth()->id(),
            ]);


            return redirect()
                ->route('products.index')
                ->with(
                    'success',
                    "Product '{$productName}' is succesvol verwijderd."
                );

} catch (\Exception $e) {

    Log::error('Fout bij verwijderen product', [
        'message' => $e->getMessage(),
        'product_id' => $product,
    ]);

    /*
     * Wanneer de database een reden geeft waarom verwijderen niet mag,
     * wordt alleen die melding getoond (zonder SQLSTATE-code, foutnummer
     * of de query/connectiegegevens die MySQL/PDO eraan plakt).
     */
    $message = 'Het product kon niet worden verwijderd.';

    if (preg_match('/SQLSTATE\[45000\]:.*?\d+\s+(.*?)\s*\(Connection:/s', $e->getMessage(), $matches)) {
        $message = trim($matches[1]);
    }

    return redirect()
        ->route('products.index')
        ->with('error', $message);
}
    }


    /**
     * Geeft producten terug waarvan de voorraad laag is.
     *
     * Deze functie wordt gebruikt door de website
     * om op de achtergrond voorraadwaarschuwingen te tonen.
     */
    public function getLowStockProducts()
    {
        try {


            /*
             * Haalt alleen producten op die onder de minimale voorraad zitten.
             */
            $lowStockProducts = DB::select(
                'CALL sp_GetProducts(?, ?, ?)',
                [
                    null,
                    null,
                    1,
                ]
            );


            return response()->json([
                'success' => true,
                'data' => $lowStockProducts,
                'count' => count($lowStockProducts),
            ]);


        } catch (\Exception $e) {


            Log::error('Fout bij ophalen lage voorraad', [
                'message' => $e->getMessage(),
            ]);


            return response()->json([
                'success' => false,
                'message' => 'De voorraadgegevens konden niet worden geladen.',
            ], 500);
        }
    }


    /**
     * Controleert of een EAN-code beschikbaar is.
     *
     * Wordt gebruikt tijdens het invullen van een formulier
     * zodat de gebruiker direct feedback krijgt.
     */
    public function checkEanCode(string $eanCode, ?int $productId = null)
    {
        try {


            /*
             * De database controleert of de EAN-code al bestaat.
             */
            $result = DB::select(
                'CALL sp_CheckEanCode(?, ?)',
                [
                    $eanCode,
                    $productId,
                ]
            );


            $available = !empty($result)
                && $result[0]->is_available == 1;


            return response()->json([
                'success' => true,
                'available' => $available,
            ]);


        } catch (\Exception $e) {


            Log::error('Fout bij controleren EAN-code', [
                'message' => $e->getMessage(),
            ]);


            return response()->json([
                'success' => false,
                'message' => 'De EAN-code kon niet worden gecontroleerd.',
            ], 500);
        }
    }
}