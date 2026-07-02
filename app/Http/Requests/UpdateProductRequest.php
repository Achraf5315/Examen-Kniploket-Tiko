<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateProductRequest
 * 
 * Valideert de input bij het bijwerken van een bestaand product.
 * Sluit het huidige product uit van uniqueness validatie.
 * 
 * @package App\Http\Requests
 */
class UpdateProductRequest extends FormRequest
{
    /**
     * Bepaalt of de gebruiker deze request mag uitvoeren
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        // TODO: vervang door auth()->user()->can('update', $this->route('product'))
        // zodra er een ProductPolicy is aangemaakt en geregistreerd.
        return $this->user() !== null;
    }

    /**
     * Definiëert de validatieregels voor het product formulier
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')->Id;

        return [
            'Productnaam' => [
                'required',
                'string',
                'max:255',
                Rule::unique('Product', 'Productnaam')
                    ->ignore($productId, 'Id'),
                'regex:/^[a-zA-Z0-9\s\-\(\)&.,]+$/',
            ],
            'EanCode' => [
                'required',
                'string',
                'regex:/^[0-9]{8,14}$/',
                Rule::unique('Product', 'EanCode')
                    ->ignore($productId, 'Id'),
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
                'regex:/^\d+(\.\d{1,2})?$/',
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
            'leveranciers.*.exists' => 'Een geselecteerde leverancier bestaat niet.',
        ];
    }

    /**
     * Bereidt de data voor validatie voor
     * 
     * @return void
     */
    public function prepareForValidation()
    {
        $this->merge([
            'Productnaam' => trim($this->input('Productnaam', '')),
            'EanCode' => preg_replace('/\s+/', '', $this->input('EanCode', '')),
            'Opmerking' => $this->input('Opmerking') ? trim($this->input('Opmerking')) : null,
        ]);
    }
}