<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AffiliateUserController extends Controller
{
    public function generate(): RedirectResponse
    {
        abort_unless(Auth::user()?->role->canModifyCi(), 403);

        $created = 0;
        Affiliate::query()
            ->whereDoesntHave('user')
            ->orderBy('id')
            ->chunkById(100, function ($affiliates) use (&$created) {
                foreach ($affiliates as $affiliate) {
                    $email = $affiliate->email ?: 'ci-'.$affiliate->ci.'@afiliados.fesirmes.local';

                    if (User::where('email', $email)->exists()) {
                        continue;
                    }

                    User::create([
                        'name' => $affiliate->full_name,
                        'email' => $email,
                        'password' => Hash::make($affiliate->ci),
                        'role' => 'Afiliado',
                        'affiliate_id' => $affiliate->id,
                        'must_change_password' => true,
                    ]);
                    $created++;
                }
            });

        AuditLogger::record('usuarios_afiliados.generacion_masiva', null, [], ['creados' => $created]);

        return back()->with('status', "Usuarios de afiliados creados: {$created}.");
    }

    public function reset(Affiliate $affiliate): RedirectResponse
    {
        abort_unless(Auth::user()?->role->canModifyCi(), 403);

        $user = $this->ensureUser($affiliate);
        $user->update([
            'password' => Hash::make($affiliate->ci),
            'must_change_password' => true,
            'password_changed_at' => null,
        ]);

        AuditLogger::record('usuarios_afiliados.password_reseteado', $affiliate, [], ['user_id' => $user->id]);

        return back()->with('status', 'Contraseña reseteada al C.I. del afiliado.');
    }

    public function block(Affiliate $affiliate): RedirectResponse
    {
        abort_unless(Auth::user()?->role->canModifyCi(), 403);

        $user = $this->ensureUser($affiliate);
        $user->update([
            'is_blocked' => true,
            'blocked_at' => now(),
            'blocked_by' => Auth::id(),
        ]);

        AuditLogger::record('usuarios_afiliados.bloqueado', $affiliate, [], ['user_id' => $user->id]);

        return back()->with('status', 'Usuario afiliado bloqueado.');
    }

    public function unblock(Affiliate $affiliate): RedirectResponse
    {
        abort_unless(Auth::user()?->role->canModifyCi(), 403);

        $user = $this->ensureUser($affiliate);
        $user->update([
            'is_blocked' => false,
            'blocked_at' => null,
            'blocked_by' => null,
        ]);

        AuditLogger::record('usuarios_afiliados.desbloqueado', $affiliate, [], ['user_id' => $user->id]);

        return back()->with('status', 'Usuario afiliado activado.');
    }

    private function ensureUser(Affiliate $affiliate): User
    {
        return $affiliate->user ?: User::create([
            'name' => $affiliate->full_name,
            'email' => $affiliate->email ?: 'ci-'.$affiliate->ci.'@afiliados.fesirmes.local',
            'password' => Hash::make($affiliate->ci),
            'role' => 'Afiliado',
            'affiliate_id' => $affiliate->id,
            'must_change_password' => true,
        ]);
    }
}
