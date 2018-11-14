<?php

namespace App\Http\Controllers\Users;

use Carbon\Carbon;
use App\Models\Users\User;
use Illuminate\Http\Request;
use App\Models\Users\PasswordReset;
use App\Http\Controllers\ApiController;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;

class PasswordResetController extends ApiController
{
     /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user){
	        return $this->errorResponse('No podemos encontrar un usuario con ese correo', 404);
		}
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
            	'email' => $user->email,
                'token' => str_random(60)
            ]
        );

        if ($user && $passwordReset){
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );
            return $this->successResponse('Hemos enviado un correo con el enlace de reinicio de contraseña', 200);
    	}
    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset){
        	return $this->errorResponse('El token de reinicio es inválido', 404);
        }
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return $this->errorResponse('El token ya ha expirado', 404);
        }
        return response()->json($passwordReset);
    }
     /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();
        if (!$passwordReset){
        	return $this->errorResponse('El token de reinicio de contraseña es inválido', 404);
        }
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user){
            return $this->errorResponse('No podemos encontrar un usuario con ese correo', 404);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        return response()->json($user);
    }
}