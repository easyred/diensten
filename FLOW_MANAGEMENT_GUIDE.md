# WhatsApp Flow Management Guide

## Overview

The WhatsApp flow system allows you to create dynamic conversation flows for different service categories. Clients can interact with the bot to request services, and service providers can manage their availability and respond to requests.

## How Flows Work

### 1. Flow Structure

- **Flows** (`wa_flows`): Top-level conversation flows
  - `code`: Unique identifier (e.g., `plumber_client_flow`)
  - `name`: Display name
  - `entry_keyword`: Keyword that starts the flow (e.g., `plumber`, `start`)
  - `target_role`: Who can use this flow (`client`, `plumber`, `gardener`, `any`)
  - `category_id`: Optional - links flow to a specific service category
  - `is_active`: Enable/disable the flow

- **Nodes** (`wa_nodes`): Individual steps in a conversation
  - `code`: Unique identifier within the flow (e.g., `start`, `collect_problem`)
  - `type`: Node type (`text`, `buttons`, `list`, `collect_text`)
  - `title`: Message title
  - `body`: Message body (supports variables like `{{user_name}}`)
  - `options_json`: For buttons/list nodes - array of options
  - `next_map_json`: Maps user responses to next node codes
  - `sort`: Order of nodes in the flow

### 2. Flow Types

#### Client Flow (Service Request)
1. **Start Node**: Welcome message
2. **Category Selection**: Choose service type (if not pre-selected)
3. **Problem Collection**: What's the issue?
4. **Urgency Selection**: How urgent? (high/normal/later)
5. **Description Collection**: More details
6. **Confirmation**: Review and confirm
7. **End Node**: Creates `WaRequest` record

#### Service Provider Flow
1. **Start Node**: Show available requests
2. **Request Selection**: Choose a request to respond to
3. **Offer Creation**: Submit an offer
4. **Status Updates**: Update request status

### 3. Managing Flows in Admin Panel

1. Go to **Admin → WhatsApp Stroomlijnen**
2. Filter by category using the category buttons
3. Click **+ Nieuwe Flow** to create a new flow
4. Fill in:
   - **Code**: Unique identifier (e.g., `plumber_client_flow`)
   - **Name**: Display name
   - **Entry Keyword**: What users type to start (e.g., `plumber`)
   - **Target Role**: `client`, `plumber`, `gardener`, or `any`
   - **Category**: Optional - select a category for category-specific flows
   - **Active**: Enable/disable

5. After creating the flow, click **Manage Nodes** to add conversation steps

### 4. Creating Nodes

1. Click **+ Nieuwe Node** in the flow's node list
2. Fill in:
   - **Code**: Unique identifier (e.g., `start`, `collect_problem`)
   - **Type**: 
     - `text`: Simple message
     - `buttons`: Multiple choice (numbered list)
     - `list`: Interactive list (WhatsApp native)
     - `collect_text`: Collect free-form text input
   - **Title**: Message title (optional)
   - **Body**: Message content (supports `{{variables}}`)
   - **Options JSON**: For buttons/list - array like:
     ```json
     [
       {"id": "high", "label": "Hoog"},
       {"id": "normal", "label": "Normaal"},
       {"id": "later", "label": "Later"}
     ]
     ```
   - **Next Map JSON**: Maps responses to next nodes:
     ```json
     {
       "1": "collect_description",
       "high": "collect_description",
       "normal": "collect_description",
       "later": "collect_description"
     }
     ```
   - **Sort**: Order (lower numbers first)

### 5. Variables in Messages

You can use variables in node titles and bodies:
- `{{user_name}}`: User's full name
- `{{category_name}}`: Selected category name
- `{{collected.problem}}`: Value collected from a previous node
- `{{collected.urgency}}`: Value collected from urgency selection

### 6. Example: Complete Client Flow

**Flow Setup:**
- Code: `plumber_client_flow`
- Entry Keyword: `plumber`
- Target Role: `client`
- Category: `Plumber`

**Node 1 - Start:**
- Code: `start`
- Type: `text`
- Body: `Welkom! Je wilt een loodgieter aanvragen.`
- Next Map: `{"next": "collect_problem"}`

**Node 2 - Collect Problem:**
- Code: `collect_problem`
- Type: `collect_text`
- Body: `Wat is het probleem?`
- Next Map: `{"next": "select_urgency"}`

**Node 3 - Select Urgency:**
- Code: `select_urgency`
- Type: `buttons`
- Body: `Hoe urgent is dit?`
- Options:
  ```json
  [
    {"id": "high", "label": "Hoog - Direct nodig"},
    {"id": "normal", "label": "Normaal - Binnenkort"},
    {"id": "later", "label": "Later - Geen haast"}
  ]
  ```
- Next Map:
  ```json
  {
    "1": "collect_description",
    "high": "collect_description",
    "normal": "collect_description",
    "later": "collect_description"
  }
  ```

**Node 4 - Collect Description:**
- Code: `collect_description`
- Type: `collect_text`
- Body: `Kun je meer details geven?`
- Next Map: `{"next": "confirm"}`

**Node 5 - Confirm:**
- Code: `confirm`
- Type: `text`
- Body: `Je verzoek:\nProbleem: {{collected.problem}}\nUrgentie: {{collected.urgency}}\nDetails: {{collected.description}}\n\nTyp 'ja' om te bevestigen.`
- Next Map: `{"yes": "end", "ja": "end"}`

**Node 6 - End:**
- Code: `end`
- Type: `text`
- Body: `Bedankt! Je verzoek is aangemaakt.`
- (Flow engine automatically creates WaRequest when reaching 'end')

## User Interaction Flow

### For Clients:

1. **Start Conversation:**
   - User sends: `menu` → Shows available services
   - User sends: `plumber` → Starts plumber flow
   - User sends: `start` → Shows category selection

2. **During Flow:**
   - Bot asks questions step by step
   - User responds with text or numbers
   - Flow progresses through nodes

3. **Completion:**
   - When flow reaches `end` node, a `WaRequest` is automatically created
   - User receives confirmation with request ID

4. **Status Check:**
   - User sends: `status` → Shows all their requests

### For Service Providers:

1. **Start Conversation:**
   - User sends: `menu` → Shows provider menu
   - Bot shows available requests automatically

2. **Respond to Requests:**
   - Provider can accept/decline requests
   - Provider can submit offers
   - Provider can update request status

## Best Practices

1. **Keep flows simple**: Don't create too many nodes
2. **Use clear entry keywords**: Make it easy for users to start
3. **Provide help text**: Always include a help/menu option
4. **Test flows**: Test each flow before making it active
5. **Use variables**: Personalize messages with user data
6. **Handle errors**: Add validation nodes for invalid inputs
7. **Category-specific flows**: Create separate flows for each category if needed

## Troubleshooting

- **Flow not starting?** Check entry_keyword matches exactly (case-insensitive)
- **Node not progressing?** Check next_map_json has correct mappings
- **Variables not working?** Ensure variable names match context keys
- **Request not created?** Ensure flow has `end` node and user is a client

