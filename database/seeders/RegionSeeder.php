<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//class RegionSeeder extends Seeder
//{
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         // Les 10 régions du Cameroun
//         $regions = [
//             'Adamaoua',
//             'Centre',
//             'Est',
//             'Extrême-Nord',
//             'Littoral',
//             'Nord',
//             'Nord-Ouest',
//             'Ouest',
//             'Sud',
//             'Sud-Ouest'
//         ];

//         // Insérer les régions seulement si elles n'existent pas déjà
//         foreach ($regions as $region) {
//             $existingRegion = DB::table('regions')->where('nom', $region)->first();
            
//             if (!$existingRegion) {
//                 DB::table('regions')->insert([
//                     'nom' => $region,
//                     'created_at' => Carbon::now(),
//                     'updated_at' => Carbon::now(),
//                 ]);
//                 $this->command->info("Région '$region' créée avec succès.");
//             } else {
//                 $this->command->info("Région '$region' existe déjà, ignorée.");
//             }
//         }

//         $this->command->info('Traitement des régions du Cameroun terminé !');
//     }
// }

class RegionSeeder extends Seeder
{
    public function run()
    {
        $regions = [
            [
                'nom' => 'Adamaoua',
                'code' => 'AD',
                'chef_lieu' => 'Ngaoundéré',
                'superficie' => 63701,
                'population' => 1265000
            ],
            [
                'nom' => 'Centre',
                'code' => 'CE',
                'chef_lieu' => 'Yaoundé',
                'superficie' => 68953,
                'population' => 4665000
            ],
            [
                'nom' => 'Est',
                'code' => 'ES',
                'chef_lieu' => 'Bertoua',
                'superficie' => 109002,
                'population' => 900000
            ],
            [
                'nom' => 'Extrême-Nord',
                'code' => 'EN',
                'chef_lieu' => 'Maroua',
                'superficie' => 34263,
                'population' => 4300000
            ],
            [
                'nom' => 'Littoral',
                'code' => 'LT',
                'chef_lieu' => 'Douala',
                'superficie' => 20248,
                'population' => 3720000
            ],
            [
                'nom' => 'Nord',
                'code' => 'NO',
                'chef_lieu' => 'Garoua',
                'superficie' => 66090,
                'population' => 2500000
            ],
            [
                'nom' => 'Nord-Ouest',
                'code' => 'NW',
                'chef_lieu' => 'Bamenda',
                'superficie' => 17300,
                'population' => 2100000
            ],
            [
                'nom' => 'Ouest',
                'code' => 'OU',
                'chef_lieu' => 'Bafoussam',
                'superficie' => 13892,
                'population' => 2000000
            ],
            [
                'nom' => 'Sud',
                'code' => 'SU',
                'chef_lieu' => 'Ebolowa',
                'superficie' => 47191,
                'population' => 775000
            ],
            [
                'nom' => 'Sud-Ouest',
                'code' => 'SW',
                'chef_lieu' => 'Buea',
                'superficie' => 25410,
                'population' => 1600000
            ]
        ];

        foreach ($regions as $regionData) {
            Region::create($regionData);
        }
    }
}