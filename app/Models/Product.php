<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Product
 *
 * Puur data-object (DTO), GEEN Eloquent-model. Er wordt in deze applicatie
 * niet met Eloquent/query builder relaties gewerkt: alle databasetoegang
 * (ophalen, aanmaken, bijwerken, verwijderen) verloopt via stored procedures
 * (sp_GetProducts, sp_GetProductById, sp_CreateProduct, sp_UpdateProduct,
 * sp_DeleteProduct — zie Sql_dag2.sql / de migraties in database/migrations).
 *
 * Dit model bestaat alleen om de rijen die DB::select(...)/DB::selectOne(...)
 * teruggeven (stdClass) om te zetten naar een getypt, voorspelbaar object
 * met dezelfde kolomnamen als de database (PascalCase), zodat de Controller
 * en Blade-views niet rechtstreeks met ongetypte stdClass hoeven te werken.
 */
class Product
{
    public function __construct(
        public readonly int $Id,
        public readonly string $Productnaam,
        public readonly string $EanCode,
        public readonly int $CategorieId,
        public readonly float $Prijs,
        public readonly int $Voorraad,
        public readonly int $MinimumVoorraad,
        public readonly bool $IsActief,
        public readonly ?string $Opmerking = null,
        public readonly ?string $DatumAangemaakt = null,
        public readonly ?string $DatumGewijzigd = null,
        public readonly ?string $categorie_naam = null,
        public readonly ?string $leveranciers_ids = null,
        public readonly ?string $leveranciers_namen = null,
    ) {
    }

    /**
     * Zet een rij (stdClass) die door een stored procedure wordt
     * teruggegeven om naar een Product-object.
     *
     * @param object $row Rij zoals teruggegeven door DB::select(...)
     */
    public static function fromRow(object $row): self
    {
        return new self(
            Id: (int) $row->Id,
            Productnaam: (string) $row->Productnaam,
            EanCode: (string) $row->EanCode,
            CategorieId: (int) ($row->CategorieId ?? $row->categorie_id),
            Prijs: (float) $row->Prijs,
            Voorraad: (int) $row->Voorraad,
            MinimumVoorraad: (int) $row->MinimumVoorraad,
            IsActief: (bool) $row->IsActief,
            Opmerking: $row->Opmerking ?? null,
            DatumAangemaakt: $row->DatumAangemaakt ?? null,
            DatumGewijzigd: $row->DatumGewijzigd ?? null,
            categorie_naam: $row->categorie_naam ?? null,
            leveranciers_ids: $row->leveranciers_ids ?? null,
            leveranciers_namen: $row->leveranciers_namen ?? null,
        );
    }

    /**
     * Zet een resultaatset (array van stdClass-rijen) om naar een array
     * van Product-objecten.
     *
     * @param array<int, object> $rows
     * @return array<int, self>
     */
    public static function collectionFromRows(array $rows): array
    {
        return array_map(static fn (object $row): self => self::fromRow($row), $rows);
    }

    /**
     * Bepaalt of dit product een lage voorraad heeft (voor de waarschuwing
     * die getoond wordt zodra de voorraad op/onder het minimum zakt).
     */
    public function heeftLageVoorraad(): bool
    {
        return $this->Voorraad <= $this->MinimumVoorraad;
    }

    /**
     * De gekoppelde leverancier-Id's als array van integers
     * (leveranciers_ids komt als kommagescheiden string uit de procedure).
     *
     * @return list<int>
     */
    public function leverancierIds(): array
    {
        if ($this->leveranciers_ids === null || $this->leveranciers_ids === '') {
            return [];
        }

        return array_map('intval', explode(',', $this->leveranciers_ids));
    }
}