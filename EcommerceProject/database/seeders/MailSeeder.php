<?php

namespace Database\Seeders;

use App\Models\Mail;
use App\Models\MailUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Create default mails */
        Mail::factory(5)->sequence(
            ['type' => 1],
            ['type' => 2],
            ['type' => 3],
            ['type' => 4],
            ['type' => 5]
        )->create();

        /* Create random mails */
        $mails = Mail::factory(10)->create();

        /* Create random mail users */
        foreach ($mails->pluck('id') as $mailId) {
            $batchKey = (string) Str::uuid();
            MailUser::factory(rand(2, 10))->resetFaker()->create([
                'mail_id' => $mailId,
                'batch_key' => $batchKey
            ]);
        }
    }
}
