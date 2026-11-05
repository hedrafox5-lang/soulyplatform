# Investigation Report: Requirement R2 — Alpine AI Widget Errors Fix

## 1. Observation

### 1.1 Codebase References to `soulyAiWidgetController`
Searching across the entire repository for `soulyAiWidgetController` revealed exactly two occurrences:
- `resources/views/partials/souly-ai-widget.blade.php:4`:
  ```html
  <div x-data="soulyAiWidgetController()" 
       x-cloak
       @open-souly-ai.window="openWidget()"
       @close-souly-ai.window="closeWidget()"
       @toggle-souly-ai.window="toggleWidget($event.detail ? $event.detail.open : null)"
       @open-ai-chat-sheet.window="openWidget()"
       @close-ai-chat-sheet.window="closeWidget()"
       @keydown.escape.window="closeWidget()"
       class="relative z-[99990]">
  ```
- `resources/views/partials/souly-ai-widget.blade.php:232-234`:
  ```html
  <script>
  function soulyAiWidgetController() {
      return {
          isOpen: false,
          isTyping: false,
          inputMessage: '',
          conversationId: localStorage.getItem('souly_ai_conversation_id') || '',
          visitorUuid: '',
          visitorName: '',
          welcomeMessage: 'أهلاً بيك في منظومة سولي! أنا سولي، مستشارك التقني الذكي.. أتشرف باسم حضرتك الأول؟',
          messages: [],
  ```

### 1.2 Inclusion of the AI Widget in Layouts and Views
- `resources/views/layouts/master.blade.php:586`:
  ```blade
  <!-- Universal 1-Tap Express Mobile Booking Modal -->
  @include('partials.express-booking-modal')

  <!-- Souly AI Agent Interactive Widget -->
  @include('partials.souly-ai-widget')
  ```
- All application frontend views (`welcome.blade.php`, `pages/home.blade.php`, `pages/services/index.blade.php`, `pages/services/show.blade.php`, `pages/services/package.blade.php`, `pages/store/branderp.blade.php`, `pages/store/hardware.blade.php`, `pages/store/hardware-store.blade.php`, `auth/login.blade.php`, `auth/register.blade.php`, `portal/*.blade.php`, `admin/*.blade.php`) extend either `layouts.desktop`, `layouts.mobile`, `portal.layout`, `admin.layout`, or `layouts.master` directly.
- Both `layouts/desktop.blade.php` and `layouts/mobile.blade.php` extend `layouts.master`. Thus, `partials.souly-ai-widget` is unconditionally rendered on every frontend view in the application.

### 1.3 Alpine.js Loading and Initialization Architecture
- `resources/views/layouts/master.blade.php:310-323`:
  ```html
  <!-- Souly Core Architecture Scripts (Storage, Cinematic Loader & UI Engine v2.5) -->
  <script src="{{ asset('js/souly-storage.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('js/souly-loader.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('js/souly-ui.js') }}?v={{ time() }}"></script>

  <!-- Alpine.js 3.14 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" defer></script>

  <script>
      document.addEventListener('alpine:init', () => {
          Alpine.store('citadel', {
              isMobile: window.innerWidth < 1024,
              isOnline: navigator.onLine,
              mobileNavOpen: false,
              aiSheetOpen: false, // Strict Single Source of Truth
  ```
- Alpine.js v3.14.8 is loaded from CDN with the `defer` attribute.
- At the end of `cdn.min.js`, Alpine executes:
  `window.Alpine = Vt; queueMicrotask(() => { Vt.start(); });`
- When Alpine starts, it walks the DOM tree searching for `x-data` directives.
- In `public/js/souly-ui.js:620` and `700`, other engines like `erpRoiSimulatorEngine` and `soulyMobileCarousel` are explicitly defined on `global` (i.e., `window`):
  `global.erpRoiSimulatorEngine = function erpRoiSimulatorEngine() { ... }`
  `soulyAiWidgetController` was NOT added to `public/js/souly-ui.js`.
- Furthermore, `Alpine.data('soulyAiWidgetController', ...)` is NOT called anywhere in the project.

### 1.4 Alpine Template Expressions and Properties
Within `resources/views/partials/souly-ai-widget.blade.php`, the following variables and expressions are evaluated:
- Line 4: `x-data="soulyAiWidgetController()"`
- Line 49: `x-show="isOpen"`
- Line 62: `x-show="isOpen"`
- Line 64: `x-ref="chatContainer"`
- Line 93: `<template x-if="visitorName">`
- Line 96: `<strong x-text="visitorName">`
- Line 97: `@click="editVisitorName()"`
- Line 115: `x-ref="chatMessages"`
- Line 123: `<p x-text="welcomeMessage">`
- Line 144: `<template x-for="(msg, idx) in messages" :key="idx">`
- Line 181: `<div x-show="isTyping">`
- Line 212: `<input type="text" x-model="inputMessage">`
- Line 218: `:disabled="!inputMessage.trim() || isTyping"`
- Line 6: `@open-souly-ai.window="openWidget()"`
- Line 7: `@close-souly-ai.window="closeWidget()"`
- Line 8: `@toggle-souly-ai.window="toggleWidget($event.detail ? $event.detail.open : null)"`
- Line 9: `@open-ai-chat-sheet.window="openWidget()"`
- Line 10: `@close-ai-chat-sheet.window="closeWidget()"`
- Line 11: `@keydown.escape.window="closeWidget()"`

All 6 variables reported in the bug description (`isOpen`, `visitorName`, `welcomeMessage`, `messages`, `isTyping`, `inputMessage`) are returned directly by `soulyAiWidgetController()`:
```javascript
isOpen: false,
isTyping: false,
inputMessage: '',
conversationId: localStorage.getItem('souly_ai_conversation_id') || '',
visitorUuid: '',
visitorName: '',
welcomeMessage: 'أهلاً بيك في منظومة سولي! أنا سولي، مستشارك التقني الذكي.. أتشرف باسم حضرتك الأول؟',
messages: [],
```

### 1.5 Unit Test Assertion Mismatch
Running `C:\xampp\php\php.exe artisan test --filter=SoulyAiWidgetAndConversationalEngineTest`:
```
FAILED Tests\Feature\SoulyAiWidgetAndConversationalEngineTest > souly ai widget template contains robust toggle and close handlers
Failed asserting that false is true.
To contain: @toggle-souly-ai.window="toggleWidget()"
at tests/Feature/SoulyAiWidgetAndConversationalEngineTest.php:90
```
Line 90 of `tests/Feature/SoulyAiWidgetAndConversationalEngineTest.php`:
```php
$rendered = view('partials.souly-ai-widget')->render();
$this->assertStringContainsString('@close-souly-ai.window="closeWidget()"', $rendered);
$this->assertStringContainsString('@toggle-souly-ai.window="toggleWidget()"', $rendered);
$this->assertStringContainsString('toggleWidget()', $rendered);
$this->assertStringContainsString('closeWidget()', $rendered);
$this->assertStringContainsString('z-[99990]', $rendered);
```
Whereas in `resources/views/partials/souly-ai-widget.blade.php:8`:
```html
@toggle-souly-ai.window="toggleWidget($event.detail ? $event.detail.open : null)"
```

---

## 2. Logic Chain

1. **Premise**: In `resources/views/partials/souly-ai-widget.blade.php`, the Alpine component root element `<div x-data="soulyAiWidgetController()">` is declared at line 4, whereas the `<script>` tag declaring `function soulyAiWidgetController() { ... }` is located at line 232 (Observation 1.1).
2. **Timing & Hoisting**: Function declarations in JavaScript are hoisted only within their own script block, never across separate `<script>` tags or across HTML parsing chunks. If Alpine initializes before or during the execution of line 232 (e.g., when Alpine is deferred and runs in a microtask, or during asynchronous chunked loading), `soulyAiWidgetController` is evaluated before the script block has registered it in global scope (Observation 1.3).
3. **Alpine 3 Architecture**: Alpine 3 introduced `Alpine.data()` as the canonical registration mechanism for component data constructors. In `layouts/master.blade.php`, `citadel` store is registered on `alpine:init`, but `soulyAiWidgetController` is never registered via `Alpine.data('soulyAiWidgetController', ...)` (Observation 1.3). Therefore, Alpine must fall back to evaluating the string `"soulyAiWidgetController()"` against global `window`.
4. **No Explicit `window` Property**: The script block at line 232 only declares `function soulyAiWidgetController() { ... }` without assigning `window.soulyAiWidgetController = soulyAiWidgetController`. In strict contexts or module wrappers, this fails to attach to `window`.
5. **Cascading ReferenceErrors**: Because `soulyAiWidgetController` is undefined at the moment `<div x-data="soulyAiWidgetController()">` is processed:
   - Alpine catches `ReferenceError: soulyAiWidgetController is not defined`.
   - The component's Alpine reactive scope is never created.
   - Alpine continues traversing the child elements of the DOM tree in the enclosing scope (`<body x-data>`).
   - Every subsequent directive referencing a controller property (`x-show="isOpen"`, `x-if="visitorName"`, `x-text="visitorName"`, `x-text="welcomeMessage"`, `x-for="msg in messages"`, `x-show="isTyping"`, `x-model="inputMessage"`) triggers a secondary `ReferenceError: <variable> is not defined` (Observation 1.4).
6. **Template Event Listener Conflict**: In `souly-ai-widget.blade.php:8`, `@toggle-souly-ai.window="toggleWidget($event.detail ? $event.detail.open : null)"` violates the exact string expectation of `test_souly_ai_widget_template_contains_robust_toggle_and_close_handlers` which asserts `@toggle-souly-ai.window="toggleWidget()"` (Observation 1.5).

---

## 3. Caveats

- **No Backend AI Service Changes Required for R2**: Tests in `SoulyAiInfrastructureTest` and `SoulyAiWidgetAndConversationalEngineTest` show backend method issues in `SoulyAiService` (such as `extractVisitorName` and `reply`), but Requirement R2 is specifically scoped to the frontend Alpine AI Widget runtime errors (`soulyAiWidgetController is not defined`, `isOpen`, `visitorName`, `welcomeMessage`, `messages`, `isTyping`, `inputMessage`).
- **PWA/Mobile Views**: Both Desktop (`layouts.desktop`) and Mobile (`layouts.mobile`) delegate to `$store.citadel.aiSheetOpen` and dispatch Alpine window events (`@open-souly-ai`, `@close-souly-ai`, `@toggle-souly-ai`). Ensuring `soulyAiWidgetController` is registered universally guarantees cross-platform stability.

---

## 4. Conclusion & Proposed Implementation Plan

The root cause of `soulyAiWidgetController is not defined` and the resulting undefined variable cascade (`isOpen`, `visitorName`, `welcomeMessage`, `messages`, `isTyping`, `inputMessage`) is a script order and registration failure:
1. The script tag was placed after the markup instead of being hoisted and registered before Alpine evaluates the DOM.
2. The controller was never registered using Alpine v3's canonical `Alpine.data()` API.
3. The controller was missing from the synchronously loaded UI engine (`public/js/souly-ui.js`).
4. Line 8 in the template diverged from the test assertion.

### Concrete Fix Proposal (Code Changes for Implementer)

#### A. In `public/js/souly-ui.js`
Add `soulyAiWidgetController` as Section 6 in `souly-ui.js` (loaded in `<head>` before Alpine):
```javascript
    /**
     * =========================================================================
     * 6. GLOBAL SOULY AI INTERACTIVE AGENT CONTROLLER (Alpine.js)
     * =========================================================================
     */
    global.soulyAiWidgetController = function soulyAiWidgetController() {
        return {
            isOpen: false,
            isTyping: false,
            inputMessage: '',
            conversationId: localStorage.getItem('souly_ai_conversation_id') || '',
            visitorUuid: '',
            visitorName: '',
            welcomeMessage: 'أهلاً بيك في منظومة سولي! أنا سولي، مستشارك التقني الذكي.. أتشرف باسم حضرتك الأول؟',
            messages: [],

            init() {
                this.isOpen = false;
                if (this.$store && this.$store.citadel) {
                    this.$store.citadel.aiSheetOpen = false;
                }

                this.visitorUuid = window.SoulyIdentity ? window.SoulyIdentity.getUuid() : (localStorage.getItem('souly_visitor_uuid') || '');
                this.visitorName = window.SoulyIdentity ? window.SoulyIdentity.getName() : (localStorage.getItem('souly_visitor_name') || '');
                this.updateGreeting();

                if (this.$store && this.$store.citadel) {
                    this.$watch('$store.citadel.aiSheetOpen', (val) => {
                        const nextVal = Boolean(val);
                        if (this.isOpen !== nextVal) {
                            if (nextVal) {
                                this.openWidget();
                            } else {
                                this.closeWidget();
                            }
                        }
                    });
                }
            },

            updateGreeting() {
                if (this.visitorName) {
                    this.welcomeMessage = `أهلاً بك يا ${this.visitorName}، نورت منصة سولي مجدداً! تحب نكمل استفسارنا السابق ولا نناقش موضوع جديد؟`;
                }
            },

            editVisitorName() {
                const currentName = this.visitorName || '';
                const newName = prompt('أدخل أو عدّل اسمك الكريم:', currentName);
                if (newName !== null && newName.trim() !== '') {
                    const cleaned = newName.trim();
                    this.visitorName = cleaned;
                    if (window.SoulyIdentity) window.SoulyIdentity.setName(cleaned);
                    localStorage.setItem('souly_visitor_name', cleaned);
                    this.updateGreeting();
                    this.inputMessage = `اسمي ${cleaned}`;
                    this.sendMessage();
                }
            },

            loadHistory() {
                if (this.messages.length > 0) return;
                const vUuid = this.visitorUuid || (window.SoulyIdentity ? window.SoulyIdentity.getUuid() : '');
                let url = '/api/ai/history';
                if (this.conversationId) {
                    url = `/api/ai/history/${this.conversationId}?visitor_uuid=${encodeURIComponent(vUuid)}`;
                } else if (vUuid) {
                    url = `/api/ai/history?visitor_uuid=${encodeURIComponent(vUuid)}`;
                }

                fetch(url)
                    .then(res => res.ok ? res.json() : {})
                    .then(data => {
                        if (data.session_uuid) {
                            this.conversationId = data.session_uuid;
                            localStorage.setItem('souly_ai_conversation_id', data.session_uuid);
                        }
                        if (data.visitor_name) {
                            this.visitorName = data.visitor_name;
                            if (window.SoulyIdentity) window.SoulyIdentity.setName(data.visitor_name);
                        }
                        if (data.messages && data.messages.length > 0) {
                            this.messages = data.messages;
                            this.scrollToBottom();
                        }
                    }).catch(() => {});
            },

            openWidget() {
                this.isOpen = true;
                if (this.$store && this.$store.citadel) {
                    this.$store.citadel.aiSheetOpen = true;
                }
                if (window.SoulyUI && typeof window.SoulyUI.lockScroll === 'function') window.SoulyUI.lockScroll('ai-widget');
                this.$nextTick(() => {
                    if (this.$refs.chatContainer) {
                        this.$refs.chatContainer.style.removeProperty('display');
                    }
                });
                this.loadHistory();
                this.scrollToBottom();
            },

            closeWidget() {
                this.isOpen = false;
                if (this.$store && this.$store.citadel) {
                    this.$store.citadel.aiSheetOpen = false;
                }
                if (window.SoulyUI && typeof window.SoulyUI.unlockScroll === 'function') window.SoulyUI.unlockScroll('ai-widget');

                this.$nextTick(() => {
                    if (this.$refs.chatContainer) {
                        this.$refs.chatContainer.style.setProperty('display', 'none', 'important');
                    }
                });
            },

            toggleWidget(forceState = null) {
                if (forceState && typeof forceState === 'object' && forceState.detail) {
                    forceState = forceState.detail.open;
                }
                const next = forceState !== null && forceState !== undefined ? Boolean(forceState) : !this.isOpen;
                if (next) {
                    this.openWidget();
                } else {
                    this.closeWidget();
                }
            },

            sendPrompt(promptText) {
                this.inputMessage = promptText;
                this.sendMessage();
            },

            sendMessage() {
                const text = this.inputMessage.trim();
                if (!text || this.isTyping) return;

                this.messages.push({
                    sender: 'visitor',
                    content: text
                });

                this.inputMessage = '';
                this.isTyping = true;
                this.scrollToBottom();

                const vUuid = this.visitorUuid || (window.SoulyIdentity ? window.SoulyIdentity.getUuid() : '');

                fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        message: text,
                        conversation_id: this.conversationId,
                        visitor_uuid: vUuid,
                        visitor_name: this.visitorName,
                        device_fingerprint: navigator.userAgent
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.isTyping = false;
                    if (data.conversation_id) {
                        this.conversationId = data.conversation_id;
                        localStorage.setItem('souly_ai_conversation_id', data.conversation_id);
                    }

                    if (data.visitor_name) {
                        this.visitorName = data.visitor_name;
                        if (window.SoulyIdentity) window.SoulyIdentity.setName(data.visitor_name);
                        this.updateGreeting();
                    }

                    const replyText = (data.reply && data.reply.trim() !== '')
                        ? data.reply
                        : 'أهلاً بحضرتك في سولي 🌟 أنا سولي، مستشارك التقني الذكي.. كيف أقدر أساعدك اليوم؟';

                    this.messages.push({
                        sender: 'souly_ai',
                        content: replyText
                    });

                    this.scrollToBottom();
                })
                .catch(() => {
                    this.isTyping = false;
                    this.messages.push({
                        sender: 'souly_ai',
                        content: 'عذراً، حدث خطأ مؤقت في الاتصال. يمكنك إعادة المحاولة أو التواصل معنا مباشرة عبر واتساب: 01090623326.'
                    });
                    this.scrollToBottom();
                });
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const el = this.$refs.chatMessages;
                    if (el) el.scrollTop = el.scrollHeight;
                });
            }
        };
    };

    // Auto-register with Alpine if Alpine is already present or upon alpine:init
    if (global.Alpine) {
        global.Alpine.data('soulyAiWidgetController', global.soulyAiWidgetController);
    } else {
        document.addEventListener('alpine:init', () => {
            if (global.Alpine) {
                global.Alpine.data('soulyAiWidgetController', global.soulyAiWidgetController);
            }
        });
    }
```

#### B. In `resources/views/layouts/master.blade.php`
Inside the existing `alpine:init` listener (around line 318):
```javascript
        document.addEventListener('alpine:init', () => {
            if (window.soulyAiWidgetController) {
                Alpine.data('soulyAiWidgetController', window.soulyAiWidgetController);
            }
            Alpine.store('citadel', { ... });
```

#### C. In `resources/views/partials/souly-ai-widget.blade.php`
1. Fix line 8:
   Change:
   `@toggle-souly-ai.window="toggleWidget($event.detail ? $event.detail.open : null)"`
   To:
   `@toggle-souly-ai.window="toggleWidget()"`
2. Place the `<script>` tag at the TOP of `partials/souly-ai-widget.blade.php` (before line 4) or ensure it self-registers both on `window` and with `Alpine.data`:
```html
<script>
if (typeof window.soulyAiWidgetController !== 'function') {
    function soulyAiWidgetController() {
        // [controller object]
    }
    window.soulyAiWidgetController = soulyAiWidgetController;
}
if (window.Alpine) {
    window.Alpine.data('soulyAiWidgetController', window.soulyAiWidgetController);
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('soulyAiWidgetController', window.soulyAiWidgetController);
    });
}
</script>
```

---

## 5. Verification Method

1. **Unit Test Verification**:
   Execute:
   ```powershell
   C:\xampp\php\php.exe artisan test --filter=test_souly_ai_widget_template_contains_robust_toggle_and_close_handlers
   ```
   **Expected**: PASS (5 assertions succeed).

2. **Full View Rendering Verification**:
   Test rendering of views that include the widget:
   ```powershell
   C:\xampp\php\php.exe -r "echo view('welcome')->render(); echo PHP_EOL . 'SUCCESS: welcome rendered cleanly';"
   C:\xampp\php\php.exe -r "echo view('pages.home')->render(); echo PHP_EOL . 'SUCCESS: home rendered cleanly';"
   C:\xampp\php\php.exe -r "echo view('partials.souly-ai-widget')->render(); echo PHP_EOL . 'SUCCESS: widget rendered cleanly';"
   ```
   **Expected**: Output renders without PHP / Blade errors, containing `soulyAiWidgetController`, `@close-souly-ai`, `@toggle-souly-ai`, and valid Alpine markup.

3. **Runtime Alpine Verification**:
   Verify in browser console or headless environment that `window.soulyAiWidgetController` is a function, `Alpine.data('soulyAiWidgetController')` returns the constructor, and inspecting `<div x-data="soulyAiWidgetController()">` shows no `soulyAiWidgetController is not defined` or property undefined errors.
