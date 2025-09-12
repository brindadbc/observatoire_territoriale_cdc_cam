<?php

namespace App\Services;

use App\Models\Commune;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportService
{
    /**
     * Exporte les données des communes selon le format spécifié
     */
    public function exportCommunes(array $communeIds = [], string $format = 'pdf', int $annee = null, string $type = 'complet'): string
    {
        $annee = $annee ?: date('Y');
        
        // Récupérer les données
        $communes = $this->getCommunesData($communeIds, $annee, $type);
        
        // Générer le nom de fichier
        $fileName = $this->generateFileName($format, $type, $annee, count($communes));
        
        // Exporter selon le format
        switch ($format) {
            case 'excel':
            case 'xlsx':
                return $this->exportToExcel($communes, $fileName, $type, $annee);
                
            case 'csv':
                return $this->exportToCSV($communes, $fileName, $type);
                
            case 'json':
                return $this->exportToJSON($communes, $fileName, $type);
                
            case 'xml':
                return $this->exportToXML($communes, $fileName, $type);
                
            default:
            case 'pdf':
                return $this->exportToPDF($communes, $fileName, $type, $annee);
        }
    }

    /**
     * Export spécialisé pour une commune unique
     */
    public function exportCommuneDetailed(Commune $commune, string $format = 'pdf', int $annee = null): string
    {
        $annee = $annee ?: date('Y');
        
        // Charger toutes les données de la commune
        $commune->load([
            'departement.region',
            'receveurs',
            'ordonnateurs', 
            'previsions' => function($q) use ($annee) { $q->where('annee_exercice', $annee); },
            'realisations' => function($q) use ($annee) { $q->where('annee_exercice', $annee); },
            'tauxRealisations' => function($q) use ($annee) { $q->where('annee_exercice', $annee); },
            'projets',
            'depotsComptes',
            'defaillances',
            'retards'
        ]);
        
        $fileName = "commune_detail_{$commune->code}_{$annee}." . $format;
        
        switch ($format) {
            case 'excel':
                return $this->exportCommuneDetailToExcel($commune, $fileName, $annee);
            case 'csv':
                return $this->exportCommuneDetailToCSV($commune, $fileName, $annee);
            default:
            case 'pdf':
                return $this->exportCommuneDetailToPDF($commune, $fileName, $annee);
        }
    }

    /**
     * Export des statistiques régionales
     */
    public function exportRegionalStats(string $format = 'pdf', int $annee = null): string
    {
        $annee = $annee ?: date('Y');
        
        $stats = $this->getRegionalStats($annee);
        $fileName = "statistiques_regionales_{$annee}." . $format;
        
        switch ($format) {
            case 'excel':
                return $this->exportRegionalStatsToExcel($stats, $fileName, $annee);
            default:
            case 'pdf':
                return $this->exportRegionalStatsToPDF($stats, $fileName, $annee);
        }
    }

    /**
     * Export au format PDF
     */
    private function exportToPDF(Collection $communes, string $fileName, string $type, int $annee): string
    {
        $data = [
            'communes' => $communes,
            'type' => $type,
            'annee' => $annee,
            'date_generation' => now(),
            'total_communes' => $communes->count(),
            'statistiques' => $this->calculateGlobalStats($communes)
        ];
        
        $pdf = PDF::loadView('exports.communes.pdf', $data)
                  ->setPaper('A4', 'landscape')
                  ->setOptions([
                      'defaultFont' => 'sans-serif',
                      'isRemoteEnabled' => true,
                      'isPhpEnabled' => true
                  ]);
        
        $filePath = storage_path("app/exports/{$fileName}");
        $pdf->save($filePath);
        
        return $fileName;
    }

    /**
     * Export au format Excel avec mise en forme avancée
     */
    private function exportToExcel(Collection $communes, string $fileName, string $type, int $annee): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configuration de base
        $sheet->setTitle('Communes ' . $annee);
        
        // En-tête du document
        $this->addExcelHeader($sheet, $type, $annee, $communes->count());
        
        // En-têtes des colonnes
        $headers = $this->getExcelHeaders($type);
        $startRow = 8; // Après l'en-tête du document
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $startRow, $header);
            $sheet->getStyle($col . $startRow)->getFont()->setBold(true);
            $sheet->getStyle($col . $startRow)->getFill()
                  ->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($col . $startRow)->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }
        
        // Données
        $row = $startRow + 1;
        foreach ($communes as $commune) {
            $this->addCommuneRowToExcel($sheet, $commune, $row, $type, $annee);
            $row++;
        }
        
        // Formatage général
        $this->formatExcelSheet($sheet, $startRow, $row - 1, count($headers));
        
        // Graphiques si demandés
        if (in_array($type, ['complet', 'resume'])) {
            $this->addChartsToExcel($spreadsheet, $communes, $annee);
        }
        
        // Sauvegarder
        $filePath = storage_path("app/exports/{$fileName}");
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        
        return $fileName;
    }

    /**
     * Export au format CSV
     */
    private function exportToCSV(Collection $communes, string $fileName, string $type): string
    {
        $headers = $this->getCSVHeaders($type);
        $data = [];
        
        // Ajouter les en-têtes
        $data[] = $headers;
        
        // Ajouter les données
        foreach ($communes as $commune) {
            $data[] = $this->getCommuneCSVData($commune, $type);
        }
        
        $filePath = storage_path("app/exports/{$fileName}");
        $file = fopen($filePath, 'w');
        
        // BOM UTF-8 pour Excel
        fwrite($file, "\xEF\xBB\xBF");
        
        foreach ($data as $row) {
            fputcsv($file, $row, ';', '"');
        }
        
        fclose($file);
        
        return $fileName;
    }

    /**
     * Export au format JSON
     */
    private function exportToJSON(Collection $communes, string $fileName, string $type): string
    {
        $data = [
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'type' => $type,
                'total_communes' => $communes->count(),
                'version' => '1.0'
            ],
            'communes' => $communes->map(function($commune) use ($type) {
                return $this->getCommuneJSONData($commune, $type);
            })->values()->toArray()
        ];
        
        $filePath = storage_path("app/exports/{$fileName}");
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return $fileName;
    }

    /**
     * Export au format XML
     */
    private function exportToXML(Collection $communes, string $fileName, string $type): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><export></export>');
        
        // Métadonnées
        $metadata = $xml->addChild('metadata');
        $metadata->addChild('generated_at', now()->toISOString());
        $metadata->addChild('type', $type);
        $metadata->addChild('total_communes', $communes->count());
        $metadata->addChild('version', '1.0');
        
        // Données des communes
        $communesXML = $xml->addChild('communes');
        
        foreach ($communes as $commune) {
            $communeXML = $communesXML->addChild('commune');
            $this->addCommuneToXML($communeXML, $commune, $type);
        }
        
        $filePath = storage_path("app/exports/{$fileName}");
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($filePath);
        
        return $fileName;
    }

    /**
     * Méthodes privées pour la récupération et le formatage des données
     */
    private function getCommunesData(array $communeIds, int $annee, string $type): Collection
    {
        $query = Commune::with([
            'departement.region',
            'receveurs',
            'ordonnateurs'
        ]);
        
        if (!empty($communeIds)) {
            $query->whereIn('id', $communeIds);
        }
        
        // Ajouter les relations selon le type d'export
        switch ($type) {
            case 'complet':
                $query->with([
                    'previsions' => function($q) use ($annee) { $q->where('annee_exercice', $annee); },
                    'realisations' => function($q) use ($annee) { $q->where('annee_exercice', $annee); },
                    'tauxRealisations' => function($q) use ($annee) { $q->where('annee_exercice', $annee); },
                    'projets',
                    'depotsComptes',
                    'defaillances' => function($q) use ($annee) { $q->whereYear('date_constat', $annee); },
                    'retards' => function($q) use ($annee) { $q->whereYear('date_retard', $annee); }
                ]);
                break;
                
            case 'financier':
                $query->with([
                    'previsions' => function($q) use ($annee) { $q->where('annee_exercice', $annee); },
                    'realisations' => function($q) use ($annee) { $q->where('annee_exercice', $annee); },
                    'tauxRealisations' => function($q) use ($annee) { $q->where('annee_exercice', $annee); },
                    'depotsComptes'
                ]);
                break;
                
            case 'gouvernance':
                $query->with([
                    'receveurs',
                    'ordonnateurs',
                    'defaillances' => function($q) use ($annee) { $q->whereYear('date_constat', $annee); },
                    'retards' => function($q) use ($annee) { $q->whereYear('date_retard', $annee); }
                ]);
                break;
        }
        
        return $query->orderBy('nom')->get();
    }

    private function generateFileName(string $format, string $type, int $annee, int $count): string
    {
        $timestamp = now()->format('YmdHis');
        $prefix = $type === 'complet' ? 'communes_rapport' : "communes_{$type}";
        
        return "{$prefix}_{$annee}_{$count}communes_{$timestamp}.{$format}";
    }

    private function addExcelHeader(object $sheet, string $type, int $annee, int $totalCommunes): void
    {
        // Titre principal
        $sheet->setCellValue('A1', 'OBSERVATOIRE DES COLLECTIVITÉS TERRITORIALES DÉCENTRALISÉES');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Sous-titre
        $typeLibelle = $this->getTypeLibelle($type);
        $sheet->setCellValue('A2', "RAPPORT {$typeLibelle} - ANNÉE {$annee}");
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Informations générales
        $sheet->setCellValue('A4', 'Date de génération :');
        $sheet->setCellValue('B4', now()->format('d/m/Y H:i'));
        $sheet->setCellValue('A5', 'Nombre de communes :');
        $sheet->setCellValue('B5', $totalCommunes);
        $sheet->setCellValue('A6', 'Période analysée :');
        $sheet->setCellValue('B6', $annee);
        
        // Styling
        $sheet->getStyle('A4:A6')->getFont()->setBold(true);
    }

    private function getExcelHeaders(string $type): array
    {
        $baseHeaders = ['Nom', 'Code', 'Département', 'Région', 'Population', 'Superficie'];
        
        switch ($type) {
            case 'complet':
                return array_merge($baseHeaders, [
                    'Prévision Budget', 'Réalisation Budget', 'Taux Exécution (%)', 
                    'Évaluation', 'Receveur', 'Ordonnateur', 'Nb Projets', 
                    'Défaillances', 'Retards', 'Téléphone', 'Email'
                ]);
                
            case 'financier':
                return array_merge($baseHeaders, [
                    'Budget Prévu (FCFA)', 'Budget Réalisé (FCFA)', 'Taux Exécution (%)', 
                    'Écart (FCFA)', 'Évaluation Performance', 'Dépôts Comptes'
                ]);
                
            case 'gouvernance':
                return array_merge($baseHeaders, [
                    'Receveur Assigné', 'Ordonnateur Assigné', 'Défaillances Année', 
                    'Retards Déclaration', 'Score Gouvernance', 'Conformité'
                ]);
                
            case 'resume':
            default:
                return array_merge($baseHeaders, [
                    'Taux Exécution (%)', 'Évaluation', 'Receveur', 'Ordonnateur'
                ]);
        }
    }

    private function addCommuneRowToExcel(object $sheet, Commune $commune, int $row, string $type, int $annee): void
    {
        $col = 'A';
        
        // Données de base
        $sheet->setCellValue($col++ . $row, $commune->nom);
        $sheet->setCellValue($col++ . $row, $commune->code);
        $sheet->setCellValue($col++ . $row, $commune->departement->nom);
        $sheet->setCellValue($col++ . $row, $commune->departement->region->nom);
        $sheet->setCellValue($col++ . $row, $commune->population ?? 0);
        $sheet->setCellValue($col++ . $row, $commune->superficie ?? 0);
        
        // Données spécifiques selon le type
        switch ($type) {
            case 'complet':
                $this->addCompleteDataToExcel($sheet, $commune, $row, $col, $annee);
                break;
            case 'financier':
                $this->addFinancialDataToExcel($sheet, $commune, $row, $col, $annee);
                break;
            case 'gouvernance':
                $this->addGovernanceDataToExcel($sheet, $commune, $row, $col, $annee);
                break;
            case 'resume':
            default:
                $this->addResumeDataToExcel($sheet, $commune, $row, $col, $annee);
                break;
        }
    }

    private function addCompleteDataToExcel(object $sheet, Commune $commune, int $row, string $startCol, int $annee): void
    {
        $col = $startCol;
        
        $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
        $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
        $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
        $sheet->setCellValue($col++ . $row, $prevision?->montant ?? 0);
        $sheet->setCellValue($col++ . $row, $realisation);
        $sheet->setCellValue($col++ . $row, $tauxRealisation?->pourcentage ?? 0);
        $sheet->setCellValue($col++ . $row, $tauxRealisation?->evaluation ?? 'Non évalué');
        $sheet->setCellValue($col++ . $row, $commune->receveurs->first()?->nom ?? 'Non assigné');
        $sheet->setCellValue($col++ . $row, $commune->ordonnateurs->first()?->nom ?? 'Non assigné');
        $sheet->setCellValue($col++ . $row, $commune->projets->count());
        $sheet->setCellValue($col++ . $row, $commune->defaillances->count());
        $sheet->setCellValue($col++ . $row, $commune->retards->count());
        $sheet->setCellValue($col++ . $row, $commune->telephone ?? '');
        $sheet->setCellValue($col++ . $row, $commune->email ?? '');
    }

    private function addFinancialDataToExcel(object $sheet, Commune $commune, int $row, string $startCol, int $annee): void
    {
        $col = $startCol;
        
        $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
        $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
        $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
        $budgetPrevu = $prevision?->montant ?? 0;
        $budgetRealise = $realisation;
        $ecart = $budgetRealise - $budgetPrevu;
        
        $sheet->setCellValue($col++ . $row, $budgetPrevu);
        $sheet->setCellValue($col++ . $row, $budgetRealise);
        $sheet->setCellValue($col++ . $row, $tauxRealisation?->pourcentage ?? 0);
        $sheet->setCellValue($col++ . $row, $ecart);
        $sheet->setCellValue($col++ . $row, $tauxRealisation?->evaluation ?? 'Non évalué');
        $sheet->setCellValue($col++ . $row, $commune->depotsComptes->count());
    }

    private function addGovernanceDataToExcel(object $sheet, Commune $commune, int $row, string $startCol, int $annee): void
    {
        $col = $startCol;
        
        $receveurAssigne = $commune->receveurs->isNotEmpty() ? 'Oui' : 'Non';
        $ordonnateurAssigne = $commune->ordonnateurs->isNotEmpty() ? 'Oui' : 'Non';
        $defaillancesAnnee = $commune->defaillances->count();
        $retardsAnnee = $commune->retards->count();
        
        // Calcul simple du score de gouvernance
        $scoreGouvernance = 0;
        if ($commune->receveurs->isNotEmpty()) $scoreGouvernance += 25;
        if ($commune->ordonnateurs->isNotEmpty()) $scoreGouvernance += 25;
        if ($defaillancesAnnee == 0) $scoreGouvernance += 25;
        if ($retardsAnnee == 0) $scoreGouvernance += 25;
        
        $conformite = ($defaillancesAnnee == 0 && $retardsAnnee == 0) ? 'Conforme' : 'Non conforme';
        
        $sheet->setCellValue($col++ . $row, $receveurAssigne);
        $sheet->setCellValue($col++ . $row, $ordonnateurAssigne);
        $sheet->setCellValue($col++ . $row, $defaillancesAnnee);
        $sheet->setCellValue($col++ . $row, $retardsAnnee);
        $sheet->setCellValue($col++ . $row, $scoreGouvernance);
        $sheet->setCellValue($col++ . $row, $conformite);
    }

    private function addResumeDataToExcel(object $sheet, Commune $commune, int $row, string $startCol, int $annee): void
    {
        $col = $startCol;
        
        $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
        $sheet->setCellValue($col++ . $row, $tauxRealisation?->pourcentage ?? 0);
        $sheet->setCellValue($col++ . $row, $tauxRealisation?->evaluation ?? 'Non évalué');
        $sheet->setCellValue($col++ . $row, $commune->receveurs->first()?->nom ?? 'Non assigné');
        $sheet->setCellValue($col++ . $row, $commune->ordonnateurs->first()?->nom ?? 'Non assigné');
    }

    private function formatExcelSheet(object $sheet, int $startRow, int $endRow, int $colCount): void
    {
        $endCol = chr(65 + $colCount - 1); // A=65, B=66, etc.
        
        // Bordures
        $sheet->getStyle("A{$startRow}:{$endCol}{$endRow}")
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);
        
        // Auto-ajustement des colonnes
        for ($i = 0; $i < $colCount; $i++) {
            $col = chr(65 + $i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Alignement des nombres
        $sheet->getStyle("E{$startRow}:{$endCol}{$endRow}")
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    private function getCSVHeaders(string $type): array
    {
        return $this->getExcelHeaders($type);
    }

    private function getCommuneCSVData(Commune $commune, string $type): array
    {
        $baseData = [
            $commune->nom,
            $commune->code,
            $commune->departement->nom,
            $commune->departement->region->nom,
            $commune->population ?? 0,
            $commune->superficie ?? 0
        ];
        
        $annee = date('Y');
        
        switch ($type) {
            case 'complet':
                $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
                $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
                $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
                
                return array_merge($baseData, [
                    $prevision?->montant ?? 0,
                    $realisation,
                    $tauxRealisation?->pourcentage ?? 0,
                    $tauxRealisation?->evaluation ?? 'Non évalué',
                    $commune->receveurs->first()?->nom ?? 'Non assigné',
                    $commune->ordonnateurs->first()?->nom ?? 'Non assigné',
                    $commune->projets->count(),
                    $commune->defaillances->count(),
                    $commune->retards->count(),
                    $commune->telephone ?? '',
                    $commune->email ?? ''
                ]);
                
            case 'financier':
                $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
                $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
                $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
                
                return array_merge($baseData, [
                    $prevision?->montant ?? 0,
                    $realisation,
                    $tauxRealisation?->pourcentage ?? 0,
                    ($realisation - ($prevision?->montant ?? 0)),
                    $tauxRealisation?->evaluation ?? 'Non évalué',
                    $commune->depotsComptes->count()
                ]);
                
            case 'gouvernance':
                return array_merge($baseData, [
                    $commune->receveurs->isNotEmpty() ? 'Oui' : 'Non',
                    $commune->ordonnateurs->isNotEmpty() ? 'Oui' : 'Non',
                    $commune->defaillances->count(),
                    $commune->retards->count(),
                    $this->calculateGovernanceScore($commune),
                    ($commune->defaillances->isEmpty() && $commune->retards->isEmpty()) ? 'Conforme' : 'Non conforme'
                ]);
                
            case 'resume':
            default:
                $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
                
                return array_merge($baseData, [
                    $tauxRealisation?->pourcentage ?? 0,
                    $tauxRealisation?->evaluation ?? 'Non évalué',
                    $commune->receveurs->first()?->nom ?? 'Non assigné',
                    $commune->ordonnateurs->first()?->nom ?? 'Non assigné'
                ]);
        }
    }

    private function getCommuneJSONData(Commune $commune, string $type): array
    {
        $baseData = [
            'id' => $commune->id,
            'nom' => $commune->nom,
            'code' => $commune->code,
            'departement' => [
                'id' => $commune->departement->id,
                'nom' => $commune->departement->nom,
                'region' => [
                    'id' => $commune->departement->region->id,
                    'nom' => $commune->departement->region->nom
                ]
            ],
            'population' => $commune->population,
            'superficie' => $commune->superficie,
            'coordonnees_gps' => $commune->coordonnees_gps,
            'adresse' => $commune->adresse,
            'telephone' => $commune->telephone,
            'email' => $commune->email
        ];
        
        $annee = date('Y');
        
        switch ($type) {
            case 'complet':
                $baseData['finances'] = $this->getCommuneFinancialData($commune, $annee);
                $baseData['gouvernance'] = $this->getCommuneGovernanceData($commune);
                $baseData['projets'] = $commune->projets->map(function($projet) {
                    return [
                        'id' => $projet->id,
                        'nom' => $projet->nom,
                        'statut' => $projet->statut,
                        'budget' => $projet->budget
                    ];
                });
                break;
                
            case 'financier':
                $baseData['finances'] = $this->getCommuneFinancialData($commune, $annee);
                break;
                
            case 'gouvernance':
                $baseData['gouvernance'] = $this->getCommuneGovernanceData($commune);
                break;
        }
        
        return $baseData;
    }

    private function getCommuneFinancialData(Commune $commune, int $annee): array
    {
        $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
        $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
        $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
        return [
            'annee' => $annee,
            'budget_prevu' => $prevision?->montant ?? 0,
            'budget_realise' => $realisation,
            'taux_execution' => $tauxRealisation?->pourcentage ?? 0,
            'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué',
            'ecart' => $realisation - ($prevision?->montant ?? 0),
            'depots_comptes' => $commune->depotsComptes->count()
        ];
    }

    private function getCommuneGovernanceData(Commune $commune): array
    {
        return [
            'receveurs' => $commune->receveurs->map(function($receveur) {
                return [
                    'id' => $receveur->id,
                    'nom' => $receveur->nom,
                    'date_affectation' => $receveur->created_at
                ];
            }),
            'ordonnateurs' => $commune->ordonnateurs->map(function($ordonnateur) {
                return [
                    'id' => $ordonnateur->id,
                    'nom' => $ordonnateur->nom,
                    'date_affectation' => $ordonnateur->created_at
                ];
            }),
            'defaillances_count' => $commune->defaillances->count(),
            'retards_count' => $commune->retards->count(),
            'score_gouvernance' => $this->calculateGovernanceScore($commune),
            'conformite' => ($commune->defaillances->isEmpty() && $commune->retards->isEmpty())
        ];
    }

    private function addCommuneToXML(\SimpleXMLElement $communeXML, Commune $commune, string $type): void
    {
        $communeXML->addAttribute('id', $commune->id);
        $communeXML->addChild('nom', htmlspecialchars($commune->nom));
        $communeXML->addChild('code', $commune->code);
        
        $localisationXML = $communeXML->addChild('localisation');
        $localisationXML->addChild('departement', htmlspecialchars($commune->departement->nom));
        $localisationXML->addChild('region', htmlspecialchars($commune->departement->region->nom));
        
        $communeXML->addChild('population', $commune->population ?? 0);
        $communeXML->addChild('superficie', $commune->superficie ?? 0);
        
        if ($type === 'complet' || $type === 'financier') {
            $this->addFinancialDataToXML($communeXML, $commune);
        }
        
        if ($type === 'complet' || $type === 'gouvernance') {
            $this->addGovernanceDataToXML($communeXML, $commune);
        }
    }

    private function addFinancialDataToXML(\SimpleXMLElement $communeXML, Commune $commune): void
    {
        $annee = date('Y');
        $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
        $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
        $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
        
        $financesXML = $communeXML->addChild('finances');
        $financesXML->addAttribute('annee', $annee);
        $financesXML->addChild('budget_prevu', $prevision?->montant ?? 0);
        $financesXML->addChild('budget_realise', $realisation);
        $financesXML->addChild('taux_execution', $tauxRealisation?->pourcentage ?? 0);
        $financesXML->addChild('evaluation', htmlspecialchars($tauxRealisation?->evaluation ?? 'Non évalué'));
    }

    private function addGovernanceDataToXML(\SimpleXMLElement $communeXML, Commune $commune): void
    {
        $gouvernanceXML = $communeXML->addChild('gouvernance');
        
        $receveursXML = $gouvernanceXML->addChild('receveurs');
        foreach ($commune->receveurs as $receveur) {
            $receveurXML = $receveursXML->addChild('receveur');
            $receveurXML->addAttribute('id', $receveur->id);
            $receveurXML->addChild('nom', htmlspecialchars($receveur->nom));
        }
        
        $ordonnateuršXML = $gouvernanceXML->addChild('ordonnateurs');
        foreach ($commune->ordonnateurs as $ordonnateur) {
            $ordonnateurXML = $ordonnateuršXML->addChild('ordonnateur');
            $ordonnateurXML->addAttribute('id', $ordonnateur->id);
            $ordonnateurXML->addChild('nom', htmlspecialchars($ordonnateur->nom));
        }
        
        $gouvernanceXML->addChild('defaillances_count', $commune->defaillances->count());
        $gouvernanceXML->addChild('retards_count', $commune->retards->count());
        $gouvernanceXML->addChild('score_gouvernance', $this->calculateGovernanceScore($commune));
    }

    private function calculateGovernanceScore(Commune $commune): int
    {
        $score = 0;
        if ($commune->receveurs->isNotEmpty()) $score += 25;
        if ($commune->ordonnateurs->isNotEmpty()) $score += 25;
        if ($commune->defaillances->isEmpty()) $score += 25;
        if ($commune->retards->isEmpty()) $score += 25;
        return $score;
    }

    private function calculateGlobalStats(Collection $communes): array
    {
        $annee = date('Y');
        
        $totalBudgetPrevu = 0;
        $totalBudgetRealise = 0;
        $communesAvecReceveur = 0;
        $communesAvecOrdonnateur = 0;
        $totalDefaillances = 0;
        $totalRetards = 0;
        
        foreach ($communes as $commune) {
            $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
            $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
            
            $totalBudgetPrevu += $prevision?->montant ?? 0;
            $totalBudgetRealise += $realisation;
            
            if ($commune->receveurs->isNotEmpty()) $communesAvecReceveur++;
            if ($commune->ordonnateurs->isNotEmpty()) $communesAvecOrdonnateur++;
            
            $totalDefaillances += $commune->defaillances->count();
            $totalRetards += $commune->retards->count();
        }
        
        return [
            'total_communes' => $communes->count(),
            'total_budget_prevu' => $totalBudgetPrevu,
            'total_budget_realise' => $totalBudgetRealise,
            'taux_execution_global' => $totalBudgetPrevu > 0 ? 
                round(($totalBudgetRealise / $totalBudgetPrevu) * 100, 2) : 0,
            'communes_avec_receveur' => $communesAvecReceveur,
            'communes_avec_ordonnateur' => $communesAvecOrdonnateur,
            'taux_couverture_receveur' => round(($communesAvecReceveur / $communes->count()) * 100, 2),
            'taux_couverture_ordonnateur' => round(($communesAvecOrdonnateur / $communes->count()) * 100, 2),
            'total_defaillances' => $totalDefaillances,
            'total_retards' => $totalRetards,
            'moyenne_defaillances_par_commune' => round($totalDefaillances / $communes->count(), 2),
            'moyenne_retards_par_commune' => round($totalRetards / $communes->count(), 2)
        ];
    }

    private function getTypeLibelle(string $type): string
    {
        $libelles = [
            'complet' => 'COMPLET',
            'financier' => 'FINANCIER',
            'gouvernance' => 'GOUVERNANCE',
            'resume' => 'RÉSUMÉ EXÉCUTIF'
        ];
        
        return $libelles[$type] ?? 'STANDARD';
    }

    private function exportCommuneDetailToPDF(Commune $commune, string $fileName, int $annee): string
    {
        $data = [
            'commune' => $commune,
            'annee' => $annee,
            'date_generation' => now(),
            'donnees_financieres' => $this->getCommuneFinancialData($commune, $annee),
            'donnees_gouvernance' => $this->getCommuneGovernanceData($commune),
            'historique' => $this->getCommuneHistoricalData($commune)
        ];
        
        $pdf = PDF::loadView('exports.commune-detail.pdf', $data)
                  ->setPaper('A4', 'portrait');
        
        $filePath = storage_path("app/exports/{$fileName}");
        $pdf->save($filePath);
        
        return $fileName;
    }

    private function getCommuneHistoricalData(Commune $commune): array
    {
        $annees = range(date('Y') - 4, date('Y'));
        $historique = [];
        
        foreach ($annees as $annee) {
            $prevision = $commune->previsions->where('annee_exercice', $annee)->first();
            $realisation = $commune->realisations->where('annee_exercice', $annee)->sum('montant');
            $tauxRealisation = $commune->tauxRealisations->where('annee_exercice', $annee)->first();
            
            $historique[$annee] = [
                'annee' => $annee,
                'budget_prevu' => $prevision?->montant ?? 0,
                'budget_realise' => $realisation,
                'taux_execution' => $tauxRealisation?->pourcentage ?? 0,
                'evaluation' => $tauxRealisation?->evaluation ?? 'Non évalué'
            ];
        }
        
        return $historique;
    }

    // Stubs pour les autres méthodes d'export détaillé
    private function exportCommuneDetailToExcel(Commune $commune, string $fileName, int $annee): string
    {
        // À implémenter si nécessaire
        return $fileName;
    }

    private function exportCommuneDetailToCSV(Commune $commune, string $fileName, int $annee): string
    {
        // À implémenter si nécessaire
        return $fileName;
    }

    private function getRegionalStats(int $annee): array
    {
        // À implémenter pour les statistiques régionales
        return [];
    }

    private function exportRegionalStatsToPDF(array $stats, string $fileName, int $annee): string
    {
        // À implémenter pour les statistiques régionales en PDF
        return $fileName;
    }

    private function exportRegionalStatsToExcel(array $stats, string $fileName, int $annee): string
    {
        // À implémenter pour les statistiques régionales en Excel
        return $fileName;
    }

    private function addChartsToExcel(Spreadsheet $spreadsheet, Collection $communes, int $annee): void
    {
        // À implémenter pour ajouter des graphiques Excel
    }
}