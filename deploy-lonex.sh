#!/bin/bash
# =========================================================================
# SOULY PLATFORM - LONEX AUTOMATED PRODUCTION DEPLOYMENT SCRIPT
# Target: /www/soulyeg.online (Lonex Control Panel, PHP 8.3 CLI)
# =========================================================================

set -e

echo "🦅 Starting Souly Platform Automated Deployment on Lonex..."

# 1. Determine PHP binary (Lonex default or standard)
PHP_BIN=$(which php83 || which php8.3 || which php || echo "php")
echo "Using PHP binary: $PHP_BIN"

# 2. Permissions check
echo "Applying secure file and directory permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 3. Run Souly Artisan Deployment Command
echo "Executing php artisan souly:deploy..."
$PHP_BIN artisan souly:deploy

echo "✓ Deployment completed successfully. Your application is live at https://soulyeg.online"
