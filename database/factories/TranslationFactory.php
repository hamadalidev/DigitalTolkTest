<?php

namespace Database\Factories;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        $key = Str::random(8) . '.' . Str::random(8);
        
        return [
            'locale_id' => Locale::factory(),
            'key' => $key,
            'value' => $this->faker->sentence,
            'device_type' => $this->faker->randomElement(Translation::DEVICE_TYPES),
            'group' => $this->faker->randomElement(['general', 'auth', 'validation', 'messages']),
            'is_active' => true,
        ];
    }
}
