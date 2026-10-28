# spec.md: technical and page-level specification

Companion files: `context.md` (background), `pdr.md` (what and why), `instructions.md` (rules). This file is the how. Where it says "verify", check with the host or the package docs; do not assume.

---

## 1. Architecture

- Monolith: Laravel application serving the storefront (Blade + Livewire + Alpine) and the Filament admin at `/admin`
- Domain layer in `app/Domain/{Catalog,Cart,Checkout,Orders,Payments,Content,Shipping}` with `Actions` and `DTOs`; controllers and Livewire components stay thin
- Queue: `database` driver, processed by cron (`* * * * * php artisan schedule:run`, plus `queue:work --stop-when-empty --max-time=55`)
- Storage: `public` disk for product images (processed variants), `private` disk for documents and invoices, served through signed URLs
- External: BTCPay Server via Greenfield API; SMTP for mail

## 2. Data model (money is integer øre, never float)

| Table | Key fields |
|---|---|
| `categories` | id, parent_id, slug (translatable), name (translatable), description (translatable), image, sort, is_active |
| `products` | id, category_id, slug, name, summary, description (all translatable), spec_json, vat_rate, compliance_status (`draft`/`approved`), compliance_confirmed_by, compliance_confirmed_at, is_published, seo_title, seo_description (translatable) |
| `product_variants` | id, product_id, sku (unique), label (translatable), price_ore, compare_at_ore (nullable), weight_g, stock, track_stock, attributes (json: volume_ml, material, glass_colour, pack_size) |
| `media` | via media library: gallery images with alt text per locale |
| `product_documents` | id, product_id, type (`datasheet`/`conformance`/`other`), title (translatable), file_path (private), is_public |
| `customers` | id, email, name, phone, company, org_number (nullable), locale |
| `addresses` | id, customer_id (nullable), order_id (nullable), name, line1, line2, postcode, city, country (`NO`) |
| `carts` / `cart_items` | id, token, locale, variant_id, quantity, expires_at |
| `orders` | id, number (human, non-sequential-looking), public_token (random 40+ chars), status, customer snapshot, billing/shipping address snapshot, subtotal_ore, discount_ore, shipping_ore, vat_ore, total_ore, locale, tracking_number, carrier, notes_internal, placed_at |
| `order_items` | order_id, variant_id, sku, name snapshot, unit_price_ore, vat_rate, quantity |
| `payments` | id, order_id, provider (`btcpay`), invoice_id, status, amount_ore, btc_amount (string), rate, expires_at, raw_last_event (json), paid_at |
| `webhook_events` | id, provider, event_id (unique), payload hash, processed_at |
| `discounts` | code, type (`percent`/`fixed`), value, min_subtotal_ore, starts_at, ends_at, usage_limit, used |
| `shipping_rates` | name, min_weight_g, max_weight_g, price_ore, free_over_ore, is_active |
| `settings` | key/value, with encrypted values for secrets |
| `pages` / `content_blocks` | slug, locale fields, body (rich text, sanitised), is_legal |

Indexes: `products(is_published, category_id)`, `product_variants(product_id, stock)`, `orders(status, placed_at)`, `payments(invoice_id)` unique, `webhook_events(event_id)` unique.

## 3. Order and payment state machine

```
cart ──► pending_payment ──► paid ──► processing ──► shipped ──► completed
               │                         │
               ├──► expired               └──► cancelled / refunded (manual, admin only)
               └──► underpaid (awaiting top-up or manual resolve)
```

Rules:
- Stock is **reserved** when the order enters `pending_payment` and released on `expired`/`cancelled`. Reservation uses a transaction with row locks to prevent overselling.
- An order moves to `paid` only after the server re-fetches the BTCPay invoice and sees the settled status. The webhook alone is never trusted.
- Webhook handling: verify HMAC from the `BTCPay-Sig` header against the raw body, dedupe by `event_id`, process in a queued job, respond 200 quickly. Handlers are idempotent.
- Totals are recomputed server-side at order creation. Client-submitted prices are ignored.
- Every status change writes to the activity log and sends the correct email.

## 4. Checkout flow (one page, guest by default)

1. Cart drawer → "Gå til kassen"
2. Single page: contact (email, phone), shipping address (Norwegian postcode validation), delivery method, optional company and org number, discount code, order summary, terms and withdrawal acknowledgement (required checkbox, links to terms)
3. Submit creates the order (`pending_payment`), reserves stock, creates a BTCPay invoice with `orderId`, redirect URL to the order status page and a webhook
4. Payment step on our own page: BTC amount, NOK total, QR and address through the BTCPay-hosted checkout or embedded modal (choose hosted checkout for the first release because it is safest), countdown to rate expiry, plain explanation of underpayment and expiry
5. Return to `/{locale}/order/{public_token}` showing live status (Livewire polling every 10 s while pending, then stops)
6. Emails: received, paid, shipped (tracking link), expired (with a "try again" link that recreates an invoice if stock is still available)

Failure copy must state what happened and the next action (e.g. "Betalingen utløp. Varene er frigjort. Legg bestillingen på nytt").

## 5. Admin panel (Filament)

### 5.1 Resources
Products (with variants repeater, media, documents, translations tabs nb/en, SEO tab, compliance tab), Categories (tree), Orders, Payments (read-only), Customers, Discounts, Shipping rates, Pages, Content blocks, Users, Roles, Activity log, Settings.

### 5.2 Order screen
Header: number, status badge, total, payment state. Body: items, addresses, timeline. Actions: add internal note, enter tracking number and carrier, **Mark as shipped** (sends email), cancel (with stock release), refund note. Filters: needs shipping, awaiting payment, expired, last 30 days.

### 5.3 Dashboard widgets
Orders to ship, awaiting payment, expired this week, low stock (≤ threshold), revenue 30 days, top products. No vanity charts.

### 5.4 Compliance gate
- Publishing requires `compliance_status = approved` and the confirmation checkbox, which records user and time
- On save, a **forbidden-terms check** scans translatable name, summary, description and spec fields against a maintained list in `config/compliance.php` (medicinal and active-compound names, administration and dosing wording, injection equipment terms, human/animal-use claims, in both nb and en). A match blocks publishing and shows which term and field
- Products in category "Water and diluents" with a sterile or bacteriostatic attribute are forced to `draft` until the setting `compliance.sterile_water_cleared` is switched on by an admin
- The list lives in config so the client can request additions; the agent must not seed it with the original catalog's product names in any visible file other than this config

### 5.5 Security in admin
Mandatory 2FA, roles (`owner`, `manager`, `fulfilment`), `fulfilment` cannot edit products or settings, session timeout, login throttling, activity log on all model changes.

## 6. Storefront routes (prefix `/no`, `/en`)

| Route | Page |
|---|---|
| `/` | Home |
| `/butikk` (nb) / `/shop` (en) | All products with filters |
| `/butikk/{category}` | Category |
| `/produkt/{slug}` / `/product/{slug}` | Product detail |
| `/handlekurv` / `/cart` | Cart |
| `/kasse` / `/checkout` | Checkout |
| `/ordre/{token}` / `/order/{token}` | Order status |
| `/sok` / `/search` | Search results |
| `/kontakt` / `/contact` | Contact |
| `/om-oss`, `/frakt-og-retur`, `/vilkar`, `/personvern`, `/informasjonskapsler`, `/angrerett` (+ en equivalents) | Content pages |

Other: `sitemap.xml`, `robots.txt`, structured data (Product, Offer in NOK, BreadcrumbList, Organization), `hreflang`, canonical, OpenGraph images.

## 7. Design tokens (implement as CSS custom properties, mapped into Tailwind)

```css
:root {
  --glacier: #8CCBEB;        /* brand light blue */
  --glacier-mist: #DCEFF9;
  --fjord-ink: #0F2B3D;      /* text */
  --deep-water: #0C2433;     /* hero stage, footer */
  --frost: #F1F7FA;          /* page surface */
  --borosilicate: #C9DAE3;   /* borders */
  --tide: #2A769F;           /* links, small interactive text */
  --amber-glass: #C27A2C;    /* low-stock, amber vial cues */
  --success: #2F7D5B;
  --error: #B3412F;

  --radius-input: 2px;
  --radius-media: 10px;
  --radius-pill: 999px;

  --shadow-overlay: 0 12px 32px -8px rgb(15 43 61 / .28), 0 2px 6px rgb(15 43 61 / .12);

  --step--1: clamp(.83rem, .8rem + .15vw, .9rem);
  --step-0: clamp(1rem, .95rem + .25vw, 1.125rem);
  --step-1: clamp(1.25rem, 1.15rem + .5vw, 1.5rem);
  --step-2: clamp(1.56rem, 1.35rem + 1vw, 2.1rem);
  --step-3: clamp(1.95rem, 1.5rem + 2vw, 3rem);
  --step-4: clamp(2.4rem, 1.6rem + 4vw, 4.8rem);

  --ease-out: cubic-bezier(.2, .7, .2, 1);
  --dur-fast: 160ms;
  --dur-base: 240ms;
}
```

Fonts: Gambetta (display), Switzer (text), self-hosted WOFF2, subset Latin + æøåÆØÅ, `font-display: swap`, `size-adjust` fallback metrics. Enable `font-variant-numeric: tabular-nums` on prices and spec tables. Lot codes only may use a monospace face.

Spacing on a 4 px base; layout container 1280 px max with a 12-column grid; product media uses fixed aspect ratios (4:5 on the gallery, 1:1 in listings).

## 8. Page specifications

### 8.1 Home
Wireframe:
```
┌──────────────────────────────────────────────┐
│ header: logo · search · nb|en · cart (3)     │
├──────────────────────────────────────────────┤
│ DEEP WATER stage: silent film loop of vials  │
│ catching light. Headline (Gambetta, left),   │
│ one sentence, one primary button             │
│ "Se produkter".  Film reveals once on load.  │
├──────────────────────────────────────────────┤
│ Categories: asymmetric tiles (one large,     │
│ two small), real photos, not equal cards     │
├──────────────────────────────────────────────┤
│ Pinned scroll sequence: one featured vial    │
│ rotates/refracts as the user scrolls; spec   │
│ callouts attach to the glass (3 to 4 beats)  │
├──────────────────────────────────────────────┤
│ Featured products (variable grid rhythm)     │
├──────────────────────────────────────────────┤
│ Documentation · delivery · payment, in short │
│ plain rows (not icon cards)                  │
├──────────────────────────────────────────────┤
│ footer on Deep Water: company details, legal │
└──────────────────────────────────────────────┘
```
Hero headline is written from the real catalog (e.g. says what the shop sells and for whom). The film has a poster, `prefers-reduced-motion` shows the poster only, and mobile loads a smaller file or poster.

### 8.2 Shop and category
- Left filter rail on desktop (volume, material, glass colour, pack size, in stock), bottom-sheet filters on mobile
- Active filters shown as removable pills; result count; sort (relevance, price, newest)
- Listing tiles show image, name, key spec (volume and material), price from, stock cue (amber only for low stock)
- Filters update through Livewire without a full reload; URL reflects state; empty state suggests removing a specific filter

### 8.3 Product detail
```
┌─────────────────────┬────────────────────────┐
│ gallery (4:5), zoom │ name, price incl. MVA  │
│ thumbnails          │ variant selectors      │
│                     │ stock · delivery note  │
│                     │ quantity · Add to cart │
├─────────────────────┴────────────────────────┤
│ spec table (tabular numerals) · documents    │
│ downloads · description · related products   │
└──────────────────────────────────────────────┘
```
Sticky add-to-cart bar on mobile after the main button leaves the viewport. Adding to cart opens the drawer with a clear confirmation. Out of stock shows a plain notice and a contact link.

### 8.4 Cart drawer and page
Slide-over from the right (240 ms). Editable quantity, remove, subtotal, shipping estimate or threshold progress, discount code field, "Gå til kassen". Keyboard accessible, focus trapped while open, `Esc` closes.

### 8.5 Checkout and order status
As in §4. Single column on mobile; two columns on desktop (form left, sticky summary right). Inline validation, accessible error summary, no layout jump.

### 8.6 Contact
Form with honeypot, rate limiting and a privacy notice; shows the client's email and company details from settings.

## 9. Components inventory (build once, reuse)

Button (primary on Glacier with Fjord Ink text, secondary outlined, quiet text), input, select, quantity stepper, price (with compare-at), stock badge, product tile (standard and featured), spec table, gallery with zoom, filter group, active-filter pill, drawer, modal, toast (for confirmations of user actions), breadcrumb, language switch, cookie consent panel, empty state, error summary, order timeline, countdown, skeleton loaders.

## 10. Internationalisation

`nb` default, `en` second. Translatable model fields via a translatable package. Locale prefix in URL; users get the nb site by default, with a remembered choice (cookie only after consent, otherwise the URL is the source of truth). Format money with `Intl`/Laravel `Number` helpers using locale `nb_NO`.

## 11. SEO and content

Unique title and meta per product and category; descriptions written from the facts of the product (material, volume, dimensions, compatibility). Product schema with price in NOK and availability. XML sitemap per locale. Clean URLs, 301 redirects when slugs change. Image alt text required in admin for both locales.

## 12. Security and privacy implementation

Everything in `instructions.md` §6, plus:
- CSP header with nonces via middleware; `frame-ancestors 'none'`; `Referrer-Policy: strict-origin-when-cross-origin`; `Permissions-Policy` locked down
- Rate limits: login 5/min, checkout 10/min per IP, contact 3/10 min, search 30/min
- Admin access optionally restricted by IP or additional challenge if the client wants it
- Backups: nightly database and uploaded files, 14-day rotation, restore tested once before launch
- Data retention: contact messages 12 months; unpaid expired orders anonymised after a set period; configurable
- Account/data requests handled manually through the admin with an export action

## 13. Testing plan

- Unit: money and VAT calculation, discount rules, shipping rate selection, stock reservation
- Feature: catalog filters, cart operations, checkout validation, order creation, webhook verification and idempotency, status transitions, compliance gate and forbidden-terms check, localisation routes
- Browser (Pest + Playwright or Dusk): home to paid-order happy path with a faked BTCPay, mobile drawer, keyboard-only checkout
- Accessibility: automated axe pass plus manual keyboard and screen-reader spot checks
- Performance: Lighthouse CI against budgets in `instructions.md` §7

## 14. Photography and film brief (for the client or a photographer)

- Surface: matte pale-blue or white acrylic, clean bench, no clutter
- Light: one large soft source from the side plus a strip light for glass edges; capture refraction and highlights
- Shots per product: front, 3/4, top of cap, scale shot with a hand or ruler, and a pack shot
- Film: 4 to 6 s silent loop, 1920×1080, slow slide across a rack of vials; deliver a still frame as poster
- Feature product for the scroll sequence: 36 to 72 frames rotating on a turntable on a clean background, or a short clip
- File delivery: originals plus web-ready AVIF/WebP generated by the pipeline

## 15. Deployment

- Environments: local, staging (password-protected, `noindex`), production
- Build assets with Vite before deploy; deploy through Git + CI or SFTP, whichever the host allows
- Steps: `composer install --no-dev -o`, `php artisan migrate --force`, `config:cache`, `route:cache`, `view:cache`, `storage:link`, `queue:restart`
- Point the document root to `public/`; keep `.env` and `storage/` out of the web root
- Production checklist: `APP_DEBUG=false`, HTTPS redirect, strong `APP_KEY`, SMTP verified, BTCPay connected in test then live mode, cron running, backups running, sitemap submitted, legal pages populated, analytics consent working

## 16. Build order (milestones)

1. **Foundation:** Laravel install, tokens, fonts, layout shell, i18n routing, Filament theme in brand colours, auth with 2FA, roles
2. **Catalog:** data model, admin resources, media pipeline, compliance gate, seed realistic laboratory products
3. **Storefront browse:** home (static hero first), shop, category, product detail, search
4. **Cart and checkout:** cart drawer, checkout, order creation, stock reservation
5. **Payments:** BTCPay integration, webhooks, status page, emails, failure paths
6. **Admin operations:** order workflow, shipping, dashboard, settings
7. **Cinematic layer:** hero film, pinned product sequence, final motion polish, reduced-motion fallbacks
8. **Content and legal:** pages, SEO, structured data, cookie consent
9. **Hardening:** security review, performance budgets, accessibility pass, backup restore test
10. **Handover:** `docs/runbook.md`, `docs/open-items.md`, client walkthrough

## 17. Acceptance criteria (summary)

- A guest can go from home to a paid test order in under two minutes on a phone
- The client can add a product with variants, photos, a datasheet and both languages without developer help
- A webhook sent twice produces one state change; a tampered price is rejected; an expired invoice releases stock
- A product containing a forbidden term cannot be published
- Lighthouse budgets met; WCAG 2.2 AA checks pass; both languages complete
- Screenshot review shows none of the banned patterns in `instructions.md` §3
