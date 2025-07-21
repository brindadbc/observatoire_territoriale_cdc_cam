<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Region;

class CommunesSeeder extends Seeder
{
    public function run()
    {
        echo "Début du seeding des communes...\n";

        // Données des communes organisées par région et département
        $communesData = [
            'ADAMAOUA' => [
                'VINA' => [
                    ['nom' => "REGION DE L'ADAMAOUA", 'code' => '/'],
                    ['nom' => 'CU NGAOUNDERE', 'code' => '401'],
                    ['nom' => 'GALIM-TIGNERE', 'code' => '416'],
                    ['nom' => 'BANKIM', 'code' => '483'],
                ],
                'MBERE' => [
                    ['nom' => 'MEIGANGA', 'code' => '404'],
                    ['nom' => 'DJOHONG', 'code' => '418'],
                ],
            ],
            'CENTRE' => [
                'MFOUNDI' => [
                     ['nom' => "REGION DU CENTRE", 'code' => '//'],
                    ['nom' => 'CU YAOUNDE', 'code' => '140'],
                    ['nom' => 'YAOUNDE I', 'code' => '147'],
                    ['nom' => 'YAOUNDE 2', 'code' => '148'],
                    ['nom' => 'YAOUNDE 3', 'code' => '149'],
                    ['nom' => 'YAOUNDE 4', 'code' => '177'],
                    ['nom' => 'YAOUNDE 5', 'code' => '144'],
                    ['nom' => 'YAOUNDE 6', 'code' => '948'],
                    ['nom' => 'YAOUNDE 7', 'code' => '188'],
                ],
                'MEFOU ET AKONO' => [
                    ['nom' => 'NGOUMOU', 'code' => '135'],
                    ['nom' => 'MBANKOMO', 'code' => '134'],
                    ['nom' => 'BIKOK', 'code' => '132'],
                    ['nom' => 'AKONO', 'code' => '131'],
                ],
                'MEFOU ET AFAMBA' => [
                    ['nom' => 'MFOU', 'code' => '130'],
                    ['nom' => 'AWAE', 'code' => '136'],
                    ['nom' => 'OLANGUINA', 'code' => '143'],
                ],
                'LEKIE' => [
                    ['nom' => 'OBALA', 'code' => '112'],
                    ['nom' => 'SA\'A', 'code' => '114'],
                ],
                'MBAM ET KIM' => [
                    ['nom' => 'YOKO', 'code' => '126'],
                ],
                'MBAM ET INOUBOU' => [
                    ['nom' => 'OMBESSA', 'code' => '125'],
                ],
                'HAUTE SANAGA' => [
                    ['nom' => 'BIBEY', 'code' => '105'],
                    ['nom' => 'NSEM', 'code' => '106'],
                ],
                'NYONG ET SO\'O' => [
                    ['nom' => 'MBALMAYO', 'code' => '181'],
                    ['nom' => 'NKOLMETET', 'code' => '187'],
                    ['nom' => 'AKOEMAN', 'code' => '185'],
                    ['nom' => 'MENGUEME', 'code' => '186'],
                    ['nom' => 'NGOMEDZAP', 'code' => '183'],
                    ['nom' => 'DZENG', 'code' => '182'],
                ],
                'NYONG ET MFOUMOU' => [
                    ['nom' => 'AKONOLINGA', 'code' => '170'],
                    ['nom' => 'MENGANG', 'code' => '176'],
                    ['nom' => 'ENDOM', 'code' => '172'],
                    ['nom' => 'KOBDOBO', 'code' => '173'],
                ],
                'NYONG ET KELLE' => [
                    ['nom' => 'ESEKA', 'code' => '160'],
                    ['nom' => 'BONDJOCK', 'code' => '169'],
                    ['nom' => 'MATOMB', 'code' => '163'],
                    ['nom' => 'BOT-MAKAK', 'code' => '161'],
                    ['nom' => 'NGUIBASSAL', 'code' => '174'],
                    ['nom' => 'MESSONDO', 'code' => '164'],
                    ['nom' => 'BIYOUHA', 'code' => '168'],
                    ['nom' => 'NGOG MAPOUBI', 'code' => '165'],
                    ['nom' => 'DIBANG', 'code' => '166'],
                ],
            ],
            'EST' => [
                'LOM ET DJEREM' => [
                    ['nom' => 'CU BERTOUA', 'code' => '230'],
                    ['nom' => 'BERTOUA I', 'code' => '240'],
                    ['nom' => 'BERTOUA 2', 'code' => '241'],
                    ['nom' => 'MANDJOU', 'code' => '242'],
                    ['nom' => 'BETARE OYA', 'code' => '232'],
                    ['nom' => 'NGOURA', 'code' => '236'],
                    ['nom' => 'BELABO', 'code' => '235'],
                    ['nom' => 'DIANG', 'code' => '237'],
                    ['nom' => 'GAROUA-BOULAI', 'code' => '233'],
                ],
                'KADEY' => [
                    ['nom' => 'BATOURI', 'code' => '220'],
                    ['nom' => 'MBANG', 'code' => '224'],
                    ['nom' => 'NDELELE', 'code' => '221'],
                    ['nom' => 'KENTZOU', 'code' => '408'],
                    ['nom' => 'NGUELEBOK', 'code' => '407'],
                    ['nom' => 'KETTE', 'code' => '277'],
                    ['nom' => 'OULI', 'code' => '223'],
                ],
                'BOUMBA ET NGOKO' => [
                    ['nom' => 'YOKADOUMA', 'code' => '202'],
                    ['nom' => 'GARI-GOMBO', 'code' => '205'],
                    ['nom' => 'MOLOUNDOU', 'code' => '201'],
                    ['nom' => 'SALAPOUMBE', 'code' => '204'],
                ],
                'HAUT NYONG' => [
                    ['nom' => 'ABONG-MBANG', 'code' => '210'],
                    ['nom' => 'DIMAKO', 'code' => '216'],
                    ['nom' => 'DOUME', 'code' => '211'],
                    ['nom' => 'ANGOSSAS', 'code' => '207'],
                    ['nom' => 'LOMIE', 'code' => '212'],
                    ['nom' => 'ATOK', 'code' => '381'],
                    ['nom' => 'MESSOK', 'code' => '219'],
                    ['nom' => 'MESSAMENA', 'code' => '213'],
                    ['nom' => 'NGUELEMENDOUKA', 'code' => '214'],
                    ['nom' => 'MBOMA', 'code' => '218'],
                    ['nom' => 'MINDOUROU', 'code' => '206'],
                    ['nom' => 'NGOYLA', 'code' => '209'],
                    ['nom' => 'SOMALOMO', 'code' => '749'],
                    ['nom' => 'DOUMAINTANG', 'code' => '208'],
                ],
            ],
            'EXTREME NORD' => [
                'DIAMARE' => [
                    ['nom' => "REGION DE L'EXTREME NORD", 'code' => '///'],
                    ['nom' => 'CU MAROUA', 'code' => '420'],
                    ['nom' => 'MAROUA I', 'code' => '760'],
                    ['nom' => 'MAROUA 2', 'code' => '761'],
                    ['nom' => 'MAROUA 3', 'code' => '762'],
                    ['nom' => 'NDOUKOULA', 'code' => '436'],
                    ['nom' => 'DARGALA', 'code' => '435'],
                    ['nom' => 'GAZAWA', 'code' => '468'],
                    ['nom' => 'PETTE', 'code' => '434'],
                    ['nom' => 'MERI', 'code' => '424'],
                    ['nom' => 'BOGO', 'code' => '422'],
                ],
                'MAYO KANI' => [
                    ['nom' => 'KAELE', 'code' => '423'],
                    ['nom' => 'GUIDIGUIS', 'code' => '427'],
                    ['nom' => 'MOUTOURWA', 'code' => '489'],
                    ['nom' => 'TOULOUM', 'code' => '466'],
                    ['nom' => 'MINDIF', 'code' => '425'],
                    ['nom' => 'DZIGUILAO', 'code' => '467'],
                ],
                'MAYO DANAI' => [
                    ['nom' => 'YAGOUA', 'code' => '451'],
                    ['nom' => 'KAR-HAY', 'code' => '450'],
                    ['nom' => 'DATCHEKA', 'code' => '456'],
                    ['nom' => 'MAGA', 'code' => '454'],
                    ['nom' => 'KAI KAI', 'code' => '459'],
                    ['nom' => 'WINA', 'code' => '485'],
                    ['nom' => 'KALFOU', 'code' => '486'],
                    ['nom' => 'GUERE', 'code' => '453'],
                    ['nom' => 'TCHATIBALI', 'code' => '457'],
                    ['nom' => 'GUEME', 'code' => '455'],
                    ['nom' => 'GOBO', 'code' => '458'],
                ],
                'LOGONE ET CHARI' => [
                    ['nom' => 'KOUSSERI', 'code' => '430'],
                    ['nom' => 'HILE ALIFA', 'code' => '497'],
                    ['nom' => 'LOGONE-BIRNI', 'code' => '433'],
                    ['nom' => 'ZINA', 'code' => '484'],
                    ['nom' => 'GOULFEY', 'code' => '469'],
                    ['nom' => 'MAKARY', 'code' => '431'],
                    ['nom' => 'FOTOKOL', 'code' => '481'],
                    ['nom' => 'WAZA', 'code' => '480'],
                    ['nom' => 'BLANGOUA', 'code' => '487'],
                    ['nom' => 'DARAK', 'code' => '763'],
                ],
                'MAYO SAVA' => [
                    ['nom' => 'MORA', 'code' => '441'],
                    ['nom' => 'TOKOMBERE', 'code' => '499'],
                    ['nom' => 'KOLOFATA', 'code' => '491'],
                ],
                'MAYO TSANAGA' => [
                    ['nom' => 'MOKOLO', 'code' => '440'],
                    ['nom' => 'SOULEDE ROUA', 'code' => '465'],
                    ['nom' => 'BOURRHA', 'code' => '461'],
                    ['nom' => 'HINA', 'code' => '488'],
                    ['nom' => 'KOZA', 'code' => '462'],
                    ['nom' => 'MOGODE', 'code' => '463'],
                    ['nom' => 'MOZOGO', 'code' => '464'],
                ],
            ],
            'LITTORAL' => [
                'WOURI' => [
                    ['nom' => 'CU DOUALA', 'code' => '330'],
                    ['nom' => 'DOUALA I', 'code' => '331'],
                    ['nom' => 'DOUALA 3', 'code' => '333'],
                    ['nom' => 'DOUALA 4', 'code' => '334'],
                    ['nom' => 'DOUALA 5', 'code' => '335'],
                ],
                'SANAGA MARITIME' => [
                    ['nom' => 'DIZANGUE', 'code' => '322'],
                    ['nom' => 'MASSOK', 'code' => '329'],
                    ['nom' => 'NDOM', 'code' => '327'],
                ],
                'MOUNGO' => [
                    ['nom' => 'CU NKONGSAMBA', 'code' => '301'],
                    ['nom' => 'NKONGSAMBA 2', 'code' => '351'],
                    ['nom' => 'MELONG', 'code' => '307'],
                    ['nom' => 'BARE-BAKEM', 'code' => '309'],
                    ['nom' => 'MOMBO', 'code' => '315'],
                ],
                'NKAM' => [
                    ['nom' => 'YABASSI', 'code' => '310'],
                    ['nom' => 'NDOBIAN', 'code' => '317'],
                    ['nom' => 'NKONDJOCK', 'code' => '311'],
                ],
            ],
            'NORD' => [
                'BENOUE' => [
                     ['nom' => "REGION DU NORD", 'code' => ''],
                    ['nom' => 'CU GAROUA', 'code' => '410'],
                    ['nom' => 'GAROUA I', 'code' => '756'],
                    ['nom' => 'GAROUA 2', 'code' => '757'],
                    ['nom' => 'GAROUA 3', 'code' => '758'],
                    ['nom' => 'MAYO HOURNA(BARNDAKE)', 'code' => '759'],
                    ['nom' => 'TOUROUA', 'code' => '478'],
                    ['nom' => 'DEMBO', 'code' => '474'],
                    ['nom' => 'BIBEMI', 'code' => '498'],
                    ['nom' => 'PITOA', 'code' => '492'],
                    ['nom' => 'GASCHIGA', 'code' => '477'],
                    ['nom' => 'NGONG', 'code' => '475'],
                    ['nom' => 'LAGDO', 'code' => '473'],
                    ['nom' => 'BASCHEO', 'code' => '476'],
                ],
                'MAYO LOUTI' => [
                    ['nom' => 'GUIDER', 'code' => '412'],
                    ['nom' => 'FIGUIL', 'code' => '471'],
                    ['nom' => 'MAYO OULO', 'code' => '472'],
                ],
                'FARO' => [
                    ['nom' => 'POLI', 'code' => '413'],
                    ['nom' => 'BEKA', 'code' => '493'],
                ],
                'MAYO-REY' => [
                    ['nom' => 'TCHOLLIRE', 'code' => '414'],
                    ['nom' => 'MADINGRING', 'code' => '479'],
                    ['nom' => 'REY-BOUBA', 'code' => '494'],
                    ['nom' => 'TOUBORO', 'code' => '495'],
                ],
            ],
            'NORD-OUEST' => [
                'MEZAM' => [
                    ['nom' => 'CU BAMENDA', 'code' => '520'],
                    ['nom' => 'BAMENDA 2', 'code' => '556'],
                    ['nom' => 'SANTA', 'code' => '524'],
                    ['nom' => 'BAFUT', 'code' => '526'],
                ],
                'MOMO' => [
                    ['nom' => 'NJIKWA', 'code' => '552'],
                    ['nom' => 'WIDIKUM', 'code' => '543'],
                ],
                'MENCHUM' => [
                    ['nom' => 'ZHOA', 'code' => '534'],
                    ['nom' => 'FURU-AWA', 'code' => '551'],
                ],
                'DONGA-MANTUNG' => [
                    ['nom' => 'NKAMBE', 'code' => '510'],
                    ['nom' => 'AKO', 'code' => '550'],
                    ['nom' => 'NDU', 'code' => '515'],
                    ['nom' => 'NWA', 'code' => '511'],
                ],
                'BUI' => [
                    ['nom' => 'KUMBO', 'code' => '501'],
                    ['nom' => 'NKOR NONI', 'code' => '507'],
                    ['nom' => 'MBIAME', 'code' => '506'],
                    ['nom' => 'ELAK OKU', 'code' => '503'],
                ],
                'BOYO' => [
                    ['nom' => 'BELO', 'code' => '547'],
                ],
                'NGOKENTUNJA' => [
                    ['nom' => 'NDOP', 'code' => '523'],
                    ['nom' => 'BALIKUMBAT', 'code' => '546'],
                ],
            ],
            'OUEST' => [
                'MIFI' => [
                    ['nom' => "REGION DE L'OUEST", 'code' => '////'],
                    ['nom' => 'CU BAFOUSSAM', 'code' => '640'],
                    ['nom' => 'BAFOUSSAM.I', 'code' => '641'],
                    ['nom' => 'BAFOUSSAM.2', 'code' => '647'],
                    ['nom' => 'BAFOUSSAM.3', 'code' => '662'],
                ],
                'BAMBOUTOS' => [
                    ['nom' => 'MBOUDA', 'code' => '601'],
                    ['nom' => 'BATCHAM', 'code' => '602'],
                    ['nom' => 'GALIM', 'code' => '603'],
                    ['nom' => 'BABADJOU', 'code' => '605'],
                ],
                'MENOUA' => [
                    ['nom' => 'SANTCHOU', 'code' => '634'],
                    ['nom' => 'DSCHANG', 'code' => '630'],
                    ['nom' => 'FONGO-TONGO', 'code' => '663'],
                    ['nom' => 'PENKA-MICHEL', 'code' => '633'],
                    ['nom' => 'FOKOUE', 'code' => '632'],
                    ['nom' => 'NKONG-NZEM', 'code' => '636'],
                ],
                'HAUT-NKAM' => [
                    ['nom' => 'BAFANG', 'code' => '620'],
                    ['nom' => 'BANA', 'code' => '622'],
                    ['nom' => 'BANKA', 'code' => '664'],
                    ['nom' => 'KEKEM', 'code' => '624'],
                    ['nom' => 'BANWA', 'code' => '627'],
                    ['nom' => 'BAKOU', 'code' => '625'],
                ],
                'NDE' => [
                    ['nom' => 'BANGANGTE', 'code' => '650'],
                    ['nom' => 'BASSAMBA', 'code' => '654'],
                    ['nom' => 'BAZOU', 'code' => '651'],
                    ['nom' => 'TONGA', 'code' => '652'],
                ],
                'NOUN' => [
                    ['nom' => 'FOUMBAN', 'code' => '611'],
                    ['nom' => 'NJIMOM', 'code' => '665'],
                    ['nom' => 'KOUTABA', 'code' => '617'],
                    ['nom' => 'MALENTOUEN', 'code' => '614'],
                    ['nom' => 'MAGBA', 'code' => '613'],
                    ['nom' => 'MASSANGAM', 'code' => '615'],
                    ['nom' => 'KOUOPTAMO', 'code' => '619'],
                ],
                'HAUTS-PLATEAUX' => [
                    ['nom' => 'BAMENDJOU', 'code' => '643'],
                    ['nom' => 'BATIE', 'code' => '657'],
                    ['nom' => 'BANGOU', 'code' => '645'],
                ],
                'KOUNG-KHI' => [
                    ['nom' => 'DEMDENG', 'code' => '656'],
                    ['nom' => 'PETE BANDJOUN', 'code' => '644'],
                    ['nom' => 'BAYANGAM', 'code' => '655'],
                ],
            ],
            'SUD' => [
                'MVINA' => [
                    ['nom' => 'CU EBOLOWA', 'code' => '150'],
                ],
                'DJA-ET-LOBO' => [
                    ['nom' => 'ZOETELE', 'code' => '50'],
                ],
                'VALLEE DU NTEM' => [
                    ['nom' => 'AMBAM', 'code' => '152'],
                    ['nom' => 'OLAMZE', 'code' => '158'],
                ],
                'OCEAN' => [
                    ['nom' => 'CU KRIBI', 'code' => '190'],
                    ['nom' => 'AKOM.2', 'code' => '192'],
                    ['nom' => 'LOLODORF', 'code' => '194'],
                ],
            ],
            'SUD-OUEST' => [
                'FAKO' => [   
                    ['nom' => "REGION DU SUD-OUEST", 'code' => '/////'],
                    ['nom' => 'CU LIMBE', 'code' => '701'],
                    ['nom' => 'IDENAU', 'code' => '706'],
                    ['nom' => 'TIKO', 'code' => '704'],
                    ['nom' => 'BUEA', 'code' => '773'],
                    ['nom' => 'IDABATO', 'code' => '735'],
                ],
                'MANYU' => [
                    ['nom' => 'MAMFE', 'code' => '710'],
                ],
                'MANENGOUBA' => [
                    ['nom' => 'BANGEM', 'code' => '721'],
                ],
            ],
        ];

        $totalCreated = 0;
        $totalSkipped = 0;

        foreach ($communesData as $regionName => $departments) {
            echo "Traitement de la région: {$regionName}\n";
            
            foreach ($departments as $departmentName => $communes) {
                echo "  Traitement du département: {$departmentName}\n";
                
                // Récupérer le département
                $departement = $this->getDepartement($departmentName, $regionName);
                
                if (!$departement) {
                    echo "    ❌ Département '{$departmentName}' non trouvé dans la région '{$regionName}'\n";
                    continue;
                }

                foreach ($communes as $communeData) {
                    // Vérifier si la commune existe déjà
                    $existingCommune = Commune::where('nom', $communeData['nom'])
                        ->where('departement_id', $departement->id)
                        ->first();

                    if ($existingCommune) {
                        echo "    ⚠️  Commune '{$communeData['nom']}' existe déjà\n";
                        $totalSkipped++;
                        continue;
                    }

                    // Créer la commune
                    try {
                        Commune::create([
                            'nom' => $communeData['nom'],
                            'code' => $communeData['code'],
                            'departement_id' => $departement->id,
                            'telephone' => null,
                        ]);

                        echo "    ✅ Commune '{$communeData['nom']}' créée\n";
                        $totalCreated++;
                    } catch (\Exception $e) {
                        echo "    ❌ Erreur lors de la création de '{$communeData['nom']}': {$e->getMessage()}\n";
                    }
                }
            }
        }

        echo "\n=== RÉSUMÉ ===\n";
        echo "✅ Communes créées: {$totalCreated}\n";
        echo "⚠️  Communes ignorées (déjà existantes): {$totalSkipped}\n";
        echo "📊 Total traité: " . ($totalCreated + $totalSkipped) . "\n";
    }

    /**
     * Récupère un département par son nom et sa région
     */
    private function getDepartement($departmentName, $regionName)
    {
        // Mapping exact des noms des régions
        $regionMappings = [
            'EXTREME NORD' => 'EXTRÊME-NORD',
            'NORD-OUEST' => 'NORD-OUEST',
            'SUD-OUEST' => 'SUD-OUEST',
            'NORD' => 'NORD',
            'OUEST' => 'OUEST',
            'CENTRE' => 'CENTRE',
            'EST' => 'EST',
            'ADAMAOUA' => 'ADAMAOUA',
            'LITTORAL' => 'LITTORAL',
            'SUD' => 'SUD',
        ];

       $departmentMappings = [
    // ADAMAOUA
    'VINA' => 'VINA',
    'MBERE' => 'MBERE',

    // CENTRE
    'MFOUNDI' => 'MFOUNDI',
    'MEFOU ET AKONO' => 'MEFOU ET AKONO',
    'MEFOU ET AFAMBA' => 'MEFOU ET AFAMBA',
    'LEKIE' => 'LEKIE',
    'MBAM ET KIM' => 'MBAM ET KIM',
    'MBAM ET INOUBOU' => 'MBAM ET INOUBOU',
    'HAUTE SANAGA' => 'HAUTE SANAGA',
    'NYONG ET SO\'O' => 'NYONG ET SO\'O',
    'NYONG ET MFOUMOU' => 'NYONG ET MFOUMOU',
    'NYONG ET KELLE' => 'NYONG ET KELLE',

    // EST
    'LOM ET DJEREM' => 'LOM ET DJEREM',
    'KADEY' => 'KADEY',
    'BOUMBA ET NGOKO' => 'BOUMBA ET NGOKO',
    'HAUT NYONG' => 'HAUT NYONG',

    // EXTREME NORD
    'DIAMARE' => 'DIAMARE',
    'MAYO KANI' => 'MAYO KANI',
    'MAYO DANAI' => 'MAYO DANAI',
    'LOGONE ET CHARI' => 'LOGONE ET CHARI',
    'MAYO SAVA' => 'MAYO SAVA',
    'MAYO TSANAGA' => 'MAYO TSANAGA',

    // LITTORAL
    'WOURI' => 'WOURI',
    'SANAGA MARITIME' => 'SANAGA MARITIME',
    'MOUNGO' => 'MOUNGO',
    'NKAM' => 'NKAM',

    // NORD
    'BENOUE' => 'BENOUE',
    'MAYO LOUTI' => 'MAYO LOUTI',
    'FARO' => 'FARO',
    'MAYO-REY' => 'MAYO-REY',

    // NORD-OUEST
    'MEZAM' => 'MEZAM',
    'MOMO' => 'MOMO',
    'MENCHUM' => 'MENCHUM',
    'DONGA-MANTUNG' => 'DONGA-MANTUNG',
    'BUI' => 'BUI',
    'BOYO' => 'BOYO',
    'NGOKENTUNJA' => 'NGOKENTUNJA',

    // OUEST
    'MIFI' => 'MIFI',
    'BAMBOUTOS' => 'BAMBOUTOS',
    'MENOUA' => 'MENOUA',
    'HAUT-NKAM' => 'HAUT-NKAM',
    'NDE' => 'NDE',
    'NOUN' => 'NOUN',
    'HAUTS-PLATEAUX' => 'HAUTS-PLATEAUX',
    'KOUNG-KHI' => 'KOUNG-KHI',

    // SUD
    'MVINA' => 'MVINA',
    'DJA-ET-LOBO' => 'DJA-ET-LOBO',
    'VALLEE DU NTEM' => 'VALLÉE DU NTEM',
    'OCEAN' => 'OCEAN',

    // SUD-OUEST
    'FAKO' => 'FAKO',
    'MANYU' => 'MANYU',
    'MANENGOUBA' => 'MANENGOUBA',
];

        // Normaliser les noms
        $searchRegionName = $regionMappings[$regionName] ?? $regionName;
        $searchDepartmentName = $departmentMappings[$departmentName] ?? $departmentName;

        // Récupérer la région
        $region = Region::where('nom', $searchRegionName)->first();
        
        if (!$region) {
            echo "    ❌ Région '{$searchRegionName}' non trouvée\n";
            return null;
        }

        // Récupérer le département dans cette région
        $departement = Departement::where('nom', $searchDepartmentName)
            ->where('region_id', $region->id)
            ->first();

        if (!$departement) {
            echo "    ❌ Département '{$searchDepartmentName}' non trouvé dans la région '{$searchRegionName}'\n";
            return null;
        }

        return $departement;
    }
}












//<?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use App\Models\Commune;
// use App\Models\Departement;
// use App\Models\Region;

// class CommunesSeeder extends Seeder
// {
//     public function run()
//     {
//         echo "Début du seeding des communes...\n";

//         // Données des communes organisées par région et département basées sur paste.txt
//         $communesData = [
//             'ADAMAOUA' => [
//                 'VINA' => [
//                     ['nom' => 'CU NGAOUNDERE', 'code' => '401'],
//                     ['nom' => 'GALIM-TIGNERE', 'code' => '416'],
//                     ['nom' => 'BANKIM', 'code' => '483'],
//                 ],
//                 'MBERE' => [
//                     ['nom' => 'MEIGANGA', 'code' => '404'],
//                     ['nom' => 'DJOHONG', 'code' => '418'],
//                 ],
//             ],
//             'CENTRE' => [
//                 'MFOUNDI' => [
//                     ['nom' => 'CU YAOUNDE', 'code' => '140'],
//                     ['nom' => 'YAOUNDE I', 'code' => '147'],
//                     ['nom' => 'YAOUNDE 2', 'code' => '148'],
//                     ['nom' => 'YAOUNDE 3', 'code' => '149'],
//                     ['nom' => 'YAOUNDE 4', 'code' => '177'],
//                     ['nom' => 'YAOUNDE 5', 'code' => '144'],
//                     ['nom' => 'YAOUNDE 6', 'code' => '948'],
//                     ['nom' => 'YAOUNDE 7', 'code' => '188'],
//                 ],
//                 'MEFOU ET AKONO' => [
//                     ['nom' => 'NGOUMOU', 'code' => '135'],
//                     ['nom' => 'MBANKOMO', 'code' => '134'],
//                     ['nom' => 'BIKOK', 'code' => '132'],
//                     ['nom' => 'AKONO', 'code' => '131'],
//                 ],
//                 'MEFOU ET AFAMBA' => [
//                     ['nom' => 'MFOU', 'code' => '130'],
//                     ['nom' => 'AWAE', 'code' => '136'],
//                     ['nom' => 'OLANGUINA', 'code' => '143'],
//                 ],
//                 'LEKIE' => [
//                     ['nom' => 'OBALA', 'code' => '112'],
//                     ['nom' => 'SA\'A', 'code' => '114'],
//                 ],
//                 'MBAM ET KIM' => [
//                     ['nom' => 'YOKO', 'code' => '126'],
//                 ],
//                 'MBAM ET INOUBOU' => [
//                     ['nom' => 'OMBESSA', 'code' => '125'],
//                 ],
//                 'HAUTE SANAGA' => [
//                     ['nom' => 'BIBEY', 'code' => '105'],
//                     ['nom' => 'NSEM', 'code' => '106'],
//                 ],
//                 'NYONG ET SO\'O' => [
//                     ['nom' => 'MBALMAYO', 'code' => '181'],
//                     ['nom' => 'NKOLMETET', 'code' => '187'],
//                     ['nom' => 'AKOEMAN', 'code' => '185'],
//                     ['nom' => 'MENGUEME', 'code' => '186'],
//                     ['nom' => 'NGOMEDZAP', 'code' => '183'],
//                     ['nom' => 'DZENG', 'code' => '182'],
//                 ],
//                 'NYONG ET MFOUMOU' => [
//                     ['nom' => 'AKONOLINGA', 'code' => '170'],
//                     ['nom' => 'MENGANG', 'code' => '176'],
//                     ['nom' => 'ENDOM', 'code' => '172'],
//                     ['nom' => 'KOBDOBO', 'code' => '173'],
//                 ],
//                 'NYONG ET KELLE' => [
//                     ['nom' => 'ESEKA', 'code' => '160'],
//                     ['nom' => 'BONDJOCK', 'code' => '169'],
//                     ['nom' => 'MATOMB', 'code' => '163'],
//                     ['nom' => 'BOT-MAKAK', 'code' => '161'],
//                     ['nom' => 'NGUIBASSAL', 'code' => '174'],
//                     ['nom' => 'MESSONDO', 'code' => '164'],
//                     ['nom' => 'BIYOUHA', 'code' => '168'],
//                     ['nom' => 'NGOG MAPOUBI', 'code' => '165'],
//                     ['nom' => 'DIBANG', 'code' => '166'],
//                 ],
//             ],
//             'EST' => [
//                 'LOM ET DJEREM' => [
//                     ['nom' => 'CU BERTOUA', 'code' => '230'],
//                     ['nom' => 'BERTOUA I', 'code' => '240'],
//                     ['nom' => 'BERTOUA 2', 'code' => '241'],
//                     ['nom' => 'MANDJOU', 'code' => '242'],
//                     ['nom' => 'BETARE OYA', 'code' => '232'],
//                     ['nom' => 'NGOURA', 'code' => '236'],
//                     ['nom' => 'BELABO', 'code' => '235'],
//                     ['nom' => 'DIANG', 'code' => '237'],
//                     ['nom' => 'GAROUA-BOULAI', 'code' => '233'],
//                 ],
//                 'KADEY' => [
//                     ['nom' => 'BATOURI', 'code' => '220'],
//                     ['nom' => 'MBANG', 'code' => '224'],
//                     ['nom' => 'NDELELE', 'code' => '221'],
//                     ['nom' => 'KENTZOU', 'code' => '408'],
//                     ['nom' => 'NGUELEBOK', 'code' => '407'],
//                     ['nom' => 'KETTE', 'code' => '277'],
//                     ['nom' => 'OULI', 'code' => '223'],
//                 ],
//                 'BOUMBA ET NGOKO' => [
//                     ['nom' => 'YOKADOUMA', 'code' => '202'],
//                     ['nom' => 'GARI-GOMBO', 'code' => '205'],
//                     ['nom' => 'MOLOUNDOU', 'code' => '201'],
//                     ['nom' => 'SALAPOUMBE', 'code' => '204'],
//                 ],
//                 'HAUT NYONG' => [
//                     ['nom' => 'ABONG-MBANG', 'code' => '210'],
//                     ['nom' => 'DIMAKO', 'code' => '216'],
//                     ['nom' => 'DOUME', 'code' => '211'],
//                     ['nom' => 'ANGOSSAS', 'code' => '207'],
//                     ['nom' => 'LOMIE', 'code' => '212'],
//                     ['nom' => 'ATOK', 'code' => '381'],
//                     ['nom' => 'MESSOK', 'code' => '219'],
//                     ['nom' => 'MESSAMENA', 'code' => '213'],
//                     ['nom' => 'NGUELEMENDOUKA', 'code' => '214'],
//                     ['nom' => 'MBOMA', 'code' => '218'],
//                     ['nom' => 'MINDOUROU', 'code' => '206'],
//                     ['nom' => 'NGOYLA', 'code' => '209'],
//                     ['nom' => 'SOMALOMO', 'code' => '749'],
//                     ['nom' => 'DOUMAINTANG', 'code' => '208'],
//                 ],
//             ],
//             'EXTREME NORD' => [
//                 'DIAMARE' => [
//                     ['nom' => 'CU MAROUA', 'code' => '420'],
//                     ['nom' => 'MAROUA I', 'code' => '760'],
//                     ['nom' => 'MAROUA 2', 'code' => '761'],
//                     ['nom' => 'MAROUA 3', 'code' => '762'],
//                     ['nom' => 'NDOUKOULA', 'code' => '436'],
//                     ['nom' => 'DARGALA', 'code' => '435'],
//                     ['nom' => 'GAZAWA', 'code' => '468'],
//                     ['nom' => 'PETTE', 'code' => '434'],
//                     ['nom' => 'MERI', 'code' => '424'],
//                     ['nom' => 'BOGO', 'code' => '422'],
//                 ],
//                 'MAYO KANI' => [
//                     ['nom' => 'KAELE', 'code' => '423'],
//                     ['nom' => 'GUIDIGUIS', 'code' => '427'],
//                     ['nom' => 'MOUTOURWA', 'code' => '489'],
//                     ['nom' => 'TOULOUM', 'code' => '466'],
//                     ['nom' => 'MINDIF', 'code' => '425'],
//                     ['nom' => 'DZIGUILAO', 'code' => '467'],
//                 ],
//                 'MAYO DANAI' => [
//                     ['nom' => 'YAGOUA', 'code' => '451'],
//                     ['nom' => 'KAR-HAY', 'code' => '450'],
//                     ['nom' => 'DATCHEKA', 'code' => '456'],
//                     ['nom' => 'MAGA', 'code' => '454'],
//                     ['nom' => 'KAI KAI', 'code' => '459'],
//                     ['nom' => 'WINA', 'code' => '485'],
//                     ['nom' => 'KALFOU', 'code' => '486'],
//                     ['nom' => 'GUERE', 'code' => '453'],
//                     ['nom' => 'TCHATIBALI', 'code' => '457'],
//                     ['nom' => 'GUEME', 'code' => '455'],
//                     ['nom' => 'GOBO', 'code' => '458'],
//                 ],
//                 'LOGONE ET CHARI' => [
//                     ['nom' => 'KOUSSERI', 'code' => '430'],
//                     ['nom' => 'HILE ALIFA', 'code' => '497'],
//                     ['nom' => 'LOGONE-BIRNI', 'code' => '433'],
//                     ['nom' => 'ZINA', 'code' => '484'],
//                     ['nom' => 'GOULFEY', 'code' => '469'],
//                     ['nom' => 'MAKARY', 'code' => '431'],
//                     ['nom' => 'FOTOKOL', 'code' => '481'],
//                     ['nom' => 'WAZA', 'code' => '480'],
//                     ['nom' => 'BLANGOUA', 'code' => '487'],
//                     ['nom' => 'DARAK', 'code' => '763'],
//                 ],
//                 'MAYO SAVA' => [
//                     ['nom' => 'MORA', 'code' => '441'],
//                     ['nom' => 'TOKOMBERE', 'code' => '499'],
//                     ['nom' => 'KOLOFATA', 'code' => '491'],
//                 ],
//                 'MAYO TSANAGA' => [
//                     ['nom' => 'MOKOLO', 'code' => '440'],
//                     ['nom' => 'SOULEDE ROUA', 'code' => '465'],
//                     ['nom' => 'BOURRHA', 'code' => '461'],
//                     ['nom' => 'HINA', 'code' => '488'],
//                     ['nom' => 'KOZA', 'code' => '462'],
//                     ['nom' => 'MOGODE', 'code' => '463'],
//                     ['nom' => 'MOZOGO', 'code' => '464'],
//                 ],
//             ],
//             'LITTORAL' => [
//                 'WOURI' => [
//                     ['nom' => 'CU DOUALA', 'code' => '330'],
//                     ['nom' => 'DOUALA I', 'code' => '331'],
//                     ['nom' => 'DOUALA 3', 'code' => '333'],
//                     ['nom' => 'DOUALA 4', 'code' => '334'],
//                     ['nom' => 'DOUALA 5', 'code' => '335'],
//                 ],
//                 'SANAGA MARITIME' => [
//                     ['nom' => 'DIZANGUE', 'code' => '322'],
//                     ['nom' => 'MASSOK', 'code' => '329'],
//                     ['nom' => 'NDOM', 'code' => '327'],
//                 ],
//                 'MOUNGO' => [
//                     ['nom' => 'CU NKONGSAMBA', 'code' => '301'],
//                     ['nom' => 'NKONGSAMBA 2', 'code' => '351'],
//                     ['nom' => 'MELONG', 'code' => '307'],
//                     ['nom' => 'BARE-BAKEM', 'code' => '309'],
//                     ['nom' => 'MOMBO', 'code' => '315'],
//                 ],
//                 'NKAM' => [
//                     ['nom' => 'YABASSI', 'code' => '310'],
//                     ['nom' => 'NDOBIAN', 'code' => '317'],
//                     ['nom' => 'NKONDJOCK', 'code' => '311'],
//                 ],
//             ],
//             'NORD' => [
//                 'BENOUE' => [
//                     ['nom' => 'CU GAROUA', 'code' => '410'],
//                     ['nom' => 'GAROUA I', 'code' => '756'],
//                     ['nom' => 'GAROUA 2', 'code' => '757'],
//                     ['nom' => 'GAROUA 3', 'code' => '758'],
//                     ['nom' => 'MAYO HOURNA(BARNDAKE)', 'code' => '759'],
//                     ['nom' => 'TOUROUA', 'code' => '478'],
//                     ['nom' => 'DEMBO', 'code' => '474'],
//                     ['nom' => 'BIBEMI', 'code' => '498'],
//                     ['nom' => 'PITOA', 'code' => '492'],
//                     ['nom' => 'GASCHIGA', 'code' => '477'],
//                     ['nom' => 'NGONG', 'code' => '475'],
//                     ['nom' => 'LAGDO', 'code' => '473'],
//                     ['nom' => 'BASCHEO', 'code' => '476'],
//                 ],
//                 'MAYO LOUTI' => [
//                     ['nom' => 'GUIDER', 'code' => '412'],
//                     ['nom' => 'FIGUIL', 'code' => '471'],
//                     ['nom' => 'MAYO OULO', 'code' => '472'],
//                 ],
//                 'FARO' => [
//                     ['nom' => 'POLI', 'code' => '413'],
//                     ['nom' => 'BEKA', 'code' => '493'],
//                 ],
//                 'MAYO-REY' => [
//                     ['nom' => 'TCHOLLIRE', 'code' => '414'],
//                     ['nom' => 'MADINGRING', 'code' => '479'],
//                     ['nom' => 'REY-BOUBA', 'code' => '494'],
//                     ['nom' => 'TOUBORO', 'code' => '495'],
//                 ],
//             ],
//             'NORD-OUEST' => [
//                 'MEZAM' => [
//                     ['nom' => 'CU BAMENDA', 'code' => '520'],
//                     ['nom' => 'BAMENDA 2', 'code' => '556'],
//                     ['nom' => 'SANTA', 'code' => '524'],
//                     ['nom' => 'BAFUT', 'code' => '526'],
//                 ],
//                 'MOMO' => [
//                     ['nom' => 'NJIKWA', 'code' => '552'],
//                     ['nom' => 'WIDIKUM', 'code' => '543'],
//                 ],
//                 'MENCHUM' => [
//                     ['nom' => 'ZHOA', 'code' => '534'],
//                     ['nom' => 'FURU-AWA', 'code' => '551'],
//                 ],
//                 'DONGA-MANTUNG' => [
//                     ['nom' => 'NKAMBE', 'code' => '510'],
//                     ['nom' => 'AKO', 'code' => '550'],
//                     ['nom' => 'NDU', 'code' => '515'],
//                     ['nom' => 'NWA', 'code' => '511'],
//                 ],
//                 'BUI' => [
//                     ['nom' => 'KUMBO', 'code' => '501'],
//                     ['nom' => 'NKOR NONI', 'code' => '507'],
//                     ['nom' => 'MBIAME', 'code' => '506'],
//                     ['nom' => 'ELAK OKU', 'code' => '503'],
//                 ],
//                 'BOYO' => [
//                     ['nom' => 'BELO', 'code' => '547'],
//                 ],
//                 'NGOKENTUNJA' => [
//                     ['nom' => 'NDOP', 'code' => '523'],
//                     ['nom' => 'BALIKUMBAT', 'code' => '546'],
//                 ],
//             ],
//             'OUEST' => [
//                 'MIFI' => [
//                     ['nom' => 'CU BAFOUSSAM', 'code' => '640'],
//                     ['nom' => 'BAFOUSSAM.I', 'code' => '641'],
//                     ['nom' => 'BAFOUSSAM.2', 'code' => '647'],
//                     ['nom' => 'BAFOUSSAM.3', 'code' => '662'],
//                 ],
//                 'BAMBOUTOS' => [
//                     ['nom' => 'MBOUDA', 'code' => '601'],
//                     ['nom' => 'BATCHAM', 'code' => '602'],
//                     ['nom' => 'GALIM', 'code' => '603'],
//                     ['nom' => 'BABADJOU', 'code' => '605'],
//                 ],
//                 'MENOUA' => [
//                     ['nom' => 'SANTCHOU', 'code' => '634'],
//                     ['nom' => 'DSCHANG', 'code' => '630'],
//                     ['nom' => 'FONGO-TONGO', 'code' => '663'],
//                     ['nom' => 'PENKA-MICHEL', 'code' => '633'],
//                     ['nom' => 'FOKOUE', 'code' => '632'],
//                     ['nom' => 'NKONG-NZEM', 'code' => '636'],
//                 ],
//                 'HAUT-NKAM' => [
//                     ['nom' => 'BAFANG', 'code' => '620'],
//                     ['nom' => 'BANA', 'code' => '622'],
//                     ['nom' => 'BANKA', 'code' => '664'],
//                     ['nom' => 'KEKEM', 'code' => '624'],
//                     ['nom' => 'BANWA', 'code' => '627'],
//                     ['nom' => 'BAKOU', 'code' => '625'],
//                 ],
//                 'NDE' => [
//                     ['nom' => 'BANGANGTE', 'code' => '650'],
//                     ['nom' => 'BASSAMBA', 'code' => '654'],
//                     ['nom' => 'BAZOU', 'code' => '651'],
//                     ['nom' => 'TONGA', 'code' => '652'],
//                 ],
//                 'NOUN' => [
//                     ['nom' => 'FOUMBAN', 'code' => '611'],
//                     ['nom' => 'NJIMOM', 'code' => '665'],
//                     ['nom' => 'KOUTABA', 'code' => '617'],
//                     ['nom' => 'MALENTOUEN', 'code' => '614'],
//                     ['nom' => 'MAGBA', 'code' => '613'],
//                     ['nom' => 'MASSANGAM', 'code' => '615'],
//                     ['nom' => 'KOUOPTAMO', 'code' => '619'],
//                 ],
//                 'HAUTS-PLATEAUX' => [
//                     ['nom' => 'BAMENDJOU', 'code' => '643'],
//                     ['nom' => 'BATIE', 'code' => '657'],
//                     ['nom' => 'BANGOU', 'code' => '645'],
//                 ],
//                 'KOUNG-KHI' => [
//                     ['nom' => 'DEMDENG', 'code' => '656'],
//                     ['nom' => 'PETE BANDJOUN', 'code' => '644'],
//                     ['nom' => 'BAYANGAM', 'code' => '655'],
//                 ],
//             ],
//             'SUD' => [
//                 'MVINA' => [
//                     ['nom' => 'CU EBOLOWA', 'code' => '150'],
//                 ],
//                 'DJA-ET-LOBO' => [
//                     ['nom' => 'ZOETELE', 'code' => '50'],
//                 ],
//                 'VALLEE DU NTEM' => [
//                     ['nom' => 'AMBAM', 'code' => '152'],
//                     ['nom' => 'OLAMZE', 'code' => '158'],
//                 ],
//                 'OCEAN' => [
//                     ['nom' => 'CU KRIBI', 'code' => '190'],
//                     ['nom' => 'AKOM.2', 'code' => '192'],
//                     ['nom' => 'LOLODORF', 'code' => '194'],
//                 ],
//             ],
//             'SUD-OUEST' => [
//                 'FAKO' => [
//                     ['nom' => 'CU LIMBE', 'code' => '701'],
//                     ['nom' => 'IDENAU', 'code' => '706'],
//                     ['nom' => 'TIKO', 'code' => '704'],
//                     ['nom' => 'BUEA', 'code' => '773'],
//                     ['nom' => 'IDABATO', 'code' => '735'],
//                 ],
//                 'MANYU' => [
//                     ['nom' => 'MAMFE', 'code' => '710'],
//                 ],
//                 'MANENGOUBA' => [
//                     ['nom' => 'BANGEM', 'code' => '721'],
//                 ],
//             ],
//         ];

//         $totalCreated = 0;
//         $totalSkipped = 0;

//         foreach ($communesData as $regionName => $departments) {
//             echo "Traitement de la région: {$regionName}\n";
            
//             foreach ($departments as $departmentName => $communes) {
//                 echo "  Traitement du département: {$departmentName}\n";
                
//                 // Récupérer le département
//                 $departement = $this->getDepartement($departmentName, $regionName);
                
//                 if (!$departement) {
//                     echo "    ❌ Département '{$departmentName}' non trouvé dans la région '{$regionName}'\n";
//                     continue;
//                 }

//                 foreach ($communes as $communeData) {
//                     // Vérifier si la commune existe déjà
//                     $existingCommune = Commune::where('nom', $communeData['nom'])
//                         ->where('departement_id', $departement->id)
//                         ->first();

//                     if ($existingCommune) {
//                         echo "    ⚠️  Commune '{$communeData['nom']}' existe déjà\n";
//                         $totalSkipped++;
//                         continue;
//                     }

//                     // Créer la commune
//                     try {
//                         Commune::create([
//                             'nom' => $communeData['nom'],
//                             'code' => $communeData['code'],
//                             'departement_id' => $departement->id,
//                             'telephone' => null,
//                         ]);

//                         echo "    ✅ Commune '{$communeData['nom']}' créée\n";
//                         $totalCreated++;
//                     } catch (\Exception $e) {
//                         echo "    ❌ Erreur lors de la création de '{$communeData['nom']}': {$e->getMessage()}\n";
//                     }
//                 }
//             }
//         }

//         echo "\n=== RÉSUMÉ ===\n";
//         echo "✅ Communes créées: {$totalCreated}\n";
//         echo "⚠️  Communes ignorées (déjà existantes): {$totalSkipped}\n";
//         echo "📊 Total traité: " . ($totalCreated + $totalSkipped) . "\n";
//     }

//     /**
//      * Récupère un département par son nom et sa région
//      */
//     private function getDepartement($departmentName, $regionName)
//     {
//         // Correspondances pour les noms de départements
//         $departmentMappings = [
//             'MAYO KANI' => 'MAYO-KANI',
//             'MAYO DANAI' => 'MAYO-DANAI',
//             'LOGONE ET CHARI' => 'LOGONE-ET-CHARI',
//             'MAYO SAVA' => 'MAYO-SAVA',
//             'MAYO TSANAGA' => 'MAYO-TSANAGA',
//             'SANAGA MARITIME' => 'SANAGA-MARITIME',
//             'MEFOU ET AKONO' => 'MEFOU-ET-AKONO',
//             'MEFOU ET AFAMBA' => 'MEFOU-ET-AFAMBA',
//             'MBAM ET KIM' => 'MBAM-ET-KIM',
//             'MBAM ET INOUBOU' => 'MBAM-ET-INOUBOU',
//             'HAUTE SANAGA' => 'HAUTE-SANAGA',
//             'NYONG ET SO\'O' => 'NYONG-ET-SO\'O',
//             'NYONG ET MFOUMOU' => 'NYONG-ET-MFOUMOU',
//             'NYONG ET KELLE' => 'NYONG-ET-KELLE',
//             'LOM ET DJEREM' => 'LOM-ET-DJEREM',
//             'BOUMBA ET NGOKO' => 'BOUMBA-ET-NGOKO',
//             'HAUT NYONG' => 'HAUT-NYONG',
//             'MAYO LOUTI' => 'MAYO-LOUTI',
//             'MAYO-REY' => 'MAYO-REY',
//             'DONGA-MANTUNG' => 'DONGA-MANTUNG',
//             'HAUT-NKAM' => 'HAUT-NKAM',
//             'HAUTS-PLATEAUX' => 'HAUTS-PLATEAUX',
//             'KOUNG-KHI' => 'KOUNG-KHI',
//             'DJA-ET-LOBO' => 'DJA-ET-LOBO',
//             'VALLEE DU NTEM' => 'VALLÉE-DU-NTEM',
//         ];

//         // Correspondances pour les noms de régions
//         $regionMappings = [
//             'EXTREME NORD' => 'EXTRÊME-NORD',
//             'NORD-OUEST' => 'NORD-OUEST',
//             'SUD-OUEST' => 'SUD-OUEST',
//         ];

//         // Normaliser les noms
//         $searchDepartmentName = $departmentMappings[$departmentName] ?? $departmentName;
//         $searchRegionName = $regionMappings[$regionName] ?? $regionName;

//         // Chercher la région
//         $region = Region::where(function($query) use ($searchRegionName, $regionName) {
//             $query->where('nom', 'LIKE', "%{$searchRegionName}%")
//                   ->orWhere('nom', 'LIKE', "%{$regionName}%");
//         })->first();

//         if (!$region) {
//             echo "    ❌ Région '{$regionName}' non trouvée\n";
//             return null;
//         }

//         // Chercher le département
//         $departement = Departement::where('region_id', $region->id)
//             ->where(function($query) use ($searchDepartmentName, $departmentName) {
//                 $query->where('nom', 'LIKE', "%{$searchDepartmentName}%")
//                       ->orWhere('nom', 'LIKE', "%{$departmentName}%");
//             })
//             ->first();

//         if (!$departement) {
//             echo "    ❌ Département '{$departmentName}' non trouvé dans la région '{$region->nom}'\n";
//             // Lister les départements disponibles pour debug
//             $availableDepartments = Departement::where('region_id', $region->id)->pluck('nom')->toArray();
//             echo "    📋 Départements disponibles: " . implode(', ', $availableDepartments) . "\n";
//         }

//         return $departement;
//     }
// }