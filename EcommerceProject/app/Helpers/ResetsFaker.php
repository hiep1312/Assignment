<?php

namespace App\Helpers;

use Faker\Generator;

trait ResetsFaker
{
    /**
     * Resets the Faker instance for the current Factory to clear unique() and generate new fake values.
     */
    public function resetFaker(): static
    {
        app()->forgetInstance(Generator::class);
        $this->faker = $this->withFaker();

        return $this;
    }
}
