<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Behandeling;

class MedewerkerPerBehandeling extends TikoModel
{
    /** @use HasFactory<\Database\Factories\MedewerkerPerBehandelingFactory> */
    use HasFactory;

    protected $table = 'MedewerkerPerBehandeling';

    public function medewerker(): BelongsTo
    {
        return $this->belongsTo(Medewerker::class, 'MedewerkerId', 'Id');
    }

    public function behandeling(): BelongsTo
    {
        return $this->belongsTo(Behandeling::class, 'BehandelingId', 'Id');
    }
}
