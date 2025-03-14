<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Character>
 */
class CharacterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomDefence = $this->faker->numberBetween(0,3);
        $randomStrength = $this->faker->numberBetween(0,20 - $randomDefence);
        $randomAccuracy = $this->faker->numberBetween(0,20 - $randomDefence - $randomStrength);
        $randomMagic = 20 - $randomDefence - $randomStrength - $randomAccuracy;
    
    
        return [
            'name' => $this->faker->word(),
            'enemy' => $this->faker->randomElement([false, true]),
            'defence' => $randomDefence,
            'strength' => $randomStrength,
            'accuracy' => $randomAccuracy,
            'magic' => $randomMagic,
        ];
    
    }
}
