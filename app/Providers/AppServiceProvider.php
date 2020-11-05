<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::failing(function ($connection, $job, $data) {

            // $affected = DB::table('mailboxes')
            //     ->where('id', 105)
            //     ->update(['text' => $data]);



            // Notify team of failing job...
        });
    }
}
