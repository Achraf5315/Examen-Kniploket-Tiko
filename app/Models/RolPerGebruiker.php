<?php

namespace App\Models;

use Database\Factories\RolPerGebruikerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolPerGebruiker extends TikoModel
{
    /** @use HasFactory<RolPerGebruikerFactory> */
    use HasFactory;

    protected $table = 'RolPerGebruiker';

    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'GebruikerId', 'Id');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'RolId', 'Id');
    }
}
