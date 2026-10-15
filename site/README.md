# Racksephnox — Static Documentation Site

Industrial-grade documentation site for the Racksephnox platform.
Pure HTML/CSS/JS — no build step. Deployed via GitHub Actions to Pages.

## Live

https://racksephnox.riqelsspiraltech.us.ci/

## Structure

- `index.html` — landing page (animated φ-spiral hero)
- `docs.html` — docs viewer (hash router, sidebar, live demos)
- `404.html` — custom 404
- `manifest.webmanifest` — PWA manifest
- `robots.txt`, `sitemap.xml` — SEO
- `assets/js/data.js` — single source of truth (20 frameworks)
- `assets/js/modules.js` — 17 interactive demos
- `assets/js/docs.js` — router + demo mount
- `assets/js/main.js` — landing interactions
- `assets/css/main.css` — golden theme (gold · black · ivory)

## Routes

- `/` — landing
- `/docs.html#/` — docs index
- `/docs.html#/f/<id>` — framework page (with live demo if defined)

## Local preview

    python3 -m http.server 8080 --directory site

## Editing content

All framework content lives in `assets/js/data.js` → `window.RX_FRAMEWORKS`.
Add `demo: "id"` to attach a module from `modules.js`.

---

Racksephnox · Infinite Spiral of Creation · 888 Hz · φ = 1.618
