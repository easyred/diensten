<?php

namespace App\Services;

use App\Models\WaRequest;
use App\Models\User;
use App\Models\WaSession;
use App\Models\WaFlow;
use App\Models\WaNode;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RequestBroadcastService
{
    const MAX_DISTANCE_KM = 50; // Maximum distance for notifications

    /**
     * Broadcast a request to nearby service providers matching the category
     */
    public function broadcastRequest(WaRequest $request): void
    {
        $customer = $request->customer;
        if (!$customer) {
            return;
        }

        $category = $request->category;
        if (!$category) {
            return;
        }

        // Find matching service providers
        $providers = $this->findMatchingProviders($customer, $category);

        // Send notifications to each provider
        foreach ($providers as $provider) {
            $this->notifyProvider($provider, $customer, $request);
        }
    }

    /**
     * Find service providers matching the category and location
     */
    protected function findMatchingProviders(User $customer, Category $category): \Illuminate\Database\Eloquent\Collection
    {
        $customerCity = $customer->city;
        
        // Base query: providers with matching category
        $query = User::whereHas('categories', function($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->whereIn('role', ['plumber', 'gardener'])
            ->where(function($q) {
                // Active subscription or no subscription required
                // Check subscriptions table for active subscriptions
                $q->whereHas('subscriptions', function($subQ) {
                    $subQ->where('status', 'active')
                         ->where('ends_at', '>', now());
                })
                ->orWhereDoesntHave('subscriptions');
            })
            ->where(function($q) {
                // Not currently working on another job
                $q->whereNotExists(function($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('wa_requests')
                        ->whereColumn('wa_requests.selected_plumber_id', 'users.id')
                        ->whereIn('wa_requests.status', ['active', 'in_progress']);
                });
            });

        // If customer has a city, filter by distance
        if ($customerCity) {
            $query->whereNotNull('city')
                  ->where('city', '!=', '');
        }

        $providers = $query->get();

        // Filter by distance if customer city is available
        if ($customerCity) {
            $providers = $providers->filter(function($provider) use ($customerCity) {
                if (!$provider->city) {
                    return false;
                }
                
                $distance = $this->calculateDistance($customerCity, $provider->city);
                
                // Only include if within max distance
                return $distance !== null && $distance <= self::MAX_DISTANCE_KM;
            })->sortBy(function($provider) use ($customerCity) {
                // Sort by distance (closest first)
                return $this->calculateDistance($customerCity, $provider->city) ?? 999;
            });
        }

        return $providers;
    }

    /**
     * Notify a service provider about a new request
     */
    protected function notifyProvider(User $provider, User $customer, WaRequest $request): void
    {
        if (!$provider->whatsapp_number) {
            return;
        }

        // Calculate distance and ETA
        $distance = null;
        $eta = null;
        if ($customer->city && $provider->city) {
            $distance = $this->calculateDistance($customer->city, $provider->city);
            $eta = $distance ? $this->calculateETA($distance) : null;
        }

        // Delete any existing session for this provider
        WaSession::where('wa_number', $provider->whatsapp_number)->delete();

        // Create new session for provider
        $category = $request->category;
        $flowCode = $category ? "{$category->code}_provider_flow" : 'provider_flow';
        
        // Try to find provider flow for this category
        $flow = WaFlow::where(function($q) use ($category, $provider) {
                $q->where('category_id', $category->id)
                  ->where(function($roleQ) use ($provider) {
                      $roleQ->where('target_role', 'any')
                            ->orWhere('target_role', $provider->role);
                  });
            })
            ->where('is_active', true)
            ->first();

        if (!$flow) {
            // Use default provider flow
            $flow = WaFlow::where('code', $flowCode)
                ->where('is_active', true)
                ->first();
        }

        if (!$flow) {
            // Create a simple notification message
            $this->sendSimpleNotification($provider, $customer, $request, $distance, $eta);
            return;
        }

        // Get first node of the flow
        $node = $flow->nodes()->orderBy('sort')->first();
        if (!$node) {
            $this->sendSimpleNotification($provider, $customer, $request, $distance, $eta);
            return;
        }

        // Create session with request context
        $session = WaSession::create([
            'wa_number' => $provider->whatsapp_number,
            'user_id' => $provider->id,
            'flow_code' => $flow->code,
            'node_code' => $node->code,
            'context_json' => [
                'request_id' => $request->id,
                'customer_id' => $customer->id,
                'customer_name' => explode(' ', $customer->full_name)[0],
                'address' => $customer->address,
                'postal_code' => $customer->postal_code,
                'city' => $customer->city,
                'problem' => $request->problem,
                'problem_type' => $request->problem_type,
                'urgency' => $request->urgency,
                'description' => $request->description,
                'distance_km' => $distance ? (string)$distance : null,
                'eta_min' => $eta ? (string)$eta : null,
                'category_id' => $category->id,
                'category_name' => $category->name,
            ],
            'last_message_at' => now(),
        ]);

        // Send the first node message
        $this->sendNodeMessage($provider->whatsapp_number, $node, $session->context_json);
    }

    /**
     * Send a simple notification if no flow is configured
     */
    protected function sendSimpleNotification(User $provider, User $customer, WaRequest $request, ?float $distance, ?int $eta): void
    {
        $category = $request->category;
        $distanceText = $distance ? " • Afstand: {$distance} km" : "";
        $etaText = $eta ? " • ETA: {$eta} min 🚗" : "";

        $message = "🔔 *Nieuwe aanvraag ontvangen!*\n\n";
        $message .= "Categorie: {$category->name}\n";
        $message .= "Probleem: {$request->problem}";
        if ($request->problem_type) {
            $message .= " ({$request->problem_type})";
        }
        $message .= "\n";
        $message .= "Urgentie: " . ucfirst($request->urgency) . "\n";
        if ($request->description) {
            $message .= "Details: " . substr($request->description, 0, 100) . "\n";
        }
        $message .= "Locatie: {$customer->city}{$distanceText}{$etaText}\n\n";
        $message .= "Typ 'accept' om te accepteren of 'decline' om te weigeren.";

        $this->sendWhatsAppMessage($provider->whatsapp_number, $message);
    }

    /**
     * Send a node message to WhatsApp
     */
    protected function sendNodeMessage(string $waNumber, WaNode $node, array $context): void
    {
        $message = $this->renderNode($node, $context);
        $this->sendWhatsAppMessage($waNumber, $message);
    }

    /**
     * Render a node with variable replacement
     */
    protected function renderNode(WaNode $node, array $context): string
    {
        $lines = [];
        
        $title = $this->replaceVariables($node->title ?? '', $context);
        $body = $this->replaceVariables($node->body ?? '', $context);

        if ($title) $lines[] = $title;
        if ($body) $lines[] = $body;

        if (in_array($node->type, ['buttons', 'list'])) {
            $opts = $node->options_json ?? [];
            if (!empty($opts)) {
                $lines[] = '';
                foreach ($opts as $i => $opt) {
                    $label = $opt['label'] ?? $opt['title'] ?? $opt['text'] ?? ('Option '.($i+1));
                    $cleanLabel = preg_replace('/^\d+[\.\)]\s*/', '', $label);
                    $lines[] = ($i+1).". ".$cleanLabel;
                }
                $lines[] = '';
                $lines[] = "Reply with the number.";
            }
        }

        if ($node->footer) {
            $footer = $this->replaceVariables($node->footer, $context);
            $lines[] = $footer;
        }

        return implode("\n", array_filter($lines, fn($l) => $l !== null && $l !== ''));
    }

    /**
     * Replace variables in text
     */
    protected function replaceVariables($text, $context): string
    {
        if (!$text) return '';
        return preg_replace_callback('/\{\{(\w+)\}\}/', function($matches) use ($context) {
            $varName = $matches[1];
            return $context[$varName] ?? $context['collected'][$varName] ?? $matches[0];
        }, $text);
    }

    /**
     * Send WhatsApp message via Node.js bot
     */
    protected function sendWhatsAppMessage(string $waNumber, string $message): void
    {
        try {
            $botUrl = config('app.whatsapp_bot_url', 'http://127.0.0.1:3000');
            Http::timeout(5)->post("{$botUrl}/send-message", [
                'number' => $waNumber,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp message', [
                'number' => $waNumber,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Calculate distance between two cities using Haversine formula (in km)
     */
    protected function calculateDistance(?string $city1, ?string $city2): ?float
    {
        if (!$city1 || !$city2 || $city1 === $city2) {
            return 0;
        }

        try {
            // Check if postal_codes table exists
            if (!DB::getSchemaBuilder()->hasTable('postal_codes')) {
                return null;
            }

            // Get coordinates for city1
            $coords1 = DB::table('postal_codes')
                ->select('Latitude', 'Longitude')
                ->where('Plaatsnaam_NL', $city1)
                ->whereNotNull('Latitude')
                ->whereNotNull('Longitude')
                ->first();

            // Get coordinates for city2
            $coords2 = DB::table('postal_codes')
                ->select('Latitude', 'Longitude')
                ->where('Plaatsnaam_NL', $city2)
                ->whereNotNull('Latitude')
                ->whereNotNull('Longitude')
                ->first();

            if (!$coords1 || !$coords2) {
                return null; // Can't calculate
            }

            // Haversine formula
            $distance = DB::selectOne('
                SELECT (6371 * acos(cos(radians(?)) * cos(radians(?)) * 
                cos(radians(?) - radians(?)) + sin(radians(?)) * 
                sin(radians(?)))) AS distance
            ', [
                $coords1->Latitude, $coords2->Latitude,
                $coords2->Longitude, $coords1->Longitude,
                $coords1->Latitude, $coords2->Latitude
            ]);

            return round($distance->distance ?? 0, 1);
        } catch (\Exception $e) {
            \Log::warning('Distance calculation failed', [
                'city1' => $city1,
                'city2' => $city2,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Calculate ETA from distance (in minutes)
     * Formula: distance × 3 minutes/km, minimum 15 minutes
     */
    protected function calculateETA(?float $distanceKm): ?int
    {
        if (!$distanceKm || $distanceKm <= 0) {
            return null;
        }

        return max(15, (int)round($distanceKm * 3));
    }
}

