@extends('layouts.app')

@section('title', 'Modifier le Service Social')

@section('content')
<div class="service-form-container">
    <div class="form-header">
        <h1><i class="fas fa-edit"></i> Modifier le Service: {{ $service->name }}</h1>
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <form method="POST" action="{{ route('services.update', $service->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-card">
            <h2 class="form-section-title"><i class="fas fa-info-circle"></i> Informations de Base</h2>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Nom du Service*</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $service->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-6">
                    <label for="category_id">Catégorie*</label>
                    <select class="form-control @error('category_id') is-invalid @enderror" 
                            id="category_id" name="category_id" required>
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="type">Type de Service*</label>
                    <input type="text" class="form-control @error('type') is-invalid @enderror" 
                           id="type" name="type" value="{{ old('type', $service->type) }}" required>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-6">
                    <label for="status">Statut*</label>
                    <select class="form-control @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        <option value="maintenance" {{ old('status', $service->status) == 'maintenance' ? 'selected' : '' }}>En Maintenance</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description du Service</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="form-card">
            <h2 class="form-section-title"><i class="fas fa-map-marker-alt"></i> Localisation</h2>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="district_id">Quartier*</label>
                    <select class="form-control @error('district_id') is-invalid @enderror" 
                            id="district_id" name="district_id" required>
                        <option value="">Sélectionner un quartier</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ old('district_id', $service->district_id) == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('district_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-6">
                    <label for="address">Adresse*</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                           id="address" name="address" value="{{ old('address', $service->address) }}" required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="latitude">Latitude</label>
                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                           id="latitude" name="latitude" value="{{ old('latitude', $service->latitude) }}">
                    @error('latitude')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-4">
                    <label for="longitude">Longitude</label>
                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                           id="longitude" name="longitude" value="{{ old('longitude', $service->longitude) }}">
                    @error('longitude')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-4">
                    <label for="map" class="d-block">Localisation sur la carte</label>
                    <button type="button" class="btn btn-outline-primary" id="openMapModal">
                        <i class="fas fa-map-marked-alt"></i> Sélectionner sur la carte
                    </button>
                </div>
            </div>
            
            @if($service->latitude && $service->longitude)
            <div class="form-group">
                <div id="previewMap" style="height: 200px;"></div>
            </div>
            @endif
        </div>
        
        <div class="form-card">
            <h2 class="form-section-title"><i class="fas fa-user-tie"></i> Responsable</h2>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="manager">Nom du Responsable</label>
                    <input type="text" class="form-control @error('manager') is-invalid @enderror" 
                           id="manager" name="manager" value="{{ old('manager', $service->manager) }}">
                    @error('manager')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-6">
                    <label for="phone">Téléphone</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone', $service->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email', $service->email) }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="form-card">
            <h2 class="form-section-title"><i class="fas fa-clock"></i> Horaires & Capacité</h2>
            
            <div class="form-group">
                <label for="schedule">Horaires d'Ouverture</label>
                <textarea class="form-control @error('schedule') is-invalid @enderror" 
                          id="schedule" name="schedule" rows="2">{{ old('schedule', $service->schedule) }}</textarea>
                @error('schedule')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="capacity">Capacité (nombre de bénéficiaires)</label>
                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                           id="capacity" name="capacity" value="{{ old('capacity', $service->capacity) }}">
                    @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-6">
                    <label for="photo">Photo du Service</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" 
                               id="photo" name="photo">
                        <label class="custom-file-label" for="photo">
                            {{ $service->photo ? 'Changer la photo actuelle' : 'Choisir un fichier' }}
                        </label>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if($service->photo)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$service->photo) }}" alt="Photo du service" class="img-thumbnail" style="max-height: 100px;">
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="remove_photo" name="remove_photo">
                            <label class="form-check-label" for="remove_photo">Supprimer la photo actuelle</label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-undo"></i> Réinitialiser
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Mettre à jour
            </button>
        </div>
    </form>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Sélectionner l'emplacement sur la carte</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="locationMap" style="height: 500px;"></div>
                <div class="mt-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="modalLatitude">Latitude</label>
                            <input type="text" class="form-control" id="modalLatitude" 
                                   value="{{ $service->latitude }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="modalLongitude">Longitude</label>
                            <input type="text" class="form-control" id="modalLongitude" 
                                   value="{{ $service->longitude }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmLocation">Confirmer l'emplacement</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .service-form-container {
        padding: 20px;
        background-color: #f5f7fa;
        min-height: 100vh;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .form-header h1 {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.8rem;
        margin: 0;
    }

    .form-header h1 i {
        margin-right: 10px;
        color: #3498db;
    }

    .form-card {
        background: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .form-section-title {
        font-size: 1.2rem;
        margin: 0 0 20px 0;
        color: #2c3e50;
        display: flex;
        align-items: center;
    }

    .form-section-title i {
        margin-right: 10px;
        color: #3498db;
    }

    .form-group label {
        font-weight: 500;
        color: #495057;
    }

    .form-control {
        border-radius: 4px;
    }

    .custom-file-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px 0;
    }

    #locationMap, #previewMap {
        border-radius: 8px;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .form-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .form-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize preview map if coordinates exist
        @if($service->latitude && $service->longitude)
        const previewMap = L.map('previewMap').setView([{{ $service->latitude }}, {{ $service->longitude }}], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(previewMap);
        
        L.marker([{{ $service->latitude }}, {{ $service->longitude }}])
            .addTo(previewMap)
            .bindPopup("<b>{{ $service->name }}</b>");
        @endif
        
        // Initialize map modal
        $('#openMapModal').click(function() {
            $('#mapModal').modal('show');
            
            // Initialize map if not already initialized
            if (typeof map === 'undefined') {
                const defaultLat = {{ $service->latitude ?? $commune->latitude ?? 14.764504 }};
                const defaultLng = {{ $service->longitude ?? $commune->longitude ?? -17.366029 }};
                
                map = L.map('locationMap').setView([defaultLat, defaultLng], 13);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                // Add marker
                const marker = L.marker([defaultLat, defaultLng], {
                    draggable: true
                }).addTo(map);
                
                // Update coordinates when marker is dragged
                marker.on('dragend', function(e) {
                    const position = marker.getLatLng();
                    $('#modalLatitude').val(position.lat);
                    $('#modalLongitude').val(position.lng);
                });
                
                // Update coordinates when clicking on map
                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    $('#modalLatitude').val(e.latlng.lat);
                    $('#modalLongitude').val(e.latlng.lng);
                });
            }
        });
        
        // Confirm location selection
        $('#confirmLocation').click(function() {
            const lat = $('#modalLatitude').val();
            const lng = $('#modalLongitude').val();
            
            if (lat && lng) {
                $('#latitude').val(lat);
                $('#longitude').val(lng);
                $('#mapModal').modal('hide');
            } else {
                alert('Veuillez sélectionner un emplacement sur la carte');
            }
        });
        
        // Update custom file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Choisir un fichier');
        });
    });
</script>
@endpush