<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Database\Factories\ProviderFactory;
use Database\Factories\ServiceCategoryFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin::create([
        //     'name' => 'Admin User',
        //     'phone' => '0991094752',
        //     'password' => '1qaz!QAZ',
        // ]);
        //ServiceCategoryFactory::new()->count(20)->create();
        // ProviderFactory::new()->count(50)->create();
    }
}
