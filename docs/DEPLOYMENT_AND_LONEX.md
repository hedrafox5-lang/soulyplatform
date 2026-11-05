# Souly Platform — Deployment & Lonex Hosting Guide

> **Target Environment:** Lonex Control Panel / cPanel Shared Hosting  
> **Domain:** [soulyeg.online](https://soulyeg.online)  
> **PHP Version:** PHP 8.3 CLI / FPM (ZTS Visual C++ 2019 or Linux x86_64)  
> **Target Path:** `/home/hedraadel11/public_html` or `/www/soulyeg.online`  

---

## 1. Hosting Architecture & Constraints

Shared hosting on platforms like **Lonex** presents specific constraints that standard Laravel boilerplate setups fail to handle:
* **No Root Terminal / Restricted SSH:** Execution of long-running daemons or sudo commands is prohibited.
* **Strict CPU & Memory Quotas:** CPU usage exceeding 1-2% triggers automatic process throttling or 503 Service Unavailable errors.
* **Apache ModSecurity Strict Filtering:** Standard JSON AJAX payloads or non-standard query parameters can trigger ModSecurity rule blocking.
* **Symlink Restrictions:** Creation of shell symlinks (`php artisan storage:link`) often fails due to permissions or missing shell functions.

**How Souly Overcomes These Constraints:**
1. **CPU Sub-1% Optimization:** Static compilation of routes, views, and configurations (`php artisan souly:deploy`), paired with client-side caching in `IndexedDB`.
2. **Web-Based Artisan Runners:** Protected web endpoints enabling full migrations and cache resets without terminal access.
3. **ModSecurity-Safe Endpoints:** Clean REST routing and parameterized endpoints that comply with Apache security filters.

---

## 2. Automated Lonex Deployment Command (`souly:deploy`)

The platform includes a dedicated Artisan console command tailored for Lonex:

```bash
php artisan souly:deploy
```

### What `souly:deploy` Executes in 7 Phases:
1. **Clear Stale Caches:** Runs `optimize:clear` to avoid outdated schema or config reflections.
2. **Database Migrations:** Executes `php artisan migrate --force` ensuring schema integrity.
3. **Catalog & Vault Seeders:** Idempotently seeds the 6 core services, 28 canned responses, BrandERP catalog, and Hardware equipment.
4. **Public Storage Symlink:** Detects whether `public/storage` exists; safely creates symlink or falls back to copy structure.
5. **Config & Route Compilation:** Compiles configurations and route definitions for zero runtime file-system disk overhead.
6. **Blade View Pre-compilation:** Caches all Blade views in `storage/framework/views` eliminating real-time view parsing.
7. **Catalog Warming:** Warms the IndexedDB cache key `souly_catalog_v2` in server cache for 24 hours.

---

## 3. Web-Based Zero-Terminal Deployment Endpoints

For scenarios where SSH terminal access is unavailable, two authenticated web endpoints are available:

### 3.1 Web Deployment Runner (`/run-souly-deploy-now`)
* **URL:** `https://soulyeg.online/run-souly-deploy-now?token=souly_deploy_2026`
* **Controller:** `App\Http\Controllers\DeployController@run`
* **Security:** Guarded by `DEPLOY_SECRET` environment variable or active `admin` session.
* **Executes:** Cache clearing, migrations, seeders, storage link check, view compilation, and returns an HTML telemetry log.

### 3.2 Web Artisan Command Runner (`/run-souly-artisan`)
* **URL:** `https://soulyeg.online/run-souly-artisan?token=souly_deploy_2026`
* **Purpose:** Triggers database migrations, storage linking, and cache resets on demand.
* **Output:** Displays an obsidian Cyber HUD terminal interface showing execution times and command outputs in milliseconds.

---

## 4. Web Server `.htaccess` Hardening & Caching

The repository includes both root `.htaccess` and `public/.htaccess` files configured for optimal Apache performance on Lonex:

```apache
# Force HTTPS & Canonical Host
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Gzip / Brotli Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain text/html text/xml text/css application/xml application/xhtml+xml application/javascript application/json image/svg+xml
</IfModule>

# Browser Caching Headers
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

# Security Hardening
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

---

## 5. PWA & Service Worker Engine

Souly operates as an installable Progressive Web App (PWA) on iOS and Android devices:

* **Manifest File (`public/manifest.json`):** Defines standalone app display, dark theme color (`#05050A`), Cairo font localization, and 512x512 cyber-falcon icons.
* **Service Worker (`public/sw.js`):** Intercepts network calls, pre-caches offline assets (falcon SVG emblem, CSS tokens, loader scripts), and displays an offline fallback card if internet connectivity drops.
* **iOS Safari Install Prompt:** Detects iOS Safari browsers and renders a custom 3-step modal guiding visitors to tap "Share" and "Add to Home Screen".
