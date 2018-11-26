<?php

namespace Dalab\MailManagement;

use Dalab\MailManagement\Drivers\MailManagement;
use Dalab\MailManagement\Extensions\MenuBuilder;
use Dalab\MailManagement\Models\MailBox;
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
    protected $namespace = 'Dalab\MailManagement\Http\Controllers';

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

        MenuBuilder::build();

        Route::model('mailbox', MailBox::class);
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
        $viewPath = resource_path('views/vendor/mailManagement');

        $sourcePath = __DIR__.'/Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/vendor/mailManagement';
        }, \Config::get('view.paths')), [$sourcePath]), 'MailManagement');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/vendor/mailManagement');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'MailManagement');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/Resources/lang', 'MailManagement');
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
