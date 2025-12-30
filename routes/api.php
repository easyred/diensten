<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WaRuntimeController;
use App\Http\Controllers\AddressController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// WhatsApp webhook endpoint (public, no auth)
Route::post('/wa/incoming', [WaRuntimeController::class, 'incoming']);

// Site Configuration API (for VPS script)
Route::get('/categories/{code}/site-config', [\App\Http\Controllers\Api\SiteConfigController::class, 'show']);
Route::get('/categories/site-configs', [\App\Http\Controllers\Api\SiteConfigController::class, 'index']);

// Address search routes (from plumber)
Route::get('/address/search-vlaanderen', function (Request $request) {
    $q = $request->get('q', '');
    if (empty($q)) {
        return response()->json([]);
    }

    $c = $request->get('c', 10);
    $url = 'https://geo.api.vlaanderen.be/geolocation/v4/Suggestion?q=' . urlencode($q) . '&c=' . $c;
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: DienstenPro/1.0 (+https://diensten.pro/)'
        ],
    ]);
    
    $res = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err || $code >= 400 || $res === false) {
        return response()->json(['error' => $err ?: "HTTP $code"], $code ?: 502);
    }
    
    return response()->json(json_decode($res, true));
});

Route::get('/address/search-vlaanderen-location', function (Request $request) {
    $q = $request->get('q', '');
    if (empty($q)) {
        return response()->json([]);
    }

    $url = 'https://geo.api.vlaanderen.be/geolocation/v4/Location?q=' . urlencode($q);
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: DienstenPro/1.0 (+https://diensten.pro/)'
        ],
    ]);
    
    $res = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err || $code >= 400 || $res === false) {
        return response()->json(['error' => $err ?: "HTTP $code"], $code ?: 502);
    }
    
    return response()->json(json_decode($res, true));
});

Route::get('/address/search-osm', function (Request $request) {
    $q = $request->get('q', '');
    if (empty($q)) {
        return response()->json([]);
    }

    $url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=10&countrycodes=be&addressdetails=1&accept-language=nl&q=' . urlencode($q);
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: DienstenPro/1.0 (+https://diensten.pro/)'
        ],
    ]);
    
    $res = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err || $code >= 400 || $res === false) {
        return response()->json(['error' => $err ?: "HTTP $code"], $code ?: 502);
    }
    
    return response()->json(json_decode($res, true));
});

Route::get('/address/search', function (Request $request) {
    $q = $request->get('q', '');
    if (empty($q)) {
        return response()->json([]);
    }

    // Normalize query for Vlaanderen API
    function normalizeForVL($qRaw) {
        $q = trim($qRaw);
        $q = preg_replace('/\s+/', ' ', $q);
        
        // Move number to end of street name
        if (preg_match('/^(\d+)\s+(.+)/i', $q, $m)) {
            $q = $m[2] . ' ' . $m[1];
        }

        // Add comma before city names
        $cities = ['brugge','brussel','antwerpen','gent','leuven','mechelen','kortrijk','hasselt','oostende','roeselare','aalst','genk','turnhout','lier','waregem','dilbeek','asse','zaventem','knokke','deinze','oudenaarde','eeklo','blankenberge','tienen','wetteren','dendermonde','beerse'];
        foreach ($cities as $c) {
            $idx = stripos($q, ' ' . $c);
            if ($idx > 0 && !strpos($q, ',')) {
                $q = substr($q, 0, $idx) . ', ' . substr($q, $idx + 1);
                break;
            }
        }

        // Capitalize first letter of each word
        $q = preg_replace_callback('/\b([a-zà-ÿ])/u', function($m) {
            return mb_strtoupper($m[1]);
        }, $q);
        
        return $q;
    }

    $normalizedQ = normalizeForVL($q);
    
    // Try Vlaanderen Suggestion first
    try {
        $vlUrl = 'https://geo.api.vlaanderen.be/geolocation/v4/Suggestion?q=' . urlencode($normalizedQ) . '&c=10';
        $ch = curl_init($vlUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: DienstenPro/1.0 (+https://diensten.pro/)'
            ],
        ]);
        
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($res !== false && $code < 400) {
            $vlResults = json_decode($res, true);
            if (is_array($vlResults) && !empty($vlResults)) {
                return response()->json(['data' => $vlResults, 'source' => 'vl']);
            }
        }
    } catch (Exception $e) {
        // Continue to fallback
    }

    // Try Vlaanderen Location as fallback
    try {
        $vlLocUrl = 'https://geo.api.vlaanderen.be/geolocation/v4/Location?q=' . urlencode($normalizedQ);
        $ch = curl_init($vlLocUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: DienstenPro/1.0 (+https://diensten.pro/)'
            ],
        ]);
        
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($res !== false && $code < 400) {
            $vlLocResults = json_decode($res, true);
            if (is_array($vlLocResults) && !empty($vlLocResults)) {
                // Convert to suggestion format
                $mapped = array_map(function($x) {
                    $location = $x['Location'] ?? [];
                    $label = '';
                    if (!empty($location)) {
                        $parts = [];
                        $line1 = trim(($location['Thoroughfarename'] ?? '') . ' ' . ($location['Housenumber'] ?? ''));
                        $line2 = trim(($location['Postalcode'] ?? '') . ' ' . ($location['Municipality'] ?? ''));
                        if ($line1) $parts[] = $line1;
                        if ($line2) $parts[] = $line2;
                        $label = implode(', ', $parts);
                    }
                    return [
                        'Suggestion' => ['Label' => $label],
                        '_vlLoc' => $x
                    ];
                }, $vlLocResults);
                return response()->json(['data' => $mapped, 'source' => 'vl']);
            }
        }
    } catch (Exception $e) {
        // Continue to OSM fallback
    }

    // Final fallback to OSM
    try {
        $osmUrl = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=10&countrycodes=be&addressdetails=1&accept-language=nl&q=' . urlencode($q);
        $ch = curl_init($osmUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: DienstenPro/1.0 (+https://diensten.pro/)'
            ],
        ]);
        
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($res !== false && $code < 400) {
            $osmResults = json_decode($res, true);
            return response()->json(['data' => $osmResults, 'source' => 'osm']);
        }
    } catch (Exception $e) {
        // Return empty array if all fail
    }

    return response()->json(['data' => [], 'source' => 'none']);
});

