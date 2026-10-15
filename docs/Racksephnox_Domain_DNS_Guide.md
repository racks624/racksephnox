# ═══════════════════════════════════════════════════════════════
#  R A C K S E P H N O X
#  Domain Name & DNS — Complete Setup Guide
#  Global Cryptocurrency · Fintech · Gaming Empire
#  Version 1.0 · Confidential
# ═══════════════════════════════════════════════════════════════

**Prepared for:** Racksephnox Technical Team
**Date:** 2026
**Status:** Production-Ready
**Audience:** Founders, Developers, DevOps, Investors

---

## Table of Contents

1. What Is a Domain Name?
2. Choosing Your Domain Name
3. Where to Buy Domains
4. Understanding DNS Records
5. Setting Up DNSHE (Current Domain)
6. Cloudflare Integration (Production Path)
7. Cloudflare Tunnel (Hosting on Your Phone)
8. SSL/TLS Certificates
9. Email Records (MX, SPF, DKIM, DMARC)
10. Troubleshooting DNS
11. Complete DNS Record Reference
12. Launch Checklist

---

## 1. What Is a Domain Name?

A domain name is your address on the internet. When a user types
`racksephnox.us.ci` into a browser, DNS (Domain Name System) translates
that human-readable name into an IP address of the server hosting your app.

A domain has three parts:



### Common TLD Examples

| TLD      | Meaning                                      | Cost                    |
|----------|----------------------------------------------|-------------------------|
| .com     | Commercial — global standard                 | ~KES 1,200/year         |
| .us.ci   | Free subdomain from DNSHE (your current one) | Free                    |
| .net     | Network — global alternative                 | ~KES 1,500/year         |
| .io      | Tech startups — expensive                    | ~$40/year               |
| .co.ke   | Kenya — local, requires KRA PIN              | ~KES 1,000/year         |

---

## 2. Choosing Your Domain Name

### Guidelines

- Keep it short — under 15 characters is ideal
- Use your brand — `racksephnox`, not `crypto-invest-2025`
- Avoid hyphens — hard to say over phone
- Prefer `.com` if budget allows — most trusted globally
- Free alternatives (.us.ci, .de5.net) work for launch phase

### For Racksephnox — Recommended Plan

| Phase            | Domain                          | Cost        |
|------------------|---------------------------------|-------------|
| Primary (now)    | racksephnox.us.ci               | Free        |
| Primary (funded) | racksephnox.com                 | ~$10/year   |
| Secondary        | racksephnox.co.ke               | ~KES 1,000  |
| Future           | racksephnox.io                  | ~$40/year   |

---

## 3. Where to Buy Domains

### Global Registrars

| Registrar            | Notes                                      |
|----------------------|--------------------------------------------|
| Namecheap            | Cheap, free WHOIS privacy                  |
| Cloudflare Registrar | At-cost pricing, no markup                 |
| GoDaddy              | Popular but expensive renewals             |
| Porkbun              | Cheap, clean UI, free SSL                  |

### Free Subdomain Providers

| Provider    | Domains Offered                          |
|-------------|------------------------------------------|
| DNSHE       | .us.ci, .de5.net, .cc.cd, .bot.cd        |
| Stackryze   | .indevs.in, .sryze.cc, .ryzedns.org      |
| Afraid DNS  | .duckdns.org dynamic DNS                 |
| FreeDNS     | Many free subdomains, ad-supported       |

### Recommendation for Racksephnox



---

## 4. Understanding DNS Records

DNS records tell the internet where to send traffic for your domain.
Each record has a **Type**, a **Name** (host), a **Value**, and a **TTL**.

### Record Types You Will Use

| Type   | Purpose                                                     |
|--------|-------------------------------------------------------------|
| A      | Maps domain to IPv4 address (e.g., 129.146.123.45)          |
| AAAA   | Maps domain to IPv6 address                                 |
| CNAME  | Maps domain to another domain (alias)                       |
| MX     | Mail exchange — routes email                                |
| TXT    | Free-text — used for verification (SPF, DKIM, etc.)         |
| NS     | Nameserver — tells who manages DNS for your domain          |
| SRV    | Service records — for specific apps                         |
| CAA    | Restricts which Certificate Authorities can issue SSL       |

### TTL — Time To Live

TTL is how long (in seconds) resolvers cache a record.



---

## 5. Setting Up DNSHE (Your Current Domain)

### Step 1 — Log In

Go to https://dnshe.com and log in to your account.

### Step 2 — Open Domain Manager




### Step 3 — Add DNS Records

Click the **DNS Records** tab. You will see an empty list.

The first record to add depends on what will host the site.

#### If Using Cloudflare Tunnel

| Field | Value                          |
|-------|--------------------------------|
| Type  | CNAME                          |
| Name  | @                              |
| Value | <TUNNEL-ID>.cfargotunnel.com   |
| TTL   | 300                            |

#### If Using a Traditional Server

| Field | Value           |
|-------|-----------------|
| Type  | A               |
| Name  | @               |
| Value | <SERVER-IP>     |
| TTL   | 300             |

#### WWW Redirect

| Field | Value                |
|-------|----------------------|
| Type  | CNAME                |
| Name  | www                  |
| Value | racksephnox.us.ci    |
| TTL   | 300                  |

---

## 6. Cloudflare Integration (Production Path)

Cloudflare gives you free SSL, DDoS protection, CDN, and Tunnel access.
It becomes the front door to your Racksephnox app.

### Step 1 — Create Cloudflare Account

Sign up at https://dash.cloudflare.com
The Free plan is enough.

### Step 2 — Add Your Domain

- Click **Add a Site**
- Enter `racksephnox.us.ci`
- Choose **Free** plan
- Cloudflare shows you 2 nameservers, e.g. `alexa.ns.cloudflare.com`

### Step 3 — Update Nameservers at DNSHE

- DNSHE Dashboard → racksephnox.us.ci → **Nameservers** tab
- Replace defaults with Cloudflare nameservers
- Save. DNS propagation takes 5–30 minutes

### Step 4 — Verify in Cloudflare

Back in Cloudflare, click **Check nameservers now**.
When it says **Active**, you are done.

### Step 5 — Configure SSL/TLS

| Setting                          | Value     |
|----------------------------------|-----------|
| SSL/TLS → Overview               | Full      |
| Edge Certificates → Always HTTPS | ON        |
| Automatic HTTPS Rewrites         | ON        |
| Minimum TLS Version              | 1.2       |

**Important:** Use **Full** not **Flexible**.
Flexible leaves the Cloudflare-to-phone leg unencrypted.

---

## 7. Cloudflare Tunnel (Hosting on Your Phone)

Cloudflare Tunnel exposes your local Laravel app to the internet without
any port forwarding or public IP. Perfect for hosting on Termux/Proot.

### Prerequisites

- `cloudflared` installed
- Domain added to Cloudflare
- Laravel app running on localhost:8000

### Commands

```bash
cloudflared tunnel login
cloudflared tunnel create racksephnox
cloudflared tunnel route dns racksephnox racksephnox.us.ci

tunnel: racksephnox
credentials-file: /root/.cloudflared/<UUID>.json

ingress:
  - hostname: racksephnox.us.ci
    service: http://localhost:8000

  - hostname: www.racksephnox.us.ci
    service: http://localhost:8000

  - service: http_status:404

# Terminal 1
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2
cloudflared tunnel run racksephnox


Method Cost Notes
Cloudflare Edge Free Automatic, no server config
Let's Encrypt Free Requires Certbot on server
Commercial $50–500/year Wildcard + warranty

Field Value
Type MX
Name @
Value mail.example.com (mail provider)
Priority 10

v=spf1 include:_spf.google.com ~all

v=DMARC1; p=quarantine; rua=mailto:dmarc@racksephnox.us.ci

Symptom Fix
Domain not resolving Check NS propagation at dnschecker.org
SSL error Cloudflare SSL mode must be Full
Site loads on www but not bare Add CNAME @ → www.example.com
Site loads everywhere except your phone Clear DNS cache on phone
Tunnel shows 502 Laravel server not running — check logs
Login redirect loops Set SESSION_DOMAIN=.yourdomain.com
Assets show 404 Run npm run build + set ASSET_URL


dig +short racksephnox.us.ci A
dig +short racksephnox.us.ci CNAME
nslookup racksephnox.us.ci
curl -I https://racksephnox.us.ci

Type Name Value
CNAME @ Tunnel CNAME (auto-created)
CNAME www racksephnox.us.ci
CNAME api racksephnox.us.ci (optional)
CNAME admin racksephnox.us.ci (optional)
MX @ your mail provider
TXT @ v=spf1 ... (SPF)
TXT _dmarc v=DMARC1; ...
CAA @ 0 issue "letsencrypt.org"


Type Name Value
CNAME @ Tunnel CNAME (auto-created)
CNAME www racksephnox.us.ci
CNAME api racksephnox.us.ci (optional)
CNAME admin racksephnox.us.ci (optional)
MX @ your mail provider
TXT @ v=spf1 ... (SPF)
TXT _dmarc v=DMARC1; ...
CAA @ 0 issue "letsencrypt.org"

Type Name Value
A @ <server IPv4>
AAAA @ <server IPv6>
CNAME www racksephnox.us.ci
MX @ mail.racksephnox.us.ci (prio 10)
TXT @ v=spf1 ...

[ ] Domain registered and active
[ ] Nameservers pointed to Cloudflare
[ ] SSL/TLS mode set to Full
[ ] Cloudflare tunnel created
[ ] DNS records routing to tunnel
[ ] Laravel APP_URL updated
[ ] ASSET_URL matches APP_URL
[ ] SESSION_DOMAIN set correctly
[ ] SESSION_SECURE_COOKIE=true
[ ] npm run build executed
[ ] All caches cleared

[ ] Load https://racksephnox.us.ci — no SSL warnings
[ ] Login/registration works
[ ] Deposit flow renders Paybill 200999
[ ] Trading chart loads
[ ] Admin panel accessible
[ ] Run SSL Labs test — grade A or A+

