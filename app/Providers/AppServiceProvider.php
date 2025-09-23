<?php

namespace App\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Update defaultStringLength
        Builder::defaultStringLength(191);

        // Init layout file
        app(\App\Core\Bootstrap\BootstrapDefault::class)->init();
        Paginator::useBootstrapFive();

        \Illuminate\Database\Query\Builder::macro('toRawSql', function ()    {
            return array_reduce($this->getBindings(), function ($sql, $binding) {
                return preg_replace('/\?/', is_numeric($binding) ? $binding : "'" . $binding . "'", $sql, 1);
            }, $this->toSql());
        });

        \Illuminate\Database\Eloquent\Builder::macro('toRawSql', function () {
            return ($this->getQuery()->toRawSql());
        });



        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        if ($this->app->hasDebugModeEnabled()) {

            DB::listen(function ($query) {
                File::append(
                    storage_path('/logs/query.log'),
                    $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
                );

                // repeat = caracteres 100 vezes
                File::append(
                    storage_path('/logs/query.log'),
                    str_repeat('-', 100) . PHP_EOL
                );
            });
        }
    }
}
