<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Productnaam' => ['required', 'string', 'max:255'],
            'EanCode' => ['required', 'string', 'regex:/^[0-9]{8,14}$/', 'unique:Product,EanCode'],
            'CategorieId' => ['required', 'exists:Categorie,Id'],
            'Prijs' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'Voorraad' => ['required', 'integer', 'min:0', 'max:99999'],
            'MinimumVoorraad' => ['required', 'integer', 'min:0', 'max:99999'],
            'Opmerking' => ['nullable', 'string', 'max:255'],
            'leveranciers' => ['nullable', 'array'],
            'leveranciers.*' => ['exists:Leverancier,Id'],
        ];
    }
}