# Racksephnox — Static Documentation Site

Golden-theme static site for the Racksephnox platform documentation.
Pure HTML/CSS/JS — no build step, deploys directly to GitHub Pages.

## Structure



.
├── index.html          # Landing page
├── docs.html           # Docs viewer (hash router)
├── 404.html            # Custom 404
├── assets/
│   ├── css/main.css
│   └── js/
│       ├── data.js     # All 20 frameworks (single source of truth)
│       ├── main.js     # Landing interactions
│       └── docs.js     # Docs router + search
└── .nojekyll

```

## Routes

- `/` — landing
- `/docs.html#/` — docs index
- `/docs.html#/f/<id>` — single framework

## Local preview

```bash
python3 -m http.server 8080
# open http://localhost:8080
```

Deploy to GitHub Pages

```bash
git init
git add .
git commit -m "feat: racksephnox static foundation"
git branch -M main
git remote add origin git@github.com:<USER>/<REPO>.git
git push -u origin main
```

Then: Settings → Pages → Source: main / root.

Custom domain (DNSHE / Cloudflare)

Create CNAME at repo root:

```bash
echo "racksephnox.yourdomain.tld" > CNAME
```

Then add a CNAME DNS record pointing to <USER>.github.io.

Editing content

All framework content lives in assets/js/data.js inside window.RX_FRAMEWORKS.
Add/edit entries there — landing grid, sidebar, search and pager update automatically.
