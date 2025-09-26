@extends('layouts.app')

@section('title', $equipement->nom)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $equipement->nom }}</h1>
                <div class="flex items-center mt-2">
                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                        {{ $equipement->etat === 'Bon' ? 'bg-green-100 text-green-800' : 
                           ($equipement->etat === 'Moyen' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $equipement->etat }}
                    </span>
                    <span class="ml-3 text-gray-600">
                        <i class="fas fa-building mr-1"></i>{{ $equipement->infrastructure->nom }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-2 mt-4 md:mt-0">
                <a href="{{ route('equipements.edit', $equipement) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md flex items-center transition">
                    <i class="fas fa-edit mr-2"></i> Modifier
                </a>
                <a href="{{ route('equipements.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md flex items-center transition">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Détails -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i> Informations Générales
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Type</p>
                                <p class="text-gray-900 font-medium">{{ $equipement->type }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Infrastructure</p>
                                <p class="text-gray-900 font-medium">{{ $equipement->infrastructure->nom }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Quantité</p>
                                <p class="text-gray-900 font-medium">{{ $equipement->quantite }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Date d'acquisition</p>
                                <p class="text-gray-900 font-medium">{{ $equipement->date_acquisition ? $equipement->date_acquisition->format('d/m/Y') : 'Non spécifiée' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Coût unitaire</p>
                                <p class="text-gray-900 font-medium">{{ $equipement->cout ? number_format($equipement->cout, 0, ',', ' ') . ' FCFA' : 'Non spécifié' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Coût total</p>
                                <p class="text-gray-900 font-medium">{{ $equipement->cout ? number_format($equipement->cout * $equipement->quantite, 0, ',', ' ') . ' FCFA' : 'Non spécifié' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-align-left mr-2 text-green-600"></i> Description
                        </h2>
                        <div class="prose max-w-none">
                            {!! $equipement->description ?? '<p class="text-gray-500">Aucune description disponible</p>' !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte et stats -->
            <div class="space-y-6">
                <!-- Infrastructure -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-building mr-2 text-purple-600"></i> Infrastructure
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
                                <i class="fas fa-{{ $equipement->infrastructure->type === 'Route' ? 'road' : ($equipement->infrastructure->type === 'École' ? 'school' : 'hospital') }}"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $equipement->infrastructure->nom }}</h4>
                                <p class="text-sm text-gray-500">{{ $equipement->infrastructure->type }}</p>
                            </div>
                        </div>
                        <div class="pl-13">
                            <p class="text-sm text-gray-600 mb-1"><i class="fas fa-map-marker-alt mr-2"></i>{{ $equipement->infrastructure->localisation ?? 'Localisation non spécifiée' }}</p>
                            <p class="text-sm text-gray-600"><i class="fas fa-city mr-2"></i>{{ $equipement->infrastructure->commune->nom }}, {{ $equipement->infrastructure->commune->region }}</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('infrastructures.show', $equipement->infrastructure) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-external-link-alt mr-1"></i> Voir l'infrastructure
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Historique -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-history mr-2 text-orange-600"></i> Dernières interventions
                        </h3>
                    </div>
                    <div class="p-4">
                        @if($equipement->maintenances->isEmpty())
                            <p class="text-sm text-gray-500">Aucune intervention enregistrée</p>
                        @else
                            <ul class="space-y-3">
                                @foreach($equipement->maintenances->take(3) as $maintenance)
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 h-6 w-6 rounded-full mt-1 mr-3 
                                        {{ $maintenance->type === 'Réparation' ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600' }} flex items-center justify-center">
                                        <i class="fas fa-{{ $maintenance->type === 'Réparation' ? 'tools' : 'check' }} text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $maintenance->type }} - {{ $maintenance->date->format('d/m/Y') }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ $maintenance->description }}</p>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <div class="mt-3">
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-list mr-1"></i> Voir tout l'historique
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Images -->
        @if($equipement->images->isNotEmpty())
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-images mr-2 text-purple-600"></i> Photos de l'équipement
                </h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($equipement->images as $image)
                    <div class="relative group">
                        <img src="{{ Storage::url($image->path) }}" alt="Photo de l'équipement" class="w-full h-40 object-cover rounded-md">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 rounded-md">
                            <button class="bg-white bg-opacity-80 rounded-full p-2 text-red-600 hover:bg-opacity-100" 
                                    onclick="confirm('Voulez-vous vraiment supprimer cette image?') && document.getElementById('delete-image-{{ $image->id }}').submit()">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-image-{{ $image->id }}" action="{{ route('equipement-images.destroy', $image) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Maintenance -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-tools mr-2 text-orange-600"></i> Gestion de maintenance
                </h3>
                <a href="{{ route('maintenances.create', ['equipement_id' => $equipement->id]) }}" 
                   class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded-md text-sm flex items-center">
                    <i class="fas fa-plus mr-1"></i> Nouvelle intervention
                </a>
            </div>
            <div class="p-4">
                @if($equipement->maintenances->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-tools text-gray-400 text-4xl mb-3"></i>
                        <h4 class="text-lg font-medium text-gray-900 mb-1">Aucune intervention enregistrée</h4>
                        <p class="text-gray-500 mb-3">Ajoutez des interventions de maintenance pour suivre l'historique de cet équipement</p>
                        <a href="{{ route('maintenances.create', ['equipement_id' => $equipement->id]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                            <i class="fas fa-plus mr-2"></i> Ajouter une intervention
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
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
                                @foreach($equipement->maintenances as $maintenance)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $maintenance->date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $maintenance->type === 'Réparation' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $maintenance->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ Str::limit($maintenance->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $maintenance->cout ? number_format($maintenance->cout, 0, ',', ' ') . ' FCFA' : 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('maintenances.show', $maintenance) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50" 
                                               title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('maintenances.edit', $maintenance) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-50" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50" 
                                                        title="Supprimer"
                                                        onclick="return confirm('Voulez-vous vraiment supprimer cette intervention?')">
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
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmation before deleting images
        document.querySelectorAll('[onclick^="confirm"]').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm(this.getAttribute('onclick').match(/'(.*?)'/)[1])) {
                    e.preventDefault();
                }
            });
        });
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
    .group:hover .group-hover\:bg-opacity-50 {
        background-opacity: 0.5;
    }
    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }
</style>
@endsection