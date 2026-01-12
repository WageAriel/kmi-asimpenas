<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
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
        Vite::prefetch(concurrency: 3);
        
        // Custom user provider yang hanya mencari user aktif untuk password reset
        Auth::provider('active_users', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends EloquentUserProvider {
                /**
                 * Retrieve a user by the given credentials.
                 */
                public function retrieveByCredentials(array $credentials)
                {
                    if (empty($credentials) ||
                        (count($credentials) === 1 &&
                         array_key_exists('password', $credentials))) {
                        return;
                    }

                    // Build the query
                    $query = $this->newModelQuery();

                    foreach ($credentials as $key => $value) {
                        if (! str_contains($key, 'password')) {
                            $query->where($key, $value);
                        }
                    }
                    
                    // Hanya ambil user yang aktif
                    $query->where('is_active', true);

                    return $query->first();
                }
            };
        });
    }
}
