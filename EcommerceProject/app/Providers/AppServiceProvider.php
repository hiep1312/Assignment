<?php

namespace App\Providers;

use Faker\Generator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Mail;
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

        // Configure global mail behavior
        if(config('mail.always_to.address') && config('app.env') !== "production"){
            Mail::alwaysTo(config('mail.always_to.address'), config('mail.always_to.name'));
        }

        if(config('mail.return_path')){
            Mail::alwaysReturnPath(config('mail.return_path'));
        }

        // Check role middleware
        Blade::if('role', function (string ...$roles) {
            return Auth::check() && in_array(Auth::user()->role->value, $roles);
        });
    }
}
