<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirige al usuario a la página de autenticación de Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtiene la información del usuario desde Google y lo autentica.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->user();
        } catch (\Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Error al autenticar con Google: ' . $e->getMessage());
        }

        // Validar que sea correo institucional UNIPAZ
        if (!str_ends_with($googleUser->getEmail(), '@unipaz.edu.co')) {
            return redirect()->route('login')
                ->with('error', 'Solo se permite el ingreso con correo institucional @unipaz.edu.co');
        }

        // Buscar o crear el usuario
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if (!$user) {
            // Nuevo estudiante
            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
                'role'              => 'student',
                'email_verified_at' => now(),
            ]);

            // Crear perfil de estudiante vacío
            StudentProfile::create([
                'user_id' => $user->id,
                'program' => 'Sin especificar',
            ]);
        } else {
            // Actualizar datos de Google si ya existe
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        }

        if (!$user->active) {
            return redirect()->route('login')
                ->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
        }

        Auth::login($user, true);

        return redirect()->intended(route('student.dashboard'));
    }
}
