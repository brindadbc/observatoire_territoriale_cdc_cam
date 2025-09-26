@extends('layouts.app')

@section('title', 'Liste des Infrastructures')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header with Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-road text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Total Infrastructures</p>
                    <h3 class="text-2xl font-bold">{{ $infrastructures->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">En bon état</p>
                    <h3 class="text-2xl font-bold">{{ $infrastructures->where('etat', 'Bon')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">État moyen</p>
                    <h3 class="text-2xl font-bold">{{ $infrastructures->where('etat', 'Moyen')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">À réparer</p>
                    <h3 class="text-2xl font-bold">{{ $infrastructures->where('etat', 'Mauvais')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 bg-white p-4 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">
            <i class="fas fa-road mr-2 text-blue-600"></i>Liste des Infrastructures
        </h1>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
            <a href="{{ route('infrastructures.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition">
                <i class="fas fa-plus mr-2"></i> Ajouter
            </a>
            <button id="filterToggle" 
                    class="bg-white border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg flex items-center justify-center">
                <i class="fas fa-filter mr-2 text-gray-600"></i> Filtres
            </button>
        </div>
    </div>

    <!-- Filter Panel -->
    <div id="filterPanel" class="bg-white rounded-lg shadow-md p-6 mb-8 hidden">
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="commune" class="block text-sm font-medium text-gray-700 mb-1">Commune</label>
                <select id="commune" name="commune" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Toutes les communes</option>
                    @foreach($communes as $commune)
                        <option value="{{ $commune->id }}" {{ request('commune') == $commune->id ? 'selected' : '' }}>
                            {{ $commune->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="type" name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tous types</option>
                    <option value="Route" {{ request('type') == 'Route' ? 'selected' : '' }}>Route</option>
                    <option value="École" {{ request('type') == 'École' ? 'selected' : '' }}>École</option>
                    <option value="Hôpital" {{ request('type') == 'Hôpital' ? 'selected' : '' }}>Hôpital</option>
                </select>
            </div>
            <div>
                <label for="etat" class="block text-sm font-medium text-gray-700 mb-1">État</label>
                <select id="etat" name="etat" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tous états</option>
                    <option value="Bon" {{ request('etat') == 'Bon' ? 'selected' : '' }}>Bon</option>
                    <option value="Moyen" {{ request('etat') == 'Moyen' ? 'selected' : '' }}>Moyen</option>
                    <option value="Mauvais" {{ request('etat') == 'Mauvais' ? 'selected' : '' }}>Mauvais</option>
                </select>
            </div>
            <div class="md:col-span-3 flex justify-end space-x-3">
                <a href="{{ route('infrastructures.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Réinitialiser
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Appliquer
                </button>
            </div>
        </form>
    </div>

    <!-- Infrastructure Table -->
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
                            Commune
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
                    @forelse($infrastructures as $infrastructure)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <i class="fas fa-{{ $infrastructure->type === 'Route' ? 'road' : ($infrastructure->type === 'École' ? 'school' : 'hospital') }}"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $infrastructure->nom }}</div>
                                    <div class="text-sm text-gray-500">{{ $infrastructure->localisation }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ $infrastructure->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $infrastructure->commune->nom }}</div>
                            <div class="text-sm text-gray-500">{{ $infrastructure->commune->region }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $infrastructure->etat === 'Bon' ? 'bg-green-100 text-green-800' : 
                                   ($infrastructure->etat === 'Moyen' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $infrastructure->etat }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('infrastructures.show', $infrastructure) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50" 
                                   title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('infrastructures.edit', $infrastructure) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-50" 
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('infrastructures.destroy', $infrastructure) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50" 
                                            title="Supprimer"
                                            onclick="return confirm('Voulez-vous vraiment supprimer cette infrastructure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Aucune infrastructure trouvée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $infrastructures->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle filter panel
        const filterToggle = document.getElementById('filterToggle');
        const filterPanel = document.getElementById('filterPanel');
        
        filterToggle.addEventListener('click', function() {
            filterPanel.classList.toggle('hidden');
            const icon = filterToggle.querySelector('i');
            if (filterPanel.classList.contains('hidden')) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-filter');
                filterToggle.classList.remove('bg-gray-100');
            } else {
                icon.classList.remove('fa-filter');
                icon.classList.add('fa-times');
                filterToggle.classList.add('bg-gray-100');
            }
        });

        // Submit filter form
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData).toString();
            window.location.href = "{{ route('infrastructures.index') }}?" + params;
        });

        // Highlight current sort column
        const urlParams = new URLSearchParams(window.location.search);
        const sortColumn = urlParams.get('sort');
        const sortDirection = urlParams.get('direction');
        
        if (sortColumn) {
            document.querySelectorAll('th').forEach(header => {
                if (header.textContent.trim() === sortColumn) {
                    const icon = header.querySelector('i');
                    icon.classList.remove('fa-sort');
                    icon.classList.add(sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
                }
            });
        }
    });
</script>

<style>
    /* Custom styles for the table */
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
    .pagination {
        display: flex;
        justify-content: center;
        padding: 1rem;
    }
    .pagination li {
        margin: 0 0.25rem;
    }
    .pagination li a {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    .pagination li.active a {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
</style>
@endsection