<?php

namespace App\Transformers;

use App\Models\Users\User;
use League\Fractal\TransformerAbstract;


class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'identificador' => (int)$user->id,
            'nombre' => (string)$user->first_name,
            'apellido' => (string)$user->last_name,
            'correo' => (string)$user->email,
            'fechaNacimiento' => (string)$user->birthdate,
            'genero' => (string)$user->gender,
            'telefono' => (int)$user->phone,
            'direccion' => (string)$user->address,
            'contactoEmergencia' => (int)$user->emergency_id,
            'estadoUsuario' => (int)$user->status_user_id,
            'fechaCreacion' => (string)$user->created_at,
            'fechaActualizacion' => (string)$user->updated_at,
            'fechaEliminacion' => isset($user->deleted_at) ? (string) $user->deleted_at : null,
            'avatar' => 'http://purasangre.asomic.com/storage/users/'.$user->avatar.'.jpg',

            'rels' => [
                'self' => [
                    'href' => route('users.show', $user->id),
                ],
                'plans' => [
                    'href' => route('users.planusers.index', $user->id),
                ],
                'active_plan' => [
                    'href' => route('users.planusers.active', $user->id),
                ],
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'identificador' => 'id',
            'nombre' => 'first_name',
            'apellido' => 'last_name',
            'correo' => 'email',
            'fechaNacimiento' => 'birthdate',
            'genero' => 'gender',
            'telefono' => 'phone',
            'direccion' => 'address',
            'contactoEmergencia' => 'emergency_id',
            'estadoUsuario' => 'status_user_id',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'identificador',
            'first_name' => 'nombre',
            'last_name' => 'apellido',
            'email' => 'correo',
            'birthdate' => 'fechaNacimiento',
            'gender' => 'genero',
            'phone' => 'telefono',
            'address' => 'direccion',
            'emergency_id' => 'contactoEmergencia',
            'status_user_id' => 'estadoUsuario',
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
