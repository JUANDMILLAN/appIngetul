<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call(StudyTypeSeeder::class);
    $this->call(\Database\Seeders\CountersSeeder::class);
     $this->call(RolesAndPermissionsSeeder::class);

}
// database/seeders/DatabaseSeeder.php




}