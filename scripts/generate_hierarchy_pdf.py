#!/usr/bin/env python3
"""
Racksephnox · Corporate Hierarchy & Roles Document Generator
Produces a professional multi-page PDF for investor and partner review.
"""

from fpdf import FPDF
from datetime import datetime

# ─────────────────────────────────────────────────────────────
#  PDF class
# ─────────────────────────────────────────────────────────────
class HierarchyPDF(FPDF):
    def header(self):
        if self.page_no() > 1:
            self.set_font('Helvetica', 'I', 8)
            self.set_text_color(120, 120, 120)
            self.cell(0, 8, 'Racksephnox  |  Corporate Hierarchy & Roles', 0, 0, 'L')
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

    # ── heading styles ──
    def h1(self, text):
        self.set_font('Helvetica', 'B', 16)
        self.set_text_color(184, 134, 11)
        self.ln(4)
        self.multi_cell(0, 8, text, 0, 'L')
        self.set_draw_color(184, 134, 11)
        self.set_line_width(0.4)
        self.line(15, self.get_y() + 1, 195, self.get_y() + 1)
        self.ln(3)

    def h2(self, text):
        self.set_font('Helvetica', 'B', 12)
        self.set_text_color(80, 60, 20)
        self.ln(3)
        self.multi_cell(0, 6, text, 0, 'L')
        self.ln(1)

    def h3(self, text):
        self.set_font('Helvetica', 'B', 10.5)
        self.set_text_color(120, 90, 30)
        self.ln(1)
        self.multi_cell(0, 6, text, 0, 'L')

    def body(self, text, size=10.5):
        self.set_font('Helvetica', '', size)
        self.set_text_color(30, 30, 30)
        self.multi_cell(0, 6, text)
        self.ln(2)

    def bullet(self, text, indent=15, size=10.5):
        self.set_font('Helvetica', '', size)
        self.set_text_color(30, 30, 30)
        self.set_x(indent)
        self.multi_cell(0, 6, '- ' + text)
        self.ln(0.5)

    def nested_bullet(self, text, indent=22, size=10):
        self.set_font('Helvetica', '', size)
        self.set_text_color(60, 60, 60)
        self.set_x(indent)
        self.multi_cell(0, 5.5, '> ' + text)
        self.ln(0.3)

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

    def role_block(self, title, reports_to, responsibilities, color=(20, 20, 40)):
        self.set_fill_color(*color)
        self.set_text_color(255, 215, 0)
        self.set_font('Helvetica', 'B', 11)
        self.cell(0, 8, '  ' + title, 0, 1, 'L', True)

        self.set_text_color(150, 150, 150)
        self.set_font('Helvetica', 'I', 9)
        self.cell(0, 5, '  Reports to: ' + reports_to, 0, 1, 'L')
        self.ln(2)

        self.set_font('Helvetica', '', 10)
        self.set_text_color(30, 30, 30)
        for r in responsibilities:
            self.set_x(18)
            self.multi_cell(0, 5.5, '- ' + r)
        self.ln(3)

# ─────────────────────────────────────────────────────────────
#  Build the document
# ─────────────────────────────────────────────────────────────
pdf = HierarchyPDF(orientation='P', unit='mm', format='A4')
pdf.set_auto_page_break(auto=True, margin=20)
pdf.set_margins(15, 20, 15)

# ═══════════════════════════════════════════════════════════
# COVER
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.set_fill_color(10, 10, 20)
pdf.rect(0, 0, 210, 297, 'F')

pdf.set_fill_color(184, 134, 11)
pdf.rect(0, 40, 210, 2, 'F')
pdf.rect(0, 250, 210, 2, 'F')

pdf.set_y(70)
pdf.set_font('Helvetica', 'B', 32)
pdf.set_text_color(255, 215, 0)
pdf.cell(0, 15, 'RACKSEPHNOX', 0, 1, 'C')

pdf.set_font('Helvetica', '', 13)
pdf.set_text_color(212, 175, 55)
pdf.cell(0, 8, 'Global Cryptocurrency · Fintech · Gaming Empire', 0, 1, 'C')

pdf.ln(15)
pdf.set_draw_color(184, 134, 11)
pdf.line(70, pdf.get_y(), 140, pdf.get_y())
pdf.ln(15)

pdf.set_font('Helvetica', 'B', 22)
pdf.set_text_color(245, 245, 230)
pdf.cell(0, 12, 'Corporate Hierarchy', 0, 1, 'C')
pdf.cell(0, 12, 'Roles & Responsibilities', 0, 1, 'C')

pdf.ln(10)
pdf.set_font('Helvetica', 'I', 11)
pdf.set_text_color(180, 180, 180)
pdf.cell(0, 8, 'Organizational Architecture · Governance · Payment Structure', 0, 1, 'C')

pdf.set_y(240)
pdf.set_font('Helvetica', '', 10)
pdf.set_text_color(200, 200, 200)
pdf.cell(0, 6, 'Prepared for: Prospective Investment Partner', 0, 1, 'C')
pdf.cell(0, 6, f'Date: {datetime.now().strftime("%d %B %Y")}', 0, 1, 'C')

pdf.set_y(260)
pdf.set_font('Helvetica', 'B', 9)
pdf.set_text_color(184, 134, 11)
pdf.cell(0, 5, 'CONFIDENTIAL · FOR PARTNER REVIEW ONLY', 0, 1, 'C')
pdf.set_font('Helvetica', 'I', 8)
pdf.set_text_color(160, 160, 160)
pdf.cell(0, 5, 'Version 1.0 · Corporate Governance Document', 0, 1, 'C')

# ═══════════════════════════════════════════════════════════
# PURPOSE & PREAMBLE
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Purpose of This Document')

pdf.body(
    "This document defines the complete organizational hierarchy of Racksephnox - a global "
    "cryptocurrency, fintech, and gaming platform. It establishes every level of authority, "
    "from the Investor and Chief Admin down to field agents and support staff. Each role is "
    "described with its reporting line, core responsibilities, and payment structure.\n\n"
    "Racksephnox operates as a distributed, committee-governed corporate structure. The "
    "Owner has architected, developed, and delivered a fully functional product. The Investor "
    "funds the domain name and hosting infrastructure. Together, they enable the platform to "
    "launch globally across web, Android, and desktop interfaces."
)

pdf.h2('Organizational Philosophy')

pdf.bullet("Clarity - Every role has a defined scope and reporting line.")
pdf.bullet("Accountability - Each department is overseen by a dedicated Admin.")
pdf.bullet("Meritocracy - Compensation scales with performance and tenure.")
pdf.bullet("Transparency - Committee governance prevents unilateral decisions.")
pdf.bullet("Scalability - The structure supports growth from launch to global scale.")

# ═══════════════════════════════════════════════════════════
# HIERARCHY OVERVIEW
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Organizational Chart · Top to Bottom')

pdf.body(
    "The hierarchy below represents the complete corporate structure. It flows from "
    "highest authority (Investor & Owner) down to the field-level subordinates."
)

pdf.ln(3)

# Chart in monospaced style
pdf.set_font('Courier', 'B', 8.5)
pdf.set_text_color(30, 30, 30)

chart = """
                    +---------------------------------------+
                    |     INVESTOR / INFRASTRUCTURE PARTNER |
                    |     (Funds Domain & Hosting)          |
                    +---------------------------------------+
                                      |
                    +---------------------------------------+
                    |     CHIEF ADMIN / FOUNDER / OWNER     |
                    |     (Architect, Vision, Governance)   |
                    +---------------------------------------+
                                      |
                    +---------------------------------------+
                    |         EXECUTIVE COMMITTEE           |
                    |   (Policy, Oversight, Approvals)      |
                    +---------------------------------------+
                                      |
        +---------+---------+---------+---------+---------+---------+
        |         |         |         |         |         |         |
   +----v---+ +---v----+ +--v-----+ +--v-----+ +--v-----+ +--v-----+ +--v-----+
   | ADMIN  | | ADMIN  | | ADMIN  | | ADMIN  | | ADMIN  | | ADMIN  | | ADMIN  |
   |  #1    | |  #2    | |  #3    | |  #4    | |  #5    | |  #6    | |  #7    |
   +----+---+ +---+----+ +---+----+ +---+----+ +---+----+ +---+----+ +---+----+
        |         |         |         |         |         |         |
    +---+---+ +---+---+ +---+---+ +---+---+ +---+---+ +---+---+ +---+---+
    |   |   | |   |   | |   |   | |   |   | |   |   | |   |   | |   |   |
   AG  AG  AG AG  AG  AG AG  AG  AG AG  AG  AG AG  AG  AG AG  AG  AG AG  AG
"""

pdf.multi_cell(0, 4.2, chart, 0, 'L')
pdf.ln(4)

pdf.set_font('Helvetica', 'I', 9)
pdf.set_text_color(100, 100, 100)
pdf.cell(0, 5, 'Note: Admin #1 through #7 correspond to the seven core departments listed in Section 4.', 0, 1, 'C')

# ═══════════════════════════════════════════════════════════
# LEVEL 1 — INVESTOR
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Level 1 · Investor / Infrastructure Partner')

pdf.role_block(
    'INVESTOR',
    'Self-governed · Reports to no one',
    [
        'Funds the acquisition of the primary .com domain name.',
        'Funds the web hosting and server infrastructure on a monthly or annual basis.',
        'Approves major infrastructure upgrades that change cost baseline.',
        'Receives 30% of monthly gross revenue (negotiable up to 38%).',
        'Receives a monthly revenue statement with transparent breakdown.',
        'Retains the right to request audits of revenue data.',
        'Does not participate in daily operational decisions.',
        'Retains exit right with 30 days written notice.',
    ],
    color=(80, 50, 10),
)

pdf.h2('Compensation')

pdf.kv_table([
    ('Revenue Share', '30% of monthly gross (negotiable to max 38%)'),
    ('Payment Schedule', 'Monthly, by the 7th of the following month'),
    ('Payment Method', 'Bank transfer, M-Pesa, or crypto'),
    ('Reporting', 'Monthly revenue statement'),
    ('Duration', 'Rolling 12 months, auto-renewable'),
])

# ═══════════════════════════════════════════════════════════
# LEVEL 2 — CHIEF ADMIN / OWNER
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Level 2 · Chief Admin / Founder / Owner')

pdf.role_block(
    'CHIEF ADMIN (You)',
    'Investor (for funding relationship only)',
    [
        'Ultimate authority on product vision, architecture, and strategy.',
        'Sole architect of the platform - code, design, business logic, algorithms.',
        'Approves or rejects all major departmental decisions.',
        'Appoints and dismisses Department Admins.',
        'Chairs the Executive Committee.',
        'Owns final decision on revenue distribution among departments.',
        'Represents the company to regulators, partners, and press.',
        'Absorbs all operational costs from their revenue share.',
        'Retains ~62% of gross revenue as their net position.',
    ],
    color=(120, 80, 20),
)

pdf.h2('Direct Reports')

pdf.bullet("Executive Committee (all department heads)")
pdf.bullet("Personal Assistants (2)")
pdf.bullet("Legal & Compliance Officer")
pdf.bullet("Investor Relations Officer")

# ═══════════════════════════════════════════════════════════
# LEVEL 3 — EXECUTIVE COMMITTEE
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Level 3 · Executive Committee')

pdf.body(
    "The Executive Committee is the primary governance body. It meets weekly to review "
    "platform performance, resolve cross-department issues, approve spending requests, "
    "and prepare reports for the Chief Admin and Investor."
)

pdf.h2('Composition')

pdf.bullet("Chief Admin (Chair)")
pdf.bullet("All 7 Department Admins (Voting Members)")
pdf.bullet("Chief Financial Officer (Secretary)")
pdf.bullet("Chief Technology Officer (Advisor)")
pdf.bullet("Chief Marketing Officer (Advisor)")
pdf.bullet("Investor Representative (Observer)")

pdf.h2('Committee Responsibilities')

pdf.bullet("Approve monthly revenue statements before distribution.")
pdf.bullet("Resolve disputes between departments.")
pdf.bullet("Set quarterly targets and performance benchmarks.")
pdf.bullet("Approve large transactions above KES 500,000.")
pdf.bullet("Oversee compliance with regulatory requirements.")
pdf.bullet("Recommend policy changes to Chief Admin.")

# ═══════════════════════════════════════════════════════════
# LEVEL 4 — DEPARTMENT ADMINS
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Level 4 · Department Admins (7 Core Departments)')

pdf.body(
    "Each of the seven core business frameworks is led by a Department Admin. "
    "Department Admins have near-identical admin dashboards, with elevated access "
    "tailored to their specific domain. All Department Admins report to the Chief Admin "
    "and are voting members of the Executive Committee."
)

# ── Admin 1 ──
pdf.h2('Admin 1 · Bitcoin & Cryptocurrency Trading')

pdf.role_block(
    'HEAD OF CRYPTO TRADING',
    'Chief Admin',
    [
        'Oversees the Bitcoin and cryptocurrency trading module end-to-end.',
        'Manages the trading pair list, order book health, and liquidity.',
        'Monitors real-time market data feeds and ChartService accuracy.',
        'Reviews flagged transactions for potential fraud or manipulation.',
        'Coordinates with CTO on exchange API integrations.',
        'Manages subordinate trading agents and analysts.',
        'Reports weekly on trading volumes and P&L.',
    ],
)

pdf.h2('Subordinate Roles')

pdf.nested_bullet("Trading Analyst (3) - monitors markets, advises strategy")
pdf.nested_bullet("Trading Agent (5) - assists users, resolves queries")
pdf.nested_bullet("Fraud Officer (1) - investigates suspicious trades")

# ── Admin 2 ──
pdf.h2('Admin 2 · Forex Trading')

pdf.role_block(
    'HEAD OF FOREX TRADING',
    'Chief Admin',
    [
        'Owns the forex trading interface and companion integrations.',
        'Maintains forex pair data, spreads, and leverage tiers.',
        'Ensures regulatory alignment in each supported jurisdiction.',
        'Reviews forex trades above threshold for compliance.',
        'Manages subordinate forex analysts and agents.',
        'Reports on forex-specific revenue and volumes.',
    ],
)

pdf.h2('Subordinate Roles')

pdf.nested_bullet("Forex Analyst (2)")
pdf.nested_bullet("Forex Agent (4)")
pdf.nested_bullet("Compliance Officer (1)")

# ── Admin 3 ──
pdf.add_page()
pdf.h2('Admin 3 · Investment Plans & RX Machine Series')

pdf.role_block(
    'HEAD OF INVESTMENTS',
    'Chief Admin',
    [
        'Oversees all Investment Plans and RX Machine Series offerings.',
        'Sets tier pricing, duration, and projected ROI for each plan.',
        'Ensures no plan exceeds the platform\'s safe exposure thresholds.',
        'Reviews the daily profit accrual engine for correctness.',
        'Manages the VIP tier structure (Level 1-3 per machine).',
        'Approves early withdrawal requests beyond standard automation.',
        'Manages subordinate investment agents.',
    ],
)

pdf.h2('Subordinate Roles')

pdf.nested_bullet("Investment Analyst (3) - monitors plan performance")
pdf.nested_bullet("Investment Agent (5) - onboards users into plans")
pdf.nested_bullet("Risk Officer (1) - tracks exposure vs. reserves")

# ── Admin 4 ──
pdf.h2('Admin 4 · BTC Investment Machines')

pdf.role_block(
    'HEAD OF BTC MACHINES',
    'Chief Admin',
    [
        'Oversees the BTC Investment Machines program.',
        'Maintains machine performance metrics and uptime.',
        'Coordinates with the crypto trading team on BTC price feeds.',
        'Manages machine-level profit distribution.',
        'Reviews any anomalous machine payouts.',
        'Manages subordinate BTC machine agents.',
    ],
)

pdf.h2('Subordinate Roles')

pdf.nested_bullet("Machine Analyst (2)")
pdf.nested_bullet("Machine Agent (4)")

# ── Admin 5 ──
pdf.add_page()
pdf.h2('Admin 5 · Lottery & Casino Gaming')

pdf.role_block(
    'HEAD OF GAMING',
    'Chief Admin',
    [
        'Oversees the entire Lottery and Casino Gaming ecosystem.',
        'Manages RNG fairness, provably-fair audits, and RTP calibration.',
        'Approves new game launches and symbol sets.',
        'Oversees jackpot pool health and must-drop enforcement.',
        'Reviews big-win payouts before release.',
        'Runs the tournaments and guilds programs.',
        'Manages subordinate gaming agents.',
    ],
)

pdf.h2('Subordinate Roles')

pdf.nested_bullet("Game Analyst (3) - monitors RTP and player behaviour")
pdf.nested_bullet("Tournament Manager (1)")
pdf.nested_bullet("Gaming Agent (6) - supports players, runs promotions")

# ── Admin 6 ──
pdf.h2('Admin 6 · Loans Services')

pdf.role_block(
    'HEAD OF LOANS',
    'Chief Admin',
    [
        'Oversees all loan products and disbursement flows.',
        'Approves loans above the automated threshold.',
        'Monitors credit scoring accuracy and default rates.',
        'Manages the guarantor and collateral programs.',
        'Coordinates with collections on overdue accounts.',
        'Reviews loan-related fraud patterns.',
        'Manages subordinate loans officers.',
    ],
)

pdf.h2('Subordinate Roles')

pdf.nested_bullet("Loans Officer (4) - approves/rejects loan requests")
pdf.nested_bullet("Collections Agent (3) - follows up overdue loans")
pdf.nested_bullet("Credit Analyst (2) - refines scoring model")

# ── Admin 7 ──
pdf.add_page()
pdf.h2('Admin 7 · User Accounts, KYC & Support')

pdf.role_block(
    'HEAD OF USER OPERATIONS',
    'Chief Admin',
    [
        'Oversees user registration, login, and profile management.',
        'Manages the KYC verification queue.',
        'Approves identity documents and resolves verification issues.',
        'Runs the customer support ticketing system.',
        'Monitors account security and abuse patterns.',
        'Owns the referral program administration.',
        'Manages subordinate support staff.',
    ],
)

pdf.h2('Subordinate Roles')

pdf.nested_bullet("KYC Officer (4)")
pdf.nested_bullet("Support Agent (8)")
pdf.nested_bullet("Security Analyst (2)")

# ── Admin 8 ──
pdf.h2('Admin 8 · Marketing & Growth')

pdf.role_block(
    'HEAD OF MARKETING',
    'Chief Admin',
    [
        'Owns all marketing strategy and execution.',
        'Runs social media presence across all platforms.',
        'Manages influencer and affiliate partnerships.',
        'Coordinates paid advertising and campaign ROI.',
        'Reviews and approves all public communications.',
        'Runs the referral bonus structure.',
        'Manages subordinate marketing agents.',
    ],
)

pdf.h2('Subordinate Roles')

pdf.nested_bullet("Social Media Manager (1)")
pdf.nested_bullet("Content Creator (2)")
pdf.nested_bullet("Marketing Agent (10) - distributed across regions")

# ── Admin 9 ──
pdf.h2('Admin 9 · Communications & Internal Affairs')

pdf.role_block(
    'HEAD OF COMMUNICATIONS',
    'Chief Admin',
    [
        'Ensures clean flow of information between all offices.',
        'Manages internal announcements and policy updates.',
        'Coordinates between Committee, Admins, Agents, and Subordinates.',
        'Runs the internal knowledge base and documentation.',
        'Handles external PR and media relations.',
        'Oversees disputes and grievance resolution.',
        'Manages subordinate communications staff.',
    ],
)

pdf.h2('Subordinate Roles')

pdf.nested_bullet("Internal Comms Officer (2)")
pdf.nested_bullet("Documentation Officer (1)")
pdf.nested_bullet("Dispute Resolution Officer (1)")

# ═══════════════════════════════════════════════════════════
# LEVEL 5 — SUB-AGENTS
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Level 5 · Sub-Agents & Field Staff')

pdf.body(
    "Sub-Agents are the operational layer of Racksephnox. They report directly to "
    "their Department Admin and perform the daily tasks that keep the platform "
    "running. Sub-Agents may be promoted to Admin over time based on performance."
)

pdf.h2('Common Sub-Agent Responsibilities')

pdf.bullet("Handle daily operational tasks assigned by their Department Admin.")
pdf.bullet("Respond to customer tickets within defined SLA windows.")
pdf.bullet("Escalate complex issues to their Admin.")
pdf.bullet("Maintain accurate records in the admin dashboard.")
pdf.bullet("Attend weekly team meetings.")
pdf.bullet("Meet monthly performance KPIs.")

pdf.h2('Sub-Agent Tiers')

pdf.kv_table([
    ('Tier 1', 'Junior Agent - entry level, supervised closely'),
    ('Tier 2', 'Agent - fully autonomous in routine tasks'),
    ('Tier 3', 'Senior Agent - mentors others, handles escalations'),
    ('Tier 4', 'Lead Agent - potential promotion path to Admin'),
])

# ═══════════════════════════════════════════════════════════
# LEVEL 6 — CUSTOMERS
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Level 6 · Customers & Platform Users')

pdf.body(
    "Customers are the reason Racksephnox exists. Every role in this document ultimately "
    "serves the customer experience. Customers interact through the web platform, "
    "Android app, and desktop interface."
)

pdf.h2('Customer Journey')

pdf.bullet("Registration - sign up with email, phone, and password.")
pdf.bullet("KYC - submit identity documents for verification.")
pdf.bullet("Wallet Funding - deposit via M-Pesa or supported crypto.")
pdf.bullet("Product Selection - choose from trading, investments, machines, loans, or lottery.")
pdf.bullet("Activity - perform transactions, earn profits, participate in gaming.")
pdf.bullet("Withdrawal - request payouts to M-Pesa or bank.")
pdf.bullet("Support - reach agents via in-app chat, email, or ticket system.")

pdf.h2('Customer Rights')

pdf.bullet("Transparent terms and conditions.")
pdf.bullet("Timely processing of deposits and withdrawals.")
pdf.bullet("Protection of personal data.")
pdf.bullet("Fair treatment in disputes.")
pdf.bullet("Clear explanation of all fees.")

# ═══════════════════════════════════════════════════════════
# PAYMENT STRUCTURE
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Payment Structure · All Levels')

pdf.body(
    "All payments are made monthly. The first three months after launch and deployment "
    "are treated as a probation period during which base salaries apply. From month four "
    "onward, salaries increase by 35% plus weekend incentives and allowances based on class."
)

pdf.h2('Level-Based Compensation')

pdf.kv_table([
    ('Investor', '30% of gross revenue (negotiable to 38%)'),
    ('Chief Admin', '~62% of gross revenue (net after costs)'),
    ('Department Admin', 'KES 8,000 base for first 3 months'),
    ('Department Admin (post-3mo)', 'KES 10,800 base + weekend + allowance'),
    ('Senior Agent (Tier 3)', 'KES 5,000 base, scales with tier'),
    ('Agent (Tier 1-2)', 'KES 3,000 base, scales with tier'),
    ('Lead Agent (Tier 4)', 'KES 6,500 base, bonus-eligible'),
])

pdf.h2('Salary Progression')

pdf.body(
    "From month four onward, every staff member receives:"
)

pdf.bullet("Base Salary - 35% increase over probation level.")
pdf.bullet("Weekend Incentives - additional pay for weekend shifts.")
pdf.bullet("Allowances - transport, data, and equipment allowance.")
pdf.bullet("Performance Bonuses - based on department KPIs.")
pdf.bullet("Annual Loyalty Bonus - for staff continuing past 12 months.")

pdf.h2('Class System')

pdf.kv_table([
    ('Class A (VVIP)', 'Chief Admin and Committee Members'),
    ('Class B (VIP)', 'Department Admins'),
    ('Class C (Elite)', 'Lead Agents and Senior Analysts'),
    ('Class D (Standard)', 'Regular Agents'),
    ('Class E (Probation)', 'Newly onboarded staff'),
])

# ═══════════════════════════════════════════════════════════
# REPORTING & COMMUNICATION
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Reporting & Communication Flow')

pdf.h2('Formal Reporting Cadence')

pdf.bullet("Daily - Sub-agents submit task completion to their Admin.")
pdf.bullet("Weekly - Admins submit department summary to Executive Committee.")
pdf.bullet("Monthly - Committee submits consolidated report to Chief Admin + Investor.")
pdf.bullet("Quarterly - Full business review with Investor.")
pdf.bullet("Annually - Strategic planning session + compensation review.")

pdf.h2('Communication Channels')

pdf.kv_table([
    ('Internal Chat', 'Admin dashboard messaging between roles'),
    ('Email', 'Formal communication, escalations, external'),
    ('Video Calls', 'Weekly committee meetings, investor reviews'),
    ('Shared Docs', 'Policy, procedures, knowledge base'),
    ('Ticketing', 'Customer support and dispute resolution'),
])

# ═══════════════════════════════════════════════════════════
# GOVERNANCE PRINCIPLES
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Governance Principles')

pdf.h2('Decision Making')

pdf.bullet("Operational decisions - Department Admin's discretion.")
pdf.bullet("Cross-department decisions - Executive Committee vote.")
pdf.bullet("Strategic decisions - Chief Admin's final call.")
pdf.bullet("Financial decisions above KES 500,000 - Committee + Chief Admin.")

pdf.h2('Accountability')

pdf.bullet("Every role has measurable KPIs reviewed monthly.")
pdf.bullet("Underperformance triggers a documented improvement plan.")
pdf.bullet("Repeated underperformance leads to demotion or dismissal.")
pdf.bullet("Exceptional performance is rewarded with promotion and bonus.")

pdf.h2('Confidentiality')

pdf.bullet("All staff sign a confidentiality agreement.")
pdf.bullet("Revenue data, user data, and strategy are confidential.")
pdf.bullet("Violation results in immediate termination and legal action.")

pdf.h2('Dispute Resolution')

pdf.bullet("Step 1 - Direct discussion between parties.")
pdf.bullet("Step 2 - Escalation to Department Admin.")
pdf.bullet("Step 3 - Mediation by Communications Officer.")
pdf.bullet("Step 4 - Final arbitration by Executive Committee.")

# ═══════════════════════════════════════════════════════════
# LAUNCH PHASES
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.h1('Launch Phases & Staffing Timeline')

pdf.h2('Phase 1 · Pre-Launch (Investor Onboarded)')

pdf.bullet("Chief Admin finalises deployment.")
pdf.bullet("Domain name and hosting funded and live.")
pdf.bullet("Executive Committee formally appointed.")
pdf.bullet("Two Department Admins onboarded first (Gaming + Crypto).")

pdf.h2('Phase 2 · Soft Launch (Month 1-3)')

pdf.bullet("Remaining 5 Department Admins onboarded.")
pdf.bullet("Sub-agents hired per department priorities.")
pdf.bullet("Platform tested with limited user base.")
pdf.bullet("First revenue reports issued to Investor.")

pdf.h2('Phase 3 · Growth (Month 4-6)')

pdf.bullet("35% salary increases applied to all staff.")
pdf.bullet("Marketing push expands user base.")
pdf.bullet("Full staffing level reached across all 9 departments.")
pdf.bullet("Second investment round considered for scaling.")

pdf.h2('Phase 4 · Scale (Month 7-12)')

pdf.bullet("Global market expansion.")
pdf.bullet("Regional sub-offices opened (Nigeria, South Africa, India).")
pdf.bullet("Android app launched on Play Store.")
pdf.bullet("Desktop app launched.")

# ═══════════════════════════════════════════════════════════
# CLOSING
# ═══════════════════════════════════════════════════════════
pdf.add_page()
pdf.set_y(80)
pdf.set_font('Helvetica', 'B', 22)
pdf.set_text_color(184, 134, 11)
pdf.cell(0, 12, 'The Empire Awaits', 0, 1, 'C')

pdf.ln(10)
pdf.set_font('Helvetica', 'I', 12)
pdf.set_text_color(80, 60, 20)
pdf.multi_cell(0, 8,
    "Every role in this document is a pillar. Every pillar supports the vision. "
    "The architecture is complete. The team structure is defined. The path to launch "
    "is clear. All that remains is the infrastructure to bring it to life.",
    0, 'C')

pdf.ln(15)
pdf.set_font('Helvetica', 'B', 11)
pdf.set_text_color(30, 30, 30)
pdf.cell(0, 8, 'Racksephnox', 0, 1, 'C')
pdf.set_font('Helvetica', '', 10)
pdf.cell(0, 6, 'Infinite Spiral of Creation · 888 Hz · Golden Ratio Phi', 0, 1, 'C')

pdf.set_y(260)
pdf.set_font('Helvetica', 'I', 8)
pdf.set_text_color(150, 150, 150)
pdf.cell(0, 5, 'This document is confidential and intended solely for the recipient.', 0, 1, 'C')
pdf.cell(0, 5, f'Generated on {datetime.now().strftime("%d %B %Y")}', 0, 1, 'C')

# ─────────────────────────────────────────────────────────────
# SAVE
# ─────────────────────────────────────────────────────────────
output_path = '/root/racksephnox/Racksephnox_Hierarchy_Roles.pdf'
pdf.output(output_path)
print(f"PDF generated: {output_path}")
print(f"Pages: {pdf.page_no()}")
