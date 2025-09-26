@extends('layouts.app')

@section('title', $infrastructure->nom)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $infrastructure->nom }}</h1>
                <div class="flex items-center mt-2">
                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                        {{ $infrastructure->etat === 'Bon' ? 'bg-green-100 text-green-800' : 
                           ($infrastructure->etat === 'Moyen' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $infrastructure->etat }}
                    </span>
                    <span class="ml-3 text-gray-600">
                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $infrastructure->commune->nom }}, {{ $infrastructure->commune->region }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-2 mt-4 md:mt-0">
                <a href="{{ route('infrastructures.edit', $infrastructure) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md flex items-center transition">
                    <i class="fas fa-edit mr-2"></i> Modifier
                </a>
                <a href="{{ route('infrastructures.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md flex items-center transition">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Details Card -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i> Informations Générales
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Type</p>
                                <p class="text-gray-900 font-medium">{{ $infrastructure->type }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Localisation</p>
                                <p class="text-gray-900 font-medium">{{ $infrastructure->localisation ?? 'Non spécifiée' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Date de construction</p>
                                <p class="text-gray-900 font-medium">{{ $infrastructure->date_construction ? $infrastructure->date_construction->format('d/m/Y') : 'Non spécifiée' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Coût de construction</p>
                                <p class="text-gray-900 font-medium">{{ $infrastructure->cout_construction ? number_format($infrastructure->cout_construction, 0, ',', ' ') . ' FCFA' : 'Non spécifié' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-align-left mr-2 text-green-600"></i> Description
                        </h2>
                        <div class="prose max-w-none">
                            {!! $infrastructure->description ?? '<p class="text-gray-500">Aucune description disponible</p>' !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map and Stats -->
            <div class="space-y-6">
                <!-- Map Card -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-map-marked-alt mr-2 text-purple-600"></i> Localisation
                        </h3>
                    </div>
                    <div id="map" class="h-64 w-full"></div>
                    <div class="p-4 border-t border-gray-200 text-center">
                        <a href="https://www.google.com/maps?q={{ $infrastructure->localisation }}" 
                           target="_blank" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-external-link-alt mr-1"></i> Ouvrir dans Google Maps
                        </a>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-chart-pie mr-2 text-orange-600"></i> Statistiques
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm text-gray-600">Équipements</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ $infrastructure->equipements_count }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm text-gray-600">Maintenances</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                {{ $infrastructure->fonctionnements_count }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Dernière mise à jour</span>
                            <span class="text-sm text-gray-600">{{ $infrastructure->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button id="equipementsTab" 
                        class="tab-button border-b-2 border-blue-500 text-blue-600 px-4 py-3 text-sm font-medium">
                    <i class="fas fa-tools mr-2"></i> Équipements
                </button>
                <button id="maintenanceTab" 
                        class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-4 py-3 text-sm font-medium">
                    <i class="fas fa-history mr-2"></i> Historique de maintenance
                </button>
            </nav>
        </div>

        <!-- Tab Contents -->
        <div id="equipementsContent" class="tab-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Équipements associés</h3>
                <a href="{{ route('equipements.create', ['infrastructure_id' => $infrastructure->id]) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm flex items-center">
                    <i class="fas fa-plus mr-1"></i> Ajouter
                </a>
            </div>

            @if($infrastructure->equipements->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-tools text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement enregistré</h3>
                    <p class="text-gray-500 mb-4">Cette infrastructure ne possède aucun équipement enregistré.</p>
                    <a href="{{ route('equipements.create', ['infrastructure_id' => $infrastructure->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i> Ajouter un équipement
                    </a>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nom
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantité
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        État
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($infrastructure->equipements as $equipement)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $equipement->nom }}</div>
                                        <div class="text-sm text-gray-500">{{ $equipement->date_acquisition ? $equipement->date_acquisition->format('d/m/Y') : 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            {{ $equipement->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                            {{ $equipement->quantite }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $equipement->etat === 'Bon' ? 'bg-green-100 text-green-800' : 
                                               ($equipement->etat === 'Moyen' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $equipement->etat }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('equipements.show', $equipement) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50" 
                                               title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('equipements.edit', $equipement) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-50" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('equipements.destroy', $equipement) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50" 
                                                        title="Supprimer"
                                                        onclick="return confirm('Voulez-vous vraiment supprimer cet équipement?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div id="maintenanceContent" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Historique de maintenance</h3>
                <a href="{{ route('fonctionnements.create', ['fonctionnable_type' => 'infrastructure', 'fonctionnable_id' => $infrastructure->id]) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm flex items-center">
                    <i class="fas fa-plus mr-1"></i> Ajouter
                </a>
            </div>

            @if($infrastructure->fonctionnements->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun historique de maintenance</h3>
                    <p class="text-gray-500 mb-4">Aucune opération de maintenance n'a été enregistrée pour cette infrastructure.</p>
                    <a href="{{ route('fonctionnements.create', ['fonctionnable_type' => 'infrastructure', 'fonctionnable_id' => $infrastructure->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i> Ajouter une opération
                    </a>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Notes
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Coût
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($infrastructure->fonctionnements as $fonct)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $fonct->date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $fonct->statut === 'Fonctionnel' ? 'bg-green-100 text-green-800' : 
                                               ($fonct->statut === 'En maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $fonct->statut }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 truncate max-w-xs">{{ $fonct->notes }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $fonct->cout_maintenance ? number_format($fonct->cout_maintenance, 0, ',', ' ') . ' FCFA' : 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('fonctionnements.show', $fonct) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50" 
                                               title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('fonctionnements.edit', $fonct) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-50" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('fonctionnements.destroy', $fonct) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50" 
                                                        title="Supprimer"
                                                        onclick="return confirm('Voulez-vous vraiment supprimer cet enregistrement?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        const map = L.map('map').setView([5.6919, 10.2226], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add marker for this infrastructure
        L.marker([5.6919, 10.2226]).addTo(map)
            .bindPopup("<b>{{ $infrastructure->nom }}</b><br>{{ $infrastructure->type }}");

        // Tab functionality
        const tabs = {
            equipements: {
                button: document.getElementById('equipementsTab'),
                content: document.getElementById('equipementsContent')
            },
            maintenance: {
                button: document.getElementById('maintenanceTab'),
                content: document.getElementById('maintenanceContent')
            }
        };

        function switchTab(activeTab) {
            // Hide all content and reset buttons
            Object.values(tabs).forEach(tab => {
                tab.content.classList.add('hidden');
                tab.button.classList.remove('border-blue-500', 'text-blue-600');
                tab.button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });

            // Show active content and style button
            activeTab.content.classList.remove('hidden');
            activeTab.button.classList.add('border-blue-500', 'text-blue-600');
            activeTab.button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        }

        // Add event listeners to tabs
        tabs.equipements.button.addEventListener('click', () => switchTab(tabs.equipements));
        tabs.maintenance.button.addEventListener('click', () => switchTab(tabs.maintenance));
    });
</script>

<style>
    .prose {
        max-width: none;
        color: #374151;
    }
    .prose p {
        margin-bottom: 1rem;
    }
    .tab-content {
        transition: all 0.3s ease;
    }
    #map {
        height: 100%;
        min-height: 300px;
    }
    table {
        min-width: 100%;
    }
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }
    th {
        background-color: #f9fafb;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
    }
    tr:hover {
        background-color: #f9fafb;
    }
</style>
@endsection