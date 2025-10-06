<?php

namespace App\Providers;

use Faker\Generator;
use Illuminate\Support\ServiceProvider;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if(class_exists(FakerPicsumImagesProvider::class)){
            $this->app->extend(Generator::class. ':' . (config('app.faker_locale') ?? 'en_US'), function (Generator $faker) {
                $faker->addProvider(new FakerPicsumImagesProvider($faker));
                return $faker;
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
