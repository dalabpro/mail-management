<?php

namespace Kgregorywd\MailManagement;

use Kgregorywd\MailManagement\Drivers\MailManagement;
use Route;
use Illuminate\Support\ServiceProvider;

class MailManagementServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Kgregorywd\MailManagement\Http\Controllers';

    /**
     * The available commands
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mapWebRoutes();

        $this->registerTranslations();

        $this->registerMigrations();

//        MenuBuilder::build();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('MailManagement', function () {

            return new MailManagement();
        });

        $this->registerCommands();

        $this->registerViews();
    }

    /**
     * Register the commands.
     */
    public function registerCommands()
    {
        $this->commands($this->commands);
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        $sourcePath = __DIR__.'/Routes';

        Route::middleware('web')
            ->namespace($this->namespace)
            ->group("$sourcePath/web.php");
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/vendor/currencies');

        $sourcePath = __DIR__.'/Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/vendor/currencies';
        }, \Config::get('view.paths')), [$sourcePath]), 'currency');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/vendor/currencies');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'currency');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/Resources/lang', 'currency');
        }
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
    }
}
