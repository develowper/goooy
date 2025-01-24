<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);
        Fortify::authenticateUsing(function (LoginRequest $request) {
            $loginCol = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            $user = User::where($loginCol, $request->login)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'login' => __('error_auth'),
                ]);
            }
            if ($user->is_block) {
                throw ValidationException::withMessages([
                    'login' => __('user_is_blocked'),
                ]);
            }
            if ($user &&
                Hash::check($request->password, $user->password)) {
                return $user;
            }
        });

        Vite::prefetch(concurrency: 3);
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
