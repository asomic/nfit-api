<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user){
            return response()->json(['message' => 'No existe un usuario con ese correo.'], 404);
        }else{
            Password::sendResetLink(['email' => $user->email]);
            return response()->json([
                'message' => 'Te hemos enviado un correo para actualizar tu contraseña'
            ]);
        }
    }
}