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
        $userPlan = $user->active_planuser();

        if ($userPlan) {
            $plan = $userPlan->plan->plan;
            $expiration = c;
            //  $expiration = ucfirst($userPlan->finish_date->formatLocalized('%A %d')).' de '.ucfirst($userPlan->finish_date->formatLocalized('%B, %Y'));
            $counter = $userPlan->counter;
        } else {
           $plan = 'Sin plan activo';
            $expiration = '---';
            $counter = '--' ;
        }

        return [
            'id' => (int)$user->id,
            'first_name' => (string)$user->first_name,
            'last_name' => (string)$user->last_name,
            'full_name' => (string)$user->first_name.' '.$user->last_name,
            'email' => (string)$user->email,
            'birthdate' => (string)$user->birthdate,
            'gender' => (string)$user->gender,
            'phone' => (int)$user->phone,
            'address' => (string)$user->address,
            'timezone' => (string)$user->timezone ?? 'utc',
            // 'emergency_contact' => (int)$user->emergency_id,
            'status' => (int)$user->status_user,
            'tutorial' => (boolean)$user->tutorial,
            'avatar' => $user->avatar,
            'rels' => [
                'self' => [
                    'href' => route('users.show', $user->id),
                ],
                'plans' => [
                    'href' => route('users.planusers.index', $user->id),
                ],
                'active_plan' => [
                    'plan' => (string)$plan,
                    'expiration' => (string)$expiration,
                    'href' => route('users.planusers.active', $user->id),
                ],
                'stats' => [
                    'clases_consumed' => (int)$user->reservations(3)->count(),
                    'clases_quantity' => (int)$counter,
                    'clases_lost' => (int)$user->reservations(4)->count(),
                    'assistance' => '',
                ],
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'birthdate' => 'birthdate',
            'gender' => 'gender',
            'phone' => 'phone',
            'address' => 'address',
            // 'contactoEmergencia' => 'emergency_id',
            'status' => 'status_user',
            'tutorial' => 'tutorial',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'birthdate' => 'birthdate',
            'gender' => 'gender',
            'phone' => 'phone',
            'address' => 'address',
            'status_user' => 'status',
            'tutorial' => 'tutorial',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
