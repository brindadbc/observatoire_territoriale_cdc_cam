<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        $departements = [
            // ADAMAOUA
            ['nom' => 'VINA', 'region' => 'Adamaoua'],
            ['nom' => 'MBERE', 'region' => 'Adamaoua'],
            
            // CENTRE
            ['nom' => 'MFOUNDI', 'region' => 'Centre'],
            ['nom' => 'MEFOU ET AKONO', 'region' => 'Centre'],
            ['nom' => 'MEFOU ET AFAMBA', 'region' => 'Centre'],
            ['nom' => 'LEKIE', 'region' => 'Centre'],
            ['nom' => 'MBAM ET KIM', 'region' => 'Centre'],
            ['nom' => 'MBAM ET INOUBOU', 'region' => 'Centre'],
            ['nom' => 'HAUTE SANAGA', 'region' => 'Centre'],
            ['nom' => 'NYONG ET SO\'O', 'region' => 'Centre'],
            ['nom' => 'NYONG ET MFOUMOU', 'region' => 'Centre'],
            ['nom' => 'NYONG ET KELLE', 'region' => 'Centre'],
            
            // EST
            ['nom' => 'LOM ET DJEREM', 'region' => 'Est'],
            ['nom' => 'KADEY', 'region' => 'Est'],
            ['nom' => 'BOUMBA ET NGOKO', 'region' => 'Est'],
            ['nom' => 'HAUT NYONG', 'region' => 'Est'],
            
            // EXTREME-NORD
            ['nom' => 'DIAMARE', 'region' => 'Extrême-Nord'],
            ['nom' => 'MAYO KANI', 'region' => 'Extrême-Nord'],
            ['nom' => 'MAYO DANAI', 'region' => 'Extrême-Nord'],
            ['nom' => 'LOGONE ET CHARI', 'region' => 'Extrême-Nord'],
            ['nom' => 'MAYO SAVA', 'region' => 'Extrême-Nord'],
            ['nom' => 'MAYO TSANAGA', 'region' => 'Extrême-Nord'],
            
            // LITTORAL
            ['nom' => 'WOURI', 'region' => 'Littoral'],
            ['nom' => 'SANAGA MARITIME', 'region' => 'Littoral'],
            ['nom' => 'MOUNGO', 'region' => 'Littoral'],
            ['nom' => 'NKAM', 'region' => 'Littoral'],
            
            // NORD
            ['nom' => 'BENOUE', 'region' => 'Nord'],
            ['nom' => 'MAYO LOUTI', 'region' => 'Nord'],
            ['nom' => 'FARO', 'region' => 'Nord'],
            ['nom' => 'MAYO-REY', 'region' => 'Nord'],
            
            // NORD-OUEST
            ['nom' => 'MEZAM', 'region' => 'Nord-Ouest'],
            ['nom' => 'MOMO', 'region' => 'Nord-Ouest'],
            ['nom' => 'MENCHUM', 'region' => 'Nord-Ouest'],
            ['nom' => 'DONGA-MANTUNG', 'region' => 'Nord-Ouest'],
            ['nom' => 'BUI', 'region' => 'Nord-Ouest'],
            ['nom' => 'BOYO', 'region' => 'Nord-Ouest'],
            ['nom' => 'NGOKENTUNJA', 'region' => 'Nord-Ouest'],
            
            // OUEST
            ['nom' => 'MIFI', 'region' => 'Ouest'],
            ['nom' => 'BAMBOUTOS', 'region' => 'Ouest'],
            ['nom' => 'MENOUA', 'region' => 'Ouest'],
            ['nom' => 'HAUT-NKAM', 'region' => 'Ouest'],
            ['nom' => 'NDE', 'region' => 'Ouest'],
            ['nom' => 'NOUN', 'region' => 'Ouest'],
            ['nom' => 'HAUTS-PLATEAUX', 'region' => 'Ouest'],
            ['nom' => 'KOUNG-KHI', 'region' => 'Ouest'],
            
            // SUD
            ['nom' => 'MVINA', 'region' => 'Sud'],
            ['nom' => 'DJA-ET-LOBO', 'region' => 'Sud'],
            ['nom' => 'VALLEE DU NTEM', 'region' => 'Sud'],
            ['nom' => 'OCEAN', 'region' => 'Sud'],
            
            // SUD-OUEST
            ['nom' => 'FAKO', 'region' => 'Sud-Ouest'],
            ['nom' => 'MANYU', 'region' => 'Sud-Ouest'],
            ['nom' => 'MANENGOUBA', 'region' => 'Sud-Ouest'],
        ];

        foreach ($departements as $departement) {
            $region = DB::table('regions')->where('nom', $departement['region'])->first();
            
            if ($region) {
                $exists = DB::table('departements')
                    ->where('nom', $departement['nom'])
                    ->where('region_id', $region->id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('departements')->insert([
                        'nom' => $departement['nom'],
                        'region_id' => $region->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->command->info("Département '{$departement['nom']}' ajouté à la région '{$departement['region']}'");
                }
            } else {
                $this->command->error("La région '{$departement['region']}' est introuvable pour le département '{$departement['nom']}' !");
            }
        }
    }
}


// <?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;

// class DepartementSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
        
//         $departements = [
//     // ADAMAOUA
//     ['nom' => 'VINA', 'region' => 'ADAMAOUA'],
//     ['nom' => 'MBERE', 'region' => 'ADAMAOUA'],
//     ['nom' => 'DJELEM', 'region' => 'ADAMAOUA'],
//     ['nom' => 'FARO ET DEO', 'region' => 'ADAMAOUA'],
//     ['nom' => 'MAYO BANYO', 'region' => 'ADAMAOUA'],
    
//     // CENTRE
//     ['nom' => 'MFOUNDI', 'region' => 'CENTRE'],
//     ['nom' => 'MEFOU ET AKONO', 'region' => 'CENTRE'],
//     ['nom' => 'MEFOU ET AFAMBA', 'region' => 'CENTRE'],
//     ['nom' => 'LEKIE', 'region' => 'CENTRE'],
//     ['nom' => 'MBAM ET KIM', 'region' => 'CENTRE'],
//     ['nom' => 'MBAM ET INOUBOU', 'region' => 'CENTRE'],
//     ['nom' => 'HAUTE SANAGA', 'region' => 'CENTRE'],
//     ['nom' => 'NYONG ET SO\'O', 'region' => 'CENTRE'],
//     ['nom' => 'NYONG ET MFOUMOU', 'region' => 'CENTRE'],
//     ['nom' => 'NYONG ET KELLE', 'region' => 'CENTRE'],
    
//     // EST
//     ['nom' => 'LOM ET DJEREM', 'region' => 'EST'],
//     ['nom' => 'KADEY', 'region' => 'EST'],
//     ['nom' => 'BOUMBA ET NGOKO', 'region' => 'EST'],
//     ['nom' => 'HAUT NYONG', 'region' => 'EST'],
    
//     // EXTREME-NORD
//     ['nom' => 'DIAMARE', 'region' => 'EXTREME-NORD'],
//     ['nom' => 'MAYO KANI', 'region' => 'EXTREME-NORD'],
//     ['nom' => 'MAYO DANAI', 'region' => 'EXTREME-NORD'],
//     ['nom' => 'LOGONE ET CHARI', 'region' => 'EXTREME-NORD'],
//     ['nom' => 'MAYO SAVA', 'region' => 'EXTREME-NORD'],
//     ['nom' => 'MAYO TSANAGA', 'region' => 'EXTREME-NORD'],
    
//     // LITTORAL
//     ['nom' => 'WOURI', 'region' => 'LITTORAL'],
//     ['nom' => 'SANAGA MARITIME', 'region' => 'LITTORAL'],
//     ['nom' => 'MOUNGO', 'region' => 'LITTORAL'],
//     ['nom' => 'NKAM', 'region' => 'LITTORAL'],
    
//     // NORD
//     ['nom' => 'BENOUE', 'region' => 'NORD'],
//     ['nom' => 'MAYO LOUTI', 'region' => 'NORD'],
//     ['nom' => 'FARO', 'region' => 'NORD'],
//     ['nom' => 'MAYO-REY', 'region' => 'NORD'],
    
//     // NORD-OUEST
//     ['nom' => 'MEZAM', 'region' => 'NORD-OUEST'],
//     ['nom' => 'MOMO', 'region' => 'NORD-OUEST'],
//     ['nom' => 'MENCHUM', 'region' => 'NORD-OUEST'],
//     ['nom' => 'DONGA-MANTUNG', 'region' => 'NORD-OUEST'],
//     ['nom' => 'BUI', 'region' => 'NORD-OUEST'],
//     ['nom' => 'BOYO', 'region' => 'NORD-OUEST'],
//     ['nom' => 'NGOKENTUNJA', 'region' => 'NORD-OUEST'],
    
//     // OUEST
//     ['nom' => 'MIFI', 'region' => 'OUEST'],
//     ['nom' => 'BAMBOUTOS', 'region' => 'OUEST'],
//     ['nom' => 'MENOUA', 'region' => 'OUEST'],
//     ['nom' => 'HAUT-NKAM', 'region' => 'OUEST'],
//     ['nom' => 'NDE', 'region' => 'OUEST'],
//     ['nom' => 'NOUN', 'region' => 'OUEST'],
//     ['nom' => 'HAUTS-PLATEAUX', 'region' => 'OUEST'],
//     ['nom' => 'KOUNG-KHI', 'region' => 'OUEST'],
    
//     // SUD
//     ['nom' => 'MVINA', 'region' => 'SUD'],
//     ['nom' => 'DJA-ET-LOBO', 'region' => 'SUD'],
//     ['nom' => 'VALLEE DU NTEM', 'region' => 'SUD'],
//     ['nom' => 'OCEAN', 'region' => 'SUD'],
    
//     // SUD-OUEST
//     ['nom' => 'FAKO', 'region' => 'SUD-OUEST'],
//     ['nom' => 'MANYU', 'region' => 'SUD-OUEST'],
//     ['nom' => 'MEME', 'region' => 'SUD-OUEST'],
//     ['nom' => 'MANENGOUBA', 'region' => 'SUD-OUEST'],
//     ['nom' => 'LEBIALEM', 'region' => 'SUD-OUEST'],
// ];

//         foreach ($departements as $departement) {
//             // Récupérer l'ID de la région
//             $region = DB::table('regions')->where('nom', $departement['region'])->first();
            
//             if ($region) {
//                 // Vérifier si le département n'existe pas déjà
//                 $exists = DB::table('departements')
//                     ->where('nom', $departement['nom'])
//                     ->where('region_id', $region->id)
//                     ->exists();
                
//                 if (!$exists) {
//                     DB::table('departements')->insert([
//                         'nom' => $departement['nom'],
//                         'region_id' => $region->id,
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ]);
//                 }
//             }
//         }
//     }
// } 