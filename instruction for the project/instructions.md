# instructions.md: how the agent must work

You are building a premium Norwegian webshop for laboratory supplies on Laravel. Read `context.md`, `pdr.md` and `spec.md` fully before writing code. If something here conflicts with your habits, follow this file.

---

## 1. Operating principles

1. **Plan, then build.** Before the first component, write a short design plan: tokens (colour, type, spacing, radii), the layout idea for each page in an ASCII wireframe, and the one cinematic moment. Compare it with `pdr.md` §6. If any part is something you would produce for any webshop, change it and note what you changed.
2. **Server is the authority.** Prices, totals, VAT, stock, shipping and discounts are computed on the server. The browser only displays.
3. **Make decisions, don't ask about trivia.** Ask the person only when a missing fact blocks correctness (legal text, company details, BTCPay credentials, hosting access). Record assumptions in `docs/assumptions.md`.
4. **Small, reviewable steps.** One feature per commit, in the order of `spec.md` §16. Each commit passes tests and lint.
5. **Verify visually.** Run the site, take screenshots at 390, 768 and 1440 px, and critique them against the anti-slop list below before calling a page done.

## 2. Stack (fixed unless the person approves a change)

- Latest stable Laravel and PHP supported by the host (verify with the host first; do not assume)
- Filament for the admin panel, fully themed to the brand (the default grey/amber Filament look is not acceptable)
- Blade + Livewire + Alpine.js for the storefront; Tailwind CSS; Vite
- MySQL or MariaDB (whatever the host provides); Redis only if available, otherwise database cache and queue
- GSAP + Lenis only for the hero and the one scroll sequence, loaded on those pages only
- Pest for tests; Laravel Pint for style
- Packages to consider: `spatie/laravel-permission`, `spatie/laravel-activitylog`, `spatie/laravel-medialibrary`, `spatie/laravel-translatable`, `spatie/laravel-sluggable`. Check each is maintained and compatible before installing; justify every dependency in the commit message.

Run `composer audit` and `npm audit` before each milestone.

## 3. Anti-slop rules (hard bans)

If any of these appear in a screenshot or diff, fix it before moving on.

**Fonts**
- No Inter, Roboto, Arial, Helvetica, Poppins, Montserrat, DM Sans, Space Grotesk, Playfair Display, Open Sans, or `system-ui` as the visible face. Use the families in `pdr.md` §6.3, self-hosted with `font-display: swap` and size-matched fallbacks.

**Visual**
- No purple-blue gradients, mesh gradients, floating blobs or decorative orbs
- No glassmorphism panels stacked on everything
- No emoji or clip-art icons. Use one consistent icon set (a single stroke weight) or custom SVG
- No row of three identical rounded cards with an icon circle on top
- No one radius and one grey shadow applied to every element
- No fade-and-slide-up on every section, no hover lift on every card
- No stock imagery, no AI-generated product photos
- No fake reviews, counters, "trusted by", urgency timers, or invented badges

**Typographic tells**
- Don't accent a single word of a headline with italics or colour
- Don't put a tracked-out ALL-CAPS eyebrow above every heading
- Don't build labels as `WORD — fragment`, join meta strings with middle dots, or append `→` to buttons
- Don't number things that aren't a sequence
- Don't use a monospace face for ordinary small labels (lot codes only)

**Copy**
- No lorem ipsum, no "Welcome to", "Elevate", "Seamless", "Unlock", "Revolutionize"
- No exclamation marks in UI text

**Code**
- No Laravel welcome page, Breeze or Jetstream default look left in place
- No inline styles, no magic numbers (use tokens), no `dd()`/`dump()`/`console.log` left in
- No business logic in Blade or controllers; use actions/services

## 4. Design rules to apply

- Light blue (Glacier) is a surface and light colour, never text on a light background.
- Left-align text by default. Keep body lines under 75 characters.
- Use the named palette and scale from `spec.md` §7 only. Add a token before adding a one-off value.
- Boldness lives in the hero film and one pinned scroll sequence. Every other screen is calm and fast.
- Motion either answers a user action or happens once. Support `prefers-reduced-motion`.
- Quality floor on every page: responsive from 360 px, visible keyboard focus, labelled form controls, 44 px touch targets, WCAG 2.2 AA contrast, meaningful alt text in both languages.

## 5. Compliance rules (non-negotiable)

- The shop sells laboratory supplies only. Never add, seed or write copy for peptides, drugs, medicinal products, injection syringes, needles or pens, or any dosing, administration, human-use or animal-use wording. This applies to seeders, tests, demo data and placeholder text.
- Implement the compliance gate and forbidden-terms check in `spec.md` §5.4. Do not remove or bypass them.
- Seed data uses plausible laboratory items (e.g. 2 ml clear glass vial, 20 mm crimp cap, aluminium crimp seal, vial rack). Bacteriostatic water is seeded as unpublished and flagged for review.
- Do not invent legal text, company numbers, certifications or test claims. Use clearly marked pending fields and list them in `docs/open-items.md`.

## 6. Security checklist (verify each, don't assume)

- HTTPS only, HSTS, secure and `SameSite` cookies, session regeneration on login
- CSRF on every state-changing route; mass-assignment protected with explicit `$fillable`
- Policies on every model; admin users need 2FA; rate limit login, checkout, contact and search
- Validate with Form Requests; uploads restricted by MIME and size, re-encoded images, stored outside the web root or on a private disk with signed URLs for documents
- Strict Content-Security-Policy with nonces; no inline scripts without a nonce
- BTCPay webhook: verify the HMAC signature, make handlers idempotent, treat the API as the source of truth by re-fetching the invoice before marking an order paid
- Secrets only in `.env`; BTCPay API key and webhook secret encrypted if stored in settings
- Order status pages use unguessable signed tokens; never expose sequential order IDs
- No personal data in logs; configure log redaction; set a retention period for contact form messages
- Cookie consent that is real: no non-essential cookies or analytics before consent. Prefer privacy-friendly, cookieless analytics

## 7. Performance rules

- Budgets: LCP < 2.5 s (4G, mid-range phone), initial JS < 150 KB gzip, CLS < 0.1, hero video < 1.5 MB with a poster under 120 KB
- Responsive images in AVIF/WebP with `srcset`; explicit width and height; lazy-load below the fold
- Preload the display font subset used above the fold; subset to Latin plus Norwegian characters (æ ø å)
- Eager-load relations; add indexes for listing filters; cache category and settings queries
- Livewire used where it earns its cost (cart, filters, checkout); static Blade elsewhere

## 8. Internationalisation rules

- Locales `nb` (default) and `en`. URL prefix `/no` and `/en`, `hreflang` and canonical tags, language switch keeps the user on the equivalent page
- All UI strings in lang files; no hard-coded text in views
- Model content translatable (name, description, slug, SEO fields)
- Format money as Norwegian: `1 500 kr` with a non-breaking space, `NOK` in structured data
- Dates in Norwegian format; weights and volumes in metric

## 9. Testing and definition of done

A task is done only when all are true:
1. Feature tests cover the happy path and the main failure paths (Pest)
2. For checkout and payments: tests for expired invoice, underpayment, duplicate webhook, tampered price, out-of-stock race
3. Pint and static analysis pass; no new warnings
4. Screenshots at 390, 768 and 1440 px reviewed against §3 and §4
5. Keyboard-only walkthrough of the flow works; no focus traps
6. Both languages render; no missing translation keys
7. Admin can perform the related task without touching code
8. A short note added to `docs/changelog.md`

## 10. Handover

At each milestone, produce `docs/open-items.md` listing everything pending from the client (copy, legal text, photos, credentials, company details) and `docs/runbook.md` explaining deploy, backup, restore and BTCPay reconnection.
