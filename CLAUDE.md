# Kaitori Akiba — Project Memory (AI Handoff Doc)

> Read this first. It explains the whole theme so any AI/developer can continue work
> without re-discovering the architecture. Keep it updated when you change behavior.

## 1. What this is

A **WordPress theme** (not a plugin) for a **Japanese second-hand smartphone/electronics
buy-back ("kaitori / 買取") business** — an Akihabara store. Customers get a price,
submit a buyback application for their phone, verify identity (eKYC, legally required
under 古物営業法), and the shop buys the device and pays by bank transfer.

- **Path:** `wp-content/themes/kaitoriakiba-theme`
- **Stack:** WordPress + **Elementor** (page builder), Bootstrap 3, jQuery, Font Awesome 4.
- **Local dev:** Laragon on Windows. Site URL `http://kaitoriakiba.test`. PHP 8.1 at
  `F:/laragon/laragon/bin/php/php-8.1.10-Win32-vs16-x64/php`. Admin user `admin`.
- **Version:** see `KAITORIAKIBA_VERSION` in `functions.php` (currently 2.2.3).
- **Language / bilingual switcher (EN ⇄ JP):** `inc/i18n-switcher.php` provides
  `kaitori_t($en, $ja)` / `kaitori_te()`. Front remembers the choice in a cookie
  (`kaitori_lang`, default ja) via `?lang=en|ja`; admin per-user meta (`_kaitori_admin_lang`,
  default en) via `?admin_lang=`; toggles in the header utility bar + admin toolbar.
  JS strings come from the `kaitori_ajax.i18n` localize (built with `kaitori_t`).
  **Converted so far:** header/footer, home, My Page, FAQ, Access, device card + single
  device, and the live form widgets (`widget-auth-form`, `widget-kyc-verify`,
  `widget-sell-apply`). **Still Japanese-only:** native form fallback templates (not
  rendered on this Elementor install) and the whole admin/L3 (CPT labels, Customers,
  Demo Import, Buyback Gate, meta-boxes). When adding strings, wrap them in `kaitori_t`.

## 2. Data model (Custom Post Types + Taxonomies) — `inc/cpt.php`

| CPT | slug | holds | access |
|-----|------|-------|--------|
| Buyback Devices | `kaitori_device` | catalog + pricing | public |
| Buyback Orders | `buyback_order` | customer applications | **admin only** |
| eKYC Verifications | `kyc_record` | identity docs | **admin only** |

Taxonomies (on `kaitori_device`): `device_category`, `carrier`, `brand`, `series`.

### ⚠️ CRITICAL capability gotcha (read before touching caps)
`buyback_order` + `kyc_record` share `'capability_type' => array('kaitori_record','kaitori_records')`
with `map_meta_cap => true`. **Do NOT remap the meta caps (`edit_post`/`read_post`/
`delete_post`) to a core primitive like `manage_options` or any bare custom cap** — doing
so registers that cap as a meta-cap alias and makes `map_meta_cap()` return `do_not_allow`
for it **site-wide** (it silently broke `manage_options` for admins once). Admins get the
`kaitori_record` caps at runtime via the `user_has_cap` filter `kaitoriakiba_grant_record_caps`.
Use **`kaitoriakiba_manage_cap()`** (returns `edit_kaitori_records`) for all admin-page /
AJAX permission checks, never a hand-rolled cap string.

### Key meta keys
- Device: `_max_buyback_price`, `_price_rank_s`.._price_rank_d`, `_storage_options`
  ("128GB:152000\n256GB:168000"), `_installment_deduction`, `_model_number`, `_jan_code`,
  `_device_primary_image_url`, `_device_gallery_images`, `_kaitori_demo` (demo tag).
- Order: `_order_status`, `_applicant_name/_kana/_email/_phone`, `_device_model/_storage/
  _color/_carrier/_imei`, `_screen_condition`, `_body_condition`, `_battery_health`,
  `_estimated_price` (server-computed), `_estimated_price_client` (ref only),
  `_bank_account_number` + `_bank_holder_kana` (**encrypted**), `_order_kyc_status`,
  `_customer_user_id`, `_kaitori_demo`. Walk-in/invoice extras: `_kaitori_walkin` (1),
  `_final_buyback_price` (agreed amount; invoice prefers it over `_estimated_price`),
  `_payment_method` (cash|bank), `_admin_memo`.
- eKYC: `_kyc_status` (pending/verified/rejected), `_kyc_name/_kana/_email/_phone/_dob/
  _address...`, `_kyc_id_front_file` / `_kyc_id_back_file` / `_kyc_selfie_file` (secure
  relative paths; legacy `_kyc_*_attach` = attachment ids), `_kyc_user_id`, `_kyc_order_id`.
- User meta: `_kaitori_kyc_status`, `_kaitori_favorites` (int[] device ids), `phone_number`.

Order statuses: single source of truth `kaitoriakiba_order_statuses()` in `inc/cpt.php`
(pending → received → inspecting → inspected → paid → cancelled). Use
`kaitoriakiba_get_order_status($key)` for the badge/label.

## 3. File map (`inc/`)

| File | Responsibility |
|------|----------------|
| `cpt.php` | CPTs, taxonomies, admin columns, order-status map, capability helpers |
| `meta-boxes.php` | Device pricing/gallery boxes; order + eKYC review boxes; user-profile section |
| `ajax-handlers.php` | Public AJAX: submit application, submit eKYC, register, login, favorites toggle, live estimate, device filter |
| `pricing.php` | **Server-side quote engine** `kaitoriakiba_calculate_quote()` from device meta |
| `crypto.php` | AES field encryption for bank data (`kaitoriakiba_encrypt/decrypt/mask_account`) |
| `kyc-security.php` | eKYC files in **protected dir** `uploads/kaitori-kyc-secure/`; staff-only streaming endpoint `kaitori_view_kyc_file` |
| `form-security.php` | Honeypot + timing, IP rate-limit (transients), `kaitori_captcha_verify` hook |
| `emails.php` | Customer order-confirmation + status-change emails (HTML `wp_mail`) |
| `seo.php` | OG/Twitter meta + JSON-LD (LocalBusiness, Product/Offer, FAQPage) |
| `admin-customers.php` | **Customers admin page** (aggregate list + filters + detail + edit) and admin AJAX (status update, customer update, save-scan) |
| `invoice.php` | **Buyback invoice** (買取明細書): printable A4 HTML with **two copies** (店舗控え + お客様控え), served via `admin-post.php?action=kaitori_print_invoice` (cap + nonce). PDF = browser Print→Save as PDF (no library). Helper `kaitoriakiba_invoice_url($order_id,$auto)` |
| `admin-order-create.php` | **Walk-in order page** (店頭買取登録, submenu under Buyback Orders): staff create a `buyback_order` by hand → issue invoice. Reuses order meta keys; encrypts bank; "Suggest price" uses `kaitori_live_estimate` |
| `sell-gate.php` | **Login + eKYC gate** for the buyback form + its settings page |
| `demo-data.php` | Seeder: taxonomies, 6 sample devices, pages, nav menu (runs on theme switch / admin_init) |
| `demo-import.php` | **One-click Demo Import admin page** (import / add sample orders+eKYC / remove demo) |
| `tgmpa/` | TGM Plugin Activation — prompts to install Elementor |
| `elementor/` | Elementor integration + 13 custom widgets |

Page templates: `front-page.php`, `page-sell-apply.php`, `page-kyc-verify.php`,
`page-register.php`, `page-mypage.php`, `page-faq.php`, `page-access.php`,
`single-kaitori_device.php`, taxonomy templates, `template-elementor-*`.
Front assets: `assets/js/theme-app.js` (public), `assets/js/admin-kaitori.js` +
`assets/css/admin-kaitori.css` (admin: scanner + inline actions).

## 4. ⚠️ Elementor vs native templates (important)

The form pages (`/sell-apply/`, `/register/`, `/kyc-verify/`) are **built with Elementor**
on this install, so they render the custom **widgets** (`inc/elementor/widgets/widget-*.php`),
NOT the native `page-*.php` fallback. **Any change to a form must be made in BOTH** the
native template and the matching widget. Examples already handled this way: honeypot fields,
the sell-apply live-estimate wiring, and the buyback gate.

The Elementor sell-apply widget uses **different field names** than the native form
(`device_model`/`device_storage`/`device_imei`/`shipping_kit`); the application AJAX handler
accepts **both** variants (`$_POST['model'] ?: $_POST['device_model']`, etc.). Keep that.

## 5. Core flows

- **Buyback application** (`/sell-apply/`): 6-step form → AJAX `kaitori_submit_application`
  → creates `buyback_order`, **recomputes price server-side** (never trusts client
  `estimated_price`), encrypts bank fields, emails the customer, links/looks-up eKYC.
  **Gated**: requires login + eKYC (see §6).
- **Live estimate**: form calls AJAX `kaitori_live_estimate` → same `calculate_quote` engine.
- **eKYC** (`/kyc-verify/`): AJAX `kaitori_submit_kyc` → `kyc_record`, files stored in the
  protected dir, linked to user + order. Review in admin → status sync to user/order.
- **Favorites (★)**: AJAX `kaitori_toggle_favorite` → members = user meta, guests =
  localStorage. Shown on `/mypage/`.
- **My Page** (`/mypage/`): logged-in dashboard — orders, eKYC status, favorites.
- **Admin Customers** (`admin.php?page=kaitori-customers`): aggregates customers by email
  (+ WP users), filter by search/eKYC/order-status/type/date, detail view with editable
  profile, inline order-status change (fires status email), and barcode scan.

## 6. Buyback gate — `inc/sell-gate.php`

A customer must be **logged in** (always) AND satisfy the **eKYC requirement** before the
buyback form shows or submits. Admin-configurable at **Appearance → Buyback Gate**
(`option kaitori_sell_gate_mode`): `submitted` (uploaded, default) | `verified` (admin-
approved) | `off` (login only). Enforced in the native template, the Elementor widget
(`kaitoriakiba_maybe_block_sell_form()`), and **server-side** in the AJAX handler.
Admins bypass (for previews). Helper: `kaitoriakiba_user_can_sell()` → `[bool, reason]`.

## 7. Security & compliance (don't regress)

- eKYC ID docs: **never** in the public media library — protected dir + permission-checked
  endpoint (`kaitoriakiba_kyc_file_src()` builds the URL; `kaitoriakiba_kyc_resolve_src()`
  handles legacy). MIME/size validated on upload.
- Bank account number + holder: **encrypted at rest** (`inc/crypto.php`), masked + Reveal in admin.
- Pricing: always recompute on the server.
- Forms: honeypot + rate-limit; passwords ≥8 with letters+numbers; `kaitori_captcha_verify`
  filter ready for reCAPTCHA (no key wired yet).
- Orders/eKYC visible to admins only (see capability gotcha §2).

## 8. Admin pages (wp-admin)

- **Buyback Devices / Buyback Orders / eKYC Verifications** (CPT menus)
- **Customers** (`admin.php?page=kaitori-customers`) — has a **＋ New Walk-in Buyback** button
- **Buyback Orders → New Walk-in** (`edit.php?post_type=buyback_order&page=kaitori-new-order`) — create an order by hand + issue invoice
- **Invoice** — "Print / Save as PDF" button on the order edit screen and in the Customers detail order list
- **Appearance → Demo Import** (`themes.php?page=kaitori-demo-import`) — 1-click seed / sample orders / reset
- **Appearance → Buyback Gate** (`themes.php?page=kaitori-sell-gate`) — eKYC gate mode
- **Users → (any user)** — Kaitori section shows their eKYC + orders

## 9. Operational notes

- **Emails**: built correctly but localhost has no mailer — needs SMTP (e.g. WP Mail SMTP) to actually deliver.
- **Barcode scanner** (admin intake, on order edit + Customers detail): native `BarcodeDetector`
  (phone camera, **needs HTTPS**) with a manual-entry fallback. A **USB keyboard-wedge
  scanner works on plain HTTP** — the modal auto-focuses an input and Enter auto-saves
  (AJAX `kaitori_admin_save_scan`). Scanner just saves the raw number; no external lookup.
- **Demo data** is tagged `_kaitori_demo=1`; the Demo Import page's remove action only
  deletes tagged items (real data is safe). New pages are created only on theme (re)activation.
- No git repo; not a child theme — customizations live directly in this theme.

## 10. How to verify changes

- Syntax: `php -l <file>` (use the Laragon PHP path above). JS: `node -c <file>`.
- Logic / DB: bootstrap WordPress from a CLI script — `define('WP_USE_THEMES',false);
  require '<wp-root>/wp-load.php';` then call theme functions (optionally
  `wp_set_current_user($id)`), e.g. verify `kaitoriakiba_calculate_quote()`, capability
  resolution, gate logic, email building (hook `pre_wp_mail` returning true to capture
  without sending). Run with `-d opcache.enable_cli=0`.
- UI: log in at `/wp-admin` and view the relevant page; the built-in browser shares the session.

## 11. Known deferred work (not built yet)

IMEI/利用制限 lookup (needs external API), courier (Yamato/Sagawa) integration, store-visit
appointment booking, price-history/auto-rate-update, 古物台帳 (antique-dealer ledger) export,
corporate bulk-buyback form, member-rank bonus logic, email verification on registration
(scaffolded, default off), reCAPTCHA key, inline-CSS → stylesheet refactor, library upgrades.
