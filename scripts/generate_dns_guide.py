#!/usr/bin/env python3
"""Racksephnox · Domain Name & DNS Setup Guide"""
from fpdf import FPDF
from datetime import datetime
import re

# ─── Unicode → ASCII sanitizer ───
REPLACEMENTS = {
    '\u2014': '-',   # em dash
    '\u2013': '-',   # en dash
    '\u2018': "'",   # left single quote
    '\u2019': "'",   # right single quote
    '\u201C': '"',   # left double quote
    '\u201D': '"',   # right double quote
    '\u2026': '...', # ellipsis
    '\u00B7': '-',   # middle dot
    '\u2022': '*',   # bullet
    '\u00D7': 'x',   # multiplication
    '\u00B0': 'deg', # degree
    '\u2264': '<=',  # <=
    '\u2265': '>=',  # >=
    '\u03C6': 'phi', # phi
    '\u03BB': 'lam', # lambda
    '\u03C0': 'pi',  # pi
    '\u2192': '->',  # right arrow
    '\u2190': '<-',  # left arrow
    '\u2705': '[OK]',
    '\u274C': '[X]',
    '\u2605': '*',
    '\u2606': '*',
    '\u2028': ' ',
    '\u2029': ' ',
    '\u00A0': ' ',
    '\u2011': '-',
    '\u2012': '-',
    '\u2015': '-',
}

def sanitize(text):
    """Convert any text to Latin-1 safe ASCII."""
    if not isinstance(text, str):
        text = str(text)
    for k, v in REPLACEMENTS.items():
        text = text.replace(k, v)
    # Any remaining non-Latin1 chars → '?'
    return text.encode('latin-1', 'replace').decode('latin-1')


class GuidePDF(FPDF):
    def header(self):
        if self.page_no() > 1:
            self.set_font('Helvetica', 'I', 8)
            self.set_text_color(120, 120, 120)
            self.cell(0, 8, sanitize('Racksephnox | Domain & DNS Setup Guide'), 0, 0, 'L')
            self.cell(0, 8, 'Confidential', 0, 1, 'R')
            self.set_draw_color(212, 175, 55)
            self.set_line_width(0.3)
            self.line(10, 18, 200, 18)
            self.ln(6)

    def footer(self):
        self.set_y(-15)
        self.set_font('Helvetica', 'I', 8)
        self.set_text_color(120, 120, 120)
        self.cell(0, 8, sanitize(f'Page {self.page_no()}'), 0, 0, 'C')

    def h1(self, t):
        self.set_font('Helvetica', 'B', 16)
        self.set_text_color(184, 134, 11)
        self.ln(4)
        self.multi_cell(0, 8, sanitize(t))
        self.set_draw_color(184, 134, 11)
        self.set_line_width(0.4)
        self.line(15, self.get_y() + 1, 195, self.get_y() + 1)
        self.ln(3)

    def h2(self, t):
        self.set_font('Helvetica', 'B', 12)
        self.set_text_color(80, 60, 20)
        self.ln(3)
        self.multi_cell(0, 6, sanitize(t))
        self.ln(1)

    def h3(self, t):
        self.set_font('Helvetica', 'B', 10.5)
        self.set_text_color(120, 90, 30)
        self.ln(1)
        self.multi_cell(0, 6, sanitize(t))

    def body(self, t, size=10.5):
        self.set_font('Helvetica', '', size)
        self.set_text_color(30, 30, 30)
        self.multi_cell(0, 6, sanitize(t))
        self.ln(2)

    def bullet(self, t, indent=15, size=10.5):
        self.set_font('Helvetica', '', size)
        self.set_text_color(30, 30, 30)
        self.set_x(indent)
        self.multi_cell(0, 6, sanitize('- ' + t))
        self.ln(0.4)

    def code(self, t, size=9):
        self.set_font('Courier', '', size)
        self.set_fill_color(245, 245, 245)
        self.set_text_color(20, 20, 80)
        self.multi_cell(0, 5, sanitize(t), 0, 'L', True)
        self.ln(2)

    def table(self, rows, col1=60, col2=125, size=10):
        for i, (k, v) in enumerate(rows):
            fill = (i % 2 == 0)
            self.set_fill_color(245, 240, 220) if fill else self.set_fill_color(255, 255, 255)
            self.set_text_color(60, 40, 10)
            self.set_font('Helvetica', 'B', size)
            self.cell(col1, 8, sanitize('  ' + k), 1, 0, 'L', fill)
            self.set_font('Helvetica', '', size)
            self.set_text_color(30, 30, 30)
            self.cell(col2, 8, sanitize('  ' + v), 1, 1, 'L', fill)
        self.ln(3)


pdf = GuidePDF(orientation='P', unit='mm', format='A4')
pdf.set_auto_page_break(auto=True, margin=20)
pdf.set_margins(15, 20, 15)

# ── COVER ──
pdf.add_page()
pdf.set_fill_color(10, 10, 20)
pdf.rect(0, 0, 210, 297, 'F')
pdf.set_fill_color(184, 134, 11)
pdf.rect(0, 40, 210, 2, 'F')
pdf.rect(0, 250, 210, 2, 'F')
pdf.set_y(75)
pdf.set_font('Helvetica', 'B', 30)
pdf.set_text_color(255, 215, 0)
pdf.cell(0, 15, 'RACKSEPHNOX', 0, 1, 'C')
pdf.set_font('Helvetica', '', 13)
pdf.set_text_color(212, 175, 55)
pdf.cell(0, 8, sanitize('Global Cryptocurrency | Fintech | Gaming Empire'), 0, 1, 'C')
pdf.ln(20)
pdf.set_font('Helvetica', 'B', 22)
pdf.set_text_color(245, 245, 230)
pdf.cell(0, 12, 'Domain Name & DNS', 0, 1, 'C')
pdf.cell(0, 12, 'Complete Setup Guide', 0, 1, 'C')
pdf.ln(12)
pdf.set_font('Helvetica', 'I', 11)
pdf.set_text_color(180, 180, 180)
pdf.cell(0, 8, 'From Purchase to Production Launch', 0, 1, 'C')
pdf.set_y(240)
pdf.set_font('Helvetica', '', 10)
pdf.set_text_color(200, 200, 200)
pdf.cell(0, 6, sanitize(f'Generated: {datetime.now().strftime("%d %B %Y")}'), 0, 1, 'C')
pdf.set_y(260)
pdf.set_font('Helvetica', 'B', 9)
pdf.set_text_color(184, 134, 11)
pdf.cell(0, 5, 'CONFIDENTIAL | INTERNAL USE', 0, 1, 'C')

# ── 1 ──
pdf.add_page()
pdf.h1('1. What Is a Domain Name?')
pdf.body(
    "A domain name is your address on the internet. When a user types "
    "racksephnox.us.ci into a browser, DNS (Domain Name System) translates that "
    "human-readable name into an IP address of the server hosting your app.\n\n"
    "A domain has three parts:\n\n"
    "  1. Top-Level Domain (TLD)  -  the extension (.com, .us.ci, .net)\n"
    "  2. Second-Level Domain     -  your brand name (racksephnox)\n"
    "  3. Subdomain               -  prefix like www, api, admin"
)
pdf.h2('Common TLD Examples')
pdf.table([
    ('.com', 'Commercial - global standard, ~KES 1,200/year'),
    ('.us.ci', 'Free subdomain from DNSHE - your current choice'),
    ('.net', 'Network - global alternative'),
    ('.io', 'Tech startups - expensive, ~$40/year'),
    ('.co.ke', 'Kenya - local, requires KRA PIN'),
])

# ── 2 ──
pdf.add_page()
pdf.h1('2. Choosing Your Domain Name')
pdf.h2('Guidelines')
pdf.bullet('Keep it short - under 15 characters is ideal')
pdf.bullet('Use your brand - racksephnox, not crypto-invest-2025')
pdf.bullet('Avoid hyphens - hard to say over phone')
pdf.bullet('Prefer .com if budget allows - most trusted globally')
pdf.bullet('Free alternatives (.us.ci, .de5.net) work for launch phase')
pdf.h2('For Racksephnox - Recommended Plan')
pdf.table([
    ('Primary (now)', 'racksephnox.us.ci - free via DNSHE'),
    ('Primary (funded)', 'racksephnox.com - buy once investor is onboard'),
    ('Secondary', 'racksephnox.co.ke - protect the brand locally'),
    ('Future', 'racksephnox.io - tech positioning'),
])

# ── 3 ──
pdf.add_page()
pdf.h1('3. Where to Buy Domains')
pdf.h2('Global Registrars')
pdf.table([
    ('Namecheap', 'Cheap, free WHOIS privacy, ~$10/year .com'),
    ('Cloudflare Registrar', 'At-cost pricing, no markup, ~$9/year .com'),
    ('GoDaddy', 'Popular but expensive renewals'),
    ('Porkbun', 'Cheap, clean UI, free SSL'),
])
pdf.h2('Free Subdomain Providers')
pdf.table([
    ('DNSHE', 'Free .us.ci, .de5.net, .cc.cd, .bot.cd'),
    ('Stackryze', 'Free .indevs.in, .sryze.cc, .ryzedns.org'),
    ('Afraid DNS', 'Free .duckdns.org dynamic DNS'),
    ('FreeDNS', 'Many free subdomains, ad-supported'),
])
pdf.h2('Recommendation for Racksephnox')
pdf.body(
    "Phase 1 (Launch):  Use DNSHE free domain - racksephnox.us.ci\n"
    "Phase 2 (Funding): Buy racksephnox.com from Cloudflare Registrar\n"
    "Phase 3 (Scale):   Protect brand - buy .co.ke and .io as well"
)

# ── 4 ──
pdf.add_page()
pdf.h1('4. Understanding DNS Records')
pdf.body(
    "DNS records tell the internet where to send traffic for your domain. Each "
    "record has a Type, a Name (host), a Value, and a TTL (time-to-live in seconds)."
)
pdf.h2('Record Types You Will Use')
pdf.table([
    ('A', 'Maps domain to IPv4 address (e.g., 129.146.123.45)'),
    ('AAAA', 'Maps domain to IPv6 address'),
    ('CNAME', 'Maps domain to another domain (alias)'),
    ('MX', 'Mail exchange - routes email'),
    ('TXT', 'Free-text - used for verification (SPF, DKIM, etc.)'),
    ('NS', 'Nameserver - tells who manages DNS for your domain'),
    ('SRV', 'Service records - for specific apps like SIP'),
    ('CAA', 'Restricts which CAs can issue SSL certs'),
])
pdf.h2('TTL - Time To Live')
pdf.body(
    "TTL is how long (in seconds) resolvers cache a record.\n\n"
    "  300   = 5 minutes   (good for testing / changing often)\n"
    "  3600  = 1 hour      (balanced)\n"
    "  86400 = 24 hours    (stable production records)"
)

# ── 5 ──
pdf.add_page()
pdf.h1('5. Setting Up DNSHE (Your Current Domain)')
pdf.h2('Step 1 - Log In')
pdf.body("Go to dnshe.com and log in to your account.")
pdf.h2('Step 2 - Open Domain Manager')
pdf.body("Dashboard -> Domains -> Click 'racksephnox.us.ci'")
pdf.h2('Step 3 - Add DNS Records')
pdf.body(
    "Click the DNS Records tab. You will see an empty list.\n\n"
    "The first record to add depends on what will host the site."
)
pdf.h2('If Using Cloudflare Tunnel')
pdf.table([
    ('Type', 'CNAME'),
    ('Name', '@'),
    ('Value', '<TUNNEL-ID>.cfargotunnel.com'),
    ('TTL', '300'),
])
pdf.h2('If Using a Traditional Server')
pdf.table([
    ('Type', 'A'),
    ('Name', '@'),
    ('Value', '<SERVER-IP>'),
    ('TTL', '300'),
])
pdf.h2('WWW Redirect')
pdf.table([
    ('Type', 'CNAME'),
    ('Name', 'www'),
    ('Value', 'racksephnox.us.ci'),
    ('TTL', '300'),
])

# ── 6 ──
pdf.add_page()
pdf.h1('6. Cloudflare Integration (Production Path)')
pdf.body(
    "Cloudflare gives you free SSL, DDoS protection, CDN, and Tunnel access. "
    "It becomes the front door to your Racksephnox app."
)
pdf.h2('Step 1 - Create Cloudflare Account')
pdf.body("Sign up at dash.cloudflare.com (free plan is enough).")
pdf.h2('Step 2 - Add Your Domain')
pdf.bullet("Click 'Add a Site'")
pdf.bullet("Enter racksephnox.us.ci")
pdf.bullet("Choose Free plan")
pdf.bullet("Cloudflare shows you 2 nameservers, e.g. alexa.ns.cloudflare.com")
pdf.h2('Step 3 - Update Nameservers at DNSHE')
pdf.bullet("DNSHE Dashboard -> racksephnox.us.ci -> Nameservers tab")
pdf.bullet("Replace defaults with Cloudflare nameservers")
pdf.bullet("Save. DNS propagation takes 5-30 minutes")
pdf.h2('Step 4 - Verify in Cloudflare')
pdf.body("Back in Cloudflare, click 'Check nameservers now'. When it says 'Active', you are done.")
pdf.h2('Step 5 - Configure SSL/TLS')
pdf.table([
    ('SSL/TLS -> Overview', 'Set to "Full" (not Flexible)'),
    ('Edge Certificates', 'Always Use HTTPS: ON'),
    ('Edge Certificates', 'Automatic HTTPS Rewrites: ON'),
    ('Edge Certificates', 'Minimum TLS Version: 1.2'),
])

# ── 7 ──
pdf.add_page()
pdf.h1('7. Cloudflare Tunnel (Hosting on Your Phone)')
pdf.body(
    "Cloudflare Tunnel exposes your local Laravel app to the internet without "
    "any port forwarding or public IP. Perfect for hosting on Termux/Proot."
)
pdf.h2('Prerequisites')
pdf.bullet("cloudflared installed (already done)")
pdf.bullet("Domain added to Cloudflare")
pdf.bullet("Laravel app running on localhost:8000")
pdf.h2('Commands')
pdf.code(
    "cloudflared tunnel login\n"
    "cloudflared tunnel create racksephnox\n"
    "cloudflared tunnel route dns racksephnox racksephnox.us.ci"
)
pdf.h2('Config File - /root/.cloudflared/config.yml')
pdf.code(
    "tunnel: racksephnox\n"
    "credentials-file: /root/.cloudflared/<UUID>.json\n"
    "ingress:\n"
    "  - hostname: racksephnox.us.ci\n"
    "    service: http://localhost:8000\n"
    "  - hostname: www.racksephnox.us.ci\n"
    "    service: http://localhost:8000\n"
    "  - service: http_status:404"
)
pdf.h2('Run Everything')
pdf.code(
    "# Terminal 1\n"
    "php artisan serve --host=0.0.0.0 --port=8000\n\n"
    "# Terminal 2\n"
    "cloudflared tunnel run racksephnox"
)
pdf.h2('Visit')
pdf.body("Open https://racksephnox.us.ci in any browser worldwide. Done.")

# ── 8 ──
pdf.add_page()
pdf.h1('8. SSL/TLS Certificates')
pdf.body(
    "SSL encrypts traffic between your user and your server. Modern browsers "
    "require HTTPS. Google penalizes sites without it."
)
pdf.h2('Three Ways to Get SSL')
pdf.table([
    ('Cloudflare Edge', 'Free, automatic, no server config needed'),
    ("Let's Encrypt", 'Free, requires Certbot on server'),
    ('Commercial', '$50-500/year, wildcard + warranty'),
])
pdf.h2('For Racksephnox')
pdf.body(
    "Use Cloudflare Edge SSL - it is free, automatic, and renews itself.\n\n"
    "Set SSL/TLS mode to 'Full' in the Cloudflare dashboard. This encrypts "
    "traffic Cloudflare-to-phone while using Cloudflare's own cert for user-to-Cloudflare."
)

# ── 9 ──
pdf.add_page()
pdf.h1('9. Email Records (MX, SPF, DKIM, DMARC)')
pdf.body(
    "If you want hello@racksephnox.us.ci or admin@racksephnox.com to work, "
    "you need email records. Without them, sending emails from your domain "
    "goes to spam."
)
pdf.h2('MX Record (Mail Exchange)')
pdf.body("Tells the internet where to deliver email for your domain.")
pdf.table([
    ('Type', 'MX'),
    ('Name', '@'),
    ('Value', 'mail.example.com (your mail provider)'),
    ('Priority', '10'),
])
pdf.h2('SPF Record (Sender Policy Framework)')
pdf.body("TXT record that lists authorized mail servers.")
pdf.code('v=spf1 include:_spf.google.com ~all')
pdf.h2('DKIM Record (DomainKeys Identified Mail)')
pdf.body("Cryptographic signature - provided by your mail host.")
pdf.h2('DMARC Record')
pdf.body("Tells receivers how to handle failed SPF/DKIM.")
pdf.code('v=DMARC1; p=quarantine; rua=mailto:dmarc@racksephnox.us.ci')
pdf.h2('Free Mail Hosts')
pdf.table([
    ('Zoho Mail', 'Free for 5 users, custom domain'),
    ('Cloudflare Email Routing', 'Free forwarding, no sending'),
    ('Mailgun', 'Free tier - 1,000 emails/month'),
    ('Brevo', 'Free tier - 300 emails/day'),
])

# ── 10 ──
pdf.add_page()
pdf.h1('10. Troubleshooting DNS')
pdf.h2('Common Errors')
pdf.table([
    ('Domain not resolving', 'Check NS records propagated - dnschecker.org'),
    ('SSL error', 'Cloudflare SSL mode must be Full, not Flexible'),
    ('Site loads on www but not bare', 'Add CNAME @ to www.example.com'),
    ('Site loads everywhere except your phone', 'Clear DNS cache on phone'),
    ('Tunnel shows 502', 'Laravel server not running - check logs'),
    ('Login redirect loops', 'Set SESSION_DOMAIN=.yourdomain.com'),
    ('Assets show 404', 'Run npm run build + set ASSET_URL'),
])
pdf.h2('Diagnostic Commands')
pdf.code(
    "dig +short racksephnox.us.ci A\n"
    "dig +short racksephnox.us.ci CNAME\n"
    "nslookup racksephnox.us.ci\n"
    "curl -I https://racksephnox.us.ci"
)
pdf.h2('Online Tools')
pdf.bullet("dnschecker.org - global DNS propagation")
pdf.bullet("whatsmydns.net - global DNS check")
pdf.bullet("ssllabs.com/ssltest - SSL grade")
pdf.bullet("securityheaders.com - HTTP security audit")

# ── 11 ──
pdf.add_page()
pdf.h1('11. Complete DNS Record Reference')
pdf.h2('Cloudflare Tunnel Setup (Recommended)')
pdf.table([
    ('CNAME', '@', 'Tunnel CNAME (auto-created)'),
    ('CNAME', 'www', 'racksephnox.us.ci'),
    ('CNAME', 'api', 'racksephnox.us.ci (optional)'),
    ('CNAME', 'admin', 'racksephnox.us.ci (optional)'),
    ('MX', '@', 'your mail provider'),
    ('TXT', '@', 'v=spf1 ... (SPF)'),
    ('TXT', '_dmarc', 'v=DMARC1; ...'),
    ('CAA', '@', '0 issue "letsencrypt.org"'),
])
pdf.h2('Traditional VPS Setup')
pdf.table([
    ('A', '@', '<server IPv4>'),
    ('AAAA', '@', '<server IPv6>'),
    ('CNAME', 'www', 'racksephnox.us.ci'),
    ('MX', '@', 'mail.racksephnox.us.ci (priority 10)'),
    ('TXT', '@', 'v=spf1 ...'),
])

# ── 12 ──
pdf.add_page()
pdf.h1('12. Launch Checklist')
pdf.h2('Pre-Launch')
pdf.bullet("[ ] Domain registered and active")
pdf.bullet("[ ] Nameservers pointed to Cloudflare")
pdf.bullet("[ ] SSL/TLS mode set to Full")
pdf.bullet("[ ] Cloudflare tunnel created")
pdf.bullet("[ ] DNS records routing to tunnel")
pdf.bullet("[ ] Laravel APP_URL updated")
pdf.bullet("[ ] ASSET_URL matches APP_URL")
pdf.bullet("[ ] SESSION_DOMAIN set correctly")
pdf.bullet("[ ] SESSION_SECURE_COOKIE=true")
pdf.bullet("[ ] npm run build executed")
pdf.bullet("[ ] All caches cleared")
pdf.h2('Post-Launch')
pdf.bullet("[ ] Load https://racksephnox.us.ci - no SSL warnings")
pdf.bullet("[ ] Login/registration works")
pdf.bullet("[ ] Deposit flow renders Paybill 200999")
pdf.bullet("[ ] Trading chart loads")
pdf.bullet("[ ] Admin panel accessible")
pdf.bullet("[ ] Run SSL Labs test - grade A or A+")

# ── CLOSING ──
pdf.add_page()
pdf.set_y(100)
pdf.set_font('Helvetica', 'B', 22)
pdf.set_text_color(184, 134, 11)
pdf.cell(0, 12, 'The Empire Is Live', 0, 1, 'C')
pdf.ln(10)
pdf.set_font('Helvetica', 'I', 12)
pdf.set_text_color(80, 60, 20)
pdf.multi_cell(0, 8, sanitize(
    "Your domain is your address. Your DNS is your routing. "
    "Your tunnel is your road. Your app is the destination."
), 0, 'C')
pdf.ln(20)
pdf.set_font('Helvetica', 'B', 11)
pdf.set_text_color(30, 30, 30)
pdf.cell(0, 8, 'Racksephnox', 0, 1, 'C')
pdf.set_font('Helvetica', '', 10)
pdf.cell(0, 6, sanitize('Infinite Spiral of Creation | 888 Hz | Golden Ratio Phi'), 0, 1, 'C')

output = '/root/racksephnox/Racksephnox_Domain_DNS_Guide.pdf'
pdf.output(output)
print(f"PDF generated: {output}")
print(f"Pages: {pdf.page_no()}")
