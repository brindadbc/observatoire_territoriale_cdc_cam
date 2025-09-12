<?php

namespace App\Services;

use App\Models\Commune;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CommuneCreated;
use App\Notifications\CommuneUpdated;
use App\Notifications\CommuneDeleted;
use App\Notifications\PerformanceAlert;
use App\Notifications\BudgetAlert;

class NotificationService
{
    /**
     * Notifie la création d'une commune
     */
    public function communeCreated(Commune $commune): void
    {
        try {
            // Récupérer les utilisateurs à notifier
            $users = $this->getUsersToNotify(['commune_created']);
            
            // Envoyer les notifications
            foreach ($users as $user) {
                $user->notify(new CommuneCreated($commune));
            }
            
            // Log de l'événement
            Log::info('Commune créée avec succès', [
                'commune_id' => $commune->id,
                'commune_nom' => $commune->nom,
                'user_id' => auth()->id(),
                'notified_users' => $users->pluck('id')->toArray()
            ]);
            
            // Envoyer notification système (Slack, Teams, etc.)
            $this->sendSystemNotification('Nouvelle commune créée: ' . $commune->nom, [
                'type' => 'commune_created',
                'commune' => $commune->nom,
                'departement' => $commune->departement->nom,
                'region' => $commune->departement->region->nom,
                'created_by' => auth()->user()->name ?? 'Système'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications de création de commune', [
                'commune_id' => $commune->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notifie la mise à jour d'une commune
     */
    public function communeUpdated(Commune $commune, array $oldData, array $newData): void
    {
        try {
            // Identifier les changements significatifs
            $changementsImportants = $this->identifySignificantChanges($oldData, $newData);
            
            if (empty($changementsImportants)) {
                return; // Pas de changements significatifs
            }
            
            $users = $this->getUsersToNotify(['commune_updated']);
            
            foreach ($users as $user) {
                $user->notify(new CommuneUpdated($commune, $changementsImportants));
            }
            
            Log::info('Commune mise à jour', [
                'commune_id' => $commune->id,
                'commune_nom' => $commune->nom,
                'changements' => $changementsImportants,
                'user_id' => auth()->id()
            ]);
            
            // Notification système pour changements critiques
            if ($this->hasCriticalChanges($changementsImportants)) {
                $this->sendSystemNotification('Modifications importantes - Commune: ' . $commune->nom, [
                    'type' => 'commune_updated_critical',
                    'commune' => $commune->nom,
                    'changements' => $changementsImportants,
                    'updated_by' => auth()->user()->name ?? 'Système'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications de mise à jour de commune', [
                'commune_id' => $commune->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notifie la suppression d'une commune
     */
    public function communeDeleted(array $communeData): void
    {
        try {
            $users = $this->getUsersToNotify(['commune_deleted']);
            
            foreach ($users as $user) {
                $user->notify(new CommuneDeleted($communeData));
            }
            
            Log::warning('Commune supprimée', [
                'commune_data' => $communeData,
                'user_id' => auth()->id()
            ]);
            
            // Notification système critique
            $this->sendSystemNotification('🚨 COMMUNE SUPPRIMÉE: ' . $communeData['nom'], [
                'type' => 'commune_deleted',
                'commune' => $communeData['nom'],
                'departement' => $communeData['departement']['nom'] ?? 'N/A',
                'deleted_by' => auth()->user()->name ?? 'Système',
                'date_suppression' => now()->format('d/m/Y H:i')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications de suppression de commune', [
                'commune_data' => $communeData,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envoie des alertes de performance
     */
    public function sendPerformanceAlert(Commune $commune, array $performance): void
    {
        try {
            // Vérifier si une alerte est nécessaire
            if (!$this->shouldSendPerformanceAlert($performance)) {
                return;
            }
            
            $users = $this->getUsersToNotify(['performance_alert'], $commune);
            
            foreach ($users as $user) {
                $user->notify(new PerformanceAlert($commune, $performance));
            }
            
            Log::warning('Alerte de performance envoyée', [
                'commune_id' => $commune->id,
                'commune_nom' => $commune->nom,
                'performance' => $performance['score_global'],
                'evaluation' => $performance['evaluation_globale']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi d\'alerte de performance', [
                'commune_id' => $commune->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envoie des alertes budgétaires
     */
    public function sendBudgetAlert(Commune $commune, array $budgetData): void
    {
        try {
            if (!$this->shouldSendBudgetAlert($budgetData)) {
                return;
            }
            
            $users = $this->getUsersToNotify(['budget_alert'], $commune);
            
            foreach ($users as $user) {
                $user->notify(new BudgetAlert($commune, $budgetData));
            }
            
            Log::warning('Alerte budgétaire envoyée', [
                'commune_id' => $commune->id,
                'commune_nom' => $commune->nom,
                'taux_execution' => $budgetData['taux_execution'] ?? 0,
                'ecart_budget' => $budgetData['ecart'] ?? 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi d\'alerte budgétaire', [
                'commune_id' => $commune->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envoie une notification système (Slack, Teams, webhook)
     */
    public function sendSystemNotification(string $message, array $data = []): void
    {
        try {
            // Webhook Slack
            if (config('notifications.slack.enabled')) {
                $this->sendSlackNotification($message, $data);
            }
            
            // Webhook Teams
            if (config('notifications.teams.enabled')) {
                $this->sendTeamsNotification($message, $data);
            }
            
            // Webhook personnalisé
            if (config('notifications.webhook.enabled')) {
                $this->sendWebhookNotification($message, $data);
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de notification système', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envoie un rapport quotidien
     */
    public function sendDailyReport(): void
    {
        try {
            $rapport = $this->generateDailyReport();
            $users = $this->getUsersToNotify(['daily_report']);
            
            foreach ($users as $user) {
                Mail::to($user)->send(new \App\Mail\DailyReport($rapport));
            }
            
            Log::info('Rapport quotidien envoyé', [
                'date' => now()->format('Y-m-d'),
                'recipients' => $users->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du rapport quotidien', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Méthodes privées
     */
    private function getUsersToNotify(array $permissions, Commune $commune = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::whereHas('permissions', function($q) use ($permissions) {
            $q->whereIn('name', $permissions);
        })->orWhere('role', 'admin');
        
        // Filtrer par région/département si nécessaire
        if ($commune && config('notifications.filter_by_location')) {
            $query->where(function($q) use ($commune) {
                $q->whereHas('regions', function($rq) use ($commune) {
                    $rq->where('id', $commune->departement->region_id);
                })->orWhereHas('departements', function($dq) use ($commune) {
                    $dq->where('id', $commune->departement_id);
                });
            });
        }
        
        return $query->where('notifications_enabled', true)->get();
    }

    private function identifySignificantChanges(array $oldData, array $newData): array
    {
        $champsImportants = [
            'nom', 'code', 'departement_id', 'population', 
            'superficie', 'telephone', 'email', 'adresse'
        ];
        
        $changements = [];
        
        foreach ($champsImportants as $champ) {
            $ancienneValeur = $oldData[$champ] ?? null;
            $nouvelleValeur = $newData[$champ] ?? null;
            
            if ($ancienneValeur != $nouvelleValeur) {
                $changements[$champ] = [
                    'ancien' => $ancienneValeur,
                    'nouveau' => $nouvelleValeur,
                    'libelle' => $this->getFieldLabel($champ)
                ];
            }
        }
        
        return $changements;
    }

    private function hasCriticalChanges(array $changements): bool
    {
        $champsCritiques = ['nom', 'code', 'departement_id'];
        
        return !empty(array_intersect(array_keys($changements), $champsCritiques));
    }

    private function shouldSendPerformanceAlert(array $performance): bool
    {
        $scoreGlobal = $performance['score_global'] ?? 100;
        $seuilAlerte = config('notifications.performance_threshold', 50);
        
        return $scoreGlobal < $seuilAlerte;
    }

    private function shouldSendBudgetAlert(array $budgetData): bool
    {
        $tauxExecution = $budgetData['taux_execution'] ?? 100;
        $seuilMin = config('notifications.budget_threshold_min', 30);
        $seuilMax = config('notifications.budget_threshold_max', 120);
        
        return $tauxExecution < $seuilMin || $tauxExecution > $seuilMax;
    }

    private function sendSlackNotification(string $message, array $data): void
    {
        $webhookUrl = config('notifications.slack.webhook_url');
        
        if (!$webhookUrl) return;
        
        $payload = [
            'text' => $message,
            'attachments' => [
                [
                    'color' => $this->getColorForNotificationType($data['type'] ?? 'info'),
                    'fields' => $this->formatDataForSlack($data),
                    'footer' => 'Observatoire des Collectivités',
                    'ts' => time()
                ]
            ]
        ];
        
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
    }

    private function sendTeamsNotification(string $message, array $data): void
    {
        $webhookUrl = config('notifications.teams.webhook_url');
        
        if (!$webhookUrl) return;
        
        $payload = [
            '@type' => 'MessageCard',
            '@context' => 'http://schema.org/extensions',
            'themeColor' => $this->getColorForNotificationType($data['type'] ?? 'info'),
            'summary' => $message,
            'sections' => [
                [
                    'activityTitle' => $message,
                    'activitySubtitle' => 'Observatoire des Collectivités',
                    'facts' => $this->formatDataForTeams($data),
                    'markdown' => true
                ]
            ]
        ];
        
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
    }

    private function sendWebhookNotification(string $message, array $data): void
    {
        $webhookUrl = config('notifications.webhook.url');
        
        if (!$webhookUrl) return;
        
        $payload = [
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'source' => 'observatoire_collectivites'
        ];
        
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . config('notifications.webhook.token')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
    }

    private function generateDailyReport(): array
    {
        return [
            'date' => now()->format('d/m/Y'),
            'statistiques' => [
                'communes_total' => \App\Models\Commune::count(),
                'communes_creees_aujourd_hui' => \App\Models\Commune::whereDate('created_at', today())->count(),
                'communes_modifiees_aujourd_hui' => \App\Models\Commune::whereDate('updated_at', today())->count(),
            ],
            'alertes' => [
                'performance_faible' => $this->getCommunesPerformanceFaible(),
                'budget_problematique' => $this->getCommunesBudgetProblematique(),
            ],
            'tendances' => $this->getTendancesJournalieres()
        ];
    }

    private function getCommunesPerformanceFaible(): int
    {
        return \App\Models\Commune::whereHas('tauxRealisations', function($q) {
            $q->where('annee_exercice', date('Y'))
              ->where('pourcentage', '<', 50);
        })->count();
    }

    private function getCommunesBudgetProblematique(): int
    {
        // Logique pour identifier les communes avec des problèmes budgétaires
        return 0; // À implémenter selon vos critères
    }

    private function getTendancesJournalieres(): array
    {
        return [
            'evolution_performance' => '+2.3%',
            'evolution_budget' => '+1.8%'
        ];
    }

    private function getFieldLabel(string $field): string
    {
        $labels = [
            'nom' => 'Nom',
            'code' => 'Code',
            'departement_id' => 'Département',
            'population' => 'Population',
            'superficie' => 'Superficie',
            'telephone' => 'Téléphone',
            'email' => 'Email',
            'adresse' => 'Adresse'
        ];
        
        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    private function getColorForNotificationType(string $type): string
    {
        $colors = [
            'commune_created' => 'good',
            'commune_updated' => 'warning', 
            'commune_updated_critical' => 'danger',
            'commune_deleted' => 'danger',
            'performance_alert' => 'warning',
            'budget_alert' => 'danger',
            'info' => 'good'
        ];
        
        return $colors[$type] ?? 'good';
    }

    private function formatDataForSlack(array $data): array
    {
        $fields = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['type'])) continue;
            
            $fields[] = [
                'title' => ucfirst(str_replace('_', ' ', $key)),
                'value' => is_array($value) ? json_encode($value) : $value,
                'short' => strlen($value) < 20
            ];
        }
        
        return $fields;
    }

    private function formatDataForTeams(array $data): array
    {
        $facts = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['type'])) continue;
            
            $facts[] = [
                'name' => ucfirst(str_replace('_', ' ', $key)),
                'value' => is_array($value) ? json_encode($value) : $value
            ];
        }
        
        return $facts;
    }
}