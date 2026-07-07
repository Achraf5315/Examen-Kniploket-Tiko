<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Categorie
 *
 * Puur data-object (DTO), GEEN Eloquent-model. De gegevens komen uit de
 * stored procedure sp_GetCategories (zie
 * 2026_07_07_041305_create_sp_categories_suppliers_final.php). Dit model
 * bestaat alleen om die stdClass-rijen om te zetten naar een getypt object.
 */
class Categorie
{
    public function __construct(
        public readonly int $Id,
        public readonly string $Naam,
        public readonly bool $IsActief,
        public readonly ?string $Opmerking = null,
        public readonly ?string $DatumAangemaakt = null,
        public readonly ?string $DatumGewijzigd = null,
    ) {
    }

    /**
     * Zet een rij (stdClass) die sp_GetCategories teruggeeft om naar een
     * Categorie-object.
     *
     * @param object $row Rij zoals teruggegeven door DB::select(...)
     */
    public static function fromRow(object $row): self
    {
        return new self(
            Id: (int) $row->Id,
            Naam: (string) $row->Naam,
            IsActief: (bool) $row->IsActief,
            Opmerking: $row->Opmerking ?? null,
            DatumAangemaakt: $row->DatumAangemaakt ?? null,
            DatumGewijzigd: $row->DatumGewijzigd ?? null,
        );
    }

    /**
     * Zet een resultaatset (array van stdClass-rijen) om naar een array
     * van Categorie-objecten.
     *
     * @param array<int, object> $rows
     * @return array<int, self>
     */
    public static function collectionFromRows(array $rows): array
    {
        return array_map(static fn (object $row): self => self::fromRow($row), $rows);
    }
}