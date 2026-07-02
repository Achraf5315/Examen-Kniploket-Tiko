<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'KlantId' => [
                'required',
                'integer',
                Rule::exists('Klant', 'Id')->where(fn ($query) => $query->where('IsActief', 1)),
            ],
            'MedewerkerId' => [
                'required',
                'integer',
                Rule::exists('Medewerker', 'Id')->where(fn ($query) => $query->where('IsActief', 1)),
            ],
            'BehandelingId' => [
                'required',
                'integer',
                Rule::exists('Behandeling', 'Id')->where(fn ($query) => $query->where('IsActief', 1)),
            ],
            'Datum' => ['required', 'date', 'after_or_equal:today'],
            'Starttijd' => ['required', 'date_format:H:i'],
        ];
    }

    /**
     * Extra validatie voor afspraken op dezelfde dag en in het verleden.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $datum = $this->input('Datum');
            $starttijd = $this->input('Starttijd');

            if (! is_string($datum) || ! is_string($starttijd)) {
                return;
            }

            try {
                $moment = Carbon::createFromFormat('Y-m-d H:i', $datum.' '.$starttijd);
            } catch (\Throwable) {
                return;
            }

            if ($moment->isPast()) {
                $validator->errors()->add('Starttijd', 'Kies een starttijd die nog niet voorbij is.');
            }
        });
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
            'Datum.after_or_equal' => 'Plan alleen afspraken vanaf vandaag in.',
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
