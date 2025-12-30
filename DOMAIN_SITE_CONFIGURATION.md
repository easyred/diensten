# Domain Site Configuration System

## Overview

Each service category (plumber, gardener, etc.) can have its own domain with custom branding:
- Logo
- Favicon
- Meta tags (title, description, keywords, OG tags)
- Site description
- Custom styling/colors

Admin configures these in hub admin panel, then a VPS script reads the config and creates/updates the static site.

---

## Database Schema

### Categories Table (Already exists, needs expansion)

```php
Schema::table('categories', function (Blueprint $table) {
    // Existing fields:
    $table->string('code')->unique();
    $table->string('name');
    $table->string('logo_url')->nullable();
    $table->string('domain')->nullable();
    $table->boolean('is_active')->default(true);
    $table->json('config')->nullable();
    
    // New fields to add:
    $table->string('favicon_url')->nullable();
    $table->text('site_description')->nullable();
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->string('meta_keywords')->nullable();
    $table->string('og_image_url')->nullable();
    $table->string('primary_color')->nullable(); // For branding
    $table->string('secondary_color')->nullable();
});
```

### Or use JSON config field (recommended):

```php
// Store everything in config JSON:
{
    "domain": "plumber.com",
    "logo": {
        "url": "/storage/logos/plumber.png",
        "alt": "Plumber Service Logo"
    },
    "favicon": {
        "url": "/storage/favicons/plumber.ico"
    },
    "meta": {
        "title": "Professional Plumber Services | Plumber.com",
        "description": "Find trusted plumbers in your area...",
        "keywords": "plumber, plumbing, repair, installation",
        "og_image": "/storage/og/plumber.jpg"
    },
    "branding": {
        "primary_color": "#0066cc",
        "secondary_color": "#00cc66",
        "site_description": "Your trusted partner for all plumbing needs"
    },
    "redirects": {
        "register": "https://diensten.pro/register?category=plumber",
        "login": "https://diensten.pro/login",
        "dashboard": "https://diensten.pro/service-provider/dashboard"
    }
}
```

---

## Admin Panel Interface

### Location: `/admin/categories/{id}/site-config`

**Fields:**
1. **Domain Settings**
   - Domain URL (e.g., `plumber.com`)
   - Site Name
   - Site Description

2. **Branding**
   - Logo Upload (with preview)
   - Favicon Upload (with preview)
   - Primary Color (color picker)
   - Secondary Color (color picker)

3. **Meta Tags**
   - Meta Title
   - Meta Description
   - Meta Keywords
   - OG Image Upload
   - OG Title (optional, defaults to meta title)
   - OG Description (optional, defaults to meta description)

4. **Redirects** (auto-generated, but editable)
   - Registration URL
   - Login URL
   - Dashboard URL

5. **Status**
   - Site Active/Inactive
   - Last Deployed (timestamp)
   - Deploy Status (pending/success/failed)

---

## API Endpoint for VPS Script

### GET `/api/categories/{code}/site-config`

Returns JSON configuration for the VPS script:

```json
{
    "code": "plumber",
    "name": "Plumber Service",
    "domain": "plumber.com",
    "logo": {
        "url": "https://diensten.pro/storage/logos/plumber.png",
        "alt": "Plumber Service Logo"
    },
    "favicon": {
        "url": "https://diensten.pro/storage/favicons/plumber.ico"
    },
    "meta": {
        "title": "Professional Plumber Services | Plumber.com",
        "description": "Find trusted plumbers in your area. Fast, reliable, and professional plumbing services.",
        "keywords": "plumber, plumbing, repair, installation, emergency plumber",
        "og_image": "https://diensten.pro/storage/og/plumber.jpg"
    },
    "branding": {
        "primary_color": "#0066cc",
        "secondary_color": "#00cc66",
        "site_description": "Your trusted partner for all plumbing needs"
    },
    "redirects": {
        "register": "https://diensten.pro/register?category=plumber",
        "login": "https://diensten.pro/login",
        "dashboard": "https://diensten.pro/service-provider/dashboard"
    },
    "hub_url": "https://diensten.pro",
    "is_active": true
}
```

### GET `/api/categories/site-configs`

Returns all active site configurations:

```json
{
    "categories": [
        {
            "code": "plumber",
            "domain": "plumber.com",
            ...
        },
        {
            "code": "gardener",
            "domain": "gardener.com",
            ...
        }
    ]
}
```

---

## VPS Script Requirements

### Script should:
1. **Fetch configuration** from hub API
2. **Create/update static site** with:
   - HTML pages (welcome, terms, privacy)
   - Assets (logo, favicon, OG image)
   - Meta tags in HTML
   - Redirects configured
   - Custom colors/styling

### Example Script Flow:

```bash
#!/bin/bash

# 1. Fetch config from hub
CONFIG=$(curl -s "https://diensten.pro/api/categories/plumber/site-config")

# 2. Extract values
DOMAIN=$(echo $CONFIG | jq -r '.domain')
LOGO_URL=$(echo $CONFIG | jq -r '.logo.url')
FAVICON_URL=$(echo $CONFIG | jq -r '.favicon.url')
META_TITLE=$(echo $CONFIG | jq -r '.meta.title')
META_DESC=$(echo $CONFIG | jq -r '.meta.description')
PRIMARY_COLOR=$(echo $CONFIG | jq -r '.branding.primary_color')

# 3. Create site directory
mkdir -p /var/www/$DOMAIN

# 4. Download assets
wget -O /var/www/$DOMAIN/logo.png $LOGO_URL
wget -O /var/www/$DOMAIN/favicon.ico $FAVICON_URL

# 5. Generate HTML with config
cat > /var/www/$DOMAIN/index.html <<EOF
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$META_TITLE</title>
    <meta name="description" content="$META_DESC">
    <link rel="icon" href="/favicon.ico">
    <style>
        :root {
            --primary-color: $PRIMARY_COLOR;
        }
    </style>
</head>
<body>
    <!-- Site content -->
</body>
</html>
EOF

# 6. Configure nginx/apache
# 7. Set up SSL
# 8. Deploy
```

---

## Implementation Plan

### Phase 1: Database Migration
- Add fields to categories table (or expand config JSON)
- Migration for new fields

### Phase 2: Admin Interface
- Create site config page (`/admin/categories/{id}/site-config`)
- File upload for logo, favicon, OG image
- Form with all meta tag fields
- Color pickers for branding
- Save/update functionality

### Phase 3: API Endpoints
- `GET /api/categories/{code}/site-config`
- `GET /api/categories/site-configs`
- Authentication for API (API key or token)

### Phase 4: File Storage
- Logo storage: `storage/app/public/logos/`
- Favicon storage: `storage/app/public/favicons/`
- OG image storage: `storage/app/public/og/`
- Public symlink for access

### Phase 5: VPS Script Template
- Bash script template
- Documentation for deployment
- Example nginx config

---

## File Structure

```
hub/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Admin/
│   │           └── CategorySiteConfigController.php
│   └── Http/
│       └── Controllers/
│           └── Api/
│               └── SiteConfigController.php
├── database/
│   └── migrations/
│       └── add_site_config_to_categories.php
├── resources/
│   └── views/
│       └── admin/
│           └── categories/
│               └── site-config.blade.php
└── storage/
    └── app/
        └── public/
            ├── logos/
            ├── favicons/
            └── og/
```

---

## Next Steps

1. ✅ Analysis complete
2. ⏳ Create migration for site config fields
3. ⏳ Create admin controller and views
4. ⏳ Create API endpoints
5. ⏳ Set up file storage
6. ⏳ Create VPS script template

