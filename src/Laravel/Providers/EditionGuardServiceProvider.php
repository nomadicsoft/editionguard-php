<?php


namespace NomadicSoft\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use NomadicSoft\EditionGuard\EditionGuard;

class EditionGuardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EditionGuard::class, function () {
            return new EditionGuard(config('edition-guard.api_token'));
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