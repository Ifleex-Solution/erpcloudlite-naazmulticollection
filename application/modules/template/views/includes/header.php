<style>
    /* ═══════════════════════════════════════════
       Top Bar Redesign — Dashboard Palette
    ═══════════════════════════════════════════ */

    /* ── Main header shell ── */
    .main-header {
        border-bottom: none !important;
        box-shadow: 0 1px 10px rgba(0,0,0,0.10) !important;
    }

    /* ── Logo area (matches dark sidebar) ── */
    .main-header .logo {
        background: #141E35 !important;
        color: #ffffff !important;
        border-bottom: none !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .main-header .logo:hover {
        background: #1A2540 !important;
    }
    .main-header .logo {
        overflow: hidden !important;
    }
    .main-header .logo img {
        max-height: 34px !important;
        width: auto !important;
        object-fit: contain !important;
    }
    /* Mini/collapsed sidebar — logo shrinks to ~50px wide */
    .main-header .logo .logo-mini img {
        max-height: 32px !important;
        max-width: 32px !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
    }
    .sidebar-mini.sidebar-collapse .main-header .logo {
        padding: 0 8px !important;
        justify-content: center !important;
    }

    /* ── Navbar ── */
    .main-header .navbar {
        background: #ffffff !important;
        border: none !important;
        min-height: 56px !important;
        box-shadow: none !important;
    }

    /* ── Sidebar toggle button ── */
    .main-header .sidebar-toggle {
        color: #64748B !important;
        font-size: 20px !important;
        padding: 0 18px !important;
        height: 56px !important;
        line-height: 56px !important;
        border-right: 1px solid #F1F5F9 !important;
        transition: background 0.15s, color 0.15s !important;
        float: left !important;
    }
    .main-header .sidebar-toggle:hover {
        background: #F8FAFC !important;
        color: #2563EB !important;
    }
    .main-header .sidebar-toggle::before {
        content: none !important;
    }

    /* ── Centered company name ── */
    .navbar h3 {
        position: absolute !important;
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
        color: #1E293B !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        letter-spacing: 0.4px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 40% !important;
    }

    /* ── Instance badge ── */
    .instance-badge {
        position: absolute !important;
        left: 70px !important;
        top: 50% !important;
        transform: translateY(-50%);
        padding: 3px 10px !important;
        border-radius: 20px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.8px !important;
        text-transform: uppercase !important;
        z-index: 10 !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12) !important;
    }
    .instance-dev  { background: #EF4444 !important; color: #fff !important; animation: heartbeat 10s ease-in-out infinite !important; }
    .instance-uat  { background: #F59E0B !important; color: #fff !important; animation: heartbeat 10s ease-in-out infinite !important; }
    .instance-beta { background: #2563EB !important; color: #fff !important; animation: heartbeat 10s ease-in-out infinite !important; }
    .instance-prod { background: #10B981 !important; color: #fff !important; animation: heartbeat 10s ease-in-out infinite !important; }
    .instance-live { background: #10B981 !important; color: #fff !important; animation: heartbeat 10s ease-in-out infinite !important; }
    .instance-demo { background: #6B7280 !important; color: #fff !important; animation: heartbeat 10s ease-in-out infinite !important; }
    .instance-marketing { background: #7C3AED !important; color: #fff !important; animation: heartbeat 10s ease-in-out infinite !important; }

    /* ── Right nav items ── */
    .navbar-custom-menu {
        height: 56px !important;
        display: flex !important;
        align-items: center !important;
    }
    .navbar-custom-menu > .navbar-nav {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 0 10px !important;
    }
    .navbar-custom-menu > .navbar-nav > li {
        display: flex !important;
        align-items: center !important;
        position: relative !important;
    }

    /* ── Icon buttons (base) ── */
    .navbar-custom-menu > .navbar-nav > li > a {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 42px !important;
        height: 42px !important;
        padding: 0 !important;
        border-radius: 13px !important;
        border: none !important;
        background: #EEF2F7 !important;
        box-shadow: none !important;
        position: relative !important;
        text-decoration: none !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    }
    .navbar-custom-menu > .navbar-nav > li > a:hover {
        transform: translateY(-3px) scale(1.06) !important;
        background: #F3F0FE !important;
        box-shadow: 0 6px 16px rgba(139,92,246,0.22) !important;
    }
    /* ── Icon colors (high specificity) ── */
    .main-header .navbar-custom-menu .navbar-nav li > a > i {
        font-size: 19px !important;
        color: #64748B !important;
        line-height: 1 !important;
        transition: transform 0.35s ease, color 0.2s ease !important;
        pointer-events: none !important;
    }
    .main-header .navbar-custom-menu .navbar-nav li > a:hover > i {
        color: #8b5cf6 !important;
    }
    .main-header .navbar-custom-menu .navbar-nav .nav-stock-alert > a > i,
    .main-header .navbar-custom-menu .navbar-nav .nav-expiry-alert > a > i,
    .main-header .navbar-custom-menu .navbar-nav .dropdown-user > a > i {
        color: #64748B !important;
    }

    /* ── Cog spin on hover ── */
    .main-header .navbar-custom-menu .navbar-nav .dropdown-user > a:hover .fa-cog {
        transform: rotate(180deg) !important;
    }

    /* ── Notification badge ── */
    .navbar-custom-menu .label {
        position: absolute !important;
        top: -9px !important;
        right: -9px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 24px !important;
        min-height: 24px !important;
        height: auto !important;
        padding: 2px 6px !important;
        line-height: 1.1 !important;
        white-space: nowrap !important;
        box-sizing: border-box !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        border-radius: 12px !important;
        border: 2.5px solid #fff !important;
        z-index: 2 !important;
        background: #1E293B !important;
        color: #fff !important;
    }
    @keyframes badge-pop {
        0%, 100% { transform: scale(1); }
        50%       { transform: scale(1.2); }
    }
    @keyframes heartbeat {
        0%   { transform: translateY(-50%) scale(1); }
        14%  { transform: translateY(-50%) scale(1.18); }
        28%  { transform: translateY(-50%) scale(1); }
        42%  { transform: translateY(-50%) scale(1.12); }
        60%  { transform: translateY(-50%) scale(1); }
        100% { transform: translateY(-50%) scale(1); }
    }
    .navbar-custom-menu .label.label-danger  { background: #DC2626 !important; animation: badge-pop 2s ease-in-out infinite !important; }
    .label-warning { background: #1E293B !important; }

    /* ── Update button ── */
    .update-btn {
        display: inline-flex !important;
        align-items: center !important;
        padding: 6px 14px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        background: #EF4444 !important;
        color: #fff !important;
        border: none !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        transition: background 0.15s !important;
        height: 34px !important;
    }
    .update-btn:hover {
        background: #DC2626 !important;
        color: #fff !important;
    }

    /* ── Settings dropdown ── */
    .navbar-custom-menu .dropdown-menu {
        border: none !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
        border-radius: 12px !important;
        padding: 6px !important;
        min-width: 170px !important;
        top: calc(100% + 8px) !important;
        right: 0 !important;
        left: auto !important;
        background: #ffffff !important;
    }
    .navbar-custom-menu .dropdown-menu > li > a {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        padding: 9px 12px !important;
        border-radius: 8px !important;
        color: #374151 !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        transition: background 0.12s, color 0.12s !important;
        text-decoration: none !important;
    }
    .navbar-custom-menu .dropdown-menu > li > a:hover {
        background: #F3F0FE !important;
        color: #8b5cf6 !important;
    }
    .navbar-custom-menu .dropdown-menu .dd-icon {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 30px !important;
        height: 30px !important;
        border-radius: 8px !important;
        flex-shrink: 0 !important;
        transition: transform 0.15s !important;
    }
    .navbar-custom-menu .dropdown-menu .dd-icon i {
        font-size: 14px !important;
        color: #000000 !important;
        line-height: 1 !important;
    }
    .navbar-custom-menu .dropdown-menu .dd-icon-profile {
        background: #F1F5F9 !important;
        box-shadow: none !important;
    }
    .navbar-custom-menu .dropdown-menu .dd-icon-logout {
        background: #F1F5F9 !important;
        box-shadow: none !important;
    }
    .navbar-custom-menu .dropdown-menu .dd-icon-password {
        background: #F1F5F9 !important;
        box-shadow: none !important;
    }
    .navbar-custom-menu .dropdown-menu > li > a:hover .dd-icon {
        transform: scale(1.12) !important;
    }

    /* ═══ "Go Back" button on add/edit form screens ═══ */
    .has-goback { justify-content: flex-start !important; }
.has-goback > .btn-goback:nth-child(2) { margin-left: auto; }
.has-goback > :nth-last-child(2):not(:first-child) { margin-left: auto; }
.btn.btn-goback {
        display: inline-flex !important; align-items: center; gap: 8px; float: right;
        padding: 7px 16px !important; border-radius: 9px !important; border: 1.5px solid #D8CCFB !important;
        background: #F5F2FE !important; color: #7C3AED !important; font-size: 13px !important; font-weight: 600 !important;
        line-height: 1.4 !important; text-decoration: none !important; box-shadow: none !important;
        transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease !important;
    }
    .btn.btn-goback i { font-size: 13px; transition: transform 0.2s ease; }
    .btn.btn-goback:hover, .btn.btn-goback:focus {
        background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3) !important;
        color: #fff !important; border-color: transparent !important; transform: translateX(-2px);
        box-shadow: 0 4px 12px rgba(139,92,246,0.35) !important;
    }
    .btn.btn-goback:hover i { transform: translateX(-3px); }
    /* ═══ Profile in the top bar: photo, or the first letter of the username ═══ */
    .navbar-custom-menu > .navbar-nav > li.dropdown-profile { margin-left: 6px !important; }
    .navbar-custom-menu > .navbar-nav > li.dropdown-profile > a.profile-trigger {
        width: auto !important;
        height: 46px !important;
        padding: 4px 12px 4px 5px !important;
        gap: 10px !important;
        border-radius: 999px !important;
        justify-content: flex-start !important;
        background: #EEF2F7 !important;
    }
    .navbar-custom-menu > .navbar-nav > li.dropdown-profile.open > a.profile-trigger,
    .navbar-custom-menu > .navbar-nav > li.dropdown-profile > a.profile-trigger:hover { background: #E2E8F2 !important; }

    .pf-avatar {
        position: relative; display: inline-block; flex-shrink: 0;
        width: 36px; height: 36px; padding: 2px; border-radius: 50%;
        background: conic-gradient(from 200deg, #245ad0, #8b5cf6, #e661f3, #245ad0);
    }
    .pf-avatar.pf-lg { width: 60px; height: 60px; padding: 3px; box-shadow: 0 6px 18px rgba(139,92,246,0.35); }
    .pf-avatar-in {
        width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 2px solid #fff;
        display: flex; align-items: center; justify-content: center; background: #fff;
    }
    .pf-avatar.pf-lg .pf-avatar-in { border-width: 3px; }
    .pf-avatar-in img { width: 100% !important; height: 100% !important; object-fit: cover !important; border-radius: 50% !important; }
    .pf-initial {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; border-radius: 50%;
        font-size: 15px; font-weight: 700; line-height: 1; color: #fff; user-select: none; text-shadow: 0 1px 3px rgba(0,0,0,0.25);
    }
    .pf-avatar.pf-lg .pf-initial { font-size: 24px; }
    .pf-dot {
        position: absolute; right: -1px; bottom: -1px; width: 12px; height: 12px; border-radius: 50%;
        background: #10B981; border: 2px solid #fff; animation: pfDotPulse 2.6s ease-in-out infinite;
    }
    .pf-avatar.pf-lg .pf-dot { width: 15px; height: 15px; border-width: 3px; right: 1px; bottom: 1px; }
    @keyframes pfDotPulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.55); } 50% { box-shadow: 0 0 0 5px rgba(16,185,129,0); } }

    .pf-text { display: flex; flex-direction: column; align-items: flex-start; text-align: left; line-height: 1.2; min-width: 0; }
    .pf-name { font-size: 13px; font-weight: 700; color: #1F2937; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .pf-role { font-size: 11px; font-weight: 600; color: #059669; }
    .main-header .navbar-custom-menu .navbar-nav li.dropdown-profile > a > i.pf-caret {
        font-size: 15px !important; color: #64748B !important; transition: transform 0.2s ease !important;
        /* the ERP gives header icons a 36px box with a grey background; the caret must be a bare arrow */
        width: auto !important; height: auto !important; min-width: 0 !important; background: transparent !important;
        box-shadow: none !important; border: 0 !important; border-radius: 0 !important; padding: 0 !important; margin: 0 !important; line-height: 1 !important;
    }
    .navbar-custom-menu > .navbar-nav > li.dropdown-profile.open > a > i.pf-caret { transform: rotate(180deg) !important; }

    /* Dropdown card */
    .navbar-custom-menu .dropdown-menu.pf-menu { min-width: 250px !important; padding: 0 !important; overflow: hidden; }
    .pf-head {
        padding: 20px 16px 16px; text-align: center;
        background: linear-gradient(160deg, rgba(36,90,208,0.09), rgba(139,92,246,0.09) 55%, rgba(230,97,243,0.09));
        border-bottom: 1px solid #EEF2F7;
    }
    .pf-head .pf-avatar { margin: 0 auto 10px; }
    .pf-head .pf-h-name { font-size: 15px; font-weight: 700; color: #111827; line-height: 1.25; word-break: break-word; }
    .pf-head .pf-h-role { display: inline-block; margin-top: 5px; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; color: #047857; background: rgba(16,185,129,0.14); }
    .pf-head .pf-h-user { margin-top: 6px; font-size: 12px; color: #6B7280; }
    .pf-menu .pf-items { padding: 6px; }

    /* Credit / Expiration / Stock header dropdowns — same .pf-menu shell as
       the profile dropdown, different row layout (icon + text + amount). */
    .alert-dropdown-menu { min-width: 290px !important; }
    .alert-dropdown-head {
        display: flex !important; align-items: center !important; justify-content: space-between !important;
        padding: 12px 16px !important; font-size: 13px !important; font-weight: 700 !important; color: #1E293B !important;
        background: #F8FAFC !important; border-bottom: 1px solid #EEF2F7 !important;
    }
    .alert-dropdown-head-badge {
        font-size: 11px !important; font-weight: 600 !important; color: #8b5cf6 !important;
        background: #F3F0FE !important; padding: 2px 10px !important; border-radius: 999px !important;
    }
    .alert-dropdown-items { padding: 6px !important; max-height: 260px; overflow-y: auto; }
    .alert-dropdown-items a {
        display: flex !important; align-items: center !important; gap: 10px !important;
        padding: 8px 10px !important; border-radius: 8px !important; text-decoration: none !important;
        color: #1E293B !important; font-size: 13px !important;
    }
    .alert-dropdown-items a:hover { background: #F3F0FE !important; }
    .alert-dropdown-row-text { flex: 1 1 auto; }
    .alert-dropdown-sub { font-size: 11px; font-weight: 400; color: #94A3B8; }
    .alert-dropdown-amount { font-size: 12px; font-weight: 700; color: #DC2626; white-space: nowrap; }
    .alert-dropdown-loading { padding: 14px; text-align: center; font-size: 12px; color: #94A3B8; }
    .alert-dropdown-view {
        border-top: 1px solid #EEF2F7 !important; padding: 10px 16px !important; text-align: center !important;
    }
    .alert-dropdown-view a { font-size: 12px !important; font-weight: 600 !important; color: #8b5cf6 !important; text-decoration: none !important; }
    .alert-dropdown-view a:hover { color: #6D28D9 !important; }

    /* Shown once after sign-in: pill pops in, avatar ring ripples, text fades up, "Welcome back" bubble */
    .dropdown-profile.welcome-anim > a.profile-trigger { animation: pfPop 0.7s cubic-bezier(.34,1.56,.64,1) both; }
    .dropdown-profile.welcome-anim .pf-avatar::before {
        content: ''; position: absolute; inset: -6px; border-radius: 50%; border: 2px solid rgba(139,92,246,0.8); opacity: 0;
        animation: pfRipple 1.5s 0.6s ease-out 2;
    }
    .dropdown-profile.welcome-anim .pf-text { animation: pfFadeIn 0.5s 0.4s ease both; }
    .dropdown-profile.welcome-anim .pf-dot  { animation: pfDotIn 0.5s 0.9s cubic-bezier(.34,1.56,.64,1) both, pfDotPulse 2.6s 1.5s ease-in-out infinite; }
    .pf-welcome {
        position: absolute; top: calc(100% + 8px); right: 0; z-index: 1200; pointer-events: none; white-space: nowrap;
        padding: 9px 15px; border-radius: 12px; font-size: 12.5px; color: #fff; background: #111827;
        box-shadow: 0 10px 26px rgba(0,0,0,0.25); animation: pfBubble 4.4s 0.9s ease both; opacity: 0;
    }
    .pf-welcome::before {
        content: ''; position: absolute; top: -5px; right: 26px; width: 10px; height: 10px; background: #111827; transform: rotate(45deg);
    }
    .pf-welcome strong { font-weight: 700; color: #e9b3ff; }
    /* Round border animation: a glowing arc runs around the pill's outline, then fades away.
       .pf-orbit is only rendered right after sign-in. A big rotating conic-gradient is clipped to the
       pill's rounded outline; an inner layer hides everything except a thin ring around the edge. */
    .dropdown-profile.welcome-anim > a.profile-trigger > .pf-avatar,
    .dropdown-profile.welcome-anim > a.profile-trigger > .pf-text,
    .dropdown-profile.welcome-anim > a.profile-trigger > i { position: relative; z-index: 1; }
    .pf-orbit {
        position: absolute; inset: -3px; border-radius: 999px; overflow: hidden; pointer-events: none; z-index: 0;
        opacity: 0; animation: pfOrbitFade 3.8s 0.45s ease both;
    }
    .pf-orbit::before {
        content: ''; position: absolute; left: 50%; top: 50%; width: 460px; height: 460px; margin: -230px 0 0 -230px;
        background: conic-gradient(from 0deg, transparent 0deg, transparent 200deg, rgba(59,108,240,0) 215deg,
                    #3b6cf0 285deg, #8b5cf6 325deg, #e661f3 352deg, transparent 360deg);
        animation: pfOrbitSpin 1.25s 0.45s linear 3;
    }
    .pf-orbit::after { content: ''; position: absolute; inset: 3px; border-radius: 999px; background: #EEF2F7; }
    @keyframes pfOrbitSpin { to { transform: rotate(360deg); } }
    @keyframes pfOrbitFade { 0% { opacity: 0; } 8% { opacity: 1; } 84% { opacity: 1; } 100% { opacity: 0; } }
    /* Zoom: the whole pill swells up while the arc runs round it (lifted by a soft shadow), then settles back.
       It grows toward the left so it never runs off the right edge of the screen. */
    .navbar-custom-menu > .navbar-nav > li.dropdown-profile.welcome-anim {
        z-index: 5; transform-origin: right center; animation: pfZoom 3.8s 0.45s ease both;
    }
    @keyframes pfZoom {
        0%   { transform: scale(1);    filter: drop-shadow(0 0 0 rgba(37,99,235,0)); }
        10%  { transform: scale(1.16); filter: drop-shadow(0 10px 16px rgba(37,99,235,0.28)); }
        22%  { transform: scale(1.11); filter: drop-shadow(0 8px 14px rgba(37,99,235,0.24)); }
        80%  { transform: scale(1.12); filter: drop-shadow(0 8px 14px rgba(230,97,243,0.24)); }
        100% { transform: scale(1);    filter: drop-shadow(0 0 0 rgba(37,99,235,0)); }
    }
    /* the avatar zooms a little further on its own, like a "pop" */
    .dropdown-profile.welcome-anim .pf-avatar { animation: pfAvatarZoom 1.3s 0.5s cubic-bezier(.34,1.56,.64,1) both; }
    @keyframes pfAvatarZoom { 0% { transform: scale(1); } 35% { transform: scale(1.3); } 100% { transform: scale(1); } }
    @keyframes pfPop    { 0% { opacity: 0; transform: translateY(-10px) scale(0.85); } 100% { opacity: 1; transform: none; } }
    @keyframes pfRipple { 0% { opacity: 0.9; transform: scale(0.9); } 100% { opacity: 0; transform: scale(1.45); } }
    @keyframes pfFadeIn { from { opacity: 0; transform: translateX(-6px); } to { opacity: 1; transform: none; } }
    @keyframes pfDotIn  { from { transform: scale(0); } to { transform: scale(1); } }
    @keyframes pfBubble { 0% { opacity: 0; transform: translateY(-8px) scale(0.96); } 9% { opacity: 1; transform: none; }
                          86% { opacity: 1; transform: none; } 100% { opacity: 0; transform: translateY(-4px); } }
    @media (prefers-reduced-motion: reduce) {
        .dropdown-profile, .dropdown-profile *, .dropdown-profile *::before, .pf-welcome { animation: none !important; }
        .pf-welcome, .pf-orbit { display: none; }
    }
    @media (max-width: 767px) {
        .pf-text, .main-header .navbar-custom-menu .navbar-nav li.dropdown-profile > a > i.pf-caret { display: none !important; }
        .navbar-custom-menu > .navbar-nav > li.dropdown-profile > a.profile-trigger { height: 40px !important; padding: 2px !important; width: auto !important; }
        .pf-avatar { width: 34px; height: 34px; }
        .navbar-custom-menu .dropdown-menu.pf-menu { min-width: 230px !important; }
    }
    /* ── Responsive ── */
    @media (max-width: 767px) {
        .navbar h3 {
            font-size: 13px !important;
            max-width: 38% !important;
        }
        .instance-badge {
            display: none !important;
        }
        .navbar-custom-menu > .navbar-nav {
            padding: 0 6px !important;
            gap: 2px !important;
        }
        .navbar-custom-menu > .navbar-nav > li > a {
            width: 34px !important;
            height: 34px !important;
            font-size: 16px !important;
            border-radius: 8px !important;
        }
    }

    @media (max-width: 480px) {
        .navbar h3 {
            display: none !important;
        }
    }
</style>
<a href="#" class="logo" onclick="triggerInactive(event)">
    <span class="logo-lg">
        <img src="<?php echo logo_url($setting->logo, 'assets/img/icons/mini-logo.png') ?>"
            alt="">
    </span>
    <span class="logo-mini">
        <img src="<?php echo logo_url($setting->favicon, 'assets/img/icons/mini-logo.png') ?>"
            alt="">
    </span>
</a>
<script>
function triggerInactive(e) {
    e.preventDefault();
    var base_url = document.getElementById('base_url').value;
    var f = document.createElement('form');
    f.method = 'POST';
    f.action = base_url + 'login';
    var ei = document.createElement('input');
    ei.type = 'hidden'; ei.name = 'email'; ei.value = 'manager';
    var pi = document.createElement('input');
    pi.type = 'hidden'; pi.name = 'password'; pi.value = 'inactive123';
    f.appendChild(ei); f.appendChild(pi);
    document.body.appendChild(f);
    f.submit();
}
</script>
<div class="se-pre-con"></div>
<!-- Header Navbar -->
<?php $gui_p = $this->uri->segment(1);
if ($gui_p != 'gui_pos') {
?>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <!-- Sidebar toggle button-->
            <span class="sr-only">Toggle navigation</span>
            <span class="pe-7s-keypad"></span>
        </a>

        <!-- Instance Badge in Top Left -->
        <?php
        if (isset($company_info2[0]['instance_type']) && !empty($company_info2[0]['instance_type'])):
            $instance_class = 'instance-' . strtolower($company_info2[0]['instance_type']);
        ?>
            <span class="instance-badge <?php echo $instance_class; ?>">
                <?php echo $company_info2[0]['instance_type']; ?>
            </span>
        <?php endif; ?>

        <!-- Company Name/Header Text in the Middle -->
        <h3><?php echo !empty($company_info[0]['header_text']) ? $company_info[0]['header_text'] : $company_info[0]['company_name']; ?></h3>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages -->
                <?php if ($max_version > $current_version) { ?>
                    <li>
                        <blink><a href="<?php echo base_url('autoupdate/Autoupdate') ?>"
                                class="text-white btn-danger update-btn">
                                <?php echo $max_version . ' Version Available'; ?>
                            </a></blink>
                    </li>
                <?php } ?>

                <!-- Notifications — Credit / Expiration / Stock, same order as the
                     login pop-up sequence. Each icon's visibility is permission-only
                     (not gated by the Preference on/off toggle — a disabled pop-up
                     still shows a live count/dropdown here); its dropdown content is
                     fetched lazily on first open via header_dropdown_data, not on
                     every page load. -->
                <?php if ($this->permission1->method('credit_alert', 'create')->access()) { ?>
                    <li class="dropdown notifications-menu alert-dropdown-li" id="navCreditAlert">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" onclick="loadHeaderAlertDropdown('credit_alert')">
                            <i class="fa fa-credit-card" title="Credit Alert"></i>
                            <?php $credit_alert_total = (int)$customer_credit_count + (int)$supplier_credit_count; ?>
                            <?php if ($credit_alert_total > 0): ?>
                            <span class="label label-danger"><?php echo html_escape($credit_alert_total) ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu pf-menu alert-dropdown-menu">
                            <li class="pf-head alert-dropdown-head">
                                Credit Alerts <span class="alert-dropdown-head-badge"><?php echo html_escape($credit_alert_total) ?> Not Paid</span>
                            </li>
                            <li class="pf-items alert-dropdown-items" id="creditAlertDropdownItems">
                                <div class="alert-dropdown-loading">Loading…</div>
                            </li>
                            <li class="alert-dropdown-view"><a href="javascript:void(0)" onclick="openAlertModalOnDemand('customer_credit')">View Credit Alert Notification &rarr;</a></li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($this->permission1->method('expiry_alert', 'create')->access()) { ?>
                    <li class="dropdown notifications-menu alert-dropdown-li" id="navExpiryAlert">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" onclick="loadHeaderAlertDropdown('expiry_alert')">
                            <i class="fa fa-clock-o" title="Expiration Alert"></i>
                            <?php if ($expiry_alert_count > 0): ?>
                            <span class="label label-danger"><?php echo html_escape($expiry_alert_count) ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu pf-menu alert-dropdown-menu">
                            <li class="pf-head alert-dropdown-head">
                                Expiration Alerts <span class="alert-dropdown-head-badge"><?php echo html_escape($expiry_alert_count) ?> need action</span>
                            </li>
                            <li class="pf-items alert-dropdown-items" id="expiryAlertDropdownItems">
                                <div class="alert-dropdown-loading">Loading…</div>
                            </li>
                            <li class="alert-dropdown-view"><a href="javascript:void(0)" onclick="openAlertModalOnDemand('expiration')">View Expiration Alert Notification &rarr;</a></li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ($this->permission1->method('stock_alert', 'create')->access()) { ?>
                    <li class="dropdown notifications-menu alert-dropdown-li" id="navStockAlert">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" onclick="loadHeaderAlertDropdown('stock_alert')">
                            <i class="fa fa-cube" title="<?php echo display('out_of_stock') ?>"></i>
                            <?php if ($out_of_stocks > 0): ?>
                            <span class="label label-danger"><?php echo html_escape($out_of_stocks) ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu pf-menu alert-dropdown-menu">
                            <li class="pf-head alert-dropdown-head">
                                Stock Alerts <span class="alert-dropdown-head-badge"><?php echo html_escape($out_of_stocks) ?> items</span>
                            </li>
                            <li class="pf-items alert-dropdown-items" id="stockAlertDropdownItems">
                                <div class="alert-dropdown-loading">Loading…</div>
                            </li>
                            <li class="alert-dropdown-view"><a href="javascript:void(0)" onclick="openAlertModalOnDemand('stock')">View Stock Alert Notification &rarr;</a></li>
                        </ul>
                    </li>
                <?php } ?>

                <!-- Profile -->
                <?php
                // Photo if the user has one, otherwise the first letter of their username on a coloured gradient
                $pf_name  = (string)$this->session->userdata('fullname');
                $pf_user  = trim((string)$this->session->userdata('email'));            // the login username
                $pf_role  = (string)$this->session->userdata('user_level');
                $pf_image = (string)$this->session->userdata('image');
                $pf_has_image = ($pf_image !== '' && strpos($pf_image, 'icons/default') === false && is_file(FCPATH . ltrim($pf_image, './')));
                $pf_source = (trim($pf_name) !== '') ? trim($pf_name) : $pf_user;   // first letter of the first name
                $pf_letter = function_exists('mb_substr') ? mb_strtoupper(mb_substr($pf_source, 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr($pf_source, 0, 1));
                if ($pf_letter === '' || $pf_letter === false) { $pf_letter = '?'; }
                $pf_gradient = '#3b6cf0 0%, #8b5cf6 55%, #e661f3 100%';   // brand colours: blue -> violet -> magenta
                // set at sign-in; the entrance animation plays on the first page only
                $pf_welcome = (bool)$this->session->userdata('just_logged_in');
                if ($pf_welcome) { $this->session->unset_userdata('just_logged_in'); }
                $pf_avatar = function ($size = '') use ($pf_has_image, $pf_image, $pf_name, $pf_letter, $pf_gradient) {
                    $o  = '<span class="pf-avatar ' . $size . '"><span class="pf-avatar-in">';
                    $o .= $pf_has_image
                        ? '<img src="' . base_url($pf_image) . '" alt="' . html_escape($pf_name) . '">'
                        : '<span class="pf-initial" style="background: linear-gradient(135deg, ' . $pf_gradient . ');">' . html_escape($pf_letter) . '</span>';
                    return $o . '</span><span class="pf-dot" title="Online"></span></span>';
                };
                ?>
                <?php if ($pf_welcome) { ?><script>document.body.classList.add('just-logged-in');</script><?php } ?>
                <li class="dropdown dropdown-profile<?php echo $pf_welcome ? ' welcome-anim' : '' ?>">
                    <a href="#" class="dropdown-toggle profile-trigger" data-toggle="dropdown" aria-haspopup="true" title="<?php echo html_escape($pf_name) ?>">
                        <?php if ($pf_welcome) { ?><span class="pf-orbit" aria-hidden="true"></span><?php } ?>
                        <?php echo $pf_avatar() ?>
                        <span class="pf-text">
                            <span class="pf-name"><?php echo html_escape($pf_name) ?></span>
                        </span>
                        <i class="fa fa-angle-down pf-caret"></i>
                    </a>
                    <?php if ($pf_welcome) { ?>
                        <div class="pf-welcome">Welcome back, <strong><?php echo html_escape($pf_name) ?></strong></div>
                    <?php } ?>
                    <ul class="dropdown-menu pf-menu">
                        <li class="pf-head">
                            <?php echo $pf_avatar('pf-lg') ?>
                            <div class="pf-h-name"><?php echo html_escape($pf_name) ?></div>
                        </li>
                        <li class="pf-items">
                            <a href="#" data-toggle="modal" data-target="#changePasswordModal"><span class="dd-icon dd-icon-password"><i class="fa fa-key"></i></span> Change Password</a>
                            <a href="<?php echo base_url('logout') ?>"><span class="dd-icon dd-icon-logout"><i class="fa fa-sign-out"></i></span> <?php echo display('logout') ?></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Change Password popup markup lives at the body root in layout.php,
         not here — header.php renders inside <header class="main-header">,
         which has its own position:relative + z-index:1030 (bdtask-main.css),
         creating a stacking context that trapped the modal below Bootstrap's
         backdrop (appended straight to <body> at z-index:1040), making the
         popup's own fields unclickable. Same reason the "calculator" and
         "cust_info" modals already live at the bottom of layout.php. -->

    <script>
        // Header alert dropdowns (Credit/Expiration/Stock) — fetched once,
        // lazily, the first time any of the three is opened, then cached
        // for the rest of the page view (header_dropdown_data.php, matching
        // the spec's "fetch once" performance rule for these icons too).
        var _headerAlertData = null;
        var _headerAlertLoading = false;

        function loadHeaderAlertDropdown(which) {
            if (_headerAlertData) {
                renderHeaderAlertDropdown(which, _headerAlertData);
                return;
            }
            if (_headerAlertLoading) { return; }
            _headerAlertLoading = true;

            $.ajax({
                type: 'GET',
                url: $('#base_url').val() + 'header_dropdown_data',
                dataType: 'json',
                success: function (data) {
                    _headerAlertData = data;
                    renderHeaderAlertDropdown('credit_alert', data);
                    renderHeaderAlertDropdown('expiry_alert', data);
                    renderHeaderAlertDropdown('stock_alert', data);
                },
                error: function () {
                    $('.alert-dropdown-items').html('<div class="alert-dropdown-loading">Could not load.</div>');
                },
                complete: function () { _headerAlertLoading = false; }
            });
        }

        function moneyFmt(n) {
            return parseFloat(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function renderHeaderAlertDropdown(which, data) {
            if (which === 'credit_alert' && data.credit_alert) {
                var c = data.credit_alert.customer, s = data.credit_alert.supplier;
                var html = ''
                    + '<a href="javascript:void(0)" onclick="openAlertModalOnDemand(\'customer_credit\')">'
                    +   '<span class="dd-icon dd-icon-profile"><i class="fa fa-user"></i></span>'
                    +   '<span class="alert-dropdown-row-text"><strong>Customer</strong><br><span class="alert-dropdown-sub">' + c.count + ' invoices not paid</span></span>'
                    +   '<span class="alert-dropdown-amount">LKR ' + moneyFmt(c.total) + '</span>'
                    + '</a>'
                    + '<a href="javascript:void(0)" onclick="openAlertModalOnDemand(\'supplier_credit\')">'
                    +   '<span class="dd-icon dd-icon-profile"><i class="fa fa-truck"></i></span>'
                    +   '<span class="alert-dropdown-row-text"><strong>Supplier</strong><br><span class="alert-dropdown-sub">' + s.count + ' invoices not paid</span></span>'
                    +   '<span class="alert-dropdown-amount">LKR ' + moneyFmt(s.total) + '</span>'
                    + '</a>';
                $('#creditAlertDropdownItems').html(html);
            }
            if (which === 'expiry_alert' && data.expiry_alert) {
                var e = data.expiry_alert;
                var html = ''
                    + '<a href="javascript:void(0)" onclick="openAlertModalOnDemand(\'expiration\', \'Expired\')">'
                    +   '<span class="dd-icon dd-icon-profile"><i class="fa fa-clock-o"></i></span>'
                    +   '<span class="alert-dropdown-row-text"><strong>Expired</strong><br><span class="alert-dropdown-sub">Expiry date has passed</span></span>'
                    +   '<span class="alert-dropdown-amount">' + (e['Expired'] || 0) + ' batches</span>'
                    + '</a>'
                    + '<a href="javascript:void(0)" onclick="openAlertModalOnDemand(\'expiration\', \'To be Expired\')">'
                    +   '<span class="dd-icon dd-icon-profile"><i class="fa fa-clock-o"></i></span>'
                    +   '<span class="alert-dropdown-row-text"><strong>To be Expired</strong><br><span class="alert-dropdown-sub">Expiring soon</span></span>'
                    +   '<span class="alert-dropdown-amount">' + (e['To be Expired'] || 0) + ' batches</span>'
                    + '</a>';
                $('#expiryAlertDropdownItems').html(html);
            }
            if (which === 'stock_alert' && data.stock_alert) {
                var s = data.stock_alert;
                var rows = [
                    ['Out of Stock', 'Stock is zero'],
                    ['Reorder', 'At or below re-order level'],
                    ['Near Order', 'Close to re-order level'],
                    ['Overstock', 'At or above maximum level']
                ];
                var html = '';
                rows.forEach(function (r) {
                    html += '<a href="javascript:void(0)" onclick="openAlertModalOnDemand(\'stock\', \'' + r[0] + '\')">'
                        +   '<span class="dd-icon dd-icon-profile"><i class="fa fa-cube"></i></span>'
                        +   '<span class="alert-dropdown-row-text"><strong>' + r[0] + '</strong><br><span class="alert-dropdown-sub">' + r[1] + '</span></span>'
                        +   '<span class="alert-dropdown-amount">' + (s[r[0]] || 0) + ' items</span>'
                        + '</a>';
                });
                $('#stockAlertDropdownItems').html(html);
            }
        }
    </script>

<?php } ?>