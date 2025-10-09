<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\NotificationUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Create default notifications */
        Notification::factory(3)->sequence(
            ['type' => 1],
            ['type' => 2],
            ['type' => 3],
            ['type' => 4],
            ['type' => 5]
        )->create();

        /* Create random notifications */
        $notifications = Notification::factory(10)->create();

        /* Create random notification users */
        foreach ($notifications->pluck('id') as $notificationId) {
            NotificationUser::factory(rand(2, 10))->resetFaker()->create([
                'notification_id' => $notificationId
            ]);
        }
    }
}
