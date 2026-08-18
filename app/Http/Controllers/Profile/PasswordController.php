<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Muestra la vista de perfil y cambio de contraseña.
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('role');

        return view('profile.password', compact('user'));
    }

    /**
     * Actualiza la contraseña del usuario autenticado.
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ], [
                'current_password.required' => 'La contraseña actual es obligatoria.',
                'current_password.current_password' => 'La contraseña actual es incorrecta.',
                'password.required' => 'La nueva contraseña es obligatoria.',
                'password.confirmed' => 'La confirmación de la contraseña no coincide.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            ]);

            $user = $request->user();
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contraseña actualizada correctamente.'
                ]);
            }

            return redirect()
                ->route('profile.password')
                ->with('success', 'Contraseña actualizada correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar cambiar la contraseña.'
                ], 500);
            }

            return back()
                ->with('error', 'Ocurrió un error al intentar cambiar la contraseña.');
        }
    }
}
