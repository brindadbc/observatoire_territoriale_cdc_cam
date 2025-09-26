@extends('layouts.app')

@section('title', 'Suivi du Fonctionnement')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">
            <i class="fas fa-tasks mr-2 text-orange-600"></i>Suivi du Fonctionnement
        </h1>
        <div class="flex space-x-3">
            <a href="{{ route('fonctionnements.create') }}" 
               class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center transition-all">
                <i class="fas fa-plus mr-2"></i> Nouveau Suivi
            </a>
        </div>
    </div>

    <!-- Timeline View -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Historique Récent</h3>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse($fonctionnements as $fonct)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full flex items-center justify-center 
                                        {{ $fonct->statut === 'Fonctionnel' ? 'bg-green-500 text-white' : 
                                           ($fonct->statut === 'En maintenance' ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white') }}">
                                        <i class="fas 
                                            {{ $fonct->statut === 'Fonctionnel' ? 'fa-check' : 
                                               ($fonct->statut === 'En maintenance' ? 'fa-tools' : 'fa-exclamation') }}"></i>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-800">
                                            <span class="font-medium">{{ $fonct->fonctionnable->nom }}</span> - 
                                            <span class="{{ $fonct->statut === 'Fonctionnel' ? 'text-green-600' : 
                                                          ($fonct->statut === 'En maintenance' ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $fonct->statut }}
                                            </span>
                                            @if($fonct->cout_maintenance)
                                            <span class="text-gray-500 ml-2">{{ number_format($fonct->cout_maintenance, 0, ',', ' ') }} FCFA</span>
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $fonct->notes }}</p>
                                    </div>
                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                        <time datetime="{{ $fonct->date->format('Y-m-d') }}">{{ $fonct->date->format('d/m/Y') }}</time>
                                        <div class="mt-1 flex space-x-1">
                                            <a href="{{ route('fonctionnements.edit', $fonct) }}" 
                                               class="text-yellow-600 hover:text-yellow-900" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('fonctionnements.destroy', $fonct) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 delete-btn" 
                                                        title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="text-center py-4 text-gray-500">
                        Aucun enregistrement de fonctionnement trouvé.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg font-semibold leading-6 text-gray-900">
                Détails du Fonctionnement
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Élément
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Coût Maintenance
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($fonctionnements as $fonct)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $fonct->date->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $fonct->fonctionnable->nom }}</div>
                            <div class="text-sm text-gray-500">
                                @if($fonct->fonctionnable_type === 'App\\Models\\Infrastructure')
                                    {{ $fonct->fonctionnable->commune->nom }}
                                @else
                                    {{ $fonct->fonctionnable->commune->nom }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($fonct->fonctionnable_type === 'App\\Models\\Infrastructure')
                                    Infrastructure ({{ $fonct->fonctionnable->type }})
                                @else
                                    Service ({{ $fonct->fonctionnable->type }})
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $fonct->statut === 'Fonctionnel' ? 'bg-green-100 text-green-800' : 
                                   ($fonct->statut === 'En maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $fonct->statut }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $fonct->cout_maintenance ? number_format($fonct->cout_maintenance, 0, ',', ' ') . ' FCFA' : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('fonctionnements.show', $fonct) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50" 
                                   title="Voir détails">
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
                                            class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 delete-btn" 
                                            title="Supprimer"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Aucun enregistrement de fonctionnement trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $fonctionnements->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmation before deletion
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush