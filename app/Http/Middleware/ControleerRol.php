<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware die controleert of de ingelogde gebruiker één van de vereiste rollen heeft.
 *
 * Gebruik in routes: middleware('rol:Admin,Medewerker')
 * De rolnamen komen uit de bestaande Rol-tabel (Admin = eigenaar/beheerder).
 */
class ControleerRol
{
    /**
     * Behandel het binnenkomende verzoek en controleer de rol van de gebruiker.
     *
     * @param  string  ...$rollen  De toegestane rolnamen (bijvoorbeeld 'Admin', 'Medewerker')
     */
    public function handle(Request $request, Closure $next, string ...$rollen): Response
    {
        $gebruiker = $request->user();

        // Controleer via de bestaande rollen-relatie of de gebruiker een actieve
        // koppeling heeft met één van de toegestane rollen
        $heeftToegang = $gebruiker !== null && $gebruiker->rollen()
            ->whereIn('Rol.Rolnaam', $rollen)
            ->where('Rol.IsActief', 1)
            ->wherePivot('IsActief', 1)
            ->exists();

        if (! $heeftToegang) {
            // Log de geweigerde toegang zodat dit terug te vinden is in de logbestanden
            Log::warning('Toegang geweigerd: gebruiker heeft niet de vereiste rol.', [
                'gebruiker_id' => $gebruiker?->Id,
                'vereiste_rollen' => $rollen,
                'url' => $request->fullUrl(),
            ]);

            abort(403, 'Je hebt geen rechten om deze pagina te bekijken.');
        }

        return $next($request);
    }
}
