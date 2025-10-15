<?php

namespace App\Providers;

use Faker\Generator;
use Illuminate\Support\Facades\Blade;
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
            $this->app->extend(Generator::class, function (Generator $faker) {
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
        Blade::componentNamespace("App\\Livewire\\Admin\\Components", 'livewire');
    }
}
