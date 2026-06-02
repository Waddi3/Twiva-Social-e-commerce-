<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\Admin\AdminBusinessController;
use Modules\Admin\Http\Controllers\Admin\AdminCommonController;
use Modules\Admin\Http\Controllers\Admin\AdminDashboardController;
use Modules\Admin\Http\Controllers\Admin\AdminInfluencerController;
use Modules\Admin\Http\Controllers\Admin\AdminInfluencerProductController;
use Modules\Admin\Http\Controllers\Admin\AdminPartnerController;
use Modules\Admin\Http\Controllers\Admin\AdminProductController;
use Modules\Admin\Http\Controllers\Admin\BuyerController;
use Modules\Admin\Http\Controllers\Admin\NotificationController;
use Modules\Admin\Http\Controllers\Admin\OrderController;
use Modules\Admin\Http\Controllers\Admin\TransactionController;
use Modules\Admin\Http\Controllers\Auth\AdminLoginController;
use Modules\System\Http\Controllers\SystemController;

Route::domain('admin.' . config('twiva.bases.shop_domain'))->group(function () {
    Route::get('sign-in', [AdminLoginController::class, 'showLogin'])->name('admin.login');
    Route::post('sign-in', [AdminLoginController::class, 'login'])->name('admin.login.post');
    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::redirect('/', '/dashboard');
        Route::get('influencers/verify', [AdminInfluencerController::class, 'verify'])->name('admin.influencers.verify');
        Route::get('influencers/verify-filter', [AdminInfluencerController::class, 'verifyFilter'])->name('admin.influencers.verifyFilter');

        Route::name('admin.')->group(function () {
            Route::resource('influencers', AdminInfluencerController::class);
            Route::resource('business', AdminBusinessController::class);
            Route::resource('influencerProduct', AdminInfluencerProductController::class);
        });

//        Route::get('business', [AdminBusinessController::class, 'index'])->name('admin.business.index');

        Route::get('/influencer/connected/{id}', [AdminBusinessController::class, 'influencersConnected'])->name('admin.influencer.connected');
        Route::get('/influencer/products/{id}', [AdminInfluencerController::class, 'products'])->name('admin.influencer.products');

        Route::post('user/{id}/login-as', [AdminCommonController::class, 'loginAs'])->name('admin.user.loginAs');

        Route::get('pagination/fetch_data', [AdminCommonController::class, 'fetchData'])->name('admin.ajax.pagination.fetch.data');


        Route::get('/business/products/{id}', [AdminBusinessController::class, 'products'])->name('admin.business.products');
//        Route::get('/changeStatus', [AdminBusinessController::class, 'changeStatus'])->name('admin.change.status.product');
        Route::get('/changeStatus', [AdminBusinessController::class, 'changeStatus'])->name('admin.change.business.status');
        Route::get('/business/product/{id}/eshops', [AdminBusinessController::class, 'eShopsConnected'])->name('admin.business.product.eshops');
        Route::post('/business/bulk/upload', [AdminBusinessController::class, 'bulkProductUpload'])->name('admin.business.product.bulk.upload');
        Route::get('/product/{id}', [AdminProductController::class, 'show'])->name('admin.products.show');

        Route::get('/update-waive-prompt', [AdminBusinessController::class,'updateWaivePrompt'])->name('admin.business.activation.waive.prompt');
        Route::get('/update-accessing-earnings-prior-to-delivery', [AdminBusinessController::class,'updateAccessingEarningsPriorToDelivery'])->name('admin.business.activation.toggle.accessing.earnings.prior.to.delivery');

        Route::post('send/credentials', [AdminCommonController::class, 'sendCredentials'])->name('admin.send.credentials');

        Route::get('/changePassword/{id}', [AdminCommonController::class, 'changePasswordPage'])->name('admin.change.password');
        Route::post('/changePassword', [AdminCommonController::class, 'changePassword'])->name('admin.change.password.post');


        Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::get('/product/{id}', [AdminProductController::class, 'show'])->name('admin.products.show');
        Route::get('download-campaigns-order-csv', [AdminProductController::class, 'downloadCampaignsOrderCsv'])
            ->name('admin.products.downloadCampaignsOrderCsv');

        Route::get('/changeBusinessProductStatus', [AdminBusinessController::class, 'productStatus'])->name('admin.change.product.status');
        Route::get('/changeTrendingProductStatus', [AdminBusinessController::class, 'toggleProductTrending'])->name('admin.change.trending.product.status');
        Route::get('/pagination/product_filter', [AdminCommonController::class, 'productFilter'])->name('admin.product.filter');

        Route::get('/getStates', [AdminCommonController::class, 'states'])->name('admin.get.states');
        Route::get('/getCities', [AdminCommonController::class, 'cities'])->name('admin.get.cites');

        Route::get('/pagination/basic_filter', [AdminCommonController::class, 'basicFilter'])->name('admin.pagination.basic.filter');

        Route::get('/influencer/product/fetch_data', [AdminCommonController::class, 'influencerProductSearch'])->name('admin.influencer.product.fetch.data');
        Route::get('/business/product/fetch_data', [AdminCommonController::class, 'businessProductSearch'])->name('admin.business.product.fetch.data');


//        Route::get('/export-influencer', 'Admin\InfluencerController@exportCsv')->name('exportInfluencers');
        Route::get('/changeInfluencerProductStatus', [AdminInfluencerController::class, 'productStatus'])->name('admin.influencer.product.change.status');
//        Route::get('/export-influencer-products/{id}', 'Admin\InfluencerController@exportProductCsv')->name('exportInfluencerProducts');

        Route::post('sign-out', [AdminLoginController::class, 'logout'])->name('admin.logout');
        Route::group(['prefix' => 'partner'], function () {
            Route::get('/', [AdminPartnerController::class, 'index'])->name('admin.partner.index');
            Route::get('/create', [AdminPartnerController::class, 'create'])->name('admin.partner.create');
            Route::post('/store', [AdminPartnerController::class, 'store'])->name('admin.partner.store');
            Route::get('/{id}/edit', [AdminPartnerController::class, 'edit'])->name('admin.partner.edit');
            Route::get('/{id}', [AdminPartnerController::class, 'show'])->name('admin.partner.show');
            Route::put('/{id}/update', [AdminPartnerController::class, 'update'])->name('admin.partner.update');
            Route::post('/upload', [AdminPartnerController::class, 'upload'])->name('admin.partner.upload');
        });
        Route::group(['prefix' => 'buyers'], function () {
            Route::get('/', [BuyerController::class, 'index'])->name('admin.buyers.index');
            Route::get('toggle-active-status', [BuyerController::class, 'toggleActiveStatus'])->name('admin.buyers.toggleActiveStatus');
        });
        Route::group(['prefix' => 'transactions'], function () {
            Route::get('/', [TransactionController::class, 'index'])->name('admin.transactions.index');
            Route::get('/filter', [TransactionController::class, 'filter']);
        });
        Route::group(['prefix' => 'orders'], function () {
            Route::get('/', [OrderController::class, 'index'])->name('admin.orders.index');
            Route::get('{id}/show', [OrderController::class, 'show'])->name('admin.orders.show');
            Route::get('{id}/download-pdf', [OrderController::class, 'createPDF'])->name('order.detail.pdf');
            Route::get('filter', [OrderController::class, 'filter']);
        });
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', [NotificationController::class, 'index'])->name('admin.notification.index');
        });
        Route::group(['prefix' => 'system-settings'], function () {
            Route::get('/', [SystemController::class, 'show'])->name('admin.system.show');
            Route::post('/update', [SystemController::class, 'update'])->name('admin.system.update');
        });

    });
});
