<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';
    protected $businessModuleNamespace = 'Modules\Business\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        $this->mapShopRoutes();
        $this->mapUpdateProductsRoutes();
        if(app()->environment('local')){
            $this->mapDevRoutes();
        }

        $this->routes(function () {
            Route::middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function mapDomainBusinessRoutes()
    {
        Route::middleware('web')
            ->namespace($this->businessModuleNamespace)
            ->group(module_path('Business', '/Routes/web.php'));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
    protected function mapShopRoutes()
    {
        Route::middleware('web')
            ->group(base_path("Modules/Shop/Routes/shopWeb.php"));
    }

    protected function mapUpdateProductsRoutes()
    {
        Route::middleware('web')
            ->group(base_path("routes/update-products.php"));
    }

    private function mapDevRoutes()
    {
        Route::middleware('web')
            ->group(base_path("routes/devRoutes.php"));
    }
}
