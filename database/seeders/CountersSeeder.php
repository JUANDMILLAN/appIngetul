<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('counters')->updateOrInsert(
            ['key' => 'quotations'],
            ['value' => 0, 'updated_at' => now(), 'created_at' => now()]
        );
    }
}
