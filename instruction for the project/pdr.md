# PDR: Product Design & Requirements

Project: custom laboratory-supplies webshop (Norway), built on full-stack Laravel with an admin panel.
Read order for the agent: `context.md` → `pdr.md` (this file) → `instructions.md` → `spec.md`.

---

## 1. Vision

A webshop for laboratory consumables that feels like a well-lit lab bench photographed for a film: clean glass, cold light, precise detail. Buyers should trust it within five seconds and be able to order in under two minutes. The client should be able to run it alone from the admin panel without calling a developer.

The brand colour is **light blue**. It is the colour of light passing through glass and water, not a UI accent sprinkled on a white template.

## 2. Product scope (catalog)

This store sells **laboratory supplies and consumables**. Initial categories as confirmed with the client:

- Vials (clear and amber glass, various volumes), stoppers, crimp caps, seals
- Laboratory-grade water and diluents (see compliance gate below)
- Other bench consumables the client adds later (labels, racks, storage, pipette consumables)

### Compliance gate (hard requirement)

The store must **not** list or describe: peptides or other active compounds, pharmaceuticals or medicinal products, injection syringes, needles or pens, or any dosing, administration, human-use or animal-use claims.

- Every product has a `compliance_status` (`draft`, `approved`). Only `approved` products can be published. The admin must tick a confirmation ("This product is a laboratory supply and carries no medicinal or usage claims") before approving.
- Bacteriostatic water and any similar sterile-water product ships **unpublished** until the client confirms in writing how it may legally be sold and labelled in Norway (check with Legemiddelverket). The product must be labelled for laboratory use only, with no injection or administration wording.
- Product descriptions are factual: material, volume, dimensions, compatibility, standards. No performance or health claims.

## 3. Audience

- **Primary:** laboratories, research groups, university and school science departments, small biotech and testing businesses in Norway. They compare specs, reorder, and care about documentation.
- **Secondary:** hobbyists and makers buying small quantities.
- Language: Norwegian Bokmål by default, English available. Currency NOK. Domestic delivery only.

Assume mobile traffic is at least half of visits, but B2B buyers will often reorder on desktop.

## 4. Goals and success measures

| Goal | Measure |
|---|---|
| Fast trust | First-time visitor can find what the shop sells, who runs it, and how to contact them without scrolling past the hero |
| Conversion | Product page → cart → paid order funnel is instrumented; checkout is one page, no forced account |
| Reorder ease | Returning buyer can repeat a previous order in two clicks from the order status or account page |
| Admin independence | Client creates a product with images, documents and translations in under five minutes |
| Performance | LCP under 2.5 s on a mid-range phone over 4G; initial JS under 150 KB gzipped; CLS under 0.1 |
| Accessibility | WCAG 2.2 AA on all customer-facing pages |

## 5. Feature scope

### Phase 1: launch

**Storefront**
- Home with cinematic hero, category entry points, featured products, documentation and delivery summary
- Shop listing with category pages, filters (category, volume, material, glass colour, in stock), sort, search with suggestions
- Product detail: gallery with zoom, variant selector (volume, pack size), price incl. MVA, stock state, spec table, downloadable documents (datasheet, certificate of conformance), related products
- Cart as slide-over drawer plus full cart page
- One-page guest checkout with Bitcoin payment through BTCPay Server
- Order status page via signed link (no account needed)
- Norwegian and English with a language switch that keeps the user on the same page
- Static pages: About, Contact (form with spam protection), Delivery and returns, Terms, Privacy, Cookies, Right of withdrawal (angrerett)
- Transactional emails: order received, payment confirmed, shipped (with tracking), payment expired

**Admin panel**
- Products, variants, categories, images, documents, translations (nb/en), compliance status
- Orders with status workflow, internal notes, tracking number entry, one-click "mark as shipped"
- Payments view showing BTCPay invoice state per order
- Customers, discount codes, store settings (shipping rates, free-shipping threshold, VAT, contact details)
- Content blocks (hero text, banners, legal pages) editable per language
- Users, roles and permissions, mandatory 2FA for admin users, activity log
- Dashboard: orders to ship, unpaid/expired invoices, low stock, revenue for the last 30 days

### Phase 2: after launch (do not build now)
- Customer accounts with order history and reorder
- B2B price lists and company invoicing
- Saved lists ("kits") a lab can reorder
- Back-in-stock notifications
- Additional payment methods (only if the client requests)

## 6. Design direction

### 6.1 Concept: "Light through glass"

The product is glass, liquid and light, so the site is built from those. Cold, bright, precise. One big cinematic moment (the home hero and one scroll-driven product sequence), then quiet, disciplined, highly usable commerce UI.

**Spend boldness in one place.** The hero film and the pinned product sequence carry the drama. Everything else is calm so the drama means something.

### 6.2 Palette (named tokens)

| Token | Hex | Role |
|---|---|---|
| Glacier | `#8CCBEB` | The brand light blue. Large surfaces, hero light, buttons, selected states |
| Glacier Mist | `#DCEFF9` | Tints, hover surfaces, filled inputs |
| Fjord Ink | `#0F2B3D` | All body and heading text, and text on Glacier |
| Deep Water | `#0C2433` | Cinematic hero stage and footer only. A deep blue, not black |
| Frost | `#F1F7FA` | Default page surface |
| Borosilicate | `#C9DAE3` | Borders, dividers, table rules |
| Tide | `#2A769F` | Links and small interactive text on light surfaces (about 5:1 on white; verify) |
| Amber Glass | `#C27A2C` | Secondary accent, used for amber-vial product cues and low-stock notices only |

Semantic: success `#2F7D5B`, error `#B3412F`. Verify every pair with a contrast checker before shipping.

**Rule:** Glacier is never used as text colour on white or Frost (it fails contrast). Text on Glacier is Fjord Ink.

### 6.3 Typography

Two families, clearly different, both self-hosted (no Google Fonts CDN, for privacy and speed):

- **Display:** Gambetta (Fontshare, serif) for hero and section headlines. Use its weight and optical contrast as part of the composition.
- **Interface and body:** Switzer (Fontshare, sans) at regular and medium, tabular figures on for prices and spec tables.
- Lot and batch codes only may use a monospace face. Pick one deliberately (e.g. Commit Mono); not required elsewhere.

If a licence or file is unavailable, stop and ask. Do not silently fall back to Inter, Roboto, Arial, Poppins, Montserrat, DM Sans, Space Grotesk, Playfair Display or the system stack as the visible face.

Scale: fluid, ratio about 1.25, `clamp()` based. Body line length under 75 characters. Serif display gets slightly looser leading than the sans body.

### 6.4 Layout and shape

- Left-aligned text by default. Centre alignment only for the hero film caption and the checkout confirmation.
- Radii by role, not one value everywhere: 2 px on inputs and data tables, 10 px on product media frames, pill on tags and the quantity stepper.
- Shadows are blue-tinted and used only where elevation carries meaning (drawer, dropdowns, sticky bar). No shadow on ordinary cards.
- Product grids vary rhythm: a featured product may span two columns; the listing is not a wall of identical tiles.

### 6.5 Imagery and film

- Real product photography only, shot on a clean bench with raking light, shallow depth of field, a few frames of refraction. No stock photos, no AI-generated product images, no 3D renders presented as photos.
- Hero: a short silent loop (4 to 6 s, light moving across a rack of vials), with a still poster for slow connections and for reduced-motion users.
- Provide a photography brief to the client (see `spec.md` §14) so the shoot matches the layout.

### 6.6 Motion

- One orchestrated hero reveal on first load.
- One scroll-driven product sequence (pinned, a few seconds of scroll) for a featured product on the home page.
- All other motion answers an action: drawer opening, add-to-cart confirmation, filter results updating, stock state changing. Duration 160 to 280 ms, ease-out.
- No fade-and-slide-up on every section. No hover animations on every card.
- Respect `prefers-reduced-motion` everywhere.

### 6.7 Trust and conversion (honest versions only)

- Visible: delivery information, VAT-inclusive prices, contact details and company identity (organisation number once supplied), documentation downloads, clear return terms.
- Checkout shows the BTC amount, the NOK equivalent, the rate-lock expiry countdown, and what happens if the invoice expires.
- No fake reviews, fake counters, invented certifications, countdown pressure timers or "X people are viewing this". These are also unlawful marketing in Norway.
- Security cues are real and specific: "Payment is handled by BTCPay. We never see or store your wallet keys." Not generic padlock badges.

## 7. Content principles

- Norwegian is written natively, not translated mechanically. Have the client review all nb copy.
- Sentence case, plain verbs, active voice. Buttons name the outcome: "Legg i handlekurv", "Gå til kassen", "Betal med Bitcoin".
- Errors say what happened and how to fix it. Empty states invite an action.
- No marketing filler ("Elevate", "Seamless", "Unleash", "Welcome to our store").
- No lorem ipsum in any commit. If real copy is missing, write short plain placeholder copy and list it in the handover notes.

## 8. Non-goals

- No marketplace, no multi-vendor, no card payments, no international shipping, no subscriptions in phase 1.
- No customer-visible AI chat, no recommendation engine, no pop-up newsletter wall.

## 9. Risks

| Risk | Mitigation |
|---|---|
| Catalog drifts back into regulated products | Compliance gate in admin; forbidden-terms check on publish (see `spec.md`) |
| Shared hosting limits (no workers, limited PHP extensions) | Database queue run by cron; confirm PHP version, SSH and Composer access up front |
| BTCPay cannot run on shared hosting | Use a separate BTCPay Server (own VPS or a hosted provider); the shop talks to it over the Greenfield API |
| Heavy film hurts performance | Poster-first loading, size budget, lazy video, reduced-motion fallback |
| Bitcoin price moves during checkout | BTCPay invoice locks the rate; show countdown; handle expiry and underpayment states |
| Norwegian legal pages missing or wrong | Client confirms company details; have a qualified person review terms and withdrawal text |
