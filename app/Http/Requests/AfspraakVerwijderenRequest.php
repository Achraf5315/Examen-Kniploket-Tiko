<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request voor het bevestigen van het verwijderen van een afspraak.
 *
 * De gebruiker moet letterlijk het woord VERWIJDEREN intypen voordat
 * de afspraak definitief verwijderd wordt (conform de wireframe).
 */
class AfspraakVerwijderenRequest extends FormRequest
{
    /**
     * Bepaalt of de gebruiker dit verzoek mag uitvoeren.
     * De rolcontrole (Admin/Medewerker) gebeurt in de route-middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * De validatieregels voor de verwijderbevestiging.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'Bevestiging' => ['required', 'in:VERWIJDEREN'],
        ];
    }

    /**
     * Nederlandse foutmeldingen voor de eindgebruiker.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Bevestiging.required' => 'Typ VERWIJDEREN om de afspraak te verwijderen.',
            'Bevestiging.in' => 'Het bevestigingswoord is niet juist. Typ VERWIJDEREN om de afspraak te verwijderen.',
        ];
    }

    /**
     * Nederlandse veldnamen voor in de foutmeldingen.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'Bevestiging' => 'bevestigingswoord',
        ];
    }
}
