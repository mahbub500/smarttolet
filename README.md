# SmartToLet WordPress Plugin

A smart property rental & to-let listing plugin built with a clean OOP architecture.

---

## Directory Structure

```
smarttolet/
├── smarttolet.php              # Plugin entry point & constants
├── uninstall.php               # Cleanup on delete
├── composer.json               # Composer / PSR-4 autoloading
│
├── includes/                   # Core bootstrap (namespace: SmartToLet\)
│   ├── class-smarttolet.php    # Singleton bootstrap
│   └── class-installer.php     # DB table creation
│
├── admin/                      # Admin-only (namespace: SmartToLet\Admin\)
│   ├── class-admin.php         # Admin bootstrap
│   ├── class-menu.php          # WP admin menus
│   ├── class-meta-boxes.php    # Property meta boxes + save
│   ├── class-settings.php      # Settings API integration
│   ├── class-enquiries-table.php # Enquiries list + status update
│   ├── class-columns.php       # Custom CPT list columns
│   ├── css/
│   │   └── admin.css
│   ├── js/
│   │   └── admin.js
│   └── views/
│       ├── dashboard.php
│       ├── enquiries.php
│       ├── settings.php
│       ├── meta-box-details.php
│       ├── meta-box-gallery.php
│       └── meta-box-location.php
│
├── frontend/                   # Frontend (namespace: SmartToLet\Frontend\)
│   ├── class-frontend.php      # Frontend bootstrap
│   ├── class-template-loader.php # Theme-overridable templates
│   ├── class-property-query.php  # WP_Query helpers
│   ├── css/
│   │   └── frontend.css
│   ├── js/
│   │   └── frontend.js
│   └── views/
│       ├── property-card.php
│       ├── listings.php
│       ├── featured.php
│       ├── search.php
│       ├── enquiry-form.php
│       ├── single-property.php
│       └── archive-property.php
│
├── common/                     # Shared (namespace: SmartToLet\Common\)
│   ├── class-post-types.php    # CPT registration
│   ├── class-taxonomies.php    # Taxonomy registration
│   ├── class-assets.php        # CSS/JS enqueueing
│   ├── class-shortcodes.php    # Shortcode registration
│   └── class-ajax.php          # AJAX handlers
│
└── languages/                  # .pot / .po / .mo files
```

---

## Installation

### 1. Clone or upload
```bash
cd wp-content/plugins/
git clone <repo> smarttolet
```

### 2. Install Composer dependencies
```bash
cd smarttolet
composer install
```

### 3. Activate
Go to **Plugins → Installed Plugins → SmartToLet → Activate**

---

## Shortcodes

| Shortcode | Attributes | Description |
|-----------|-----------|-------------|
| `[smarttolet_listings]` | `per_page`, `type`, `location`, `orderby`, `order` | Property grid |
| `[smarttolet_search]` | — | AJAX search form |
| `[smarttolet_featured]` | `limit` | Featured properties |
| `[smarttolet_enquiry]` | `property_id` | Enquiry form |

---

## Theme Template Overrides

Copy any template from `frontend/views/` to your theme's `smarttolet/` folder:

```
your-theme/
└── smarttolet/
    ├── property-card.php
    ├── single-property.php
    └── archive-property.php
```

---

## Settings

**SmartToLet → Settings** lets you configure:
- Currency symbol & price suffix
- Google Maps API key
- Admin notification email
- Listings page assignment

---

## Development

```bash
# Lint (requires phpcs + WPCS)
composer run lint

# Auto-fix
composer run lint-fix

# Tests
composer run test
```

---

## Namespace Map

| Namespace | Directory | Purpose |
|-----------|-----------|---------|
| `SmartToLet\` | `includes/` | Core bootstrap & installer |
| `SmartToLet\Admin\` | `admin/` | All wp-admin logic |
| `SmartToLet\Frontend\` | `frontend/` | All public-facing logic |
| `SmartToLet\Common\` | `common/` | Shared (CPTs, assets, AJAX) |
