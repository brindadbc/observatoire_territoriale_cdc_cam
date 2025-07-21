<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Vérifier si l'utilisateur existe déjà avant de le créer
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Ou utiliser firstOrCreate pour plus de simplicité
        // User::firstOrCreate(
        //     ['email' => 'test@example.com'],
        //     ['name' => 'Test User']
        // );

        $this->call([
           
            RegionSeeder::class,
            DepartementSeeder::class,
            CommunesSeeder::class, 
        ]);
    }
}