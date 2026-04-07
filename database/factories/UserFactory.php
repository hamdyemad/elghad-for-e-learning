<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    // Tell Laravel which model this factory is for
    protected $model = User::class;

    public function definition()
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('12345679'), // password
            'remember_token'    => Str::random(10),
        ];
    }

    public function run(): void
    {
        \App\Models\User::factory(10)->create();
    }

}
