<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request voor het valideren van een te wijzigen afspraak.
 *
 * De rolcontrole (Admin/Medewerker) gebeurt in de route-middleware,
 * daarom mag authorize() hier true teruggeven.
 */
class AfspraakWijzigenRequest extends FormRequest
{
    /**
     * Bepaalt of de gebruiker dit verzoek mag uitvoeren.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * De validatieregels voor het wijzigen van een afspraak.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'KlantId' => ['required', 'integer', 'exists:Klant,Id'],
            'MedewerkerId' => ['required', 'integer', 'exists:Medewerker,Id'],
            'BehandelingId' => ['required', 'integer', 'exists:Behandeling,Id'],
            'Datum' => ['required', 'date'],
            'Starttijd' => ['required', 'date_format:H:i'],
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
            'KlantId.required' => 'Kies een klant.',
            'KlantId.exists' => 'De gekozen klant bestaat niet.',
            'MedewerkerId.required' => 'Kies een medewerker.',
            'MedewerkerId.exists' => 'De gekozen medewerker bestaat niet.',
            'BehandelingId.required' => 'Kies een behandeling.',
            'BehandelingId.exists' => 'De gekozen behandeling bestaat niet.',
            'Datum.required' => 'Vul een datum in.',
            'Datum.date' => 'Vul een geldige datum in.',
            'Starttijd.required' => 'Vul een starttijd in.',
            'Starttijd.date_format' => 'Vul een geldige starttijd in (uu:mm).',
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
            'KlantId' => 'klant',
            'MedewerkerId' => 'medewerker',
            'BehandelingId' => 'behandeling',
            'Datum' => 'datum',
            'Starttijd' => 'starttijd',
        ];
    }
}
