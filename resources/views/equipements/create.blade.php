@extends('layouts.app')

@section('title', 'Ajouter un Équipement')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- En-tête -->
            <div class="bg-gradient-to-r from-purple-600 to-purple-800 px-6 py-4">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Nouvel Équipement
                </h2>
            </div>

            <!-- Formulaire -->
            <form action="{{ route('equipements.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nom -->
                    <div class="md:col-span-2">
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom de l'équipement *
                        </label>
                        <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Sélectionnez un type</option>
                            <option value="Médical" {{ old('type') == 'Médical' ? 'selected' : '' }}>Médical</option>
                            <option value="Scolaire" {{ old('type') == 'Scolaire' ? 'selected' : '' }}>Scolaire</option>
                            <option value="Bureautique" {{ old('type') == 'Bureautique' ? 'selected' : '' }}>Bureautique</option>
                            <option value="Technique" {{ old('type') == 'Technique' ? 'selected' : '' }}>Technique</option>
                            <option value="Autre" {{ old('type') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Infrastructure -->
                    <div>
                        <label for="infrastructure_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Infrastructure *
                        </label>
                        <select id="infrastructure_id" name="infrastructure_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Sélectionnez une infrastructure</option>
                            @foreach($infrastructures as $infrastructure)
                                <option value="{{ $infrastructure->id }}" {{ old('infrastructure_id') == $infrastructure->id ? 'selected' : '' }}>
                                    {{ $infrastructure->nom }} ({{ $infrastructure->commune->nom }})
                                </option>
                            @endforeach
                        </select>
                        @error('infrastructure_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantité -->
                    <div>
                        <label for="quantite" class="block text-sm font-medium text-gray-700 mb-1">
                            Quantité *
                        </label>
                        <input type="number" id="quantite" name="quantite" value="{{ old('quantite', 1) }}" min="1" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        @error('quantite')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- État -->
                    <div>
                        <label for="etat" class="block text-sm font-medium text-gray-700 mb-1">
                            État *
                        </label>
                        <select id="etat" name="etat" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Sélectionnez un état</option>
                            <option value="Bon" {{ old('etat') == 'Bon' ? 'selected' : '' }}>Bon</option>
                            <option value="Moyen" {{ old('etat') == 'Moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="Mauvais" {{ old('etat') == 'Mauvais' ? 'selected' : '' }}>Mauvais</option>
                        </select>
                        @error('etat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date d'acquisition -->
                    <div>
                        <label for="date_acquisition" class="block text-sm font-medium text-gray-700 mb-1">
                            Date d'acquisition
                        </label>
                        <input type="date" id="date_acquisition" name="date_acquisition" value="{{ old('date_acquisition') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        @error('date_acquisition')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Coût unitaire -->
                    <div>
                        <label for="cout" class="block text-sm font-medium text-gray-700 mb-1">
                            Coût unitaire (FCFA)
                        </label>
                        <input type="number" id="cout" name="cout" value="{{ old('cout') }}" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        @error('cout')
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
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Images -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Images (max 3)
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none">
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
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 border-t border-gray-200 pt-6">
                    <a href="{{ route('equipements.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md flex items-center transition">
                        <i class="fas fa-times mr-2"></i> Annuler
                    </a>
                    <button type="submit" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md flex items-center transition">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prévisualisation des images
        const imageInput = document.getElementById('images');
        const imagePreview = document.getElementById('imagePreview');
        
        imageInput.addEventListener('change', function() {
            imagePreview.innerHTML = '';
            
            if (this.files && this.files.length > 0) {
                const files = Array.from(this.files).slice(0, 3); // Limite à 3 fichiers
                
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

        // Calcul du coût total lorsque le coût unitaire ou la quantité change
        const quantiteInput = document.getElementById('quantite');
        const coutInput = document.getElementById('cout');
        const coutTotalDisplay = document.createElement('p');
        coutTotalDisplay.className = 'text-sm text-gray-500 mt-1';
        coutTotalDisplay.id = 'cout-total';
        coutInput.parentNode.appendChild(coutTotalDisplay);

        function updateCoutTotal() {
            const quantite = parseInt(quantiteInput.value) || 0;
            const cout = parseFloat(coutInput.value) || 0;
            const coutTotal = quantite * cout;
            
            if (coutTotal > 0) {
                coutTotalDisplay.textContent = 'Coût total: ' + coutTotal.toLocaleString('fr-FR') + ' FCFA';
            } else {
                coutTotalDisplay.textContent = '';
            }
        }

        quantiteInput.addEventListener('input', updateCoutTotal);
        coutInput.addEventListener('input', updateCoutTotal);
    });
</script>

<style>
    /* Styles personnalisés */
    .border-dashed {
        border-style: dashed;
    }
    #imagePreview img {
        transition: all 0.3s ease;
    }
    #imagePreview img:hover {
        transform: scale(1.05);
    }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
    .focus\:ring-purple-500:focus {
        --tw-ring-color: rgba(139, 92, 246, 0.5);
    }
    .focus\:border-purple-500:focus {
        border-color: #8b5cf6;
    }
</style>
@endsection