<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OauthConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OauthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)
            ->redirect();
    }

    public function callback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        // Si el usuario está autenticado, es una conexión (no login)
        if (Auth::check()) {
            $user = Auth::user();

            OauthConnection::updateOrCreate(
                ['user_id' => $user->id, 'provider' => $provider],
                [
                    'provider_id' => $socialUser->getId(),
                    'email' => $socialUser->getEmail(),
                    'name' => $socialUser->getName(),
                    'avatar' => $socialUser->getAvatar(),
                    'access_token' => $socialUser->token ?? null,
                    'refresh_token' => $socialUser->refreshToken ?? null,
                    'token_expires_at' => now()->addSeconds($socialUser->expiresIn ?? 3600),
                ]
            );

            return redirect()->route('settings.index')->with('success', ucfirst($provider) . ' conectado correctamente.');
        }

        // Si no está autenticado, se usa para login/registro
        $user = \App\Models\User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            ['name' => $socialUser->getName(), 'password' => bcrypt(str()->random(12))]
        );

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function disconnect($provider)
    {
        $user = Auth::user();

        $connection = OauthConnection::where('user_id', $user->id)
            ->where('provider', $provider)
            ->first();

        if (!$connection) {
            return back()->with('error', 'No hay conexión activa con ' . ucfirst($provider));
        }

        // Opcional: revocar token en el proveedor (solo si tienes el access_token)
        // if ($provider === 'google' && $connection->access_token) {
        //     try {
        //         Http::asForm()->post('https://oauth2.googleapis.com/revoke', [
        //             'token' => $connection->access_token,
        //         ]);
        //     } catch (\Exception $e) {
        //         // Silencioso: no es crítico si falla
        //     }
        // }

        // Eliminar la conexión
        $connection->delete();

        return back()->with('success', ucfirst($provider) . ' desconectado correctamente.');
    }
}
