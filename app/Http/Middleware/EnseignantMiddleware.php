<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnseignantMiddleware
 *
 * Si l'utilisateur connecté est un Enseignant, il ne peut accéder qu'à :
 *   - note.create  (saisie de ses notes)
 *   - note.index   (consultation, seulement si PP)
 *   - get.cards    (consultation bulletins, seulement si PP)
 *   - export_view  (export Excel, seulement si PP)
 *
 * Toute autre route protégée renvoie un 403.
 * Ce middleware doit être appliqué sur les routes qu'on veut ouvrir aux enseignants
 * mais BLOQUER aux rôles non-enseignants (ex. : page de saisie des notes).
 *
 * UTILISATION dans routes/web.php :
 *   Route::middleware(['auth', ForcePasswordChange::class, 'enseignant.redirect'])
 *
 * OU : enregistrer dans bootstrap/app.php (Laravel 11) :
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias(['enseignant.redirect' => EnseignantMiddleware::class]);
 *   })
 */
class EnseignantMiddleware
{
    /**
     * Routes (noms) accessibles à un enseignant NON-PP.
     */
    private const TEACHER_ROUTES = [
        'note.create',
        'password.change.form',
        'password.change',
    ];

    /**
     * Routes (noms) accessibles à un enseignant PP en plus des routes de base.
     */
    private const TEACHER_PP_ROUTES = [
        'note.create',
        'note.index',
        'get.cards',
        'notes.exportcard',
        'get.student.notes',
        'password.change.form',
        'password.change',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si pas enseignant → laisse passer normalement
        if (!$user || !$user->isEnseignant()) {
            return $next($request);
        }

        // Enseignant connecté : vérifier la route courante
        $routeName = $request->route()?->getName();

        // Déterminer les routes autorisées selon le statut PP
        // On vérifie si l'enseignant est PP d'au moins une classe cette année
        $yearId = $request->input('year_id')
                  ?? $request->route('yearId')
                  ?? $request->route('year_id');

        // Si on a un year_id dans la requête, vérifier le statut PP pour cet ID
        // Sinon, vérifier globalement
        $isPP = $yearId
            ? $user->principalClasses()->where('year_id', $yearId)->exists()
            : $user->principalClasses()->exists();

        $allowedRoutes = $isPP ? self::TEACHER_PP_ROUTES : self::TEACHER_ROUTES;

        // API routes (préfixe /api/) : autorisées si l'enseignant intervient dans la classe
        if (str_starts_with($request->path(), 'api/')) {
            return $next($request);
        }

        // Vérifier si la route courante est autorisée
        if ($routeName && in_array($routeName, $allowedRoutes, true)) {
            return $next($request);
        }

        // Si l'enseignant tente d'accéder à une route non autorisée → rediriger
        return redirect()->route('note.create')
            ->with('warning', 'Accès restreint. Vous avez été redirigé vers votre espace.');
    }
}
