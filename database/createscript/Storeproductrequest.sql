<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
/**
 * StoreProductRequest
 * 
 * Valideert de input bij het aanmaken van een nieuw product.
 * Bevat zowel server-side als hints voor client-side validatie.
 * 
 * @package App\Http\Requests
 */
class StoreProductRequest extends FormRequest
{
    /**
     * Bepaalt of de gebruiker deze request mag uitvoeren
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        // Controleer of gebruiker het recht heeft producten aan te maken
        return auth()->check() && auth()->user()->can('create', \App\Models\Product::class);
    }
 
    /**
     * Definiëert de validatieregels voor het product formulier
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'Productnaam' => [
                'required',
                'string',
                'max:255',
                'unique:Product,Productnaam',
                'regex:/^[a-zA-Z0-9\s\-\(\)&.,]+$/', // Alleen relevante karakters
            ],
            'EanCode' => [
                'required',
                'string',
                'regex:/^[0-9]{8,14}$/', // EAN codes zijn 8, 12, 13 of 14 cijfers
                'unique:Product,EanCode',
            ],
            'CategorieId' => [
                'required',
                'integer',
                'exists:Categorie,Id',
            ],
            'Prijs' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999.99',
                'regex:/^\d+(\.\d{1,2})?$/', // Max 2 decimalen
            ],
            'Voorraad' => [
                'required',
                'integer',
                'min:0',
                'max:99999',
            ],
            'MinimumVoorraad' => [
                'required',
                'integer',
                'min:0',
                'max:99999',
                'lte:Voorraad', // Minimum voorraad mag niet hoger zijn dan huidige voorraad
            ],
            'Opmerking' => [
                'nullable',
                'string',
                'max:255',
            ],
            'leveranciers' => [
                'nullable',
                'array',
            ],
            'leveranciers.*' => [
                'integer',
                'exists:Leverancier,Id',
            ],
        ];
    }
 
    /**
     * Geeft aangepaste validatie foutmeldingen
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Productnaam.required' => 'Productnaam is verplicht.',
            'Productnaam.unique' => 'Deze productnaam bestaat al in het systeem.',
            'Productnaam.regex' => 'Productnaam bevat ongeldige karakters.',
            'EanCode.required' => 'EAN-code is verplicht.',
            'EanCode.regex' => 'EAN-code moet uit 8 tot 14 cijfers bestaan.',
            'EanCode.unique' => 'Deze EAN-code is al geregistreerd.',
            'CategorieId.required' => 'Selecteer een categorie.',
            'CategorieId.exists' => 'De geselecteerde categorie bestaat niet.',
            'Prijs.required' => 'Prijs is verplicht.',
            'Prijs.numeric' => 'Prijs moet een geldig getal zijn.',
            'Prijs.min' => 'Prijs moet minimaal €0,01 zijn.',
            'Prijs.regex' => 'Prijs mag maximaal 2 decimalen bevatten.',
            'Voorraad.required' => 'Voorraad is verplicht.',
            'Voorraad.integer' => 'Voorraad moet een geheel getal zijn.',
            'Voorraad.min' => 'Voorraad kan niet negatief zijn.',
            'MinimumVoorraad.required' => 'Minimumvoorraad is verplicht.',
            'MinimumVoorraad.integer' => 'Minimumvoorraad moet een geheel getal zijn.',
            'MinimumVoorraad.lte' => 'Minimumvoorraad mag niet hoger zijn dan de huidige voorraad.',
            'leveranciers.*.exists' => 'Een geselecteerde leverancier bestaat niet.',
        ];
    }
 
    /**
     * Voert aanvullende validatie uit na de standard validatie
     * 
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Aangepaste validatie logica hier indien nodig
            // Bijvoorbeeld: controleer of EAN code valide is volgens Luhn algoritme
        });
    }
 
    /**
     * Bereidt de data voor validatie voor
     * 
     * @return void
     */
    public function prepareForValidation()
    {
        // Trim whitespace van strings
        $this->merge([
            'Productnaam' => trim($this->input('Productnaam')),
            'EanCode' => preg_replace('/\s+/', '', $this->input('EanCode')),
            'Opmerking' => trim($this->input('Opmerking')),
        ]);
    }
}