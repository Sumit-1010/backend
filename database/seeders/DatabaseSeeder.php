<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Base;
use App\Models\Asset;
use App\Models\Assignment;
use App\Models\Transaction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Base::factory(5)->create();
        Asset::factory(20)->create();
        Assignment::factory(8)->create();
        Transaction::factory(15)->create();

        // $this->call([
        //     UserSeeder::class
        // ]);
    }
}
