<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    protected static int $width = 800;
    protected static int $height = 600;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pathImages = storage_path('app/public/images');
        if(!file_exists($pathImages)) mkdir($pathImages, 0777, true);

        return [
            'image_url' => basename($pathImages) . "/" . $this->faker->image(dir: $pathImages, width: self::$width, height: self::$height, isFullPath: false, randomize: true, imageExtension: FakerPicsumImagesProvider::WEBP_IMAGE),
            'is_main' => false,
            'position' => null
        ];
    }

    public function product(): static
    {
        self::$width = self::$height = 800;
        return $this;
    }

    public function banner(): static
    {
        self::$width = 1920;
        self::$height = 600;
        return $this;
    }

    public function blog(): static
    {
        self::$width = 1200;
        self::$height = 675;
        return $this;
    }

    public function main(): static
    {
        return $this->state(fn (array $attributes) => ['is_main' => true]);
    }

    public function position(int $sortOrder): static
    {
        return $this->state(fn (array $attributes) => ['position' => $sortOrder]);
    }
}
