@extends('layouts.app')

@section('title', 'Créer une Infrastructure')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Nouvelle Infrastructure
                </h2>
            </div>

            <!-- Form -->
            <form action="{{ route('infrastructures.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nom -->
                    <div class="md:col-span-2">
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom de l'infrastructure *
                        </label>
                        <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('nom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                            Type *
                        </label>
                        <select id="type" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionnez un type</option>
                            <option value="Route" {{ old('type') == 'Route' ? 'selected' : '' }}>Route</option>
                            <option value="École" {{ old('type') == 'École' ? 'selected' : '' }}>École</option>
                            <option value="Hôpital" {{ old('type') == 'Hôpital' ? 'selected' : '' }}>Hôpital</option>
                            <option value="Pont" {{ old('type') == 'Pont' ? 'selected' : '' }}>Pont</option>
                            <option value="Marché" {{ old('type') == 'Marché' ? 'selected' : '' }}>Marché</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Commune -->
                    <div>
                        <label for="commune_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Commune *
                        </label>
                        <select id="commune_id" name="commune_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionnez une commune</option>
                            @foreach($communes as $commune)
                                <option value="{{ $commune->id }}" {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                    {{ $commune->nom }} ({{ $commune->region }})
                                </option>
                            @endforeach
                        </select>
                        @error('commune_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Localisation -->
                    <div>
                        <label for="localisation" class="block text-sm font-medium text-gray-700 mb-1">
                            Localisation
                        </label>
                        <input type="text" id="localisation" name="localisation" value="{{ old('localisation') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('localisation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- État -->
                    <div>
                        <label for="etat" class="block text-sm font-medium text-gray-700 mb-1">
                            État *
                        </label>
                        <select id="etat" name="etat" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionnez un état</option>
                            <option value="Bon" {{ old('etat') == 'Bon' ? 'selected' : '' }}>Bon</option>
                            <option value="Moyen" {{ old('etat') == 'Moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="Mauvais" {{ old('etat') == 'Mauvais' ? 'selected' : '' }}>Mauvais</option>
                        </select>
                        @error('etat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date de construction -->
                    <div>
                        <label for="date_construction" class="block text-sm font-medium text-gray-700 mb-1">
                            Date de construction
                        </label>
                        <input type="date" id="date_construction" name="date_construction" value="{{ old('date_construction') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('date_construction')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Coût de construction -->
                    <div>
                        <label for="cout_construction" class="block text-sm font-medium text-gray-700 mb-1">
                            Coût de construction (FCFA)
                        </label>
                        <input type="number" id="cout_construction" name="cout_construction" value="{{ old('cout_construction') }}" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('cout_construction')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Images (max 3)
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <div class="flex text-sm text-gray-600">
                                <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Téléverser des fichiers</span>
                                    <input id="images" name="images[]" type="file" multiple class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, JPEG jusqu'à 2MB
                            </p>
                        </div>
                    </div>
                    <div id="imagePreview" class="mt-2 flex flex-wrap gap-2"></div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 border-t border-gray-200 pt-6">
                    <a href="{{ route('infrastructures.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md flex items-center transition">
                        <i class="fas fa-times mr-2"></i> Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center transition">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview functionality
        const imageInput = document.getElementById('images');
        const imagePreview = document.getElementById('imagePreview');
        
        imageInput.addEventListener('change', function() {
            imagePreview.innerHTML = '';
            
            if (this.files && this.files.length > 0) {
                const files = Array.from(this.files).slice(0, 3); // Limit to 3 files
                
                files.forEach(file => {
                    if (!file.type.match('image.*')) {
                        return;
                    }
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const imgContainer = document.createElement('div');
                        imgContainer.className = 'relative';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'h-24 w-24 object-cover rounded-md';
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs';
                        removeBtn.innerHTML = '&times;';
                        removeBtn.onclick = function() {
                            imgContainer.remove();
                            // TODO: Remove file from input
                        };
                        
                        imgContainer.appendChild(img);
                        imgContainer.appendChild(removeBtn);
                        imagePreview.appendChild(imgContainer);
                    };
                    
                    reader.readAsDataURL(file);
                });
            }
        });

        // Dynamic map integration for location
        if (document.getElementById('localisation')) {
            // You could integrate a map here to help with location input
        }
    });
</script>

<style>
    /* Custom styles for the form */
    .form-group {
        margin-bottom: 1.5rem;
    }
    label {
        display: block;
        margin-bottom: 0.5rem;
    }
    input[type="text"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus,
    textarea:focus {
        outline: none;
        ring: 2px;
        ring-color: #3b82f6;
        border-color: #3b82f6;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }
    .btn-primary:hover {
        background-color: #2563eb;
    }
    .btn-secondary {
        background-color: #6b7280;
        color: white;
    }
    .btn-secondary:hover {
        background-color: #4b5563;
    }
    .error {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endsection