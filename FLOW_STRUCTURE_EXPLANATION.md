# WhatsApp Flow Structure - Complete Explanation

## Overview

The WhatsApp flow system is **category-based** and **dynamic**, allowing you to create different conversation flows for each service category (plumber, gardener, etc.). The system automatically handles category selection, flow routing, and notifications.

---

## 📊 Current Flow Structure

### 1. **Database Structure**

#### `wa_flows` Table
- **`id`**: Unique flow identifier
- **`category_id`**: Links flow to a specific category (nullable - can be global)
- **`code`**: Unique code per category (e.g., `plumber_client_flow`, `gardener_client_flow`)
- **`name`**: Display name (e.g., "Plumber Service Request Flow")
- **`entry_keyword`**: Keyword that starts the flow (e.g., `plumber`, `gardener`, `start`)
- **`target_role`**: Who can use this flow (`client`, `plumber`, `gardener`, `any`)
- **`is_active`**: Enable/disable the flow
- **Unique Constraint**: `(category_id, code)` - ensures unique codes per category

#### `wa_nodes` Table
- **`id`**: Unique node identifier
- **`flow_id`**: Links to parent flow
- **`code`**: Node identifier within flow (e.g., `start`, `collect_problem`, `collect_urgency`)
- **`type`**: Node type (`text`, `buttons`, `list`, `collect_text`, `dispatch`)
- **`title`**: Message title
- **`body`**: Message body (supports variables like `{{user_name}}`, `{{category_name}}`)
- **`options_json`**: For buttons/list nodes - array of options
- **`next_map_json`**: Maps user responses to next node codes
- **`sort`**: Order of nodes in the flow

#### `wa_sessions` Table
- **`wa_number`**: WhatsApp number
- **`user_id`**: Optional link to user
- **`flow_code`**: Current flow code
- **`node_code`**: Current node in the flow
- **`context_json`**: Stores collected data (category_id, problem, urgency, description, etc.)
- **`last_message_at`**: For session timeout (4 hours)

---

## 🔄 Flow Execution Process

### **Step 1: Client Initiates Conversation**

When a client sends a message via WhatsApp:

1. **Check for Active Session**
   - If session exists → Continue with current flow
   - If no session → Start new flow

2. **Handle Special Commands**
   - `menu` → Show menu with available categories
   - `help` → Show help message
   - `status` → Show client's active requests
   - `start` → Start new request flow

3. **Category Detection**
   - User types category name/code (e.g., `plumber`, `gardener`)
   - System finds matching category from `categories` table
   - If category found → Start category-specific flow

### **Step 2: Flow Selection Logic**

```php
// Priority order:
1. Find flow with matching entry_keyword AND category_id
2. If not found, find flow with entry_keyword (global flow)
3. If category found but no flow → Create default message
4. If no flow found → Show help/menu
```

### **Step 3: Flow Execution**

1. **Create Session**
   - Store `flow_code`, `node_code`, `category_id` in context
   - Start at first node (lowest `sort` value)

2. **Node Processing**
   - **Text Node**: Display message, move to next node
   - **Buttons/List Node**: Show options, wait for user selection
   - **Collect Text Node**: Wait for user input, store in context
   - **Dispatch Node**: Trigger action (e.g., create request)

3. **Progress Through Flow**
   - User responds → System matches response to `next_map_json`
   - Move to next node based on mapping
   - Store collected data in `context_json`

4. **Flow Completion**
   - When node code is `end` or `complete`:
     - Extract collected data from context
     - Create `WaRequest` record with:
       - `category_id` (from flow or context)
       - `problem` (from collected data)
       - `problem_type` (sub-option, e.g., "leakage")
       - `urgency` (high/normal/later)
       - `description` (client's description)
       - `status` = "broadcasting"
     - Broadcast request to matching providers
     - End session

---

## 🏗️ Example Flow Structures

### **Plumber Client Flow**

```
Flow: plumber_client_flow
Category: plumber
Entry Keyword: plumber
Target Role: client

Nodes:
1. start (text)
   → Welcome message: "Welkom bij Plumber Service!"

2. collect_problem (buttons)
   Options:
   - Leakage
   - Drainage
   - Heating
   - Installation
   Next Map: { "leakage": "collect_urgency", "drainage": "collect_urgency", ... }

3. collect_urgency (buttons)
   Options:
   - High (max 60 min)
   - Normal (max 2 hours)
   - Later today
   Next Map: { "high": "collect_description", "normal": "collect_description", ... }

4. collect_description (collect_text)
   → "Please describe the problem..."
   Next Map: { "next": "confirm" }

5. confirm (buttons)
   Options:
   - Yes, send request
   - No, cancel
   Next Map: { "yes": "end", "no": "cancel" }

6. end (dispatch)
   → Creates WaRequest
   → Broadcasts to plumbers
```

### **Gardener Client Flow**

```
Flow: gardener_client_flow
Category: gardener
Entry Keyword: gardener
Target Role: client

Nodes:
1. start (text)
   → Welcome message: "Welkom bij Gardener Service!"

2. collect_problem (buttons)
   Options:
   - Lawn Mowing
   - Tree Trimming
   - Garden Design
   - Maintenance
   Next Map: { "lawn_mowing": "collect_urgency", ... }

3. collect_urgency (buttons)
   ... (same as plumber)

4. collect_description (collect_text)
   ... (same as plumber)

5. confirm (buttons)
   ... (same as plumber)

6. end (dispatch)
   → Creates WaRequest
   → Broadcasts to gardeners
```

### **Provider Flow (Plumber/Gardener)**

```
Flow: plumber_provider_flow
Category: plumber
Entry Keyword: (triggered by notification)
Target Role: plumber

Nodes:
1. new_request (text)
   → Shows request details (problem, urgency, location, distance, ETA)
   → Variables: {{problem}}, {{customer_name}}, {{city}}, {{distance_km}}, {{eta_min}}

2. accept_decline (buttons)
   Options:
   - Accept Request
   - Decline
   Next Map: { "accept": "create_offer", "decline": "end" }

3. create_offer (collect_text)
   → "Enter your offer message..."
   Next Map: { "next": "end" }

4. end (dispatch)
   → Creates WaOffer
   → Notifies customer
```

---

## 🔔 Notification & Broadcasting System

### **When Request is Created**

1. **RequestBroadcastService** is triggered
2. **Find Matching Providers**:
   ```php
   - Has category matching request category_id
   - Within 50km of customer location
   - Has active subscription (or no subscription required)
   - Not currently working on another job
   ```

3. **Send Notifications**:
   - For each matching provider:
     - Find provider flow: `{category_code}_provider_flow`
     - Create session with request context
     - Send first node message via WhatsApp
     - Provider can accept/decline via flow

---

## ➕ Adding a New Category

### **What Happens Automatically**

1. **Category Detection**
   - When client types category name/code → System finds it from `categories` table
   - Menu automatically shows new category
   - `showMenu()` dynamically lists all active categories

2. **Flow Routing**
   - System looks for flow with `category_id` matching new category
   - If flow exists → Starts that flow
   - If no flow → Shows default message: "Welkom bij {Category Name}!"

3. **Request Creation**
   - When flow completes → `WaRequest` is created with new `category_id`
   - System automatically links request to category

4. **Notification Filtering**
   - `RequestBroadcastService` filters providers by:
     - Providers who have new category in their `category_user` pivot table
     - Location (within 50km)
     - Availability & subscription

### **What You Need to Do Manually**

1. **Create Category in Admin**
   - Go to Admin → Domains
   - Add new category (e.g., "Electrician")
   - Set `code` (e.g., "electrician")
   - Set `is_active` = true

2. **Create Client Flow** (Optional but Recommended)
   - Go to Admin → WhatsApp Stroomlijnen
   - Filter by new category
   - Click "+ Nieuwe Stroom"
   - Fill in:
     - **Code**: `electrician_client_flow`
     - **Name**: "Electrician Service Request Flow"
     - **Category**: Select "Electrician"
     - **Entry Keyword**: `electrician` (or `start`)
     - **Target Role**: `client`
   - Create nodes (problem selection, urgency, description, confirmation, end)

3. **Create Provider Flow** (Optional but Recommended)
   - Create another flow:
     - **Code**: `electrician_provider_flow`
     - **Name**: "Electrician Provider Notification Flow"
     - **Category**: Select "Electrician"
     - **Entry Keyword**: (leave empty - triggered by notification)
     - **Target Role**: `any` or `plumber` (if electricians use plumber role)
   - Create nodes (new request notification, accept/decline, offer creation)

4. **Register Service Providers**
   - Service providers need to select new category in their profile
   - They can select multiple categories (plumber + electrician)

---

## 🎯 Key Features

### **1. Dynamic Category Menu**
- Menu automatically shows all active categories
- No code changes needed when adding categories
- Clients can type category name or number

### **2. Category-Specific Flows**
- Each category can have its own flow
- Different problem options per category
- Same structure, different content

### **3. Global Flows**
- Flows with `category_id = null` work for all categories
- Useful for general flows (help, menu, status)

### **4. Flexible Routing**
- Entry keywords can be category names
- Multiple entry points (e.g., `start`, `plumber`, `help`)
- Fallback to default messages if flow not found

### **5. Context-Aware**
- Flow context stores category_id
- Variables in messages: `{{category_name}}`, `{{problem}}`, etc.
- Context passed through entire flow

---

## 📝 Flow Creation Best Practices

### **Naming Convention**
- Client flows: `{category_code}_client_flow`
- Provider flows: `{category_code}_provider_flow`
- Global flows: `client_general_flow`, `provider_general_flow`

### **Entry Keywords**
- Use category code: `plumber`, `gardener`, `electrician`
- Or use `start` for general flow
- Can have multiple flows with same entry keyword (different categories)

### **Node Structure**
- Always start with `start` node
- Use `collect_text` for free-form input
- Use `buttons` or `list` for selections
- End with `end` or `complete` node for client flows

### **Next Map Examples**
```json
// For buttons node:
{
  "1": "next_node_code",
  "2": "another_node_code",
  "yes": "confirm_node",
  "no": "cancel_node"
}

// For collect_text node:
{
  "next": "next_node_code"
}
```

---

## 🔍 Current Implementation Status

### **Plumber Flow**
- ✅ Category exists in database
- ⚠️ Flow may need to be created in admin panel
- ✅ Provider notification system works
- ✅ Request broadcasting works

### **Gardener Flow**
- ✅ Category exists in database
- ⚠️ Flow may need to be created in admin panel
- ✅ Provider notification system works
- ✅ Request broadcasting works

### **New Categories**
- ✅ System automatically detects new categories
- ✅ Menu shows new categories dynamically
- ✅ Request creation works with any category
- ✅ Provider filtering works by category
- ⚠️ Need to create flows manually in admin panel

---

## 🚀 Summary

**The flow system is fully dynamic and category-aware:**

1. **Adding a category** → Automatically appears in menu
2. **Creating a flow** → Links to category via `category_id`
3. **Client requests** → Automatically filtered by category
4. **Provider notifications** → Only sent to providers with matching category
5. **No code changes needed** → Everything is database-driven

**To add a new category:**
1. Create category in admin
2. (Optional) Create client flow
3. (Optional) Create provider flow
4. Register providers for that category
5. Done! ✅

