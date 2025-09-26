<?php

namespace App\Http\Controllers;

use App\Models\ServiceSocial;
use App\Models\ServiceCategory;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceSocialController extends Controller
{
    /**
     * Affiche la liste des services
     */
    public function index(Request $request)
    {
        // Récupération des paramètres de filtrage
        $search = $request->input('search');
        $category = $request->input('category');
        $status = $request->input('status');
        $district = $request->input('district');

        // Construction de la requête avec les filtres
        $query = ServiceSocial::with(['category', 'district'])
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('type', 'like', '%'.$search.'%');
            })
            ->when($category, function ($query) use ($category) {
                return $query->where('category_id', $category);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($district, function ($query) use ($district) {
                return $query->where('district_id', $district);
            });

        // Tri des résultats si spécifié
        if ($request->has('sort')) {
            $query->orderBy($request->sort, $request->direction ?? 'asc');
        }

        // Calcul des statistiques de demande (exemple simplifié)
        $query->withCount(['requests as recent_requests_count' => function($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        }])
        ->withCount(['requests as completed_requests_count' => function($q) {
            $q->where('status', 'completed');
        }]);

        // Pagination des résultats
        $services = $query->paginate(10)->withQueryString();

        // Calcul du taux de complétion pour chaque service
        $services->each(function ($service) {
            $service->request_completion_rate = $service->recent_requests_count > 0 
                ? round(($service->completed_requests_count / $service->recent_requests_count) * 100)
                : 0;
        });

        // Récupération des données pour les filtres
        $categories = ServiceSocial::orderBy('name')->get();
        // $districts = District::orderBy('name')->get();

        return view('services.index', [
            'services' => $services,
            'categories' => $categories,
            // 'districts' => $districts,
        ]);
    }

    /**
     * Affiche le formulaire de création d'un service
     */
    public function create()
    {
        $categories = ServiceSocial::orderBy('name')->get();
        // $districts = District::orderBy('name')->get();
        $statuses = ['active', 'inactive', 'maintenance'];

        return view('services.create', compact('categories', 'districts', 'statuses'));
    }

    /**
     * Enregistre un nouveau service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'category_id' => 'required|exists:service_categories,id',
            'district_id' => 'required|exists:districts,id',
            'status' => 'required|in:active,inactive,maintenance',
            'manager' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:255',
            'schedule' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de l'upload de la photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('services', 'public');
        }

        ServiceSocial::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service créé avec succès.');
    }

    /**
     * Affiche les détails d'un service
     */
    public function show(ServiceSocial $service)
    {
        $service->load(['category', 'district', 'requests']);
        
        // Calcul des statistiques
        $recentRequests = $service->requests()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        $completedRequests = $service->requests()
            ->where('status', 'completed')
            ->count();
            
        $completionRate = $recentRequests > 0 
            ? round(($completedRequests / $recentRequests) * 100)
            : 0;

        return view('services.show', compact('service', 'recentRequests', 'completionRate'));
    }

    /**
     * Affiche le formulaire d'édition d'un service
     */
    public function edit(ServiceSocial $service)
    {
        $categories = ServiceSocial::orderBy('name')->get();
        // $districts = District::orderBy('name')->get();
        $statuses = ['active', 'inactive', 'maintenance'];

        return view('services.edit', compact('service', 'categories', 'districts', 'statuses'));
    }

    /**
     * Met à jour un service
     */
    public function update(Request $request, ServiceSocial $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'category_id' => 'required|exists:service_categories,id',
            'district_id' => 'required|exists:districts,id',
            'status' => 'required|in:active,inactive,maintenance',
            'manager' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:255',
            'schedule' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_photo' => 'nullable|boolean',
        ]);

        // Gestion de la photo
        if ($request->has('remove_photo')) {
            // Suppression de la photo existante
            if ($service->photo) {
                Storage::disk('public')->delete($service->photo);
                $validated['photo'] = null;
            }
        } elseif ($request->hasFile('photo')) {
            // Upload de la nouvelle photo
            if ($service->photo) {
                Storage::disk('public')->delete($service->photo);
            }
            $validated['photo'] = $request->file('photo')->store('services', 'public');
        } else {
            // Conserver la photo existante
            unset($validated['photo']);
        }

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * Supprime un service
     */
    public function destroy(ServiceSocial $service)
    {
        // Suppression de la photo si elle existe
        if ($service->photo) {
            Storage::disk('public')->delete($service->photo);
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service supprimé avec succès.');
    }
}