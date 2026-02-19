<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Filament\Actions\Imports\Models\Import as FilamentImport;
use App\Models\Import as AppImport;


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
        $this->app['config']->set('database.migrations', 'app.migrations');

        $this->app->bind(FilamentImport::class, AppImport::class);

        JsonResource::withoutWrapping();

    }
}
