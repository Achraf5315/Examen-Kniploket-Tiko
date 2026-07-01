<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Afspraak extends TikoModel
{
    /** @use HasFactory<\Database\Factories\AfspraakFactory> */
    use HasFactory;

    protected $table = 'Afspraak';

    public function klant(): BelongsTo
    {
        return $this->belongsTo(Klant::class, 'KlantId', 'Id');
    }

    public function medewerker(): BelongsTo
    {
        return $this->belongsTo(Medewerker::class, 'MedewerkerId', 'Id');
    }

    public function behandeling(): BelongsTo
    {
        return $this->belongsTo(Behandeling::class, 'BehandelingId', 'Id');
    }
}
