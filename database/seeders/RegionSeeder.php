<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Les 10 régions du Cameroun
        $regions = [
            'Adamaoua',
            'Centre',
            'Est',
            'Extrême-Nord',
            'Littoral',
            'Nord',
            'Nord-Ouest',
            'Ouest',
            'Sud',
            'Sud-Ouest'
        ];

        // Insérer les régions seulement si elles n'existent pas déjà
        foreach ($regions as $region) {
            $existingRegion = DB::table('regions')->where('nom', $region)->first();
            
            if (!$existingRegion) {
                DB::table('regions')->insert([
                    'nom' => $region,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $this->command->info("Région '$region' créée avec succès.");
            } else {
                $this->command->info("Région '$region' existe déjà, ignorée.");
            }
        }

        $this->command->info('Traitement des régions du Cameroun terminé !');
    }
}