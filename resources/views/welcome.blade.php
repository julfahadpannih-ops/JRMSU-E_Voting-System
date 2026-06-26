@extends('layouts.app')
@section('title', 'JRMSU SSG E-Voting System — Siocon Campus')

@section('extra-styles')
    :root {
        --navy:       #001f3f;
        --navy-light: #003366;
        --gold:       #DAA520;
        --gold-light: #FFD700;
        --gold-muted: #f5e9c4;
    }

    * { box-sizing: border-box; }

    /* ── Hero ── */
    .hero {
        background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 60%, #004080 100%);
        position: relative;
        overflow: hidden;
    }

    /* Subtle diagonal stripe texture */
    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: repeating-linear-gradient(
            -45deg,
            transparent,
            transparent 40px,
            rgba(218,165,32,.06) 40px,
            rgba(218,165,32,.06) 41px
        );
        pointer-events: none;
    }

    /* Gold circle glow — top right */
    .hero::after {
        content: '';
        position: absolute;
        top: -120px;
        right: -120px;
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(218,165,32,.18) 0%, transparent 70%);
        pointer-events: none;
    }

    /* ── Seal ring ── */
    .seal-ring {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid var(--gold);
        box-shadow: 0 0 0 6px rgba(218,165,32,.20), 0 8px 32px rgba(0,0,0,.4);
        object-fit: cover;
        background: white;
    }

    /* ── Gold badge divider ── */
    .gold-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--gold);
    }
    .gold-divider span { height: 1px; flex: 1; background: var(--gold); opacity: .5; }

    /* ── Feature cards ── */
    .feature-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-top: 3px solid var(--gold);
        border-radius: 10px;
        padding: 28px 24px;
        transition: transform .2s, box-shadow .2s;
    }
    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,31,63,.12);
    }

    /* ── CTA buttons ── */
    .btn-gold {
        background: var(--gold);
        color: var(--navy);
        font-weight: 700;
        padding: 14px 36px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        font-size: 1rem;
        transition: background .2s, transform .15s, box-shadow .2s;
        border: 2px solid var(--gold);
    }
    .btn-gold:hover {
        background: var(--gold-light);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(218,165,32,.4);
    }
    .btn-outline {
        background: transparent;
        color: white;
        font-weight: 600;
        padding: 14px 36px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        font-size: 1rem;
        border: 2px solid rgba(255,255,255,.55);
        transition: border-color .2s, background .2s, transform .15s;
    }
    .btn-outline:hover {
        border-color: white;
        background: rgba(255,255,255,.08);
        transform: translateY(-2px);
    }

    /* ── How it works steps ── */
    .step-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--gold);
        color: var(--navy);
        font-size: 1.1rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .step-connector {
        flex: 1;
        height: 2px;
        background: linear-gradient(to right, var(--gold), rgba(218,165,32,.2));
    }

    /* ── Stats strip ── */
    .stat-strip {
        background: linear-gradient(90deg, var(--navy) 0%, var(--navy-light) 100%);
        border-top: 3px solid var(--gold);
    }

    /* ── Footer ── */
    footer {
        background: #0a1628;
        border-top: 3px solid var(--gold);
    }

    /* Pulse for the vote icon */
    @keyframes subtlePulse {
        0%, 100% { opacity: .7; }
        50% { opacity: 1; }
    }
    .pulse-icon { animation: subtlePulse 2.5s ease-in-out infinite; }

    /* Fade-in on load */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(22px); }
        to   { opacity: 1; transform: translateY(0);    }
    }
    .fade-up { animation: fadeUp .65s ease both; }
    .fade-up-d1 { animation-delay: .1s; }
    .fade-up-d2 { animation-delay: .22s; }
    .fade-up-d3 { animation-delay: .34s; }
    .fade-up-d4 { animation-delay: .46s; }
@endsection

@section('body')

{{-- ══════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════ --}}
<nav style="background: var(--navy); border-bottom: 2px solid var(--gold);" class="sticky top-0 z-50 shadow-lg">
    <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
        {{-- Logo + name --}}
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/SSG.jpg') }}" alt="SSG Logo"
                 class="w-10 h-10 rounded-full object-cover border-2"
                 style="border-color: var(--gold);">
            <div>
                <p class="text-white font-bold text-sm leading-tight">JRMSU SSG E-Voting</p>
                <p style="color: var(--gold-light); font-size: 11px;" class="leading-tight">Siocon Campus</p>
            </div>
        </div>

        {{-- Nav links --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('voter.login') }}"
               style="color: rgba(255,255,255,.85);"
               class="text-sm font-medium hover:text-white transition-colors px-3 py-1">
                Voter Login
            </a>
            <a href="{{ route('admin.login') }}"
               style="color: rgba(255,255,255,.85);"
               class="text-sm font-medium hover:text-white transition-colors px-3 py-1">
                Admin
            </a>
            <a href="{{ route('voter.login') }}"
               class="btn-gold text-sm !py-2 !px-5">
                Vote Now
            </a>
        </div>
    </div>
</nav>

{{-- ══════════════════════════════════════════
     HERO
══════════════════════════════════════════ --}}
<section class="hero py-20 px-6 text-center text-white relative z-10">
    <div class="max-w-3xl mx-auto relative z-10">

        {{-- Seal --}}
        <div class="fade-up flex justify-center mb-6">
            <img src="{{ asset('images/SSG.jpg') }}"
                 alt="JRMSU SSG Seal"
                 class="seal-ring">
        </div>

        {{-- Eyebrow --}}
        <div class="fade-up fade-up-d1 gold-divider justify-center mb-5 mx-auto max-w-xs">
            <span></span>
            <p style="color: var(--gold); font-size: 12px; letter-spacing: .12em; font-weight: 600;" class="uppercase whitespace-nowrap">
                A.Y. 2025–2026 Election
            </p>
            <span></span>
        </div>

        {{-- Headline --}}
        <h1 class="fade-up fade-up-d2 text-5xl font-extrabold leading-tight mb-4" style="letter-spacing: -.015em;">
            Your Vote.<br>
            <span style="color: var(--gold-light);">Your Voice.</span>
        </h1>

        {{-- Subtext --}}
        <p class="fade-up fade-up-d3 text-lg mb-8 max-w-xl mx-auto" style="color: rgba(255,255,255,.75); line-height: 1.7;">
            The official online election platform of the <strong class="text-white">Jose Rizal Memorial State University</strong>
            Supreme Government of Students — Siocon Campus.
        </p>

        {{-- CTA --}}
        <div class="fade-up fade-up-d4 flex flex-wrap justify-center gap-4">
            <a href="{{ route('voter.login') }}" class="btn-gold">
                🗳&nbsp; Cast Your Vote
            </a>
            <a href="#how-it-works" class="btn-outline">
                How It Works
            </a>
        </div>

        {{-- Election status badge --}}
        <div class="mt-8 fade-up fade-up-d4">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold"
                  style="background: rgba(218,165,32,.15); border: 1px solid rgba(218,165,32,.35); color: var(--gold-light);">
                <span class="pulse-icon">●</span>
                Election is now open
            </span>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     STATS STRIP
══════════════════════════════════════════ --}}
<div class="stat-strip py-8 px-6">
    <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center text-white">
        <div>
            <p class="text-3xl font-extrabold" style="color: var(--gold-light);">100%</p>
            <p class="text-sm mt-1" style="color: rgba(255,255,255,.65);">Digital & Paperless</p>
        </div>
        <div>
            <p class="text-3xl font-extrabold" style="color: var(--gold-light);">Secure</p>
            <p class="text-sm mt-1" style="color: rgba(255,255,255,.65);">Encrypted Votes</p>
        </div>
        <div>
            <p class="text-3xl font-extrabold" style="color: var(--gold-light);">Live</p>
            <p class="text-sm mt-1" style="color: rgba(255,255,255,.65);">Real-time Results</p>
        </div>
        <div>
            <p class="text-3xl font-extrabold" style="color: var(--gold-light);">1 Vote</p>
            <p class="text-sm mt-1" style="color: rgba(255,255,255,.65);">Per Registered Voter</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     FEATURES
══════════════════════════════════════════ --}}
<section class="py-16 px-6 bg-gray-50">
    <div class="max-w-5xl mx-auto">

        <div class="text-center mb-12">
            <p class="text-xs uppercase font-semibold tracking-widest mb-2" style="color: var(--gold);">Platform Features</p>
            <h2 class="text-3xl font-extrabold" style="color: var(--navy);">Built for a Fair & Transparent Election</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">

            <div class="feature-card">
                <div class="text-3xl mb-3">🔒</div>
                <h3 class="font-bold text-lg mb-2" style="color: var(--navy);">Secure Authentication</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Voters log in using their registered student credentials. Each account is limited to one vote — no duplicates, no fraud.
                </p>
            </div>

            <div class="feature-card">
                <div class="text-3xl mb-3">📊</div>
                <h3 class="font-bold text-lg mb-2" style="color: var(--navy);">Real-time Results</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Watch the tally update live as votes come in. Transparent, instant, and visible to all registered participants.
                </p>
            </div>

            <div class="feature-card">
                <div class="text-3xl mb-3">🧑‍💼</div>
                <h3 class="font-bold text-lg mb-2" style="color: var(--navy);">Admin Dashboard</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Election officers can manage candidates, positions, and voters — plus export results to PDF or CSV instantly.
                </p>
            </div>

            <div class="feature-card">
                <div class="text-3xl mb-3">📋</div>
                <h3 class="font-bold text-lg mb-2" style="color: var(--navy);">Audit Logs</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Every admin action is logged with a timestamp. Full accountability from start to finish.
                </p>
            </div>

            <div class="feature-card">
                <div class="text-3xl mb-3">📱</div>
                <h3 class="font-bold text-lg mb-2" style="color: var(--navy);">Mobile Friendly</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Works on any device — phone, tablet, or desktop. Vote anytime, anywhere on campus.
                </p>
            </div>

            <div class="feature-card">
                <div class="text-3xl mb-3">🏆</div>
                <h3 class="font-bold text-lg mb-2" style="color: var(--navy);">Instant Winner Declaration</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Results are computed automatically. No manual counting errors — just clear, trustworthy tallies.
                </p>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════ --}}
<section id="how-it-works" class="py-16 px-6 bg-white">
    <div class="max-w-4xl mx-auto">

        <div class="text-center mb-12">
            <p class="text-xs uppercase font-semibold tracking-widest mb-2" style="color: var(--gold);">Step by Step</p>
            <h2 class="text-3xl font-extrabold" style="color: var(--navy);">How to Vote</h2>
        </div>

        {{-- Steps --}}
        <div class="flex flex-col gap-6 md:flex-row md:items-start">

            {{-- Step 1 --}}
            <div class="flex-1 flex flex-col items-center text-center">
                <div class="step-circle mb-4">1</div>
                <h4 class="font-bold mb-1" style="color: var(--navy);">Log In</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Use your registered student number and password to access the Voter Portal.</p>
            </div>

            <div class="hidden md:flex items-center pt-6">
                <div style="width: 60px; height: 2px; background: linear-gradient(to right, var(--gold), rgba(218,165,32,.25));"></div>
            </div>

            {{-- Step 2 --}}
            <div class="flex-1 flex flex-col items-center text-center">
                <div class="step-circle mb-4">2</div>
                <h4 class="font-bold mb-1" style="color: var(--navy);">Review Candidates</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Browse the official list of candidates for each SSG position before making your choice.</p>
            </div>

            <div class="hidden md:flex items-center pt-6">
                <div style="width: 60px; height: 2px; background: linear-gradient(to right, var(--gold), rgba(218,165,32,.25));"></div>
            </div>

            {{-- Step 3 --}}
            <div class="flex-1 flex flex-col items-center text-center">
                <div class="step-circle mb-4">3</div>
                <h4 class="font-bold mb-1" style="color: var(--navy);">Cast Your Vote</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Select your candidates and submit. Your vote is recorded securely and counted immediately.</p>
            </div>

            <div class="hidden md:flex items-center pt-6">
                <div style="width: 60px; height: 2px; background: linear-gradient(to right, var(--gold), rgba(218,165,32,.25));"></div>
            </div>

            {{-- Step 4 --}}
            <div class="flex-1 flex flex-col items-center text-center">
                <div class="step-circle mb-4">4</div>
                <h4 class="font-bold mb-1" style="color: var(--navy);">See Results</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Watch live election results update in real time after your vote is submitted.</p>
            </div>

        </div>

        {{-- CTA --}}
        <div class="text-center mt-12">
            <a href="{{ route('voter.login') }}" class="btn-gold">
                Go to Voter Portal →
            </a>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════
     CANDIDATES TEASER / POSITIONS HIGHLIGHT
══════════════════════════════════════════ --}}
<section class="py-16 px-6" style="background: var(--gold-muted);">
    <div class="max-w-3xl mx-auto text-center">
        <p class="text-xs uppercase font-semibold tracking-widest mb-3" style="color: var(--navy); opacity: .7;">SSG Positions</p>
        <h2 class="text-3xl font-extrabold mb-4" style="color: var(--navy);">Vote for Your Student Leaders</h2>
        <p class="text-gray-600 mb-8 leading-relaxed">
            This election covers all major SSG executive positions. Log in to view the complete list of candidates and their platforms.
        </p>
        <div class="flex flex-wrap justify-center gap-3 mb-8">
            @foreach(['President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor', 'P.R.O.', 'Senators'] as $pos)
                <span class="px-4 py-1.5 rounded-full text-sm font-semibold"
                      style="background: var(--navy); color: var(--gold-light);">
                    {{ $pos }}
                </span>
            @endforeach
        </div>
        <a href="{{ route('voter.login') }}" class="btn-gold">
            View Candidates & Vote
        </a>
    </div>
</section>

{{-- ══════════════════════════════════════════
     FOOTER
══════════════════════════════════════════ --}}
<footer class="py-10 px-6 text-center" style="background: #0a1628; border-top: 3px solid var(--gold);">
    <img src="{{ asset('images/SSG.jpg') }}"
         alt="SSG Logo"
         class="w-14 h-14 rounded-full object-cover mx-auto mb-4 border-2"
         style="border-color: var(--gold);">
    <p class="text-white font-bold text-base mb-1">JRMSU Supreme Government of Students</p>
    <p class="text-sm mb-4" style="color: rgba(255,255,255,.5);">Siocon Campus — Jose Rizal Memorial State University</p>
    <div class="flex justify-center gap-6 mb-6">
        <a href="{{ route('voter.login') }}" class="text-sm hover:underline" style="color: var(--gold);">Voter Login</a>
        <span style="color: rgba(255,255,255,.2);">|</span>
        <a href="{{ route('admin.login') }}" class="text-sm hover:underline" style="color: var(--gold);">Admin Login</a>
    </div>
    <p class="text-xs" style="color: rgba(255,255,255,.3);">
        &copy; {{ date('Y') }} JRMSU SSG E-Voting System. All rights reserved.
    </p>
</footer>

{{-- Smooth scroll for anchor links --}}
@push('scripts')
<script>
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});
</script>
@endpush

@endsection