<?php

namespace App\Providers;

use App\Http\Services\ProcessPaymentInterface;
use App\Http\Services\ProcessPaymentWompi;
use App\Http\Services\SendMessageInterface;
use App\Http\Services\SendWhatsAppMessage;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::resourceVerbs([
            'create' => 'crear',
            'edit' => 'editar',
        ]);

        Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, 'app.locale_time');

        Schema::defaultStringLength(191);//para que los indices de tablas y demás no sobrepasen la longitud de MySQL

        Paginator::useBootstrapFour();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        define('HOURS_TO_CANCEL_TRAINING', 4);
        define('MONTHS_FOR_NEW_HEALTH_ASSESSMENT', 2);
        $this->app->bind(SendMessageInterface::class, SendWhatsAppMessage::class);
        $this->app->bind(ProcessPaymentInterface::class, ProcessPaymentWompi::class);
    }
}
