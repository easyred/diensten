<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, WaLog, WaSession, WaFlow, WaNode, Category};
use App\Services\WaFlowEngine;
use Illuminate\Support\Facades\Http;

class WaRuntimeController extends Controller
{
    protected $flowEngine;

    public function __construct(WaFlowEngine $flowEngine)
    {
        $this->flowEngine = $flowEngine;
    }

    public function incoming(Request $request)
    {
        $from = preg_replace('/\D+/', '', (string) $request->input('from'));
        $originalText = trim((string) $request->input('message'));
        $text = strtolower($originalText);

        // Log incoming message
        WaLog::create([
            'wa_number' => $from,
            'direction' => 'in',
            'payload_json' => $request->all(),
            'status' => 'recv'
        ]);

        // Find user
        $user = User::where('whatsapp_number', $from)
            ->orWhere('phone', $from)
            ->first();

        // Try to get active session
        $session = WaSession::where('wa_number', $from)
            ->where('last_message_at', '>=', now()->subMinutes(240))
            ->first();

        // If session exists, continue with flow
        if ($session) {
            $reply = $this->flowEngine->progress($session, $text);
            return $this->replyText($from, $reply);
        }

        // No session: handle special commands or start flow
        $role = $user ? $user->role : null;
        $category = null;

        // Check if message is a category name/code or number
        if ($user && $user->role === 'client') {
            $category = $this->findCategoryByKeyword($text);
            
            // If user typed a number, try to match with category list
            if (!$category && ctype_digit($text)) {
                $categories = Category::where('is_active', true)->orderBy('id')->get();
                $index = (int)$text - 1;
                if (isset($categories[$index])) {
                    $category = $categories[$index];
                }
            }
        }

        // Handle menu/help commands
        if ($text === 'menu' || $text === 'help') {
            $reply = $this->flowEngine->showMenu($from, $role, $category);
            return $this->replyText($from, $reply);
        }

        // Handle status command for clients
        if ($text === 'status' && $user && $user->role === 'client') {
            return $this->showClientStatus($from, $user);
        }

        // Handle start command
        if ($text === 'start') {
            return $this->handleStartCommand($from, $user, $category);
        }

        // Try to find flow by entry keyword
        $flow = $this->findFlowByKeyword($text, $user, $category);

        // If category found but no flow, start category-specific flow
        if ($category && !$flow) {
            return $this->startCategoryFlow($from, $user, $category);
        }

        // If flow found, start it
        if ($flow) {
            $reply = $this->flowEngine->startOrResume($from, $role, $text, $category);
            return $this->replyText($from, $reply);
        }

        // No flow found - show help
        $helpMessage = "👋 Welkom bij diensten.pro!\n\n";
        if (!$user) {
            $helpMessage .= "Je bent niet geregistreerd. Bezoek " . config('app.url') . " om een account aan te maken.\n\n";
        } else {
            $helpMessage .= "Typ 'menu' voor het hoofdmenu of 'help' voor hulp.\n\n";
            if ($user->role === 'client') {
                $categories = Category::where('is_active', true)->get();
                if ($categories->count() > 0) {
                    $helpMessage .= "Beschikbare diensten:\n";
                    foreach ($categories as $cat) {
                        $helpMessage .= "• Typ '{$cat->code}' of '{$cat->name}' voor {$cat->name}\n";
                    }
                }
            }
        }
        return $this->replyText($from, $helpMessage);
    }

    protected function findCategoryByKeyword(string $keyword): ?Category
    {
        return Category::where('is_active', true)
            ->where(function($q) use ($keyword) {
                $q->where('code', $keyword)
                  ->orWhere('name', 'like', "%{$keyword}%");
            })
            ->first();
    }

    protected function findFlowByKeyword(string $keyword, ?User $user, ?Category $category): ?WaFlow
    {
        $query = WaFlow::where('is_active', true)
            ->where('entry_keyword', $keyword);

        // Filter by role
        if ($user) {
            $query->where(function($q) use ($user) {
                $q->where('target_role', $user->role)
                  ->orWhere('target_role', 'any');
            });
        } else {
            $query->where('target_role', 'any');
        }

        // Filter by category
        if ($category) {
            $query->where(function($q) use ($category) {
                $q->where('category_id', $category->id)
                  ->orWhereNull('category_id');
            });
        }

        return $query->first();
    }

    protected function startCategoryFlow(string $from, ?User $user, Category $category): \Illuminate\Http\JsonResponse
    {
        // Find or create a default flow for this category
        $flow = WaFlow::where('category_id', $category->id)
            ->where('target_role', $user ? $user->role : 'any')
            ->where('is_active', true)
            ->first();

        if (!$flow) {
            // Create a simple default flow on the fly
            $message = "👋 Welkom bij {$category->name}!\n\n";
            $message .= "Je wilt een verzoek indienen voor {$category->name}.\n\n";
            $message .= "Typ 'start' om een nieuw verzoek te beginnen, of 'menu' voor het hoofdmenu.";
            return $this->replyText($from, $message);
        }

        $role = $user ? $user->role : null;
        $reply = $this->flowEngine->startOrResume($from, $role, $flow->entry_keyword, $category);
        return $this->replyText($from, $reply);
    }

    protected function handleStartCommand(string $from, ?User $user, ?Category $category): \Illuminate\Http\JsonResponse
    {
        if (!$user || $user->role !== 'client') {
            return $this->replyText($from, "Je moet ingelogd zijn als klant om een verzoek te starten.");
        }

        // Check for active request
        $activeRequest = \App\Models\WaRequest::where('customer_id', $user->id)
            ->whereIn('status', ['broadcasting', 'active', 'in_progress'])
            ->first();

        if ($activeRequest) {
            $message = "Je hebt al een actief verzoek (ID: #{$activeRequest->id}).\n\n";
            $message .= "Status: " . ucfirst($activeRequest->status) . "\n";
            $message .= "Typ 'status' voor meer details.";
            return $this->replyText($from, $message);
        }

        // If category is specified, start that flow
        if ($category) {
            return $this->startCategoryFlow($from, $user, $category);
        }

        // Otherwise, start general flow with category selection
        $generalFlow = WaFlow::where('code', 'client_general_flow')
            ->where('is_active', true)
            ->where('target_role', 'client')
            ->orWhere('target_role', 'any')
            ->first();

        if ($generalFlow) {
            $reply = $this->flowEngine->startOrResume($from, $user->role, 'start', null);
            return $this->replyText($from, $reply);
        }

        // Fallback: show category selection manually
        $categories = Category::where('is_active', true)->get();
        if ($categories->count() === 0) {
            return $this->replyText($from, "Geen diensten beschikbaar op dit moment.");
        }

        $message = "👋 Welkom bij diensten.pro!\n\n";
        $message .= "Waarmee kunnen we je helpen?\n\n";
        $message .= "Selecteer een dienst:\n";
        foreach ($categories as $i => $cat) {
            $message .= ($i + 1) . ". {$cat->name}\n";
        }
        $message .= "\nTyp het nummer of de naam van de dienst (bijv. 'plumber' of '1').";

        return $this->replyText($from, $message);
    }

    protected function showClientStatus(string $from, User $user): \Illuminate\Http\JsonResponse
    {
        $requests = \App\Models\WaRequest::where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($requests->count() === 0) {
            return $this->replyText($from, "Je hebt nog geen verzoeken. Typ 'start' om een nieuw verzoek te beginnen.");
        }

        $message = "📊 *Je Verzoeken:*\n\n";
        foreach ($requests as $req) {
            $statusLabels = [
                'broadcasting' => 'Uitzenden',
                'active' => 'Actief',
                'in_progress' => 'In Uitvoering',
                'completed' => 'Voltooid',
                'cancelled' => 'Geannuleerd'
            ];
            $status = $statusLabels[$req->status] ?? $req->status;
            
            $message .= "• Verzoek #{$req->id}\n";
            $message .= "  Probleem: {$req->problem}\n";
            $message .= "  Status: {$status}\n";
            $message .= "  Datum: " . $req->created_at->format('d/m/Y H:i') . "\n\n";
        }

        return $this->replyText($from, $message);
    }

    protected function replyText($to, $text)
    {
        WaLog::create([
            'wa_number' => $to,
            'direction' => 'out',
            'payload_json' => ['type' => 'text', 'body' => $text],
            'status' => 'queued'
        ]);

        return response()->json(['reply' => ['type' => 'text', 'body' => $text]]);
    }
}
