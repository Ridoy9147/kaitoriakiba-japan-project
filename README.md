# Kaitori Akiba (kaitoriakiba.jp) - Web Portal & WordPress Theme

Official English website and WordPress Theme for **Kaitori Akiba** (`https://kaitoriakiba.jp/`), cloned and adapted from Japanese mobile buyback layout with modern English content, full localization, responsive styling, and complete WordPress theme architecture.

---

## 🌐 Brand & Domain Specifications

- **Domain:** `https://kaitoriakiba.jp/`
- **Brand Name:** Kaitori Akiba (買取アキバ)
- **Tagline:** iPhone & Smartphone Buyback Specialist (Tokyo • Akihabara)
- **Language:** English (en)

---

## 📁 Directory Structure

```text
d:/japan project/
│
├── index.html                  # Standalone English HTML5 website with Kaitori Akiba branding
├── README.md                   # Project documentation
├── server.mjs                  # Lightweight local preview web server
│
├── assets/                     # Static assets (CSS, JS, Fonts, Images)
│   ├── css/
│   │   ├── bootstrap.min.css
│   │   ├── jquery-ui.min.css
│   │   ├── font-awesome.min.css
│   │   ├── base.css
│   │   ├── style.css
│   │   └── swiper3.1.0.min.css
│   ├── js/
│   │   ├── jquery-2.2.0.min.js
│   │   ├── bootstrap.min.js
│   │   ├── swiper3.1.0.jquery.min.js
│   │   ├── common.js
│   │   ├── response-layout.js
│   │   └── scroller.js
│   ├── fonts/                  # FontAwesome & Glyphicons
│   └── images/                 # Theme icons, logos, sprites & background images
│       └── topmenu/logo.svg    # Kaitori Akiba Vector SVG Brand Logo
│
├── upload/                     # Media & banner uploads (Carriers, Brands, Products)
│   ├── logo/logo.svg           # High-resolution vector logo
│   ├── ad/                     # Banners & sliders
│   ├── brand/                  # Brand logos (Apple, Samsung, Google, etc.)
│   ├── career/                 # Carrier badges (docomo, au, SoftBank, etc.)
│   └── goods/                  # Product photography & thumbnails
│
└── wordpress-theme/
    └── kaitoriakiba-theme/     # Production-ready WordPress Theme
        ├── style.css           # Theme metadata header
        ├── functions.php       # Enqueue scripts & styles, theme setup
        ├── header.php          # Header, navigation & mobile menu
        ├── footer.php          # Footer, sitemap, legal notices & scripts
        ├── sidebar.php         # Left categories, carriers & brands navigation
        ├── front-page.php      # Homepage template
        ├── index.php           # Fallback template
        ├── assets/             # Theme static assets
        └── upload/             # Media assets
```

---

## 🚀 How to Run & Preview

### 1. Direct Browser Opening:
Double-click [index.html](file:///d:/japan%20project/index.html) or open it in any modern browser.

### 2. Local Node Web Server:
Run in terminal:
```bash
node server.mjs
```
Then visit: `http://localhost:3000`

---

## 🧩 WordPress Theme Installation

The `wordpress-theme/kaitoriakiba-theme/` folder is ready to be dropped directly into WordPress:
1. Copy the `kaitoriakiba-theme` folder into your WordPress `wp-content/themes/` directory.
2. In the WordPress Admin Dashboard, navigate to **Appearance > Themes**.
3. Activate **Kaitori Akiba Theme**.
