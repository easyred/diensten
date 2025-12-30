# Complete WhatsApp Flow Documentation

## Category-Based Notification System

### Overview

When a client completes a service request flow, the system automatically broadcasts the request to nearby service providers who:
1. **Match the category** - Only providers registered for the selected service category
2. **Are nearby** - Within 50km radius (configurable)
3. **Are available** - Not currently working on another job
4. **Have active subscription** - Active subscription or no subscription required

### Flow Structure

#### 1. Client Flow: Category Selection

When a client types `start`:
- System shows available categories (Plumber, Gardener, etc.)
- Client selects a category (by number or name)
- System starts the category-specific flow

#### 2. Category-Specific Flow

Each category has its own flow with:
- **Sub-options** (e.g., Plumber: Leakage, Drainage, Heating)
- **Urgency selection** (High, Normal, Later)
- **Description collection**
- **Confirmation**

#### 3. Request Creation & Broadcasting

When flow completes:
1. `WaRequest` is created with:
   - `category_id` - Links to the selected category
   - `problem` - Main problem type
   - `problem_type` - Specific sub-option (e.g., "leakage")
   - `urgency` - Urgency level
   - `description` - Client's description
   - `status` - Set to "broadcasting"

2. **RequestBroadcastService** finds matching providers:
   ```php
   - Has category matching request category
   - Within 50km of customer location
   - Has active subscription
   - Not currently working
   ```

3. **Notifications sent**:
   - Each provider receives WhatsApp message
   - Includes: problem, urgency, location, distance, ETA
   - Provider can accept/decline via flow

### Technical Implementation

#### Database Changes

**Migration: `add_category_id_to_wa_requests_table`**
- Added `category_id` foreign key to `wa_requests`
- Added `problem_type` field for sub-options

#### Services

**RequestBroadcastService** (`app/Services/RequestBroadcastService.php`)
- `broadcastRequest()` - Main entry point
- `findMatchingProviders()` - Queries providers by category and location
- `notifyProvider()` - Sends WhatsApp notification
- `calculateDistance()` - Haversine formula for distance
- `calculateETA()` - ETA calculation (distance × 3 min/km, min 15 min)

**WaFlowEngine** (`app/Services/WaFlowEngine.php`)
- Updated `completeFlow()` to:
  - Store `category_id` from flow
  - Call `RequestBroadcastService::broadcastRequest()`
  - Show confirmation with category name

#### Models

**WaRequest** (`app/Models/WaRequest.php`)
- Added `category_id` and `problem_type` to fillable
- Added `category()` relationship

### Configuration

Add to `.env`:
```env
WHATSAPP_BOT_URL=http://127.0.0.1:3000
```

### Example Flow

**Client Journey:**
```
1. Client: "start"
   Bot: "Select category: 1. Plumber, 2. Gardener"

2. Client: "1" or "plumber"
   Bot: "Select problem: 1. Leakage, 2. Drainage, 3. Heating"

3. Client: "1"
   Bot: "Select urgency: 1. High, 2. Normal, 3. Later"

4. Client: "1"
   Bot: "Describe the problem:"

5. Client: "Water leaking from kitchen sink"
   Bot: "Request created! Broadcasting to nearby plumbers..."

6. System finds 3 plumbers within 50km
   - Plumber A (5km away) ← Notified
   - Plumber B (8km away) ← Notified
   - Plumber C (12km away) ← Notified
   - Plumber D (60km away) ← NOT notified (too far)
   - Gardener X (3km away) ← NOT notified (wrong category)
```

**Provider Journey:**
```
1. Provider receives:
   "🔔 Nieuwe aanvraag ontvangen!
   Categorie: Plumber
   Probleem: Leakage
   Urgentie: High
   Locatie: Brussels • Afstand: 5 km • ETA: 15 min 🚗
   
   1. Accepteer
   2. Weiger"

2. Provider: "1"
   Bot: "Je hebt de aanvraag geaccepteerd. Contactgegevens worden gedeeld..."

3. Customer receives:
   "🎉 Plumber A heeft je aanvraag geaccepteerd!"
```

### Distance Calculation

Uses Haversine formula with coordinates from `postal_codes` table:
- Requires `postal_codes` table with `Latitude` and `Longitude`
- Calculates distance in kilometers
- Returns `null` if coordinates not found

### Maximum Distance

Default: **50km** (configurable in `RequestBroadcastService::MAX_DISTANCE_KM`)

Providers beyond this distance are not notified, even if they match the category.

---

# Complete WhatsApp Flow Documentation (Original)

## Overview
This document explains the complete flow of how users interact with the WhatsApp bot, how requests are created, and how service providers respond.

---

## 🔄 Complete Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                    WHATSAPP FLOW OVERVIEW                        │
└─────────────────────────────────────────────────────────────────┘

1. SETUP & CONNECTION
   └─> Admin scans QR code in admin panel
   └─> Node.js bot connects to WhatsApp
   └─> Bot ready to receive/send messages

2. CLIENT REGISTRATION
   └─> Client registers on website (diensten.pro)
   └─> Client provides WhatsApp number
   └─> Account created with role='client'

3. CLIENT INITIATES CONVERSATION
   └─> Client sends message to WhatsApp bot
   └─> Bot receives message via Baileys
   └─> Bot sends to Laravel API (/api/wa/incoming)

4. MESSAGE PROCESSING
   └─> WaRuntimeController receives message
   └─> Finds user by WhatsApp number
   └─> Checks for active session
   └─> Routes to appropriate flow

5. FLOW EXECUTION
   └─> WaFlowEngine processes flow
   └─> Progresses through nodes
   └─> Collects user input
   └─> Creates WaRequest when complete

6. SERVICE PROVIDER NOTIFICATION
   └─> System finds available providers
   └─> Sends notification to providers
   └─> Provider can accept/decline

7. REQUEST MANAGEMENT
   └─> Provider accepts request
   └─> Request status updated
   └─> Client notified
   └─> Work progresses
   └─> Request completed
```

---

## 📱 Step-by-Step Flow

### **PHASE 1: Setup & Connection**

1. **Admin Setup:**
   - Admin goes to `/admin/whatsapp` in hub dashboard
   - QR code is displayed
   - Admin scans QR code with WhatsApp
   - Node.js bot connects to WhatsApp Web
   - Connection status: ✅ Connected

2. **Bot Initialization:**
   ```
   Node.js Bot (Baileys) → Connects to WhatsApp
   ↓
   Listens for incoming messages
   ↓
   Ready to receive/send messages
   ```

---

### **PHASE 2: Client Registration**

1. **Client Registration on Website:**
   - Client visits `diensten.pro/register`
   - Selects service categories (e.g., Plumber, Gardener)
   - Provides WhatsApp number
   - Account created with `role='client'`

2. **Client Login:**
   - Client uses WhatsApp + OTP login
   - No email/password needed
   - Authenticated via WhatsApp number

---

### **PHASE 3: Client Initiates Service Request**

#### **Scenario A: Client types category name**

```
Client WhatsApp → "plumber"
     ↓
Node.js Bot receives message
     ↓
POST /api/wa/incoming
     ↓
WaRuntimeController::incoming()
```

**Processing:**
1. Extract WhatsApp number from message
2. Find user in database by WhatsApp number
3. Check if user has active session (within 4 hours)
4. If no session, check if message matches entry keyword
5. Find flow with `entry_keyword='plumber'` and `category_id=1`
6. Start flow using `WaFlowEngine`

#### **Scenario B: Client types "menu"**

```
Client WhatsApp → "menu"
     ↓
WaRuntimeController checks command
     ↓
Shows menu with available services:
  • Typ 'plumber' voor Plumber Service
  • Typ 'gardener' voor Gardening Service
  • Typ 'start' om een nieuw verzoek te beginnen
  • Typ 'status' om je verzoeken te bekijken
```

#### **Scenario C: Client types "start"**

```
Client WhatsApp → "start"
     ↓
Check for active request
     ↓
If no active request:
  Show category selection
  OR
  Start category-specific flow
```

---

### **PHASE 4: Flow Execution**

#### **Example: Plumber Service Request Flow**

**Node 1: Start (Welcome)**
```
Bot: "Welkom! Je wilt een loodgieter aanvragen."
     "Wat is het probleem?"
     
User: "Leaky faucet"
     ↓
Flow Engine: Collects text → stores in context['collected']['problem']
     ↓
Next Node: select_urgency
```

**Node 2: Select Urgency**
```
Bot: "Hoe urgent is dit?"
     "1. Hoog - Direct nodig"
     "2. Normaal - Binnenkort"
     "3. Later - Geen haast"
     
User: "1" or "Hoog"
     ↓
Flow Engine: Maps "1" or "high" → stores in context['collected']['urgency']
     ↓
Next Node: collect_description
```

**Node 3: Collect Description**
```
Bot: "Kun je meer details geven?"
     
User: "Kitchen faucet leaking for 2 days"
     ↓
Flow Engine: Collects text → stores in context['collected']['description']
     ↓
Next Node: confirm
```

**Node 4: Confirm**
```
Bot: "Je verzoek:"
     "Probleem: Leaky faucet"
     "Urgentie: Hoog"
     "Details: Kitchen faucet leaking for 2 days"
     "Typ 'ja' om te bevestigen."
     
User: "ja"
     ↓
Flow Engine: Maps "ja" → "yes" → next node: "end"
```

**Node 5: End (Complete Flow)**
```
Flow Engine detects "end" node
     ↓
WaFlowEngine::completeFlow()
     ↓
Creates WaRequest:
  - customer_id: user.id
  - problem: "Leaky faucet"
  - urgency: "high"
  - description: "Kitchen faucet leaking for 2 days"
  - status: "broadcasting"
     ↓
Session ended
     ↓
Bot: "✅ Je verzoek is aangemaakt (ID: #123)"
     "We sturen je verzoek naar beschikbare service providers..."
```

---

### **PHASE 5: Request Broadcasting**

1. **System Finds Service Providers:**
   ```
   Query: Find all service providers where:
     - role IN ('plumber', 'gardener')
     - Has category matching request
     - Is available/active
     - Subscription is active
   ```

2. **Notify Service Providers:**
   ```
   For each provider:
     ↓
   Create WaSession for provider
     ↓
   Send WhatsApp message:
     "Nieuwe aanvraag ontvangen!"
     "Probleem: Leaky faucet"
     "Urgentie: Hoog"
     "Locatie: [city]"
     "1. Accepteer"
     "2. Weiger"
   ```

---

### **PHASE 6: Service Provider Response**

#### **Provider Accepts:**
```
Provider WhatsApp → "1" or "Accepteer"
     ↓
WaRuntimeController processes
     ↓
Update WaRequest:
  - selected_plumber_id: provider.id
  - status: "active"
     ↓
Notify client:
  "✅ Een service provider heeft je verzoek geaccepteerd!"
  "Naam: [Provider Name]"
  "Contact: [Provider WhatsApp]"
```

#### **Provider Declines:**
```
Provider WhatsApp → "2" or "Weiger"
     ↓
Continue broadcasting to other providers
     ↓
No notification to client (unless all decline)
```

---

### **PHASE 7: Request Status Updates**

#### **Client Checks Status:**
```
Client WhatsApp → "status"
     ↓
WaRuntimeController::showClientStatus()
     ↓
Bot: "📊 Je Verzoeken:"
     "• Verzoek #123"
     "  Probleem: Leaky faucet"
     "  Status: Actief"
     "  Datum: 26/12/2024 03:00"
```

#### **Provider Updates Status:**
```
Provider can update status:
  - "in_progress" → Work started
  - "completed" → Job done
     ↓
Client automatically notified
```

---

## 🔧 Technical Flow Details

### **Message Flow Architecture:**

```
┌─────────────┐
│   Client    │
│  WhatsApp   │
└──────┬──────┘
       │ Message: "plumber"
       ↓
┌─────────────────────┐
│  Node.js Bot        │
│  (Baileys)          │
│  Port: 3000         │
└──────┬──────────────┘
       │ POST /api/wa/incoming
       ↓
┌─────────────────────┐
│  Laravel API        │
│  WaRuntimeController│
└──────┬──────────────┘
       │
       ├─> Find User
       ├─> Check Session
       ├─> Find Flow
       │
       ↓
┌─────────────────────┐
│  WaFlowEngine       │
│  - startOrResume()  │
│  - progress()       │
│  - completeFlow()   │
└──────┬──────────────┘
       │
       ├─> Create/Update WaSession
       ├─> Process Node
       ├─> Collect Data
       ├─> Create WaRequest (when complete)
       │
       ↓
┌─────────────────────┐
│  Response JSON      │
│  {reply: {text: ...}}│
└──────┬──────────────┘
       │
       ↓
┌─────────────────────┐
│  Node.js Bot        │
│  Sends to WhatsApp  │
└──────┬──────────────┘
       │
       ↓
┌─────────────┐
│   Client    │
│  Receives   │
│  Response   │
└─────────────┘
```

---

## 📊 Database Flow

### **Session Management:**
```
wa_sessions table:
  - wa_number: "32470123456"
  - user_id: 123
  - flow_code: "plumber_client_flow"
  - node_code: "collect_problem"
  - context_json: {
      "collected": {
        "problem": "Leaky faucet",
        "urgency": "high"
      }
    }
  - last_message_at: 2024-12-26 03:00:00
```

### **Request Creation:**
```
wa_requests table:
  - customer_id: 123
  - problem: "Leaky faucet"
  - urgency: "high"
  - description: "Kitchen faucet leaking..."
  - status: "broadcasting"
  - selected_plumber_id: null (initially)
```

### **Logging:**
```
wa_logs table:
  - wa_number: "32470123456"
  - direction: "in" or "out"
  - payload_json: {message content}
  - status: "recv" or "sent"
```

---

## 🎯 Key Commands

### **For Clients:**
- `menu` - Show main menu
- `help` - Show help message
- `start` - Start new request
- `status` - Check request status
- `plumber` / `gardener` - Start category-specific flow
- `exit` / `cancel` - Cancel current flow

### **For Service Providers:**
- `menu` - Show provider menu
- `status` - Check availability
- `accept` - Accept a request
- `decline` - Decline a request
- `complete` - Mark job as complete

---

## 🔄 Session Lifecycle

1. **Session Created:**
   - When flow starts
   - Stores: flow_code, node_code, context_json
   - TTL: 4 hours of inactivity

2. **Session Active:**
   - User responds within 4 hours
   - Session continues from last node
   - Context data preserved

3. **Session Expired:**
   - No activity for 4+ hours
   - Session marked as inactive
   - User must start new flow

4. **Session Completed:**
   - Flow reaches "end" node
   - Request created
   - Session ended

---

## 🚀 Example Complete Conversation

```
[Client] → "plumber"
[Bot]    → "Welkom! Je wilt een loodgieter aanvragen."
          "Wat is het probleem?"

[Client] → "Leaky faucet"
[Bot]    → "Hoe urgent is dit?"
          "1. Hoog - Direct nodig"
          "2. Normaal - Binnenkort"
          "3. Later - Geen haast"

[Client] → "1"
[Bot]    → "Kun je meer details geven?"

[Client] → "Kitchen faucet leaking for 2 days"
[Bot]    → "Je verzoek:"
          "Probleem: Leaky faucet"
          "Urgentie: Hoog"
          "Details: Kitchen faucet leaking for 2 days"
          "Typ 'ja' om te bevestigen."

[Client] → "ja"
[Bot]    → "✅ Je verzoek is aangemaakt (ID: #123)"
          "We sturen je verzoek naar beschikbare service providers."
          "Je ontvangt binnenkort een reactie."

[Client] → "status"
[Bot]    → "📊 Je Verzoeken:"
          "• Verzoek #123"
          "  Probleem: Leaky faucet"
          "  Status: Actief"
          "  Datum: 26/12/2024 03:00"
```

---

## 🔐 Security & Validation

1. **User Authentication:**
   - User must be registered
   - WhatsApp number must match database
   - Role-based access control

2. **Flow Validation:**
   - Only active flows are accessible
   - Role must match (client/plumber/gardener)
   - Category filtering applied

3. **Session Security:**
   - Sessions expire after 4 hours
   - Each session tied to specific user
   - Context data validated

4. **Request Validation:**
   - Client can only have one active request
   - Request data validated before creation
   - Status transitions validated

---

## 📝 Summary

**Complete Flow:**
1. ✅ Bot connects via QR code
2. ✅ Client registers on website
3. ✅ Client sends message to WhatsApp
4. ✅ Bot routes to appropriate flow
5. ✅ Flow collects information step-by-step
6. ✅ Request created automatically
7. ✅ Service providers notified
8. ✅ Provider accepts/declines
9. ✅ Request status updated
10. ✅ Client and provider communicate
11. ✅ Request completed

**Key Components:**
- **Node.js Bot (Baileys)** - Handles WhatsApp connection
- **WaRuntimeController** - Processes incoming messages
- **WaFlowEngine** - Manages conversation flows
- **WaSession** - Tracks conversation state
- **WaRequest** - Stores service requests
- **WaFlow/WaNode** - Defines conversation structure

