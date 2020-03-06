<?php

namespace App\Providers;

use App\Models\Users\User;
use Laravel\Passport\Passport;
use App\Policies\Users\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        \Laravel\Passport\Passport::routes(null, ['middleware' => 'tenancy.enforce']);

        $this->commands([
            \Laravel\Passport\Console\InstallCommand::class,
            \Laravel\Passport\Console\ClientCommand::class,
            \Laravel\Passport\Console\KeysCommand::class,
        ]);

        Passport::tokensExpireIn(now()->addDays(7));

        Passport::refreshTokensExpireIn(now()->addDays(60));
    }
}
