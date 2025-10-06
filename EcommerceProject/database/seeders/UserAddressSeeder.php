<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake();
        $userIds = User::pluck('id');

        foreach ($userIds as $userId) {
            foreach (range(1, rand(1, 3)) as $i) {
                UserAddress::create([
                    'user_id' => $userId,
                    'recipient_name' => $faker->name(),
                    'phone' => $faker->phoneNumber(),
                    'province' => $faker->city(),
                    'district' => $faker->citySuffix(),
                    'ward' => $faker->streetSuffix(),
                    'street' => $faker->streetAddress(),
                    'postal_code' => $faker->postcode(),
                    'is_default' => $i === 1,
                ]);
            }
        }
    }
}
