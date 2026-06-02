<?php

namespace Modules\ToUpModule\Providers;

use Config;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Modules\ToUpModule\Console\Commands\CleanInfluencerShopTable;
use Modules\ToUpModule\Console\Commands\FixBusinessDetails;
use Modules\ToUpModule\Console\Commands\FixInfluencersDetails;
use Modules\ToUpModule\Console\Commands\FixOrdersSectionCommand;
use Modules\ToUpModule\Console\Commands\FixSizeColumnsCommand;
use Modules\ToUpModule\Console\Commands\FixUsersDetails;
use Modules\ToUpModule\Console\Commands\MigrateBuyersFromUserTableCommand;
use Modules\ToUpModule\Console\Commands\ProductionCommandsOrder;
use Modules\ToUpModule\Console\Commands\RemoveCategoriesDuplicationCommand;
use Modules\ToUpModule\Console\Commands\RemoveUnusedTables;
use Modules\ToUpModule\Console\Commands\RenameReferralColumnInUsersTable;
use Modules\ToUpModule\Console\Commands\RenameTables;

class ToUpModuleServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'ToUpModule';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'toupmodule';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->commands(
            [
                FixSizeColumnsCommand::class,
                RemoveUnusedTables::class,
                FixUsersDetails::class,
                FixInfluencersDetails::class,
                FixBusinessDetails::class,
                FixSizeColumnsCommand::class,
                ProductionCommandsOrder::class,
                RemoveUnusedTables::class,
                RenameReferralColumnInUsersTable::class,
                CleanInfluencerShopTable::class,
                RemoveCategoriesDuplicationCommand::class,
                RenameTables::class,
                MigrateBuyersFromUserTableCommand::class,
                FixOrdersSectionCommand::class
            ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath))
        {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath, $this->moduleNameLower);
        }
        else
        {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path)
        {
            if (is_dir($path . '/modules/' . $this->moduleNameLower))
            {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
