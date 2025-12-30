<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\ServiceProviderCoverage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoverageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);

        // current selections
        $coverages = ServiceProviderCoverage::where('user_id', $user->id)
            ->orderBy('hoofdgemeente')
            ->orderBy('city')
            ->get();

        // Calculate coverage counts based on type
        $counts = [];
        foreach ($coverages as $coverage) {
            if ($coverage->coverage_type === 'municipality') {
                // For municipality coverage, count all towns in that municipality
                $counts[$coverage->hoofdgemeente] = DB::table('postal_codes')
                    ->select(DB::raw('COUNT(DISTINCT CONCAT(Postcode,"|",Plaatsnaam_NL)) as towns_count'))
                    ->where('Hoofdgemeente', $coverage->hoofdgemeente)
                    ->whereNotNull('Hoofdgemeente')
                    ->value('towns_count');
            } else {
                // For city coverage, count only 1 (the specific city)
                $displayKey = $coverage->hoofdgemeente . ' - ' . $coverage->city;
                $counts[$displayKey] = 1;
            }
        }

        return view('service-provider.coverage.index', compact('coverages', 'counts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);

        $data = $request->validate([
            'hoofdgemeente' => 'required|string|max:255',
        ]);

        // validate it exists in postal_codes
        $exists = DB::table('postal_codes')
            ->where('Hoofdgemeente', $data['hoofdgemeente'])
            ->exists();

        if (! $exists) {
            return back()->with('error', 'Municipality not found.');
        }

        ServiceProviderCoverage::firstOrCreate([
            'user_id'    => $user->id,
            'hoofdgemeente' => $data['hoofdgemeente'],
        ]);

        return back()->with('success', 'Coverage added.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);

        $coverage = ServiceProviderCoverage::where('user_id', $user->id)->findOrFail($id);
        $coverage->delete();

        return back()->with('success', 'Coverage removed.');
    }

    // AJAX: search distinct municipalities
    public function searchMunicipalities(Request $request)
    {
        $term = trim($request->query('term', ''));
        $q = DB::table('postal_codes')
            ->select('Hoofdgemeente')
            ->whereNotNull('Hoofdgemeente');

        if ($term !== '') {
            // First try to find municipality by city name (case-insensitive)
            // Convert term to uppercase to match database format
            $termUpper = strtoupper(trim($term));
            $cityMatch = DB::table('postal_codes')
                ->where('Plaatsnaam_NL', $termUpper)
                ->whereNotNull('Hoofdgemeente')
                ->value('Hoofdgemeente');
            
            if ($cityMatch) {
                // If found by city name, return that municipality
                return response()->json([$cityMatch]);
            }
            
            // If not found by city name, try to find by postal code if term looks like a postal code
            if (preg_match('/^\d{4}$/', $term)) {
                $postalMatch = DB::table('postal_codes')
                    ->where('Postcode', $term)
                    ->whereNotNull('Hoofdgemeente')
                    ->value('Hoofdgemeente');
                
                if ($postalMatch) {
                    return response()->json([$postalMatch]);
                }
            }
            
            // If user is logged in and city matches, try postal code as fallback
            $user = Auth::user();
            $postalCode = $request->query('postal_code');
            
            // Use provided postal code or user's postal code
            if (!$postalCode && $user && $user->city && strtoupper(trim($user->city)) === $termUpper) {
                $postalCode = $user->postal_code;
            }
            
            if ($postalCode) {
                $postalMatch = DB::table('postal_codes')
                    ->where('Postcode', $postalCode)
                    ->whereNotNull('Hoofdgemeente')
                    ->value('Hoofdgemeente');
                
                if ($postalMatch) {
                    return response()->json([$postalMatch]);
                }
            }
            
            // Otherwise, search by municipality name
            $q->where('Hoofdgemeente', 'LIKE', "%{$term}%");
        }

        $items = $q->groupBy('Hoofdgemeente')
            ->orderBy('Hoofdgemeente')
            ->limit(20)
            ->pluck('Hoofdgemeente');

        return response()->json($items);
    }

    // AJAX: list towns/postcodes under a municipality (preview)
    public function municipalityTowns($name)
    {
        $user = Auth::user();
        $userCity = $user ? $user->city : null;
        
        $rows = DB::table('postal_codes')
            ->select('Postcode', 'Plaatsnaam_NL')
            ->where('Hoofdgemeente', $name)
            ->whereNotNull('Postcode')
            ->whereNotNull('Plaatsnaam_NL')
            ->groupBy('Postcode', 'Plaatsnaam_NL')
            ->orderBy('Postcode')
            ->get();

        // Calculate distances if user has a city
        if ($userCity) {
            $userCoords = DB::table('postal_codes')
                ->select('Latitude', 'Longitude')
                ->where('Plaatsnaam_NL', $userCity)
                ->whereNotNull('Latitude')
                ->whereNotNull('Longitude')
                ->first();

            if ($userCoords) {
                foreach ($rows as $row) {
                    if ($row->Plaatsnaam_NL === $userCity) {
                        $row->distance = 0;
                    } else {
                        // Get coordinates for this specific city
                        $cityCoords = DB::table('postal_codes')
                            ->select('Latitude', 'Longitude')
                            ->where('Plaatsnaam_NL', $row->Plaatsnaam_NL)
                            ->whereNotNull('Latitude')
                            ->whereNotNull('Longitude')
                            ->first();

                        if ($cityCoords) {
                            $distance = DB::selectOne('
                                SELECT (6371 * acos(cos(radians(?)) * cos(radians(?)) * 
                                cos(radians(?) - radians(?)) + sin(radians(?)) * 
                                sin(radians(?)))) AS distance
                            ', [
                                $userCoords->Latitude, $cityCoords->Latitude,
                                $cityCoords->Longitude, $userCoords->Longitude,
                                $userCoords->Latitude, $cityCoords->Latitude
                            ]);
                            $row->distance = $distance->distance ?? 0;
                        } else {
                            $row->distance = 0;
                        }
                    }
                }
            } else {
                // User city not found, set all distances to 0
                foreach ($rows as $row) {
                    $row->distance = 0;
                }
            }
        } else {
            // No user city, set all distances to 0
            foreach ($rows as $row) {
                $row->distance = 0;
            }
        }

        return response()->json($rows);
    }

    // AJAX: calculate distance between two cities
    public function calculateDistance(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        if (!$from || !$to) {
            return response()->json(['distance' => 0]);
        }

        // Get coordinates for both cities
        $fromCoords = DB::table('postal_codes')
            ->select('Latitude', 'Longitude')
            ->where('Plaatsnaam_NL', $from)
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude')
            ->first();

        $toCoords = DB::table('postal_codes')
            ->select('Latitude', 'Longitude')
            ->where('Plaatsnaam_NL', $to)
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude')
            ->first();

        if (!$fromCoords || !$toCoords) {
            return response()->json(['distance' => 0]);
        }

        // Calculate distance using Haversine formula
        $distance = DB::selectOne('
            SELECT (6371 * acos(cos(radians(?)) * cos(radians(?)) * 
            cos(radians(?) - radians(?)) + sin(radians(?)) * 
            sin(radians(?)))) AS distance
        ', [
            $fromCoords->Latitude, $toCoords->Latitude,
            $toCoords->Longitude, $fromCoords->Longitude,
            $fromCoords->Latitude, $toCoords->Latitude
        ]);

        return response()->json(['distance' => $distance->distance ?? 0]);
    }

    // AJAX: calculate distances from user's city to multiple cities
    public function calculateDistances(Request $request)
    {
        $from = $request->query('from');
        $cities = $request->query('cities', []);

        if (!$from || empty($cities)) {
            return response()->json([]);
        }

        // Get coordinates for the source city
        $fromCoords = DB::table('postal_codes')
            ->select('Latitude', 'Longitude')
            ->where('Plaatsnaam_NL', $from)
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude')
            ->first();

        if (!$fromCoords) {
            return response()->json([]);
        }

        $distances = [];
        
        // Calculate distances for each city
        foreach ($cities as $city) {
            if ($city === $from) {
                $distances[$city] = 0;
                continue;
            }

            $toCoords = DB::table('postal_codes')
                ->select('Latitude', 'Longitude')
                ->where('Plaatsnaam_NL', $city)
                ->whereNotNull('Latitude')
                ->whereNotNull('Longitude')
                ->first();

            if ($toCoords) {
                $distance = DB::selectOne('
                    SELECT (6371 * acos(cos(radians(?)) * cos(radians(?)) * 
                    cos(radians(?) - radians(?)) + sin(radians(?)) * 
                    sin(radians(?)))) AS distance
                ', [
                    $fromCoords->Latitude, $toCoords->Latitude,
                    $toCoords->Longitude, $fromCoords->Longitude,
                    $fromCoords->Latitude, $toCoords->Latitude
                ]);

                $distances[$city] = $distance->distance ?? 0;
            } else {
                $distances[$city] = 0;
            }
        }

        return response()->json($distances);
    }

    // AJAX: find nearby municipalities within specified radius
    public function nearbyMunicipalities(Request $request)
    {
        $municipality = $request->query('municipality');
        $radius = $request->query('radius', 10); // Default 10km radius

        if (!$municipality) {
            return response()->json([]);
        }

        // Get the center coordinates of the selected municipality
        $center = DB::table('postal_codes')
            ->select('Latitude', 'Longitude')
            ->where('Hoofdgemeente', $municipality)
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude')
            ->first();

        if (!$center) {
            return response()->json([]);
        }

        // First, add the user's own municipality at the top (distance = 0)
        $userMunicipality = [
            'Hoofdgemeente' => $municipality,
            'Latitude' => $center->Latitude,
            'Longitude' => $center->Longitude,
            'distance' => 0.0
        ];

        // Find nearby municipalities using Haversine formula
        $nearby = DB::table('postal_codes')
            ->select('Hoofdgemeente', 'Latitude', 'Longitude')
            ->selectRaw('
                (6371 * acos(cos(radians(?)) * cos(radians(Latitude)) * 
                cos(radians(Longitude) - radians(?)) + sin(radians(?)) * 
                sin(radians(Latitude)))) AS distance
            ', [$center->Latitude, $center->Longitude, $center->Latitude])
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude')
            ->where('Hoofdgemeente', '!=', $municipality)
            ->having('distance', '<=', $radius)
            ->groupBy('Hoofdgemeente', 'Latitude', 'Longitude')
            ->orderBy('distance')
            ->limit(10)
            ->get();

        // Combine user's municipality (at top) with nearby municipalities
        $allMunicipalities = collect([$userMunicipality])->merge($nearby);

        return response()->json($allMunicipalities);
    }

    // Auto-add 10km nearby areas based on user's city
    public function autoAddNearby(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);

        if (!$user->city) {
            return response()->json([
                'success' => false,
                'message' => 'Please set your city in your profile first.'
            ], 400);
        }

        // Find the user's municipality based on their city (case-insensitive)
        // Try exact match first, then LIKE for variations, then by postal code
        $cityUpper = strtoupper(trim($user->city));
        $userMunicipality = DB::table('postal_codes')
            ->where('Plaatsnaam_NL', $cityUpper)
            ->whereNotNull('Hoofdgemeente')
            ->value('Hoofdgemeente');
        
        // If not found, try LIKE search (handles spelling variations)
        if (!$userMunicipality) {
            $userMunicipality = DB::table('postal_codes')
                ->where('Plaatsnaam_NL', 'LIKE', $cityUpper)
                ->whereNotNull('Hoofdgemeente')
                ->value('Hoofdgemeente');
        }
        
        // If still not found, try matching by postal code if user has one
        if (!$userMunicipality && $user->postal_code) {
            $userMunicipality = DB::table('postal_codes')
                ->where('Postcode', $user->postal_code)
                ->whereNotNull('Hoofdgemeente')
                ->value('Hoofdgemeente');
        }

        if (!$userMunicipality) {
            return response()->json([
                'success' => false,
                'message' => 'Could not find municipality for your city. Please contact support.'
            ], 400);
        }

        // Get center coordinates of user's municipality
        $center = DB::table('postal_codes')
            ->select('Latitude', 'Longitude')
            ->where('Hoofdgemeente', $userMunicipality)
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude')
            ->first();

        if (!$center) {
            return response()->json([
                'success' => false,
                'message' => 'Could not find coordinates for your municipality.'
            ], 400);
        }

        // Find nearby municipalities within 10km
        $nearby = DB::select("
            SELECT DISTINCT Hoofdgemeente,
                (6371 * acos(cos(radians(?)) * cos(radians(Latitude)) * 
                cos(radians(Longitude) - radians(?)) + sin(radians(?)) * 
                sin(radians(Latitude)))) AS distance
            FROM postal_codes
            WHERE Latitude IS NOT NULL 
                AND Longitude IS NOT NULL 
                AND Hoofdgemeente != ?
            HAVING distance <= 10
            ORDER BY distance ASC
            LIMIT 20
        ", [$center->Latitude, $center->Longitude, $center->Latitude, $userMunicipality]);
        
        $nearbyMunicipalities = collect($nearby)->pluck('Hoofdgemeente');

        if ($nearby->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No nearby municipalities found within 10km of your location.'
            ], 400);
        }

        // Add municipalities to coverage (avoid duplicates)
        $added = 0;
        $existing = ServiceProviderCoverage::where('user_id', $user->id)
            ->pluck('hoofdgemeente')
            ->toArray();

        foreach ($nearbyMunicipalities as $municipality) {
            if (!in_array($municipality, $existing)) {
                ServiceProviderCoverage::create([
                    'user_id' => $user->id,
                    'hoofdgemeente' => $municipality,
                    'coverage_type' => 'municipality'
                ]);
                $added++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully added {$added} nearby municipalities within 10km of your location.",
            'added_count' => $added
        ]);
    }

    // AJAX: bulk add multiple municipalities and cities
    public function bulkStore(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['plumber', 'gardener']), 403);

        $data = $request->validate([
            'municipalities' => 'sometimes|array',
            'municipalities.*' => 'string|max:255',
            'cities' => 'sometimes|array',
            'cities.*' => 'string|max:255',
        ]);

        $added = 0;
        $errors = [];
        $processedMunicipalities = []; // Track to avoid duplicates

        // Process municipalities
        $municipalities = $data['municipalities'] ?? [];
        foreach ($municipalities as $item) {
            // First, check if this is a municipality (Hoofdgemeente)
            $municipalityExists = DB::table('postal_codes')
                ->where('Hoofdgemeente', $item)
                ->exists();

            if ($municipalityExists) {
                // This is a municipality, add entire municipality
                $municipality = $item;
                $coverageType = 'municipality';
                $city = null;
                
                // Check if municipality already exists in coverage
                $alreadyExists = ServiceProviderCoverage::where('user_id', $user->id)
                    ->where('hoofdgemeente', $municipality)
                    ->where('coverage_type', 'municipality')
                    ->exists();

                if ($alreadyExists) {
                    $errors[] = "Municipality '{$municipality}' already added.";
                    continue;
                }

                // Skip if we already processed this municipality
                if (in_array($municipality, $processedMunicipalities)) {
                    continue;
                }

                ServiceProviderCoverage::create([
                    'user_id' => $user->id,
                    'hoofdgemeente' => $municipality,
                    'city' => null,
                    'coverage_type' => 'municipality',
                ]);

                $processedMunicipalities[] = $municipality;
                $added++;
                
            } else {
                // This might be a city, find its parent municipality
                $cityData = DB::table('postal_codes')
                    ->select('Hoofdgemeente', 'Plaatsnaam_NL')
                    ->where('Plaatsnaam_NL', $item)
                    ->first();

                if (!$cityData) {
                    $errors[] = "Item '{$item}' not found as municipality or city.";
                    continue;
                }

                $municipality = $cityData->Hoofdgemeente;
                $city = $cityData->Plaatsnaam_NL;
                
                // Check if this specific city already exists in coverage
                $alreadyExists = ServiceProviderCoverage::where('user_id', $user->id)
                    ->where('hoofdgemeente', $municipality)
                    ->where('city', $city)
                    ->where('coverage_type', 'city')
                    ->exists();

                if ($alreadyExists) {
                    $errors[] = "City '{$city}' already added.";
                    continue;
                }
                
                // Check if the entire municipality is already covered
                $municipalityCovered = ServiceProviderCoverage::where('user_id', $user->id)
                    ->where('hoofdgemeente', $municipality)
                    ->where('coverage_type', 'municipality')
                    ->exists();

                if ($municipalityCovered) {
                    $errors[] = "Municipality '{$municipality}' already fully covered.";
                    continue;
                }

                ServiceProviderCoverage::create([
                    'user_id' => $user->id,
                    'hoofdgemeente' => $municipality,
                    'city' => $city,
                    'coverage_type' => 'city',
                ]);

                $added++;
            }
        }

        // Process cities
        $cities = $data['cities'] ?? [];
        foreach ($cities as $cityName) {
            // Find the parent municipality for this city
            $cityData = DB::table('postal_codes')
                ->select('Hoofdgemeente', 'Plaatsnaam_NL')
                ->where('Plaatsnaam_NL', $cityName)
                ->first();

            if (!$cityData) {
                $errors[] = "City '{$cityName}' not found.";
                continue;
            }

            $municipality = $cityData->Hoofdgemeente;
            $city = $cityData->Plaatsnaam_NL;
            
            // Check if this specific city already exists in coverage
            $alreadyExists = ServiceProviderCoverage::where('user_id', $user->id)
                ->where('hoofdgemeente', $municipality)
                ->where('city', $city)
                ->where('coverage_type', 'city')
                ->exists();

            if ($alreadyExists) {
                $errors[] = "City '{$city}' already added.";
                continue;
            }
            
            // Check if the entire municipality is already covered
            $municipalityCovered = ServiceProviderCoverage::where('user_id', $user->id)
                ->where('hoofdgemeente', $municipality)
                ->where('coverage_type', 'municipality')
                ->exists();

            if ($municipalityCovered) {
                $errors[] = "Municipality '{$municipality}' already fully covered.";
                continue;
            }

            ServiceProviderCoverage::create([
                'user_id' => $user->id,
                'hoofdgemeente' => $municipality,
                'city' => $city,
                'coverage_type' => 'city',
            ]);

            $added++;
        }

        $response = [
            'success' => true,
            'added' => $added,
            'errors' => $errors,
            'message' => '',
        ];

        if ($added > 0) {
            $response['message'] = "Successfully added {$added} coverage area(s).";
        } else {
            $response['message'] = "No new coverage areas were added.";
        }

        if (!empty($errors)) {
            $response['message'] .= ' ' . implode(' ', $errors);
        }

        return response()->json($response);
    }
}
