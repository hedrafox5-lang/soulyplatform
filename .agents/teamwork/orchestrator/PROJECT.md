# Project: SoulyPlatform Cross-Module Error Stabilization

## Architecture
- **Framework**: Laravel 11 / PHP 8.3 / MySQL / Blade / Alpine.js 3.14 / TailwindCSS
- **Module Boundaries**:
  1. `HardwareStore`: Standalone hardware marketplace, order processing, and wallet deduction (`app/Http/Controllers/HardwareStoreController.php`, `resources/views/pages/store/hardware*.blade.php`)
  2. `AiWidget`: Frontend interactive conversational agent (`public/js/souly-ui.js`, `resources/views/layouts/master.blade.php`, `resources/views/partials/souly-ai-widget.blade.php`)
  3. `ClientPortal`: Client management dashboard, service bookings, device tracking, profile updates (`app/Http/Controllers/ClientPortalController.php`, `resources/views/portal/*.blade.php`)
  4. `BrandErp`: Modular ERP catalog, orders, activations, wishlist (`app/Http/Controllers/ErpStoreController.php`, `erp_order_items`, `erp_orders`, `erp_store_catalog`)
  5. `DevOps & Delivery`: Git repository branch tracking (`main`), automated test suite verification, and push to origin

## Feature Inventory
| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | R1: Hardware Store Checkout Validation | Fix 422 error on `POST /store/hardware/checkout`, alias customer name/phone, support guest & auth users, validate payment methods, fix order item & wallet transaction creation | M1 | Survey (explorer_survey_1) |
| 2 | R2: Alpine AI Widget Initialization | Fix `soulyAiWidgetController is not defined` & undefined variables (`isOpen`, `visitorName`, `welcomeMessage`, `messages`, `isTyping`, `inputMessage`), register on Alpine.data, align event listener | M2 | Survey (explorer_survey_2) |
| 3 | R3: Portal Bookings View Error Fix | Fix `Undefined variable $services` in `portal/bookings.blade.php:8` by supplying active services and wallet from `ClientPortalController@bookings`, fix `/portal/devices` 500 & `/portal/profile` 404 | M3 | Survey (explorer_survey_3 & followup) |
| 4 | R4: BrandERP Store Query Schema Alignment | Fix `Unknown column 'erp_order_items.erp_catalog_item_id'` in `ErpStoreController.php:69` (`order_id` and `catalog_item_id`), fix table name `erp_store_catalog`, fix order item insertion & wishlist | M4 | Survey (explorer_survey_3) |
| 5 | R5: Integration Verification & Git Delivery | Verify all fixes via PHPUnit/Artisan tests and Tinker, stage project files, commit with clear message, and push to `origin/main` | M5 | Survey (explorer_survey_3) |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1: Hardware Store Checkout Fix | `app/Http/Controllers/HardwareStoreController.php` | none | IN_PROGRESS |
| 2 | M2: Alpine AI Widget Error Fix | `public/js/souly-ui.js`, `resources/views/layouts/master.blade.php`, `resources/views/partials/souly-ai-widget.blade.php` | none | PLANNED |
| 3 | M3: Portal Bookings & Portal Routes Fix | `app/Http/Controllers/ClientPortalController.php`, `resources/views/portal/bookings.blade.php`, `resources/views/portal/devices.blade.php`, `routes/web.php` | none | PLANNED |
| 4 | M4: BrandERP Store Query & Schema Fix | `app/Http/Controllers/ErpStoreController.php` | none | PLANNED |
| 5 | M5: Verification, Git Commit & Push | Run test suites, stage source files, commit and push to `origin/main` | M1, M2, M3, M4 | PLANNED |

## Interface Contracts
### Client ↔ HardwareStoreController
- Endpoint: `POST /store/hardware/checkout`
- Payload: accepts `{ items: [{id, quantity}], customer_name?, name?, customer_phone?, phone?, shipping_address, payment_method, ... }`
- Guest behavior: `customer_name` / `customer_phone` required
- Auth behavior: if user is authenticated, automatically populates from `Auth::user()` if omitted
- Response: `{ success: true, message: string, order_number: string, status: string, redirect: string }`

### Alpine ↔ soulyAiWidgetController
- Global exposure: `window.soulyAiWidgetController` defined as constructor function before Alpine initializes
- Alpine registration: `Alpine.data('soulyAiWidgetController', window.soulyAiWidgetController)`
- Public reactive properties: `isOpen: boolean`, `isTyping: boolean`, `inputMessage: string`, `visitorName: string`, `welcomeMessage: string`, `messages: Array<{sender, content}>`
- Window event listeners: `@open-souly-ai.window="openWidget()"`, `@close-souly-ai.window="closeWidget()"`, `@toggle-souly-ai.window="toggleWidget()"`

### ClientPortalController ↔ portal/bookings.blade.php
- Variables required in view:
  - `$user`: `App\Models\User`
  - `$wallet`: `App\Models\Wallet`
  - `$bookings`: `LengthAwarePaginator<App\Models\Booking>`
  - `$services`: `Collection<App\Models\Service>` with active `packages`
  - `$totalCount`: `int`

### ErpStoreController ↔ Database Tables
- Table `erp_order_items`: columns `id`, `order_id`, `catalog_item_id`, `item_code`, `item_name`, `tier`, `price`, `created_at`, `updated_at`
- Table `erp_orders`: columns `id`, `user_id`, `order_number`, `total_price`, `status`, `is_trial`, `created_at`, `updated_at`
- Table `erp_store_catalog`: columns `id`, `slug`, `code`, `name_ar`, `name_en`, `tier`, `price`, ...
- Table `erp_wishlists`: columns `id`, `user_id`, `catalog_item_id`, `created_at`, `updated_at`

## Code Layout
- Controllers:
  - `app/Http/Controllers/HardwareStoreController.php` (Owned by M1)
  - `app/Http/Controllers/ClientPortalController.php` (Owned by M3)
  - `app/Http/Controllers/ErpStoreController.php` (Owned by M4)
- Views:
  - `resources/views/partials/souly-ai-widget.blade.php` (Owned by M2)
  - `resources/views/layouts/master.blade.php` (Owned by M2)
  - `resources/views/portal/bookings.blade.php` (Owned by M3)
  - `resources/views/portal/devices.blade.php` (Owned by M3)
- Assets:
  - `public/js/souly-ui.js` (Owned by M2)
- Routes:
  - `routes/web.php` (Owned by M3)
