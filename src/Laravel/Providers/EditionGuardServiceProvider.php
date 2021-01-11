<?php


namespace NomadicSoft\Laravel\Providers;

use Illuminate\Support\ServiceProvider;

class EditionGuardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\NomadicSoft\EditionGuard\EditionGuard::class, function () {
            return new \NomadicSoft\EditionGuard\EditionGuard(config('edition-guard.api_key'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Configs/edition-guard.php' => config_path('edition-guard.php'),
        ]);
    }
}