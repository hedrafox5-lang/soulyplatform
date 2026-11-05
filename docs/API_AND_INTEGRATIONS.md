# Souly Platform — API Reference & Integrations

> **Protocol:** RESTful JSON over HTTPS  
> **Base URL:** `https://soulyeg.online`  
> **Headers:** `Accept: application/json`, `X-CSRF-TOKEN: {token}` (for browser POST/PUT/DELETE requests)  

---

## 1. Public AI Conversational API

### 1.1 Chat with Souly AI
* **Endpoint:** `POST /api/ai/chat`
* **Controller:** `App\Http\Controllers\AiAgentController@chat`
* **Purpose:** Handles visitor questions, searches the Pinecone vector knowledge base, suggests relevant packages, and detects Egyptian phone numbers for lead generation.

#### Request Payload:
```json
{
  "message": "عايز أعرف تفاصيل كاشير BrandERP وهل ينفع لمحل ملابس؟ رقمي 01012345678",
  "session_uuid": "c3b88950-84a2-4a0b-9c59-efd5377f0a1c"
}
```

#### Response (200 OK):
```json
{
  "status": "success",
  "reply": "أهلاً بك! نعم، نظام BrandERP يدعم محلات الملابس بشكل كامل مع إدارة المقاسات والألوان والباركود والربط المعتمد مع الفاتورة والإيصال الإلكتروني. تبدأ النواة الأساسية بـ 2,000 ج.م فقط.\n\nتم تسجيل رقم هاتفك بنجاح وسيتواصل معك مهندس المبيعات لترتيب العرض التوضيحي.",
  "session_uuid": "c3b88950-84a2-4a0b-9c59-efd5377f0a1c",
  "suggested_packages": [
    {
      "name": "النواة الأساسية لكاشير ونقاط البيع",
      "price": "2,000 ج.م",
      "slug": "starter",
      "url": "/services/brand-erp/starter"
    }
  ],
  "lead_captured": true
}
```

### 1.2 Fetch Chat Session History
* **Endpoint:** `GET /api/ai/history/{sessionUuid?}`
* **Response (200 OK):**
```json
{
  "status": "success",
  "messages": [
    {
      "sender": "visitor",
      "content": "عايز أعرف تفاصيل كاشير BrandERP",
      "created_at": "2026-11-05T10:15:00Z"
    },
    {
      "sender": "agent",
      "content": "أهلاً بك! نظام BrandERP يدعم...",
      "created_at": "2026-11-05T10:15:02Z"
    }
  ]
}
```

---

## 2. 0ms Catalog Icons API

* **Endpoint:** `GET /api/catalog/icons`
* **Purpose:** Provides a lightweight JSON feed of all 130+ service icons and interactive feature bullets to hydrate client-side `IndexedDB` on the visitor's first visit. Cached on the server for 24 hours.

#### Response (200 OK):
```json
{
  "status": "success",
  "count": 130,
  "icons": [
    {
      "id": 1,
      "service_id": 1,
      "service_name": "نظام BrandERP المتكامل",
      "service_slug": "brand-erp",
      "group_type": "feature",
      "title": "كاشير أوفلاين ذكي",
      "icon_name": "cashier-pos",
      "badge_text": "فائق السرعة",
      "popup_title": "نظام الكاشير الميداني فائق السرعة",
      "popup_summary": "يعمل بكفاءة تامة حتى عند انقطاع الإنترنت مع مزامنة سحابية لحظية فور عودة الاتصال.",
      "popup_bullets_json": [
        "إصدار فواتير بيع خلال أقل من ثانيتين",
        "دعم قارئ الباركود والميزان الإلكتروني",
        "تخزين مؤقت محلي مشفر ومحمي من الفقدان"
      ],
      "popup_cta_text": "استعراض باقات BrandERP"
    }
  ]
}
```

---

## 3. Universal 1-Tap Express Mobile Booking

* **Endpoint:** `POST /express-book`
* **Controller:** `App\Http\Controllers\ExpressBookingController@store`
* **Purpose:** Allows guests or authenticated clients to book any service or package in 1 click without long form filling. If the visitor is a guest, a client account and wallet are auto-provisioned.

#### Request Payload:
```json
{
  "name": "كريم عبد العزيز",
  "phone": "01099887766",
  "service_id": 4,
  "service_package_id": 1,
  "address": "المعادي - القاهرة",
  "notes": "صيانة عاجلة للابتوب ماك بوك برو"
}
```

#### Response (200 OK / Redirect):
* Auto-creates booking #`BK-XXXX`
* Assigns booking to `initial_contact` pipeline
* Generates user credentials and auto-authenticates session
* Redirects client to `/portal/bookings`

---

## 4. Public QR Invoice & Warranty Verification

* **Endpoint:** `GET /invoice/verify/{hash_id}`
* **Controller:** `App\Http\Controllers\ClientInvoiceController@verifyPublic`
* **Purpose:** Public-facing authenticity check accessible by scanning the QR code printed on official Souly invoices and warranty certificates.

#### URL Example:
`https://soulyeg.online/invoice/verify/e8f9a2b1c4d7e6`

#### View Rendered:
Renders `resources/views/partials/public-invoice-card.blade.php` displaying:
* Digital Signature Verification Status: `🟢 موثق ومعتمد رسمياً من سولي للتكنولوجيا`
* Invoice Number & Date
* Client Name & Service Sector
* Total Paid (EGP) & Payment Method
* Official Engineering Warranty Duration & Expiry Date

---

## 5. Automated Payment Webhooks

### 5.1 Paymob Webhook Listener
* **Endpoint:** `POST /api/paymob/webhook`
* **Controller:** `App\Http\Controllers\PaymentWebhookController@handleCallback`

#### HMAC Verification:
* Calculated over concatenated payload fields using `hash_hmac('sha512', $data, config('services.paymob.hmac_secret'))`.
* If signature matches:
  * Marks transaction as `approved` in `wallet_transactions`.
  * Increments balance in `wallets`.
  * If linked to a hardware order or booking, updates status to `paid` and triggers engineering dispatch notifications.
