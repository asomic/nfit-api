<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    /**
     * [reset description]
     *
     * @param  Request $request
     * @return JSON
     */
    public function reset(Request $request)
    {
        $request->validate(['email' => 'required|string|email']);

        $userDoesntExist = User::where('email', $request->email)->doesntExist();

        if ($userDoesntExist) {
            return response()->json(
                ['message' => 'No existe un usuario con ese correo.'],
                404
            );
        }

        Password::sendResetLink(['email' => $user->email]);

        return response()->json([
            'message' => 'Te hemos enviado un correo para actualizar tu contraseña'
        ]);
    }
}
