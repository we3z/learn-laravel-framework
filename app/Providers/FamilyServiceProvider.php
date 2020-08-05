<?php

namespace App\Providers;

use App\Service\Family\FamilyService;
use App\Service\Family\PersonService;
use App\Service\Family\TvService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class FamilyServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // 注册

        // 第一种方式
        $this->app->bind('Family', 'App\Service\Family\FamilyService');

        // 第二种方式
        // $this->app->bind('Family', function(){
        //  return new FamilyService(new PersonService(), new TvService());
        // });

        // 第三种方式

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
