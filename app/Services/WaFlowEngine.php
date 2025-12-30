<?php

namespace App\Services;

use App\Models\User;
use App\Models\WaFlow;
use App\Models\WaNode;
use App\Models\WaSession;
use App\Models\WaLog;
use App\Models\WaRequest;
use App\Models\Category;
use App\Services\RequestBroadcastService;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class WaFlowEngine
{
    const SESSION_TTL_MIN = 240; // 4 hours of inactivity

    protected $broadcastService;

    public function __construct(RequestBroadcastService $broadcastService)
    {
        $this->broadcastService = $broadcastService;
    }

    public function startOrResume(string $waNumber, ?string $role, string $message, ?Category $category = null): string
    {
        // Try to resume
        $session = $this->getActiveSession($waNumber);

        if ($session) {
            return $this->progress($session, $message);
        }

        // No session: try to start by entry_keyword
        $keyword = trim(strtolower($message));
        $flow = WaFlow::query()
            ->where('is_active', true)
            ->when($role, fn($q) => $q->where(function($query) use ($role) {
                $query->where('target_role', $role)
                      ->orWhere('target_role', 'any');
            }))
            ->when($category, fn($q) => $q->where(function($query) use ($category) {
                $query->where('category_id', $category->id)
                      ->orWhereNull('category_id');
            }))
            ->where('entry_keyword', $keyword)
            ->first();

        if (!$flow) {
            // Not a start keyword → show menu or help
            if ($keyword === 'menu' || $keyword === 'help') {
                return $this->showMenu($waNumber, $role, $category);
            }
            return "Type 'menu' for menu 📋 or 'help' for help ℹ️";
        }

        // Create session at the flow's first node (lowest sort) or a node with code 'start'
        $node = $flow->nodes()->orderBy('sort')->first();
        if (!$node) {
            return "⚠️ Flow has no nodes yet.";
        }

        $user = $this->findUserByWa($waNumber);
        
        $session = WaSession::create([
            'wa_number'      => $waNumber,
            'user_id'        => $user?->id,
            'flow_code'      => $flow->code,
            'node_code'      => $node->code,
            'context_json'   => [
                'started_at' => now()->toISOString(),
                'category_id' => $category?->id,
                'category_name' => $category?->name,
            ],
            'last_message_at'=> now(),
        ]);

        $out = $this->renderNode($node, $session);
        $this->log($waNumber, 'out', ['text' => $out], 'ok');

        return $out;
    }

    public function progress(WaSession $session, string $incomingText): string
    {
        $incomingText = trim($incomingText);
        $session->last_message_at = now();
        $session->save();

        $flow = WaFlow::where('code', $session->flow_code)->first();
        if (!$flow) {
            $this->endSession($session);
            return "Session expired. Type 'menu' to start again.";
        }

        $node = $flow->nodes()->where('code', $session->node_code)->first();
        if (!$node) {
            $this->endSession($session);
            return "Session expired. Type 'menu' to start again.";
        }

        // Check for special commands
        if (in_array(strtolower($incomingText), ['exit', 'cancel'])) {
            $this->endSession($session);
            return "Flow cancelled. Type 'menu' to start again.";
        }

        // Decide next node based on current node type + user reply
        $nextCode = $this->resolveNextNodeCode($node, $incomingText, $session);

        // If node collects text, stash in context
        if ($node->type === 'collect_text') {
            $ctx = $session->context_json ?? [];
            $ctxKey = $node->code;
            $ctx['collected'][$ctxKey] = $incomingText;
            $session->context_json = $ctx;
            $session->save();
        }
        
        // If node is buttons/list, also collect the selection
        if (in_array($node->type, ['buttons', 'list'])) {
            $ctx = $session->context_json ?? [];
            $ctxKey = $node->code;
            
            $options = $node->options_json ?? [];
            $selectedValue = null;
            
            if (ctype_digit($incomingText)) {
                $idx = (int)$incomingText - 1;
                if (isset($options[$idx])) {
                    $opt = $options[$idx];
                    $selectedValue = $opt['id'] ?? $opt['label'] ?? $opt['text'] ?? $incomingText;
                }
            } else {
                // Try to match by text
                foreach ($options as $opt) {
                    $id = strtolower((string)($opt['id'] ?? ''));
                    $label = strtolower((string)($opt['label'] ?? ''));
                    $text = strtolower((string)($opt['text'] ?? ''));
                    if (in_array(strtolower($incomingText), [$id, $label, $text])) {
                        $selectedValue = $opt['id'] ?? $opt['label'] ?? $opt['text'] ?? $incomingText;
                        break;
                    }
                }
            }
            
            if ($selectedValue) {
                $ctx['collected'][$ctxKey] = $selectedValue;
                $session->context_json = $ctx;
                $session->save();
            }
        }

        if (!$nextCode) {
            // Re-render same node with a gentle hint
            $hint = $this->hintForNode($node);
            $out = ($hint ?: "Please reply with a valid option.");
            $this->log($session->wa_number, 'out', ['text' => $out], 'ok');
            return $out;
        }

        // Check if next node is 'end' or 'complete' - trigger completion
        if ($nextCode === 'end' || $nextCode === 'complete') {
            return $this->completeFlow($session, $flow);
        }

        $nextNode = $flow->nodes()->where('code', $nextCode)->first();
        if (!$nextNode) {
            // If no next node found, try to complete the flow
            return $this->completeFlow($session, $flow);
        }

        // move session
        $session->node_code = $nextNode->code;
        $session->save();

        $out = $this->renderNode($nextNode, $session);
        $this->log($session->wa_number, 'out', ['text' => $out], 'ok');
        return $out;
    }

    protected function completeFlow(WaSession $session, WaFlow $flow): string
    {
        $ctx = $session->context_json ?? [];
        $collected = $ctx['collected'] ?? [];
        
        // If this is a client flow, create a WaRequest
        if ($flow->target_role === 'client' || $flow->target_role === 'any') {
            $user = $session->user;
            if ($user && $user->role === 'client') {
                // Check if required fields are present
                $problem = $collected['problem'] ?? $collected['service'] ?? null;
                $problemType = $collected['problem_type'] ?? $collected['service_type'] ?? null;
                $urgency = $collected['urgency'] ?? 'normal';
                $description = $collected['description'] ?? $collected['details'] ?? '';
                
                // Get category from flow or context
                $categoryId = $flow->category_id ?? $ctx['category_id'] ?? null;
                
                if ($problem) {
                    // Create WaRequest
                    $waRequest = WaRequest::create([
                        'customer_id' => $user->id,
                        'category_id' => $categoryId,
                        'problem' => $problem,
                        'problem_type' => $problemType,
                        'urgency' => $urgency,
                        'description' => $description,
                        'status' => 'broadcasting',
                    ]);
                    
                    // Broadcast to matching service providers
                    try {
                        $this->broadcastService->broadcastRequest($waRequest);
                    } catch (\Exception $e) {
                        \Log::error('Failed to broadcast request', [
                            'request_id' => $waRequest->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                    
                    // Store request ID in context for future reference
                    $ctx['request_id'] = $waRequest->id;
                    $session->context_json = $ctx;
                    $session->save();
                    
                    $this->endSession($session);
                    
                    $categoryName = $flow->category ? $flow->category->name : 'Service';
                    
                    $message = "✅ Je verzoek is aangemaakt (ID: #{$waRequest->id})\n\n";
                    $message .= "Categorie: {$categoryName}\n";
                    $message .= "Probleem: {$problem}\n";
                    if ($problemType) {
                        $message .= "Type: " . ucfirst($problemType) . "\n";
                    }
                    $message .= "Urgentie: " . ucfirst($urgency) . "\n";
                    if ($description) {
                        $message .= "Beschrijving: {$description}\n";
                    }
                    $message .= "\nWe sturen je verzoek naar beschikbare {$categoryName} providers in je omgeving. Je ontvangt binnenkort een reactie.\n\n";
                    $message .= "Typ 'menu' voor het hoofdmenu of 'status' om de status te controleren.";
                    
                    $this->log($session->wa_number, 'out', ['text' => $message, 'request_id' => $waRequest->id], 'ok');
                    return $message;
                }
            }
        }
        
        // For other flows, just end with a thank you
        $this->endSession($session);
        return "Bedankt! Je verzoek is verwerkt. Typ 'menu' om terug te gaan naar het hoofdmenu.";
    }

    protected function showMenu(string $waNumber, ?string $role, ?Category $category): string
    {
        $user = $this->findUserByWa($waNumber);
        $message = "📋 *Menu - diensten.pro*\n\n";
        
        if ($user && $user->role === 'client') {
            $message .= "Je bent ingelogd als klant.\n\n";
            $message .= "*Beschikbare commando's:*\n";
            $message .= "• Typ een categorie naam (bijv. 'plumber', 'gardener') om een verzoek te starten\n";
            $message .= "• Typ 'start' om een nieuw verzoek te beginnen\n";
            $message .= "• Typ 'status' om je actieve verzoeken te bekijken\n";
            $message .= "• Typ 'help' voor meer informatie\n\n";
            
            // Show available categories
            $categories = Category::where('is_active', true)->get();
            if ($categories->count() > 0) {
                $message .= "*Beschikbare diensten:*\n";
                foreach ($categories as $cat) {
                    $message .= "• {$cat->name} - Typ '{$cat->code}' of '{$cat->name}'\n";
                }
            }
        } elseif ($user && in_array($user->role, ['plumber', 'gardener'])) {
            $message .= "Je bent ingelogd als service provider ({$user->role}).\n\n";
            $message .= "*Beschikbare commando's:*\n";
            $message .= "• Typ 'status' om je status te bekijken\n";
            $message .= "• Typ 'help' voor meer informatie\n";
        } else {
            $message .= "Je bent niet geregistreerd.\n\n";
            $message .= "Bezoek " . config('app.url') . " om een account aan te maken.\n\n";
            $message .= "Typ 'help' voor meer informatie.";
        }
        
        $this->log($waNumber, 'out', ['text' => $message], 'ok');
        return $message;
    }

    protected function resolveNextNodeCode(WaNode $node, string $incomingText, WaSession $session): ?string
    {
        $map = $node->next_map_json ?: [];
        $t = strtolower(trim($incomingText));

        // direct match
        if (isset($map[$t])) {
            return $map[$t];
        }

        // common shorthands
        if (in_array($t, ['y','yes']) && isset($map['yes'])) return $map['yes'];
        if (in_array($t, ['n','no']) && isset($map['no']))   return $map['no'];

        // If node has options (buttons/list), map numbers (1..n) to options' ids/keys
        if (in_array($node->type, ['buttons','list'])) {
            $options = $node->options_json ?: [];
            if (ctype_digit($t)) {
                $idx = (int)$t - 1;
                if (isset($options[$idx])) {
                    $opt = $options[$idx];
                    $key = strtolower((string)($opt['id'] ?? $opt['label'] ?? ''));
                    if (isset($map[$key])) return $map[$key];
                    if (isset($map[(string)$t])) return $map[(string)$t];
                }
            }

            foreach ($options as $opt) {
                $id  = strtolower((string)($opt['id'] ?? ''));
                $lbl = strtolower((string)($opt['label'] ?? ''));
                if ($id && $id === $t && isset($map[$id])) return $map[$id];
                if ($lbl && $lbl === $t && isset($map[$lbl])) return $map[$lbl];
            }
        }

        // collect_text: if a 'next' is defined, use it
        if ($node->type === 'collect_text' && isset($map['next'])) {
            return $map['next'];
        }

        return null;
    }

    protected function renderNode(WaNode $node, WaSession $session): string
    {
        $lines = [];
        $ctx = $session->context_json ?? [];

        // Replace variables in title and body
        $title = $this->replaceVariables($node->title ?? '', $ctx);
        $body = $this->replaceVariables($node->body ?? '', $ctx);

        if ($title)  $lines[] = $title;
        if ($body)   $lines[] = $body;

        if (in_array($node->type, ['buttons','list'])) {
            $opts = $node->options_json ?: [];
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
            $footer = $this->replaceVariables($node->footer, $ctx);
            $lines[] = $footer;
        }

        return implode("\n", array_filter($lines, fn($l) => $l !== null && $l !== ''));
    }

    protected function replaceVariables($text, $ctx)
    {
        if (!$text) return '';
        return preg_replace_callback('/\{\{(\w+)\}\}/', function($matches) use ($ctx) {
            $varName = $matches[1];
            return $ctx[$varName] ?? $ctx['collected'][$varName] ?? $matches[0];
        }, $text);
    }

    protected function hintForNode(WaNode $node): ?string
    {
        return match ($node->type) {
            'buttons','list'   => 'Please reply with the number of your choice.',
            'collect_text'     => 'Please type a short message.',
            default            => null,
        };
    }

    protected function getActiveSession(string $waNumber): ?WaSession
    {
        $cutoff = Carbon::now()->subMinutes(self::SESSION_TTL_MIN);
        return WaSession::where('wa_number', $waNumber)
            ->where('last_message_at', '>=', $cutoff)
            ->orderByDesc('last_message_at')
            ->first();
    }

    protected function endSession(WaSession $session): void
    {
        $session->last_message_at = now()->subMinutes(self::SESSION_TTL_MIN + 1);
        $session->save();
    }

    protected function findUserByWa(string $waNumber): ?User
    {
        $digits = preg_replace('/\D+/', '', $waNumber);
        return User::where('whatsapp_number', $digits)
            ->orWhere('phone', $digits)
            ->first();
    }

    protected function log(string $wa, string $dir, array $payload, string $status = 'ok'): void
    {
        WaLog::create([
            'wa_number'    => preg_replace('/\D+/', '', $wa),
            'direction'    => $dir,
            'payload_json' => $payload,
            'status'       => $status,
        ]);
    }
}
