<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DetectionAutomatiqueService;
use Illuminate\Support\Facades\Log;

class DetecterAnomaliesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communes:detecter-anomalies {--annee=} {--type=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Détecte automatiquement les retards et défaillances des communes';

    /**
     * Service de détection
     */
    private $detectionService;

    /**
     * Create a new command instance.
     */
    public function __construct(DetectionAutomatiqueService $detectionService)
    {
        parent::__construct();
        $this->detectionService = $detectionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $annee = $this->option('annee') ?: date('Y');
        $type = $this->option('type');

        $this->info("Démarrage de la détection automatique pour l'année {$annee}...");

        try {
            $resultats = $this->detectionService->detecterToutesAnomalies($annee);

            // Affichage des résultats
            $this->info("Détection terminée avec succès !");
            $this->table(
                ['Type', 'Nombre détecté'],
                [
                    ['Retards', $resultats['retards']],
                    ['Défaillances', $resultats['defaillances']]
                ]
            );

            // Génération du rapport
            $rapport = $this->detectionService->genererRapportDetection($resultats);
            $this->comment($rapport['resume']);

            return 0;

        } catch (\Exception $e) {
            $this->error("Erreur lors de la détection : " . $e->getMessage());
            Log::error("Erreur commande détection", ['error' => $e->getMessage()]);
            return 1;
        }
    }
}