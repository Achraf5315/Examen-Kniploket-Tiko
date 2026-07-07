<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model voor gebruikersaccounts (login), gekoppeld aan de databasetabel 'Gebruiker'.
 *
 * Bevat accessors die de Nederlandse PascalCase-kolommen (Gebruikersnaam, Email,
 * Wachtwoord) mappen naar de standaard Laravel-authenticatievelden (name, email,
 * password), zodat de ingebouwde auth-scaffolding zonder aanpassingen werkt.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // De databasetabel heet 'Gebruiker' (Nederlands) in plaats van 'users'.
    protected $table = 'Gebruiker';

    protected $primaryKey = 'Id';

    // Standaardwaarde voor IsActief, zodat een nieuwe gebruiker altijd actief begint.
    // Dit voorkomt een NOT NULL-fout wanneer IsActief niet expliciet wordt meegegeven.
    protected $attributes = [
        'IsActief' => true,
    ];

    // Mass-assignment whitelist.
    // De PascalCase-kolommen (Gebruikersnaam/Email/Wachtwoord/IsActief) staan hier expliciet in,
    // zodat je via Tinker rechtstreeks een gebruiker kunt aanmaken zonder "geen default value"-crash:
    //   User::create(['Gebruikersnaam' => 'admin', 'Email' => 'a@b.nl', 'Wachtwoord' => 'geheim']);
    // De aliassen name/email/password blijven meegaan voor de standaard Laravel-authenticatie
    // (de accessors hieronder mappen die naar de juiste PascalCase-kolommen).
    protected $fillable = [
        'Gebruikersnaam',
        'Email',
        'Wachtwoord',
        'IsActief',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'Wachtwoord',
    ];

    public const CREATED_AT = 'DatumAangemaakt';

    public const UPDATED_AT = 'DatumGewijzigd';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'IsActief' => 'boolean',
            'DatumAangemaakt' => 'datetime',
            'DatumGewijzigd' => 'datetime',
            'email_verified_at' => 'datetime',
            'Wachtwoord' => 'hashed',
        ];
    }

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $attributes['Id'] ?? null,
            set: fn ($value) => ['Id' => $value],
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $attributes['Gebruikersnaam'] ?? null,
            set: fn ($value) => ['Gebruikersnaam' => $value],
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $attributes['Email'] ?? null,
            set: fn ($value) => ['Email' => $value],
        );
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $attributes['Wachtwoord'] ?? null,
            set: fn ($value) => ['Wachtwoord' => $value],
        );
    }

    protected function emailVerifiedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $attributes['EmailGeverifieerdOp'] ?? null,
            set: fn ($value) => ['EmailGeverifieerdOp' => $value],
        );
    }

    public function getAuthPasswordName(): string
    {
        return 'Wachtwoord';
    }

    public function getEmailForPasswordReset(): string
    {
        return (string) $this->Email;
    }

    public function routeNotificationForMail($notification = null): ?string
    {
        return $this->Email;
    }

    public function klant(): HasOne
    {
        return $this->hasOne(Klant::class, 'GebruikerId', 'Id');
    }

    public function medewerker(): HasOne
    {
        return $this->hasOne(Medewerker::class, 'GebruikerId', 'Id');
    }

    public function rollen(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'RolPerGebruiker', 'GebruikerId', 'RolId')
            ->withPivot(['Id', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }
}
