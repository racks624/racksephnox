#!/usr/bin/env python3
"""
Racksephnox · Business Partnership Proposal PDF Generator
Produces a multi-page, investor-ready PDF.
"""

from fpdf import FPDF
from datetime import datetime

# ─────────────────────────────────────────────────────────────
#  PDF class with header & footer
# ─────────────────────────────────────────────────────────────
class ProposalPDF(FPDF):
    def header(self):
        if self.page_no() > 1:
            self.set_font('Helvetica', 'I', 8)
            self.set_text_color(120, 120, 120)
            self.cell(0, 8, 'Racksephnox  |  Business Partnership Proposal', 0, 0, 'L')
            self.cell(0, 8, 'Confidential', 0, 1, 'R')
            self.set_draw_color(212, 175, 55)
            self.set_line_width(0.3)
            self.line(10, 18, 200, 18)
            self.ln(6)

    def footer(self):
        self.set_y(-15)
        self.set_font('Helvetica', 'I', 8)
        self.set_text_color(120, 120, 120)
        self.cell(0, 8, f'Page {self.page_no()}', 0, 0, 'C')

    # ── helpers ──
    def h1(self, text):
        self.set_font('Helvetica', 'B', 16)
        self.set_text_color(184, 134, 11)
        self.ln(4)
        self.multi_cell(0, 8, text, 0, 'L')
        self.ln(2)

    def h2(self, text):
        self.set_font('Helvetica', 'B', 12)
        self.set_text_color(80, 60, 20)
        self.ln(2)
        self.multi_cell(0, 6, text, 0, 'L')
        self.ln(1)

    def body(self, text, size=10.5):
        self.set_font('Helvetica', '', size)
        self.set_text_color(30, 30, 30)
        self.multi_cell(0, 6, text)
        self.ln(2)

    def bullet(self, text, size=10.5):
        self.set_font('Helvetica', '', size)
        self.set_text_color(30, 30, 30)
        self.set_x(15)
        self.multi_cell(0, 6, '- ' + text)
        self.ln(1)

    def kv_table(self, rows, col1=70, col2=115, font_size=10):
        self.set_font('Helvetica', '', font_size)
        for i, (k, v) in enumerate(rows):
            fill = (i % 2 == 0)
            self.set_fill_color(245, 240, 220) if fill else self.set_fill_color(255, 255, 255)
            self.set_text_color(60, 40, 10)
            self.set_font('Helvetica', 'B', font_size)
            self.cell(col1, 8, '  ' + k, 1, 0, 'L', fill)
            self.set_font('Helvetica', '', font_size)
            self.set_text_color(30, 30, 30)
            self.cell(col2, 8, '  ' + v, 1, 1, 'L', fill)
        self.ln(3)

# ─────────────────────────────────────────────────────────────
#  Build the document
# ─────────────────────────────────────────────────────────────
pdf = ProposalPDF(orientation='P', unit='mm', format='A4')
pdf.set_auto_page_break(auto=True, margin=20)
pdf.set_margins(15, 20, 15)

# ═══════════════════════════════════════════════════════════
# COVER PAGE
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.set_fill_color(10, 10, 20)
pdf.rect(0, 0, 210, 297, 'F')

# Top decorative band
pdf.set_fill_color(184, 134, 11)
pdf.rect(0, 40, 210, 2, 'F')
pdf.rect(0, 250, 210, 2, 'F')

# Title block
pdf.set_y(70)
pdf.set_font('Helvetica', 'B', 32)
pdf.set_text_color(255, 215, 0)
pdf.cell(0, 15, 'RACKSEPHNOX', 0, 1, 'C')

pdf.set_font('Helvetica', '', 14)
pdf.set_text_color(212, 175, 55)
pdf.cell(0, 10, 'Divine Golden Phi  |  888 Hz', 0, 1, 'C')

pdf.ln(15)
pdf.set_draw_color(184, 134, 11)
pdf.line(70, pdf.get_y(), 140, pdf.get_y())
pdf.ln(15)

pdf.set_font('Helvetica', 'B', 22)
pdf.set_text_color(245, 245, 230)
pdf.cell(0, 12, 'Business Partnership', 0, 1, 'C')
pdf.cell(0, 12, 'Proposal', 0, 1, 'C')

pdf.ln(10)
pdf.set_font('Helvetica', 'I', 12)
pdf.set_text_color(180, 180, 180)
pdf.cell(0, 8, 'Strategic Investment Opportunity', 0, 1, 'C')
pdf.cell(0, 8, 'in a Cryptocurrency & Fintech Ecosystem', 0, 1, 'C')

# Bottom block
pdf.set_y(240)
pdf.set_font('Helvetica', '', 10)
pdf.set_text_color(200, 200, 200)
pdf.cell(0, 6, 'Prepared for: Prospective Investment Partner', 0, 1, 'C')
pdf.cell(0, 6, f'Date: {datetime.now().strftime("%d %B %Y")}', 0, 1, 'C')

pdf.set_y(260)
pdf.set_font('Helvetica', 'B', 9)
pdf.set_text_color(184, 134, 11)
pdf.cell(0, 5, 'CONFIDENTIAL DOCUMENT', 0, 1, 'C')
pdf.set_font('Helvetica', 'I', 8)
pdf.set_text_color(160, 160, 160)
pdf.cell(0, 5, 'Not for public distribution', 0, 1, 'C')

# ═══════════════════════════════════════════════════════════
# LETTER OF INTRODUCTION
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.set_y(25)
pdf.h1('Letter of Introduction')

pdf.body(
    "Dear Prospective Partner,\n\n"
    "Thank you for considering this partnership proposal. Racksephnox is a comprehensive "
    "cryptocurrency, fintech, and digital wealth platform built on modern technology and "
    "designed for long-term scalability. We are seeking a strategic partner to fund the "
    "initial infrastructure acquisition - specifically the domain name and web hosting - "
    "in exchange for a defined monthly revenue share.\n\n"
    "This document details the opportunity, the structure of the partnership, the revenue "
    "distribution model, and the terms under which we propose to collaborate. All figures "
    "are transparent, and the terms are negotiable in good faith.\n\n"
    "I invite you to review this proposal carefully. Should the structure align with your "
    "investment goals, I look forward to a productive discussion.\n\n"
    "With respect and clarity,\n\n"
    "Founder & Owner\n"
    "Racksephnox"
)

# ═══════════════════════════════════════════════════════════
# EXECUTIVE SUMMARY
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Executive Summary')

pdf.body(
    "Racksephnox is a live, fully developed digital wealth platform integrating "
    "cryptocurrency trading, machine-based investment products, lottery mechanics, "
    "loan services, and referral systems into a single unified ecosystem. The "
    "application is complete and awaiting production deployment.\n\n"
    "We propose a revenue-sharing partnership in which the Investor funds the "
    "domain name and web hosting infrastructure, and in return receives 30% of "
    "monthly net revenue from the platform. The Owner retains 70% of gross revenue "
    "but absorbs all operational costs - resulting in an approximate 62% net share.\n\n"
    "All terms are negotiable. The Owner's firm floor is a 62% net position, meaning "
    "the Investor may negotiate their share upward to a maximum of 38%."
)

pdf.h2('The Opportunity in Brief')

pdf.kv_table([
    ('Investment Required', 'Domain name + web hosting setup'),
    ('Investor Revenue Share', '30% of monthly gross revenue (negotiable up to 38%)'),
    ('Owner Revenue Share', '70% of monthly gross revenue (net ~62% after costs)'),
    ('Payment Frequency', 'Monthly'),
    ('Platform Status', 'Fully developed - ready for deployment'),
    ('Technology', 'Laravel 13, PHP 8.4, SQLite/Postgres, Docker'),
    ('Primary Market', 'Kenya, Africa, and global crypto-adjacent users'),
])

# ═══════════════════════════════════════════════════════════
# 1. BUSINESS OVERVIEW
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('1. Business Overview')

pdf.body(
    "Racksephnox is a multi-revenue digital platform that consolidates several "
    "high-demand financial services into a single authenticated experience. "
    "The platform's core philosophy centers on transparency, speed, and "
    "long-term scalability, with a strong visual identity inspired by the "
    "golden ratio and sacred geometry."
)

pdf.h2('1.1 Core Business Modules')

pdf.bullet("Cryptocurrency Trading Console - Real-time charts, order book, buy/sell engine, KLineChart rendering, 5-second live polling.")
pdf.bullet("Machine Investment Products - RX Machine Series with 7 tiers and 3 VIP levels each, projected returns up to 88% over 14-day cycles.")
pdf.bullet("Lottery Framework - Provably fair spins, multi-tier progressive jackpots, achievements, missions, tournaments, and guilds.")
pdf.bullet("Loans Services - Loan products, credit scoring, automated repayment scheduling, and default detection.")
pdf.bullet("Wallet & Transactions - Full double-entry ledger, deposits via M-Pesa, withdrawals, and audit trails.")
pdf.bullet("Referral Program - Multi-level referral bonuses with transparent tracking.")
pdf.bullet("Admin Console - Complete management dashboard for users, transactions, lottery, machines, loans, and reports.")

pdf.h2('1.2 Technology Stack')

pdf.kv_table([
    ('Framework', 'Laravel 13 (PHP 8.4)'),
    ('Database', 'SQLite (dev) / PostgreSQL (production-ready)'),
    ('Frontend', 'Blade + TailwindCSS + Alpine.js'),
    ('Charts', 'KLineChart (MIT) - mobile-optimised, ~40 KB'),
    ('Deployment', 'Docker + GitHub Actions CI/CD'),
    ('Repository', 'github.com/spiralsourcetechlaws-max8778/racksephnox'),
])

# ═══════════════════════════════════════════════════════════
# 2. MARKET OPPORTUNITY
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('2. Market Opportunity')

pdf.body(
    "The digital wealth and fintech sector in Africa and emerging markets continues "
    "to expand rapidly. Cryptocurrency adoption, mobile money penetration, and demand "
    "for accessible investment products are all growing at double-digit rates."
)

pdf.h2('2.1 Target Market')

pdf.bullet("Primary: Kenya - mobile-first users aged 18-45 with strong M-Pesa adoption.")
pdf.bullet("Secondary: East Africa (Uganda, Tanzania, Rwanda) via mobile-money corridors.")
pdf.bullet("Tertiary: Global diaspora seeking access to African-market fintech products.")

pdf.h2('2.2 Why Now')

pdf.bullet("Mobile money infrastructure in Kenya is world-class and mature.")
pdf.bullet("Crypto awareness has reached mainstream adoption in urban Africa.")
pdf.bullet("Established platforms are either over-priced, poorly supported, or geographically restricted.")
pdf.bullet("The technical foundation is already built - only infrastructure funding is required.")

# ═══════════════════════════════════════════════════════════
# 3. PARTNERSHIP STRUCTURE
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('3. Partnership Structure')

pdf.h2('3.1 Investor Contribution')

pdf.kv_table([
    ('Domain Name', 'Annual registration fee'),
    ('Web Hosting', 'Monthly or annual hosting plan'),
    ('SSL Certificate', 'Included with hosting'),
    ('Setup Support', 'Provided by Owner'),
])

pdf.h2('3.2 Investor Returns')

pdf.kv_table([
    ('Revenue Share', '30% of monthly gross revenue'),
    ('Payment Frequency', 'Monthly, on or before the 7th of each month'),
    ('Payment Method', 'Bank transfer, M-Pesa, or crypto (Investor\'s choice)'),
    ('Reporting', 'Monthly statement with full revenue breakdown'),
    ('Negotiable Uplift', 'Up to 38% based on negotiation'),
])

pdf.h2('3.3 Owner Contribution')

pdf.kv_table([
    ('Development', 'Full platform development (already completed)'),
    ('Maintenance', 'Ongoing technical support and updates'),
    ('Operations', 'Daily management, user support, compliance'),
    ('Cost Absorption', 'Hosting upgrades, payment gateway fees, marketing, taxes'),
])

# ═══════════════════════════════════════════════════════════
# 4. REVENUE DISTRIBUTION MODEL
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('4. Revenue Distribution Model')

pdf.body(
    "The revenue distribution model is designed for clarity and fairness. "
    "The Investor receives a fixed percentage of gross monthly revenue before "
    "operational costs are deducted. The Owner absorbs all operational expenses "
    "from their share."
)

pdf.h2('4.1 Distribution Waterfall')

pdf.kv_table([
    ('Gross Monthly Revenue', '100%'),
    ('Less: Investor Share (30%)', 'Paid directly to Investor'),
    ('Less: Owner Share (70%)', 'Retained by Owner'),
    ('Less: Operational Costs', 'Borne entirely by Owner'),
    ('Owner Net Position', 'Approximately 62% of gross'),
])

pdf.h2('4.2 Illustrative Example')

pdf.body(
    "For illustration, assume the platform generates KES 100,000 in gross monthly revenue:"
)

pdf.kv_table([
    ('Gross Revenue', 'KES 100,000'),
    ('Investor Share (30%)', 'KES 30,000'),
    ('Owner Gross Share (70%)', 'KES 70,000'),
    ('Operational Costs (~8%)', 'KES 8,000 (borne by Owner)'),
    ('Owner Net', 'KES 62,000 (62% of gross)'),
])

pdf.body(
    "Note: Operational costs vary month-to-month. The 8% figure reflects an "
    "average across hosting upgrades, payment gateway fees, marketing spend, "
    "and tax obligations."
)

# ═══════════════════════════════════════════════════════════
# 5. FINANCIAL PROJECTIONS
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('5. Financial Projections')

pdf.h2('5.1 Conservative 12-Month Outlook')

pdf.body(
    "The projections below assume gradual user acquisition, with revenue "
    "growing from a modest base to moderate monthly volumes over the first year."
)

pdf.kv_table([
    ('Months 1-3 (Launch)', 'KES 50,000 - KES 150,000 / month'),
    ('Months 4-6 (Traction)', 'KES 200,000 - KES 500,000 / month'),
    ('Months 7-9 (Growth)', 'KES 500,000 - KES 1,200,000 / month'),
    ('Months 10-12 (Scale)', 'KES 1,200,000 - KES 2,500,000 / month'),
])

pdf.h2('5.2 Investor Return Projection (30% Share)')

pdf.kv_table([
    ('Year 1 Total Revenue (est.)', 'KES 5,000,000 - KES 12,000,000'),
    ('Investor Share (30%)', 'KES 1,500,000 - KES 3,600,000'),
    ('Average Monthly Investor Return', 'KES 125,000 - KES 300,000'),
    ('Investment Recovery', 'Within 1-3 months of stable revenue'),
])

pdf.body(
    "Important: These figures are projections only and do not constitute a "
    "guarantee of returns. Actual performance will depend on user adoption, "
    "market conditions, and operational execution. Investors should assess "
    "these projections against their own risk tolerance."
)

# ═══════════════════════════════════════════════════════════
# 6. USE OF INVESTMENT
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('6. Use of Investment')

pdf.body(
    "The investment is used exclusively for infrastructure acquisition as "
    "outlined below. There are no hidden fees, and no portion of the investment "
    "is used for personal expenses."
)

pdf.kv_table([
    ('Domain Registration', 'Annual fee for racksephnox.us.ci or equivalent'),
    ('Web Hosting Setup', 'First month or year of hosting service'),
    ('SSL Certificate', 'Included with hosting provider'),
    ('Reserve', 'Small buffer for overages or upgrades'),
])

pdf.h2('6.1 Infrastructure Choice')

pdf.body(
    "The Owner will select the most cost-effective, reliable, and scalable "
    "hosting environment available at the time of deployment. Preference is "
    "given to platforms with strong uptime records and transparent pricing. "
    "The Investor will be informed of the final choice before purchase."
)

# ═══════════════════════════════════════════════════════════
# 7. INVESTOR BENEFITS
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('7. Investor Benefits')

pdf.bullet("Passive monthly income from a fully developed, revenue-generating platform.")
pdf.bullet("No technical involvement required - the Owner handles all operations.")
pdf.bullet("Full transparency through monthly revenue statements.")
pdf.bullet("Negotiable share structure with room to increase position.")
pdf.bullet("Exposure to the fast-growing African fintech and crypto market.")
pdf.bullet("Low entry cost - the infrastructure investment is modest.")
pdf.bullet("No dilution of the Investor's capital beyond the initial outlay.")

# ═══════════════════════════════════════════════════════════
# 8. OWNER COMMITMENT
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('8. Owner Commitment')

pdf.body(
    "The Owner is fully committed to the long-term success of Racksephnox. "
    "The platform represents years of development effort and a personal vision "
    "of accessible digital wealth for emerging markets."
)

pdf.bullet("Full-time dedication to platform operations and growth.")
pdf.bullet("Continuous development of new features and improvements.")
pdf.bullet("Transparent communication with the Investor at all times.")
pdf.bullet("Absorption of all operational costs from the Owner's share.")
pdf.bullet("Monthly reporting with clear revenue breakdowns.")
pdf.bullet("Protection of the Investor's capital through prudent management.")

# ═══════════════════════════════════════════════════════════
# 9. RISK ANALYSIS
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('9. Risk Analysis & Mitigation')

pdf.h2('9.1 Identified Risks')

pdf.bullet("Market Adoption Risk - Users may be slow to onboard.")
pdf.bullet("Regulatory Risk - Crypto and fintech regulations may tighten.")
pdf.bullet("Technical Risk - Downtime, bugs, or security incidents.")
pdf.bullet("Revenue Volatility - Monthly revenue may fluctuate.")

pdf.h2('9.2 Mitigation Strategies')

pdf.bullet("Market: Aggressive referral program + targeted marketing campaigns.")
pdf.bullet("Regulatory: Compliance-first design, KYC/AML built in, legal review before scaling.")
pdf.bullet("Technical: Automated backups, monitoring, rate limiting, penetration testing.")
pdf.bullet("Revenue: Diversified revenue streams reduce reliance on any single product.")

# ═══════════════════════════════════════════════════════════
# 10. TERMS & CONDITIONS (NEGOTIABLE)
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('10. Terms & Conditions')

pdf.body(
    "The following terms are proposed as a starting point. All terms are "
    "negotiable in good faith."
)

pdf.kv_table([
    ('Revenue Split', '30% Investor / 70% Owner (negotiable up to 38%)'),
    ('Payment Schedule', 'Monthly, by the 7th of the following month'),
    ('Reporting', 'Monthly statement provided to Investor'),
    ('Duration', 'Rolling 12 months, auto-renewable by mutual consent'),
    ('Exit Clause', 'Either party may terminate with 30 days written notice'),
    ('Dispute Resolution', 'Mutual discussion; if unresolved, mediation'),
    ('Confidentiality', 'Both parties agree to keep terms confidential'),
    ('Amendments', 'Any change to revenue split requires written agreement'),
])

pdf.h2('10.1 Owner\'s Firm Floor')

pdf.body(
    "The Owner's minimum acceptable net position is 62% of gross revenue. "
    "This means the maximum the Investor may negotiate to is 38%. Beyond this "
    "point, the Owner's operational obligations cannot be sustained profitably."
)

# ═══════════════════════════════════════════════════════════
# 11. WHY THIS PARTNERSHIP
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('11. Why This Partnership')

pdf.body(
    "This is not a pitch for a hypothetical idea. The platform is fully built, "
    "the code is complete, and the infrastructure requirement is modest. The "
    "only barrier between the current state and a revenue-generating platform "
    "is the cost of a domain name and hosting."
)

pdf.bullet("The technology is already done - no development risk.")
pdf.bullet("The investment required is small - low capital exposure.")
pdf.bullet("The revenue split is generous - the Investor receives a clean 30%.")
pdf.bullet("The market is real and growing - African fintech is a proven sector.")
pdf.bullet("The Owner is committed - long-term vision, not a quick flip.")

# ═══════════════════════════════════════════════════════════
# 12. NEXT STEPS
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('12. Next Steps')

pdf.body(
    "If you are interested in exploring this partnership further, the following "
    "steps outline a clear path forward:"
)

pdf.bullet("Step 1: Review this proposal thoroughly.")
pdf.bullet("Step 2: Schedule a call or meeting to discuss questions and terms.")
pdf.bullet("Step 3: Agree on the final revenue split and payment schedule.")
pdf.bullet("Step 4: Sign a simple partnership agreement.")
pdf.bullet("Step 5: Investor funds the domain and hosting acquisition.")
pdf.bullet("Step 6: Owner deploys the platform to production.")
pdf.bullet("Step 7: First revenue statement issued one month later.")

pdf.ln(6)
pdf.h2('Contact')

pdf.body(
    "For further discussion, please reach out directly to the Owner. "
    "Detailed financials, technical documentation, or a live demo of the "
    "platform can be provided upon request."
)

# ═══════════════════════════════════════════════════════════
# CLOSING PAGE
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.set_y(100)
pdf.set_font('Helvetica', 'B', 20)
pdf.set_text_color(184, 134, 11)
pdf.cell(0, 12, 'Thank You', 0, 1, 'C')

pdf.ln(10)
pdf.set_font('Helvetica', 'I', 12)
pdf.set_text_color(80, 60, 20)
pdf.cell(0, 8, 'Your partnership can turn this vision into reality.', 0, 1, 'C')
pdf.cell(0, 8, 'Let us build abundance together.', 0, 1, 'C')

pdf.ln(20)
pdf.set_font('Helvetica', 'B', 11)
pdf.set_text_color(30, 30, 30)
pdf.cell(0, 8, 'Racksephnox', 0, 1, 'C')
pdf.set_font('Helvetica', '', 10)
pdf.cell(0, 6, 'Infinite Spiral of Creation  |  888 Hz  |  Golden Ratio Phi', 0, 1, 'C')

pdf.set_y(260)
pdf.set_font('Helvetica', 'I', 8)
pdf.set_text_color(150, 150, 150)
pdf.cell(0, 5, 'This document is confidential and intended solely for the named recipient.', 0, 1, 'C')
pdf.cell(0, 5, f'Generated on {datetime.now().strftime("%d %B %Y")}', 0, 1, 'C')

# ─────────────────────────────────────────────────────────────
# Save
# ─────────────────────────────────────────────────────────────
output_path = '/root/racksephnox/Racksephnox_Partnership_Proposal.pdf'
pdf.output(output_path)
print(f"PDF generated: {output_path}")
print(f"Pages: {pdf.page_no()}")
