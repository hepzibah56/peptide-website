# context.md: background the agent needs

## 1. The project in one paragraph

A developer is building a custom ecommerce site for a Norwegian client. It is a webshop for **laboratory supplies** (vials, caps, seals, laboratory water and similar consumables), run on full-stack Laravel with an admin panel. The client uploads and manages products, handles orders and edits content himself. The buyer's brand colour is **light blue**. The result must look cinematic, premium and distinctive, and must not look like a template or AI-generated site.

## 2. History and why the catalog is what it is

The client's first brief pointed at a reference site that sold research peptides and injectables. That catalog was dropped after discussion with the client. **The store now sells laboratory supplies only.** Do not recreate or reference the original product list anywhere (seeders, copy, tests, SEO, image names). The compliance gate in `spec.md` §5.4 exists to keep the catalog in scope.

## 3. Client and business facts (from the brief)

- Market: Norway, domestic delivery only
- Language: Norwegian required; English switch wanted
- Payment: Bitcoin only, through **BTCPay Server**. No card payments
- Customer questions: by email (address set in settings, not hard-coded)
- Shipping: Posten (Norway Post). The brief includes a Posten parcel-booking link, `sending.posten.no/bestill/pakke/under5kg`, for parcels under 5 kg. How it is used is unclear (see §7)
- The client's original suggestion was a WordPress build on a local setup; the developer chose Laravel instead
- Domain and hosting: already owned, shared hosting plan "VH-Boost" at vsys.host
- Documentation such as certificates is "available on request" in the brief; the new design provides downloadable per-product datasheets instead, where the client has them
- Brand name, logo and organisation number: **not yet supplied**. Use `config('shop.name')` and `{{ brand }}` placeholders; never invent a name

## 4. Audience and tone

Labs, schools, small testing businesses and hobby makers in Norway. They value exact specs, clear documentation, predictable delivery and calm, competent copy. The tone is precise, plain and a little understated, like a good instrument manufacturer. Not salesy.

## 5. Constraints that shape the build

**Hosting (shared plan):**
- Probably no long-running queue workers or Redis. Use the database queue and run `queue:work --stop-when-empty` plus `schedule:run` from cron
- Confirm before starting: PHP version and extensions (intl, gd or imagick, mbstring, bcmath, zip), SSH access, Composer availability, cron frequency, ability to set the document root to `/public`, memory limits, outbound HTTPS to the BTCPay host
- If SSH or Composer is missing, build locally and deploy `vendor/` and built assets via CI or SFTP

**BTCPay:**
- BTCPay Server needs its own server (a small VPS) or a hosted provider; it cannot run on shared hosting. The shop connects through the Greenfield API with a store ID, API key and webhook secret
- Wallet keys stay with the client. The shop never touches them
- The client must set up a BTCPay store and wallet; provide a setup checklist in `docs/runbook.md`

**Legal and regulatory (Norway):**
- Prices shown to consumers include MVA (25% standard; confirm with the client's accountant). Business buyers may need ex-VAT display later
- Distance-selling rules: clear pre-contract information, order confirmation, a 14-day right of withdrawal for consumers (angrerett) unless an exception applies, standard withdrawal form
- Privacy (GDPR): privacy policy, lawful basis, retention, consent for non-essential cookies
- Marketing law: no misleading claims, fake reviews or invented urgency
- Product classification: laboratory supplies only; the compliance gate controls publishing
- All legal pages are drafted by or reviewed with a qualified person before launch. The agent provides structure and placeholders, not final legal text

## 6. Design intent summary

- Concept: "Light through glass". Cold, bright, precise, filmic
- Brand colour: Glacier light blue `#8CCBEB`, with Fjord Ink `#0F2B3D` text, Frost `#F1F7FA` surface, and a Deep Water `#0C2433` stage for the hero and footer
- Type: Gambetta (display serif) and Switzer (sans), self-hosted
- One bold moment (hero film and a pinned product sequence), calm everywhere else
- Full details: `pdr.md` §6 and `spec.md` §7

## 7. Open questions for the client (record answers in `docs/assumptions.md`)

1. Brand name, logo, organisation number, registered address, contact email and phone
2. Who ships: does the client ship all parcels and enter tracking numbers (default assumption), or do customers book Posten labels themselves using the link in the brief?
3. Shipping rates and zones within Norway, free-shipping threshold, packaging weights
4. Is VAT-registered B2B pricing (ex-VAT display, invoices) needed at launch?
5. Final launch catalog: product list, volumes, variants, prices, stock
6. Which products have datasheets or certificates of conformance to publish?
7. Written confirmation from Legemiddelverket (or the client's adviser) on how any sterile or bacteriostatic water may be sold and labelled. Until then that product stays unpublished
8. Photography: will the client supply photos and a short vial-rack film, or should a shoot be commissioned? (Brief in `spec.md` §14)
9. Hosting details listed in §5 and who will provide the BTCPay server
10. Analytics: none, or a cookieless option?

## 8. Glossary

| Term | Meaning |
|---|---|
| MVA | Norwegian VAT |
| Angrerett | Consumer right of withdrawal for distance sales |
| COA / CoC | Certificate of analysis / certificate of conformance. Only publish what the client actually holds |
| Greenfield API | BTCPay Server's REST API |
| nb / en | Norwegian Bokmål / English locale codes |
| Compliance gate | Admin-side check that blocks publishing out-of-scope products |
| Glacier / Fjord Ink / Frost / Deep Water | Named colour tokens |
