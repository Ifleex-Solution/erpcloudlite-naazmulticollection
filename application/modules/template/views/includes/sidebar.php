<style>
    /* ═══════════════════════════════════════════════════
       Sidebar Redesign — Dashboard Palette
       Charcoal #1A1D24 · Accent #16A34A · Sub #22272F
    ═══════════════════════════════════════════════════ */

    /* ── Base ── */
    .main-sidebar,
    .skin-blue .main-sidebar,
    .skin-blue-light .main-sidebar,
    .skin-black .main-sidebar,
    .skin-black-light .main-sidebar,
    .skin-green .main-sidebar,
    .skin-red .main-sidebar,
    .skin-purple .main-sidebar,
    .skin-yellow .main-sidebar {
        background: #1A1D24 !important;
    }

    /* ── Scrollbar ── */
    .main-sidebar {
        overflow-y: auto !important;
        overflow-x: hidden !important;
        height: 100vh !important;
    }
    .main-sidebar .slimScrollDiv,
    .main-sidebar .sidebar {
        height: auto !important;
        overflow: visible !important;
    }
    .main-sidebar::-webkit-scrollbar { width: 4px; }
    .main-sidebar::-webkit-scrollbar-track { background: transparent; }
    .main-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 4px; }
    .main-sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.28); }

    /* ── User Panel ── */
    .sidebar .user-panel {
        background: transparent !important;
        border-bottom: 1px solid rgba(255,255,255,0.07) !important;
        padding: 22px 10px 18px !important;
        overflow: visible !important;
        margin-bottom: 4px;
        display: block !important;
        text-align: center !important;
    }
    .sidebar .user-panel::after {
        content: '' !important;
        display: table !important;
        clear: both !important;
    }
    .sidebar .user-panel .image {
        display: block !important;
        float: none !important;
        padding: 0 !important;
        margin: 0 auto 10px !important;
        width: 72px !important;
        height: 72px !important;
        border-radius: 50% !important;
        background: rgba(255,255,255,0.07) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .sidebar .user-panel .image img {
        float: none !important;
        width: 58px !important;
        height: 58px !important;
        border-radius: 50% !important;
        border: 2px solid rgba(37,99,235,0.6) !important;
        object-fit: cover !important;
        display: block !important;
        margin: 0 auto !important;
    }
    .sidebar .user-panel .info {
        display: block !important;
        float: none !important;
        padding: 0 !important;
        text-align: center !important;
    }
    .sidebar .user-panel .info > p {
        color: #ffffff !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        margin: 0 0 6px 0 !important;
    }
    .sidebar .user-panel .info > a {
        color: #10B981 !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        justify-content: center !important;
    }
    .sidebar .user-panel .info a .fa-circle {
        font-size: 8px !important;
        color: #10B981 !important;
    }

    /* ── Collapsed / mini sidebar user panel ── */
    .sidebar-mini.sidebar-collapse .sidebar .user-panel {
        padding: 14px 4px !important;
        margin-bottom: 2px !important;
    }
    .sidebar-mini.sidebar-collapse .sidebar .user-panel .image {
        width: 46px !important;
        height: 46px !important;
        margin: 0 auto !important;
    }
    .sidebar-mini.sidebar-collapse .sidebar .user-panel .image img {
        width: 40px !important;
        height: 40px !important;
        border-width: 2px !important;
    }
    .sidebar-mini.sidebar-collapse .sidebar .user-panel .info {
        display: none !important;
    }

    /* ── Menu container ── */
    .sidebar .sidebar-menu {
        background: transparent !important;
        padding: 14px 8px 20px !important;
        margin: 0 !important;
        list-style: none;
    }

    /* ── Top-level item ── */
    .sidebar-menu > li {
        margin-bottom: 1px;
    }
    .sidebar-menu > li > a {
        display: flex !important;
        align-items: center !important;
        padding: 9px 10px !important;
        border-radius: 8px !important;
        color: #7E94B3 !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        transition: background 0.16s, color 0.16s, box-shadow 0.16s !important;
        gap: 0;
        margin: 0 !important;
    }
    .sidebar-menu > li > a:hover {
        background: rgba(37,99,235,0.1) !important;
        color: #E2EAF8 !important;
        box-shadow: inset 3px 0 0 rgba(37,99,235,0.5) !important;
    }
    .sidebar-menu > li.active > a,
    .sidebar-menu > li.menu-open > a {
        background: rgba(37,99,235,0.16) !important;
        color: #ffffff !important;
        box-shadow: inset 3px 0 0 #2563EB !important;
        font-weight: 600 !important;
    }

   
  
    /* ── Chevron ── */
    /* The base theme (bdtask-main.css) pins this with position:absolute;
       right:10px, which pulls it out of the flex row entirely and is why
       a plain margin-left here had no visible effect — it has to be put
       back into normal flow first. */
    .sidebar-menu .pull-right-container {
        position: static !important;
        margin-left: auto !important;
        margin-top: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    /* Inset the whole row from the sidebar's right edge so the
       right-aligned arrow column sits closer to the labels instead of
       flush against the edge — still a straight column, just nearer. */
    .sidebar-menu > li > a {
        padding-right: 28px !important;
    }
    .sidebar-menu .fa-angle-left {
        color: #7E94B3 !important;
        font-size: 12px !important;
        transition: transform 0.2s ease, color 0.16s !important;
        transform: rotate(180deg) !important;
    }
    .sidebar-menu > li:hover .fa-angle-left,
    .sidebar-menu > li.active .fa-angle-left,
    .sidebar-menu > li.menu-open .fa-angle-left {
        color: #ffffff !important;
    }
    .sidebar-menu > li.active > a .fa-angle-left,
    .sidebar-menu > li.menu-open > a .fa-angle-left {
        transform: rotate(-90deg) !important;
    }

    /* ── 2nd-level submenu ── */
    .sidebar-menu .treeview-menu {
        background: rgba(0,0,0,0.15) !important;
        list-style: none !important;
        padding: 4px 4px 6px 0 !important;
        margin: 0 0 2px 20px !important;
        border-left: 2px solid rgba(37,99,235,0.25) !important;
        border-radius: 0 !important;
    }
    .sidebar-menu .treeview-menu li,
    .sidebar-menu .treeview-menu > li {
        list-style: none !important;
        list-style-type: none !important;
    }
    /* Kill ALL pseudo-elements on the ul and li that draw tree connector lines */
    .sidebar-menu .treeview-menu::before,
    .sidebar-menu .treeview-menu::after,
    .sidebar-menu .treeview-menu:before,
    .sidebar-menu .treeview-menu:after,
    .sidebar-menu .treeview-menu li::marker,
    .sidebar-menu .treeview-menu li::before,
    .sidebar-menu .treeview-menu li::after,
    .sidebar-menu .treeview-menu li:before,
    .sidebar-menu .treeview-menu li:after,
    .sidebar-menu .treeview-menu > li > a::before,
    .sidebar-menu .treeview-menu > li > a::after,
    .sidebar-menu .treeview-menu > li > a:before,
    .sidebar-menu .treeview-menu > li > a:after {
        content: '' !important;
        display: none !important;
        width: 0 !important;
        height: 0 !important;
        background: none !important;
        border: none !important;
        visibility: hidden !important;
    }
    .main-sidebar .sidebar-menu .treeview-menu > li,
    .main-sidebar .sidebar-menu .treeview-menu > li > a,
    .main-sidebar .sidebar-menu .treeview-menu > li > a:hover,
    .main-sidebar .sidebar-menu .treeview-menu > li > a:focus,
    .main-sidebar .sidebar-menu .treeview-menu > li.active > a {
        border: none !important;
        border-left: none !important;
        outline: none !important;
        box-shadow: none !important;
    }
    .main-sidebar .sidebar-menu .treeview-menu > li > a {
        display: flex !important;
        align-items: center !important;
        min-height: 34px !important;
        padding: 6px 10px 6px 16px !important;
        color: #7E9EC0 !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        border-radius: 8px !important;
        transition: background 0.14s, color 0.14s !important;
        background: transparent !important;
    }
    .main-sidebar .sidebar-menu .treeview-menu > li > a:hover {
        color: #C4D5EC !important;
        background: rgba(37,99,235,0.09) !important;
    }
    .main-sidebar .sidebar-menu .treeview-menu > li.active > a {
        color: #93C5FD !important;
        background: rgba(37,99,235,0.14) !important;
        font-weight: 600 !important;
    }

    /* ── 3rd-level submenu (nested inside treeview-menu) ── */
    .sidebar-menu .treeview-menu .treeview-menu {
        padding: 2px 2px 4px 0 !important;
        margin: 0 0 2px 8px !important;
        border-left: 2px solid rgba(37,99,235,0.12) !important;
        background: transparent !important;
        border-radius: 0 !important;
        list-style: none !important;
    }
    .sidebar-menu .treeview-menu .treeview-menu li,
    .sidebar-menu .treeview-menu .treeview-menu li::marker,
    .sidebar-menu .treeview-menu .treeview-menu li::before {
        list-style: none !important;
        content: none !important;
    }
    .main-sidebar .sidebar-menu .treeview-menu .treeview-menu > li > a {
        display: flex !important;
        align-items: center !important;
        min-height: 30px !important;
        font-size: 11.5px !important;
        padding: 5px 6px 5px 14px !important;
        color: #7E9EC0 !important;
        border: none !important;
        border-left: none !important;
        box-shadow: none !important;
    }
    .main-sidebar .sidebar-menu .treeview-menu .treeview-menu > li > a:hover,
    .main-sidebar .sidebar-menu .treeview-menu .treeview-menu > li.active > a {
        color: #93C5FD !important;
        background: rgba(37,99,235,0.08) !important;
    }

    /* ── 2nd-level section headers only (href="#" = expandable group, not a page link) ── */
    /* Leaf items like "Branch List" have real URLs and are excluded by a[href="#"] */
    .sidebar-menu > li > .treeview-menu > li.treeview > a[href="#"] {
        color: #8FACC8 !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
        padding: 9px 10px !important;
        background: transparent !important;
    }
    .sidebar-menu > li > .treeview-menu > li.treeview > a[href="#"]:hover {
        color: #C4DCF0 !important;
        background: transparent !important;
    }
    .sidebar-menu > li > .treeview-menu > li.treeview.active > a[href="#"] {
        background: transparent !important;
        color: #A8CADF !important;
        font-weight: 700 !important;
    }
    /* Hide the original rotating fa-angle-left arrow on section headers */
    .sidebar-menu > li > .treeview-menu > li.treeview > a[href="#"] > .pull-right-container {
        display: none !important;
    }
    /* Static down arrow via ::after — always ∨, never rotates.
       !important overrides the global ::after suppressor rule above. */
    .sidebar-menu > li > .treeview-menu > li.treeview > a[href="#"]::after {
        content: '\f107' !important;
        display: inline !important;
        font-family: 'FontAwesome' !important;
        margin-left: auto !important;
        padding-left: 6px !important;
        font-size: 10px !important;
        color: #8FACC8 !important;
        font-weight: 400 !important;
        font-style: normal !important;
        flex-shrink: 0 !important;
        -webkit-font-smoothing: antialiased;
        visibility: visible !important;
        width: auto !important;
        height: auto !important;
        background: none !important;
        border: none !important;
    }
    .sidebar-menu > li > .treeview-menu > li.treeview:hover > a[href="#"]::after,
    .sidebar-menu > li > .treeview-menu > li.treeview.active > a[href="#"]::after {
        color: #7DB4E8 !important;
    }

    /* ── fa/ti icons inside submenu links (Settings section) ── */
    .sidebar-menu .treeview-menu a > i:first-child {
        color: #3D5275 !important;
        margin-right: 6px;
        width: 14px;
        text-align: center;
    }
    .sidebar-menu .treeview-menu li:hover > a > i:first-child,
    .sidebar-menu .treeview-menu li.active > a > i:first-child {
        color: #7E94B3 !important;
    }

    /* ── Default icon colors (fa/ti used in some sub-items) ── */
    .sidebar-menu i,
    .sidebar-menu svg { color: inherit; }

    /* ── Mobile ── */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.55);
        z-index: 1039;
        cursor: pointer;
    }
    .sidebar-close-btn { display: none; }

    @media (max-width: 767px) {
        body.sidebar-open .sidebar-overlay { display: block; }
        .sidebar-close-btn {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 10px 14px 4px;
        }
        .sidebar-close-btn button {
            background: none;
            border: none;
            color: #7E94B3;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .sidebar-close-btn button:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        .main-sidebar {
            -webkit-transition: -webkit-transform .3s ease-in-out;
            transition: transform .3s ease-in-out;
            z-index: 1040 !important;
        }
    }
    /* ═══════════════════════════════════════════════════════════════════════════════
       Sidebar polish - blue -> violet -> magenta, to match the profile and the menu icons
       (appended last so it overrides the base rules above)
    ═══════════════════════════════════════════════════════════════════════════════ */

    /* ── Panel: soft vertical gradient, a hairline edge and a gentle shadow ── */
    .main-sidebar,
    .skin-blue .main-sidebar, .skin-blue-light .main-sidebar, .skin-black .main-sidebar, .skin-black-light .main-sidebar,
    .skin-green .main-sidebar, .skin-red .main-sidebar, .skin-purple .main-sidebar, .skin-yellow .main-sidebar {
        background:
            radial-gradient(120% 40% at 0% 0%, rgba(139,92,246,0.10) 0%, rgba(139,92,246,0) 60%),
            linear-gradient(180deg, #181C28 0%, #1A1D24 42%, #14171F 100%) !important;
        border-right: 1px solid rgba(255,255,255,0.045) !important;
        box-shadow: 6px 0 26px rgba(0,0,0,0.20) !important;
    }

    /* ── Logo strip: deep blue gradient with a glowing line underneath ── */
    .main-header .logo {
        background: linear-gradient(135deg, #131C33 0%, #1C2750 100%) !important;
        position: relative !important;
    }
    .main-header .logo::after {
        content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 2px;
        background: linear-gradient(90deg, rgba(59,108,240,0), #3b6cf0, #8b5cf6, #e661f3, rgba(230,97,243,0));
        opacity: 0.85;
    }
    .main-header .logo:hover { background: linear-gradient(135deg, #17213D 0%, #232F5E 100%) !important; }

    /* ── Section label ── */
    .sidebar .sidebar-menu::before {
        content: 'MAIN MENU'; display: block; padding: 2px 14px 10px;
        font-size: 10.5px; font-weight: 700; letter-spacing: 1.7px; color: #4B5878;
    }
    .sidebar .sidebar-menu { padding: 16px 10px 28px !important; }

    /* ── Top-level items ── */
    .sidebar-menu > li { margin-bottom: 3px !important; }
    .sidebar-menu > li::before { display: none !important; }            /* the theme's old green side bar */
    .sidebar-menu > li > a {
        position: relative !important;
        padding: 6px 10px 6px 8px !important;
        border-radius: 12px !important;
        color: #8A9BBA !important;
        font-weight: 500 !important;
        letter-spacing: 0.1px;
        border: 0 !important;
        box-shadow: none !important;
        overflow: hidden;
        transition: background 0.22s ease, color 0.2s ease, transform 0.22s ease, box-shadow 0.22s ease !important;
    }
    /* glowing accent bar on the left edge */
    .sidebar-menu > li > a::before {
        content: ''; position: absolute; left: 0; top: 18%; bottom: 18%; width: 3px; border-radius: 0 4px 4px 0;
        background: linear-gradient(180deg, #3b6cf0, #8b5cf6, #e661f3);
        box-shadow: 0 0 10px rgba(139,92,246,0.7);
        transform: scaleY(0); transform-origin: center; transition: transform 0.24s ease;
    }
    /* icon tile */
    .sidebar-menu > li > a > svg {
        width: 30px !important; height: 30px !important; box-sizing: border-box; padding: 5px; flex-shrink: 0;
        border-radius: 9px; background: rgba(255,255,255,0.045);
        transition: background 0.25s ease, transform 0.28s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s ease;
    }
    .sidebar-menu > li > a > span:not(.pull-right-container) { margin-left: 11px !important; }

    .sidebar-menu > li > a:hover {
        background: linear-gradient(90deg, rgba(255,255,255,0.06), rgba(255,255,255,0.01)) !important;
        color: #F1F5FF !important;
        transform: translateX(2px);
    }
    .sidebar-menu > li > a:hover::before { transform: scaleY(0.55); }
    .sidebar-menu > li > a:hover > svg {
        background: linear-gradient(135deg, rgba(255,255,255,0.10), rgba(255,255,255,0.05));
        transform: scale(1.1) rotate(-5deg);
        box-shadow: 0 6px 16px rgba(0,0,0,0.25);
    }

    /* current page / open group */
    .sidebar-menu > li.active > a,
    .sidebar-menu > li.menu-open > a {
        background: linear-gradient(90deg, rgba(255,255,255,0.10), rgba(255,255,255,0.02)) !important;
        color: #FFFFFF !important;
        font-weight: 600 !important;
        box-shadow: inset 0 0 0 1px rgba(139,92,246,0.55) !important;
    }
    .sidebar-menu > li.active > a::before,
    .sidebar-menu > li.menu-open > a::before {
        /* overrides the theme's solid green bar (.sidebar-menu>li.active>a:before) */
        content: '' !important; position: absolute !important; left: 0 !important; top: 18% !important; bottom: 18% !important;
        width: 3px !important; height: auto !important; border-radius: 0 4px 4px 0 !important;
        background: #8b5cf6 !important;
        box-shadow: 0 0 8px rgba(139,92,246,0.45) !important; transform: scaleY(1) !important;
    }
    .sidebar-menu > li.active > a > svg,
    .sidebar-menu > li.menu-open > a > svg {
        background: linear-gradient(135deg, rgba(255,255,255,0.16), rgba(255,255,255,0.08));
        box-shadow: 0 6px 16px rgba(0,0,0,0.25);
    }

    /* ── Chevron: a small round button that turns when the group opens ── */
    .sidebar-menu .pull-right-container i.fa {
        display: inline-block !important; width: 22px !important; height: 22px !important; line-height: 22px !important;
        margin: 0 !important; padding: 0 !important; text-align: center; border-radius: 50%;
        background: rgba(255,255,255,0.05); font-size: 12px !important;
        transition: transform 0.25s ease, background 0.2s ease, color 0.2s ease !important;
    }
    .sidebar-menu > li > a:hover .pull-right-container i.fa { background: rgba(255,255,255,0.12); }
    .sidebar-menu > li.menu-open > a .pull-right-container i.fa,
    .sidebar-menu > li.active > a .pull-right-container i.fa { background: rgba(255,255,255,0.16); }

    /* ── Submenu ── */
    .sidebar-menu .treeview-menu {
        background: rgba(255,255,255,0.028) !important;
        /* NO vertical margin/padding here: the open/close slide animates only the height, so anything vertical on
           this box would pop in/out instead of sliding. The spacing is on the first/last item below. */
        margin: 0 0 0 22px !important;
        padding: 0 6px 0 0 !important;
        border-left: 2px solid rgba(255,255,255,0.12) !important;
        border-radius: 0 12px 12px 0 !important;
    }
    .sidebar-menu > li > .treeview-menu > li:first-child { padding-top: 6px; }
    .sidebar-menu > li > .treeview-menu > li:last-child  { padding-bottom: 8px; }
    .main-sidebar .sidebar-menu .treeview-menu > li > a {
        position: relative; border-radius: 9px !important; color: #7F92B2 !important; padding: 7px 10px 7px 16px !important;
        transition: background 0.18s ease, color 0.18s ease, transform 0.2s ease !important;
    }
    /* small dot in front of each page link */
    .main-sidebar .sidebar-menu .treeview-menu > li > a:not([href="#"])::before {
        content: '' !important; display: inline-block !important; visibility: visible !important;
        width: 5px !important; height: 5px !important; border-radius: 50% !important; flex-shrink: 0;
        margin-right: 10px !important; background: #3E4C6C !important;
        transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }
    .main-sidebar .sidebar-menu .treeview-menu > li > a:hover {
        background: linear-gradient(90deg, rgba(255,255,255,0.06), rgba(255,255,255,0.01)) !important;
        color: #E7ECFA !important; transform: translateX(3px);
    }
    .main-sidebar .sidebar-menu .treeview-menu > li > a:not([href="#"]):hover::before {
        background: #94A3B8 !important; box-shadow: none; transform: scale(1.35);
    }
    .main-sidebar .sidebar-menu .treeview-menu > li.active > a {
        background: linear-gradient(90deg, rgba(255,255,255,0.09), rgba(255,255,255,0.02)) !important;
        color: #F1F5F9 !important; font-weight: 600 !important;
    }
    .main-sidebar .sidebar-menu .treeview-menu > li.active > a:not([href="#"])::before {
        background: #CBD5E1 !important; box-shadow: none;
    }
    .sidebar-menu .treeview-menu .treeview-menu { border-left-color: rgba(255,255,255,0.08) !important; }

    /* ── Slim scrollbar ── */
    .main-sidebar { scrollbar-width: thin; scrollbar-color: rgba(139,92,246,0.45) transparent; }
    .main-sidebar::-webkit-scrollbar { width: 5px; }
    .main-sidebar::-webkit-scrollbar-thumb { background: linear-gradient(180deg, rgba(59,108,240,0.5), rgba(230,97,243,0.5)); border-radius: 5px; }
    .main-sidebar::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #3b6cf0, #e661f3); }

    /* ── Collapsed (icons only) ── */
    .sidebar-mini.sidebar-collapse .sidebar .sidebar-menu::before { display: none; }
    .sidebar-mini.sidebar-collapse .sidebar .sidebar-menu { padding: 12px 6px 24px !important; }
    .sidebar-mini.sidebar-collapse .sidebar-menu > li > a { padding: 6px !important; justify-content: center !important; }
    .sidebar-mini.sidebar-collapse .sidebar-menu > li > a:hover { transform: none; }

    /* ── Menu slides in once after sign-in (body gets .just-logged-in from header.php) ── */
    body.just-logged-in .sidebar-menu > li { animation: slItemIn 0.5s cubic-bezier(.22,.68,0,1.2) both; }
    body.just-logged-in .sidebar-menu > li:nth-child(1) { animation-delay: 0.295s; }
    body.just-logged-in .sidebar-menu > li:nth-child(2) { animation-delay: 0.34s; }
    body.just-logged-in .sidebar-menu > li:nth-child(3) { animation-delay: 0.385s; }
    body.just-logged-in .sidebar-menu > li:nth-child(4) { animation-delay: 0.43s; }
    body.just-logged-in .sidebar-menu > li:nth-child(5) { animation-delay: 0.475s; }
    body.just-logged-in .sidebar-menu > li:nth-child(6) { animation-delay: 0.52s; }
    body.just-logged-in .sidebar-menu > li:nth-child(7) { animation-delay: 0.565s; }
    body.just-logged-in .sidebar-menu > li:nth-child(8) { animation-delay: 0.61s; }
    body.just-logged-in .sidebar-menu > li:nth-child(9) { animation-delay: 0.655s; }
    body.just-logged-in .sidebar-menu > li:nth-child(10) { animation-delay: 0.7s; }
    body.just-logged-in .sidebar-menu > li:nth-child(11) { animation-delay: 0.745s; }
    body.just-logged-in .sidebar-menu > li:nth-child(12) { animation-delay: 0.79s; }
    body.just-logged-in .sidebar-menu > li:nth-child(13) { animation-delay: 0.835s; }
    body.just-logged-in .sidebar-menu > li:nth-child(14) { animation-delay: 0.88s; }
    body.just-logged-in .sidebar-menu > li:nth-child(15) { animation-delay: 0.925s; }
    body.just-logged-in .sidebar-menu > li:nth-child(16) { animation-delay: 0.97s; }
    body.just-logged-in .sidebar-menu > li:nth-child(17) { animation-delay: 1.015s; }
    body.just-logged-in .sidebar-menu > li:nth-child(18) { animation-delay: 1.06s; }
    body.just-logged-in .sidebar-menu > li:nth-child(19) { animation-delay: 1.105s; }
    body.just-logged-in .sidebar-menu > li:nth-child(20) { animation-delay: 1.15s; }
    body.just-logged-in .sidebar-menu > li:nth-child(21) { animation-delay: 1.195s; }
    body.just-logged-in .sidebar-menu > li:nth-child(22) { animation-delay: 1.24s; }
    body.just-logged-in .sidebar-menu > li:nth-child(23) { animation-delay: 1.285s; }
    body.just-logged-in .sidebar-menu > li:nth-child(24) { animation-delay: 1.33s; }
    body.just-logged-in .sidebar-menu > li:nth-child(25) { animation-delay: 1.375s; }
    body.just-logged-in .sidebar-menu > li:nth-child(26) { animation-delay: 1.42s; }
    body.just-logged-in .sidebar-menu > li:nth-child(27) { animation-delay: 1.465s; }
    body.just-logged-in .sidebar-menu > li:nth-child(28) { animation-delay: 1.51s; }
    body.just-logged-in .sidebar-menu > li:nth-child(29) { animation-delay: 1.555s; }
    body.just-logged-in .sidebar-menu > li:nth-child(30) { animation-delay: 1.6s; }
    @keyframes slItemIn { from { opacity: 0; transform: translateX(-16px); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) {
        .sidebar-menu *, .sidebar-menu *::before, .main-sidebar * { animation: none !important; transition: none !important; }
    }
    /* ═══ Collapsed (icons-only) sidebar: flyout with the name / the group's sub-items ═══ */
    /* the theme's own hover flyout is clipped by the sidebar's scroll area, so it is switched off and replaced */
    body.sidebar-mini.sidebar-collapse .main-sidebar .sidebar-menu > li:hover > .treeview-menu,
    body.sidebar-mini.sidebar-collapse .main-sidebar .sidebar-menu > li:hover > a > span:not(.pull-right) { display: none !important; }
    body.sidebar-mini.sidebar-collapse .sidebar-menu > li.fly-hover > a > svg {
        background: linear-gradient(135deg, rgba(59,108,240,0.34), rgba(230,97,243,0.26));
        transform: scale(1.1) rotate(-5deg); box-shadow: 0 6px 16px rgba(0,0,0,0.25);
    }
    #sbFly, #sbTip { position: fixed; z-index: 3000; display: none; font-family: inherit; }
    #sbFly {
        width: 244px; max-height: calc(100vh - 24px); overflow-y: auto; padding: 10px; border-radius: 16px;
        background: linear-gradient(180deg, #1F2333, #181B26); border: 1px solid rgba(139,92,246,0.30);
        box-shadow: 0 22px 50px rgba(0,0,0,0.55), inset 0 0 0 1px rgba(255,255,255,0.025);
        scrollbar-width: thin; scrollbar-color: rgba(139,92,246,0.45) transparent;
    }
    #sbFly.show, #sbTip.show { display: block; animation: sbFlyIn 0.16s ease both; }
    @keyframes sbFlyIn { from { opacity: 0; transform: translateX(-6px) scale(0.98); } to { opacity: 1; transform: none; } }
    #sbFly .fly-title {
        padding: 6px 10px 10px; margin-bottom: 6px; border-bottom: 1px solid rgba(255,255,255,0.07);
        font-size: 11px; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase; color: #A78BFA;
    }
    #sbFly ul { list-style: none; margin: 0; padding: 0; }
    #sbFly li { list-style: none; }
    #sbFly .treeview-menu {
        display: block !important; position: static !important; width: auto !important; margin: 0 !important; opacity: 1 !important;
        transform: none !important; background: transparent !important; border: 0 !important; padding: 0 0 0 10px !important;
    }
    #sbFly > .treeview-menu { padding-left: 0 !important; }
    #sbFly a {
        display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 9px; color: #9AA9C7;
        font-size: 13px; font-weight: 500; text-decoration: none; transition: background 0.15s, color 0.15s, transform 0.15s;
    }
    #sbFly a[href]::before {
        content: ''; width: 5px; height: 5px; border-radius: 50%; background: #3E4C6C; flex-shrink: 0; transition: background 0.15s, box-shadow 0.15s;
    }
    #sbFly a[href]:hover { background: linear-gradient(90deg, rgba(255,255,255,0.07), rgba(255,255,255,0.01)); color: #fff; transform: translateX(3px); }
    #sbFly a[href]:hover::before { background: linear-gradient(135deg, #3b6cf0, #e661f3); box-shadow: 0 0 6px rgba(139,92,246,0.6); }
    #sbFly li.active > a[href] { color: #F1F5F9; font-weight: 600; background: linear-gradient(90deg, rgba(255,255,255,0.10), rgba(255,255,255,0.02)); }
    #sbFly li.active > a[href]::before { background: linear-gradient(135deg, #3b6cf0, #e661f3); }
    #sbFly a.fly-head {
        cursor: default; padding: 10px 10px 4px; background: none !important; transform: none !important;
        font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: #7F8FB0;
    }
    #sbTip {
        padding: 7px 13px; border-radius: 10px; background: #12151D; color: #E7ECFA; font-size: 12.5px; font-weight: 600;
        border: 1px solid rgba(139,92,246,0.32); box-shadow: 0 10px 26px rgba(0,0,0,0.45); white-space: nowrap; pointer-events: none;
    }
    @media (prefers-reduced-motion: reduce) { #sbFly, #sbTip { animation: none !important; } }

    /* ── Sidebar menu search ── */
    .sidebar-search-wrap {
        position: relative;
        margin: 4px 10px 12px;
    }
    .sidebar-search-input {
        width: 100%;
        box-sizing: border-box;
        background: rgba(255,255,255,0.05) !important;
        border: 1px solid rgba(255,255,255,0.08) !important;
        border-radius: 9px !important;
        color: #E2E8F0 !important;
        font-size: 13px !important;
        padding: 8px 30px 8px 32px !important;
        outline: none !important;
        box-shadow: none !important;
        transition: background 0.16s, border-color 0.16s !important;
    }
    .sidebar-search-input::placeholder { color: #7E94B3; }
    .sidebar-search-input:focus {
        background: rgba(255,255,255,0.08) !important;
        border-color: rgba(139,92,246,0.55) !important;
    }
    .sidebar-search-icon {
        position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
        color: #7E94B3; font-size: 12px; pointer-events: none;
    }
    .sidebar-search-clear {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        color: #7E94B3; font-size: 12px; cursor: pointer; padding: 4px;
    }
    .sidebar-search-clear:hover { color: #E2E8F0; }
    .sidebar-menu > li.sb-search-hidden { display: none !important; }
    .sidebar-menu li.sb-search-hidden { display: none !important; }
    /* Collapsed (icon-only) sidebar — the search box never got a collapsed
       state, so it kept its full-width layout in a ~50px-wide rail,
       squeezing/clipping the "Search menu..." text. Hide it like the menu
       item text labels already do in this mode (line ~605), since there's
       no room to usefully type in it anyway; it reappears once expanded. */
    .sidebar-mini.sidebar-collapse .sidebar-search-wrap {
        display: none !important;
    }
</style>

<script>
    $(document).ready(function () {
        // Close sidebar when overlay is tapped
        $(document).on('click', '#sidebarOverlay', function () {
            $('body').removeClass('sidebar-open');
        });

        // Close sidebar when X button is tapped
        $(document).on('click', '#sidebarCloseBtn', function () {
            $('body').removeClass('sidebar-open');
        });

        // ── Sidebar menu search/filter ──
        var $sbSearchInput = $('#sidebarSearchInput');
        var $sbSearchClear = $('#sidebarSearchClear');
        var $sbTopItems    = $('.sidebar-menu > li');

        // Remember which top-level groups were already open (current page's
        // active section) so clearing the search restores that, not a fully
        // collapsed menu.
        $sbTopItems.each(function () {
            $(this).data('sbWasOpen', $(this).hasClass('menu-open'));
        });

        function sbMenuText($a) {
            return $a.clone().children('.pull-right-container').remove().end().text().replace(/\s+/g, ' ').trim().toLowerCase();
        }

        function sbClearFilter() {
            $('.sidebar-menu li').removeClass('sb-search-hidden');
            $sbTopItems.each(function () {
                var $li = $(this);
                if ($li.data('sbWasOpen')) {
                    $li.addClass('menu-open');
                } else {
                    $li.removeClass('menu-open');
                }
            });
        }

        $sbSearchInput.on('input', function () {
            var q = $.trim($(this).val()).toLowerCase();
            $sbSearchClear.toggle(q.length > 0);

            if (!q) { sbClearFilter(); return; }

            $sbTopItems.each(function () {
                var $top     = $(this);
                var topMatch = sbMenuText($top.find('> a')).indexOf(q) !== -1;
                var anyChildMatch = false;

                $top.find('li').each(function () {
                    var $sub      = $(this);
                    var subMatch  = sbMenuText($sub.find('> a')).indexOf(q) !== -1;
                    var show      = subMatch || topMatch;
                    $sub.toggleClass('sb-search-hidden', !show);
                    if (subMatch) { anyChildMatch = true; }
                });

                var showTop = topMatch || anyChildMatch;
                $top.toggleClass('sb-search-hidden', !showTop);

                // Auto-expand a group when only its children matched, so the
                // match is actually visible instead of hidden in a closed
                // submenu.
                if (showTop && anyChildMatch && !topMatch) {
                    $top.addClass('menu-open');
                } else if (showTop && !anyChildMatch) {
                    if (!$top.data('sbWasOpen')) { $top.removeClass('menu-open'); }
                }
            });
        });

        $sbSearchClear.on('click', function () {
            $sbSearchInput.val('').trigger('input').focus();
        });
    });
</script>

<div class="sidebar">
    <!-- Mobile close button -->
    <div class="sidebar-close-btn">
        <button id="sidebarCloseBtn" title="Close menu">&#10005;</button>
    </div>
    <!-- Sidebar user panel -->

    <!-- The profile (photo / initial, name, role, logout) is in the top bar: see header.php -->


    <!-- Menu search -->
    <div class="sidebar-search-wrap">
        <i class="fa fa-search sidebar-search-icon"></i>
        <input type="text" id="sidebarSearchInput" class="sidebar-search-input" placeholder="Search menu..."
            autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
        <i class="fa fa-times sidebar-search-clear" id="sidebarSearchClear" style="display:none;"></i>
    </div>


    <!-- sidebar menu -->
    <ul class="sidebar-menu">

        <?php if ($this->session->userdata('screen') == 1) { ?>
            <!-- <li class="treeview <?php echo (($this->uri->segment(1) == "home") ? "active" : null) ?>">
                <a href="<?php echo base_url('home') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 24 24">
                    <image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAEiUlEQVR4AbRUbWwUVRQ9982u0NJtQ4G03WIiFklEiQTUECSS/lDiDxKC0KRbDB8tRUtRIqWtaMBFUaClIKxGaPkQuohFQOCHYiSKqDEGFIsSgRAF0l0IKVu6u7a7nXnX97adfiXEAuFm7p5559x37+y7d0bgPlt3gardu4fcj1qCmal6u78eHc6/q2p3j+pdZOwqfiBrcXRC5pLwWICptzbQe7Ghdu/zEsY1lvJrJsdr9kZ3aeuMUHP0JCCLDXCluzRyQhez9YGiKCsuOEaMXUQiVF7kWQplKvkzIJp3c0rUE3wxdKVpZui0mW3NIyE/GFESyVQhA766e2DvyF5yaxJA68y0+EfxjPbvPBlJa2YOT950Y9KtOkha7TDkctyBiept9U+BrVkAD0/sk8YQddhPhkeZ49R65DCngSRDMUDuzSnhi8T0uOL7XA0NbPgbeWLD79ynhzpICCfaIaiVGZYmmj5MOZ4ZT3G1jW47OFhQhycjGV82twGEQNov6S4ivo5edvgvdrWMlKdCLdapppB5ae8Z6/1eMoQjnH7BsuhnELXawrVB4YLs/cOmtVk8Z3rjjcaQKU8yaIYRj1eoB9mKXhZtR/HZy+b4Y792YNMXMQrelBX7znKOHSLiQ0KzDIFVBPmQTTalu+oZmJp9MH2ysyGtNOvA0Gr350NXqIc4E/Cl/mjHaSSW6RNyHGiJMh590IBDEMFEuta0i7LCAj9AywBxCbZ5yQz4UjxgOqr8OQI9IUArFVdth9h4LSwO7PomJqdNcCItWT0m858ZLfjN1sX6uk8XqJetFsSP2GQnEut+BH2ulU0+1zsSPHZkya3RnVrX7yp2VO2PrARjaU6WWDf96UEVI5Ick3NzyeyKgCgvyt9BRAvBdNEm+6N6L6qYMJWFWOteHDmSWRp9AbPZcDeH6wA+cuLN5C35443KORNp/ZxJPb3UecS67f5cgrWAwVma6O+J5KBYcIurRP2TWWyIUgF+1p0RaVSx5wJbUncovO0lhLT+gaBzqjfh/lHZpZH3ACHVMb1la8HNyVdUL94I+FyPBXyp623+dig6jMERNXqtkrn73HSwTq4myQj4Uir0+m5dONmaAtBsgshAl2Utad2gmqon6Z6S63RieWH+IdVgL8CXNTGxmJ3E+COopkev79V7xhRyjE52eht1BHypO/X93frGrXuyaj5pyNb7e8YUuL55176cmu3+h7Xb9/3x/zSts9MYY5nW0Zqd9eOErtLhjAUB+rfdsg5ZoDUWi6qYKT8zISoUHtfYblp7TaaNJmh1zJRHNKcwocWkrFPax5pLOJNHHXmSaVJJosCKuXOblxd5XibgWwnrbZLWWgBflRfmL1KB5xNIOKzmf4M0HSvUdP2U4MCdGnO9iqvVnHbB0k9M/vKFBa8kCqhkictwOSsrCl86v2xhwamIEXtXk0aqc4bGqIjXhK9e+KFyUd4VR6rjVc3ZWsTl3BO96j6kOe1lRQXflxXlJ/b3KfB6Xp768EN9NIm98+e3Q5nN6bXX65WKgs3Z6M3Li3u9uabW+vt/AAAA///aC+WNAAAABklEQVQDAAOkH09Rn2OjAAAAAElFTkSuQmCC" x="0" y="0" width="24" height="24"/>
                  </svg>
                    <span style="margin-left: 6px;"><?php echo display('dashboard') ?></span>
                </a>
            </li> -->

            <li class="treeview <?php echo (($this->uri->segment(1) == "home") ? "active" : null) ?>">
                <a href="<?php echo base_url('home') ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAD9klEQVR4AcRUf2iVZRR+znfv3dpmC3FZ2cqICK22hi4SKnRK9EeYRPQDC3K0prjEZtPVZPptoRs3KbFxp9PWAg3sx4yMaJUT8tegyWitHwpB6g3W0GwbzK67956e82332vDeyki8PM89zznnfd/zvud7v8/BZf5d+QLvuZrxSY2WfPayln25WtcdrNLarkot7V6lJX3M/VMD0p5gp6t37F6rO/3nMaBxdEKxXYA6BeohaBVFZ/ws+n9YqW8fK9e8dIUuKrDN1ezW9boNMfRy0tOk4VMu6FKMjLOG/vvUARZe4s/AQuqUmFBgi6v53OFXnFTO0WfIal8UNy1slIcfCkod/T+Mc16Xhlmb5Ynz53CdxHH/rXl4h3GEV3C+q37TCSYLhBp0siPYx53NZvKAE0PhkxskuCgow/Q9iKCRwgrRAMUtMjKjSQ6FB5F5skLf8MVw4rd+b4yXtz+vgOuqExnFbgZuJz8a7ceCxRvlV+oJmB+U1x7YJFv+GvzxBS3m3KOMvUjaekdok7AAsgN4hpEHyeMRwbNLW2SU+m+xn63oXanV6uAQB84kwUuwa8pW+dB0gg53n8GeJ449HABmJZLp7OFKvS13CIeZt5Zl0BrCEcUKE4PLtG3kef1GuQknEMACBm8hbdezHcUXO9bqdPopwfdgMZ/VUW7qHg74mVSPiuemN8tZaiCOybSF0VOYay0au2IcIIrHOdENB/ALB0xAR5Xm7KvSVq62i4kcciP5EykiCN0cks+pPbBV7SYYf8xRxRxzooqOClc+WFovG1xXohZLsL1Kp6oP3RCUMnYqrt6pT1Pb6b/PimANdRKOHx3mcDPFdoIb6IysdmWANiUCftgzmsHkXl7FIgiGqBvIc2zHU9N4XamTyAnBbmCEgRsdHmMKxVjvKNLgLos7Pqwa9iHCXb1LP1MFL81skm+pJ0DAZgP2ouZxLAaZnUSmhQBfWzIeQ3uOwO65nab97s3SbPE0vJrxIStwkuKaN12dRpsSo1ehnomDZAGfWQHtgUAmymhT4kyZ5jNhBU5YAdsRouI9OMYvxqOu/N6Vhbm8wgWcUHBvLuYVNkratgacsbXY/iMcjz3ekoIl4K+lVu98a41adXoXwJsVL9kkffeR4vITdyHlqYHlOim8XIs8Z3wtnnaPU1ODTgWOMzG/qU57+Or3OVkI0r8k8BMT8gE9p5dpN9ebB8Ex/w7s5y0SheKV8dVsB72xOFrG/X9teKYQB39H2tcYtqbwNlmLUF0r9uaVCvBIeR2Kyl6VHg68JOQ3S9f1U1GogkXsfWn2dvFa7xWwlSrXSVvFetkrrGr+f6G4Er+2WT7O3SptifnJAonA/23/BAAA//+PXqL2AAAABklEQVQDAD76U0D5j139AAAAAElFTkSuQmCC"
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('dashboard') ?>
                    </span>
                </a>
            </li>
        <?php } ?>



        <?php if (
            $this->permission1->method('branch_list', 'read')->access()
            || $this->permission1->method('add_branch', 'create')->access()
        ) {
            // one link: the manage screen (it has the "Add" button); users who may only add go straight to the form
            $nav_target = $this->permission1->method('branch_list', 'read')->access() ? 'branch_list' : 'branch_form';
        ?>
            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("branch_form") || $this->uri->segment('1') == ("branch_list")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="<?php echo base_url($nav_target) ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAEKUlEQVR4AZxVf2iVZRR+zvfde5vGZfSBA693hDQJkmVjbaMEbQjrX0kMknQhc2tDBSVcKMawXFCXMRzzRzVcQfhXCEIQWotoIAWK/iGmm4IMNPuN7Ue79/u+03Nesa6i3+Be7vne855z3uc557zv934eFvhd36FtUzt1xc/btf63Ln3lT8p0p9b/06UrdKu2LbAciQRTu3RRrNgURXhDI+QjDxtUsEEUec7bOd+sm/XxJJJEgmnFcyT4PBbMlhRTBB9Txdi8j6nYw1wIfIY0nq2YgKAbSynciQTNcQo1oaKGWdeIhyURaPNwJ/SxsWIC9XDSE9xmFUcVuEmScWY9HoW4RbKjJLrN8WTFBH9U42wJmGTWn4aCi5HiFNt1illfVB+jRcVkVR5nKybI3oSwFT5BI8poDJxnu86z/6McY859MKZiAltIILCCS08fki6SjXCjR3LD0sW2XTKfxSRJ4imyhcwcBDbViO7TzeccCY9Egr9z4HF34iDYkv8IDJwVKBjjnI94JBK09kmIGCtjQc/EDn2MG30u9nHO9CLQw5O1UvoY8whwMycSWMALg3K5ZUCu/pXC+7GgrhTjSY79Tw3L1dxhuWwxSbIggS3+freuigRPNAzKl/VDcpp7smRypzaabyFxBMd6tXpkj2ZP9GnuxD6t/WKvLj3Tq9Vf7dLg9B7N8Vh28yT13gMrZfAWSTp/2q453lfBtU6t/rVTl/7eo7UzWzXHSzCrtFm8I/CqcMCvwhpeautFMQigY8bD6jiDdwTYF4d4e3VBfqHd/VdRn/XQ6wv2FkPsz2TwIl++bbynBsM01s+msIatPGDB3tC7upybt5gZtvKMTzDbgPM5vrVZ9vp5npxvoww2WXC5pIDXuOY7bnQT34ks180xNuBVMsHqWimL5zp0uUfWPE/GAJ0XYsEVBgzRNqY+rpUEhXnBj7xvpsvBTSfoDO0/MJECE7rOK+UbjkPswBUmeoE4A0wi79FYx+CAAc3cyBSlMfSQ45jnZdcQxUiTrMVAy4UxLUwkzeQaWEWercwRp7HoI8XKmjUFq6bO271fjjPzbgaleT0fIWgN5x2ct7OqZZLCMfa3vxzc9HmgP6KPrVjGJNtZ7TYC29ojtKVZWfeiT+S422QGtND5JrNuCoFXOb7McS0Dt1Bfx+s5MNAHJKB/Hf1bmNBaShvF1jbRbliuakfATO0KuNXTJ0Hk4yXOP3z9PQlI/BEBHsD9f2o+tuXjZw5JwAQ/4J601g5TF3475G6cIyAjyM6u3DXyRDjFbCRDMeOm9z24ZzAf2+fWmX4vwNYZps0dAQ3upjSDLWR7THUAlqWbPORhPodOnwGW0lT4N90wqcIRMLDAUofNIDE/kcC46bR/zdIP8mTcsHm5zIS4wRYeZLVnzE7Q8SI/r6bTdphJFkz/FwAA//9CgcfUAAAABklEQVQDAM7a5bxBMLqzAAAAAElFTkSuQmCC"
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('branch') ?>
                    </span>
                </a>
            </li>
        <?php } ?>


        <!-- Store start -->
        <?php if (
            $this->permission1->method('storelist', 'read')->access()
            || $this->permission1->method('add_store', 'create')->access()
        ) {
            // one link: the manage screen (it has the "Add" button); users who may only add go straight to the form
            $nav_target = $this->permission1->method('storelist', 'read')->access() ? 'storelist' : 'store_form';
        ?>
            <li class="treeview <?php
                                if (

                                    $this->uri->segment('1') == ("storelist") || $this->uri->segment('1') == ("store_form")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="<?php echo base_url($nav_target) ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAFA0lEQVR4AZRUfUyVZRT/nffyIZE20al9kHNJSVa0SRFLq8nUNJetRTa3mhmVAQrix70F10F/5HD5AQI6Z3OksRW6OaU05iSdga7homxpGjpxOUIwbgPk3vu+p99z5d7u2Gpwd8/7nPM85/zOOb/nw8KwX8caTbqap5nX8jWdesKw5VGbkQRt+TrtfIEe6LOxL2BhiQ0sQwBHb+RqdfcqHTdq5KGAUILmNfp4MBb7HQtlMyrlpZQd4plWJUXJVfJCUPC130ZD7zuaNBQzqsFqKtUYCHay6tqA4OOQHQWRXC3fBBXevnhURU2PWLXQj8UEPpYxFnuCQOe4XnzyU4G6SZf7wip1X8rX3NsxOGNbiP8jT6eOGHnI0SLXs4MuNEqpOAQ5xSRT/C60O0A77XbHhSwoMklVoy14dihuxINFahIE6DURAaCFgJK+VeqfqJD6mZSAIpYU/UzwXhso68jTEzfydEtnrr7a9Z7ea+L+TyyCXh4EHjFOiT5cp/309+vUfbZI3eco7GJiarV0c34GqcxhQUfZ5UOO4m0Ws6tnpZ72va/7+t7VXP8KTdNsdRmssFhs/2DQwnLwd3M8HiUV41hpNivOJqgZmy+t0njaSylbSN0s+ihjGAFv0i6ZTb2DhSwjtTsG70GTnaO1ZtGI9XK5XGPgxSMezX+xXNpsFxYxgYdzHh5bD+nZ74tFDe0EAk9mFxOYJIld3PRb2M578h31J7nmqIWB+CDmM/5BA24kdA9+jEcxAVIaPLqXZ15P3o0TCT6cHLRgs7pydnIu0I8UW7GQ4HsDLrSw4skEjWFCH2P7qPdS0v6OxxXOxRhwI6EEpaXi+JmEDsm8FZszBvDtrfFoIM91toMPntkq1em7JZBWKednVkrdjArxTN8hi3gZn5M4LGfFNY7gOIEbKNfJgs092T2Qo3NDCfZ5dY4dRCNbrVq8SeYvLJd588plAQMvzNku7aaS/5LkbdLzQI00TamRikk7JWfCLnlqbDeyeADqmPRN6zOvrmTbq102lry2SQ5FA5GeaHPEutQL68VV0giLnwxmW//6JukyCJ+v08QDH2qG0UmZGSJyaq0uaF6rh8+s1erWIi1uK9C3flmtWb8Vamob4yKOVG5TSBcTCDAYROwer66r9eqxmDFoYNLDXAc3zwwR4fx0JnVxbxwG38c7kclXYOmgYn2cH1/yOJ+9kqeF4QBSDHPRoHGY5FgoZpA5GeDYbZy4J2aIloNMOp7ALQpcph6gzyJyPZX6XZQxlIkmwHTAgtgBt9kv+JOgv6rAN9CJ+QzoNE7D92AgEf3soOv5T6Uuc4ts63ewkfYPadsl67EKmUvwg0x4wsRiDEIMmD2AbUOZrY+J+GoAdAr5MFFojP4MT0rQyDLvC+zIDQCMbYXBDF+sBpjFBOzKRIVso0RJ2D88FZ3Q6OH1fykSDPpjsIy8SrjiMHDYOQzWQyW6YpqhKs1oxPgbMboRssJNDmADJ38n+C0Cp3MP3BzjjIOpyIzRwr2KmH9RYxy/d/48KAgOUWQ6CFHEZ8JXUiJfeIol25WI+9lJGx0vVpZpM8FSd29U9x5KbYm6eeQK2UFK/UfqPuRRN490IYt5uHGDupsorHi24+AN89ST8pVcwxDbdyooKpKB9SVypNArK6akYg6dXmEXrUzYys1rZcJmJlhtbFbayvXTtAuMzTerNajYTP+vjM1jfJxS9g8AAAD//1WCwe4AAAAGSURBVAMAHaxXpg48jWYAAAAASUVORK5CYII="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('store') ?>
                    </span>
                </a>
            </li>
        <?php } ?>





        <!-- product menu part -->
        <?php if (
            $this->permission1->method('manage_product', 'read')->access()
            || $this->permission1->method('brand_list', 'read')->access()
            || $this->permission1->method('manage_category', 'read')->access()
            || $this->permission1->method('subcategory_list', 'read')->access()
            || $this->permission1->method('manage_unit', 'read')->access()
            || $this->permission1->method('product_grouplist', 'read')->access()
            || $this->permission1->method('labelprint', 'read')->access()
            || $this->permission1->method('create_product', 'create')->access()
        ) { ?>
            <li class="treeview <?php echo (($this->uri->segment(1) == "category_form" || $this->uri->segment(1) == "category_list"
                                    || $this->uri->segment(1) == "brand_form" || $this->uri->segment(1) == "brand_list"
                                    || $this->uri->segment(1) == "subcategory_form" || $this->uri->segment(1) == "subcategory_list"
                                    || $this->uri->segment(1) == "unit_form" || $this->uri->segment(1) == "unit_list" || $this->uri->segment(1) == "product_form" || $this->uri->segment(1) == "product_list" || $this->uri->segment(1) == "barcode"
                                    || $this->uri->segment(1) == "qrcode"  || $this->uri->segment(1) == "product_details"
                                    || $this->uri->segment(1) == "labelprint"
                                    || $this->uri->segment(1) == "add_product_group"  || $this->uri->segment(1) == "product_grouplist"
                                    ) ? "active" : '') ?>">
                <a href="#" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAFGklEQVR4AZRVDUyVVRh+3nu5apmysmlLpxlzarbp1P5kabJ+qdQp6JiQJoiJEqCCCMj4C4FEJCQnWVrNqZeZ9DvXpOZMy1YmkptrK1Oy/KPSQLn38n1vzwE+5A518+57v3Pev+c5533P+a4Lt/H79zWd899inXMbKbglwZnlev+ZZbrDAWwHBrS7MaBbX6xVmqj3OvqNxlsSDN8kf1oC3+nlGm6S/S4csShmfjlRHwso7pEauWT0m8kNCX54XYc7Ce2CVLfi5KkknaDA9ADl/BIdb3twLuBCihOn0ep25j3HXgSapy5xo/Roqr7bsEr7W3dAWwXv2IJVJLtkA83cRYZlobwtBG26UPu1LtZ8fyi8PYGdeS8CyRN7UoXE2Ioa91WI348PA27sCKuW2BHV4h36tuy2gFLaTkDx/uU+GNruwsk+WxHlgPYcexE4zsmVcuRaXzzE5OZxlfjIsf+arLPZ6DKSRAcEVyh3h26RnQJhBZ2o62MQgTdP+9Sna8JX6Zp4aKWOYFOfZM0PHE3DT8dTdffPqRrlcyGBpbpAew1LtZf61PMJOuTiUp10JVHHXIfunAUR4AQsvxvNPI7/WH1wlau0CXaVcs0v+M1SvEpbM3fVMLJaNnL1Fnujgb54mPZolu35Ttjr7yCCubVivVAie599U2p9FsJ4SiIIPo3AKQSdzB2dp+3Y2EopNxD0RVKPCNhovW+zZA7aLBuNvacEEXhXa2hdlqZ9mqkHCRZju5BF4DC/B/05FhCwbmJFJ3hDqkZwpw/4gSyWKub3ZXrwjyRN+ztRQ9HjF0RgeVDDhFgCneWqa58plUae7rh2xRraZpHkyncrdPr3K7SC+mq4ETd2kzRabtTSd5a7iW0hRg98dBN8sFansaYtc4tlkiUoZkL0x5l6yKdYBA/iuMrP6Z/CXjzBRXzCsrzSBsQfS9NDJItmfPHITcwFWpqW6jSHpIPA61U3m1vIlWQbR1SxHJ+9TlI8rYhgYtM1G9v4WUgiSQPBGli6ZXYIbUBT/3ZEjKuUlDGVctzk8m5ks9mFzs3uILjwC4axngNZiuTtORrWEchXZJX4ZhbLrpdLJJJ9SOfRzCdBPg98+pRyiXy8QnaNYgxDO57GZA1rcyOZMQNPDcYwY+wgWJ4jp88BE1kajz8Ee97L1X3bc3X+tjztZ4KMsCwh9I/mjkb7BCHGZuRrxny7UuezL/t4AffQ7xk1CBMfrJbTxt9BYCZ5/ESwxk0EKUQAcWzyEJeN+h3ZWuXN0gm2jTKurIa7qCFI2ZfpOqF+lVZJC+r5WRnSxhzaCw2GEMtgGukmMAoDwFJh0Tq5GF8oGxYUSLgP2Mn+JLP+jUxutN1o5EobjY3jzqfWS3j4BtkwtUoucpdg/Q1UtwQRcHVgYrfTTBa9IYdjCyU+pkhyTbKJmVEsuS+VSPxzpXLYxDjCQwLbUbrGYAJqvPJdrt6D8RmS3p5Oi9k970Kn0vUmZNeMA78rTaxzcmmhRuXxw0dT0GNWb0iCjFS8jP0iQ6NMLu9JE03dTxDB2iypUyCGvQi704Nv1hfplrcKdLwTbUpgSBzdm6Oja7O0xOPDYeZMDlhY8HSZ1Dl+MwYRGEN2tvxFKc3Ilkd5W2vaXEiqyNcDlQWayNXfxRL125qr0dvX6mdsagEv3f5Z6/DIjBLJfLG882gaHEd6ETgOM67JlR8zcmQJb+dMls/D+i60BAncyWB1Iza2SObNK5L9cpM/G4PxPwAAAP//TnX/OQAAAAZJREFUAwAziD1bLYu22wAAAABJRU5ErkJggg=="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('product') ?>
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('manage_product', 'read')->access()) { ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('product_list', 'product_form')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url('product_list') ?>"><?php echo 'Products' ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('brand_list', 'read')->access()) { ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('brand_list', 'brand_form')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url('brand_list') ?>"><?php echo 'Brands' ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_category', 'read')->access()) { ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('category_list', 'category_form')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url('category_list') ?>"><?php echo 'Categories' ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('subcategory_list', 'read')->access()) { ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('subcategory_list', 'subcategory_form')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url('subcategory_list') ?>"><?php echo 'Subcategories' ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_unit', 'read')->access()) { ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('unit_list', 'unit_form')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url('unit_list') ?>"><?php echo 'Units' ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('product_grouplist', 'read')->access()) { ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('product_grouplist', 'add_product_group')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url('product_grouplist') ?>"><?php echo 'Product Groups' ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('labelprint', 'read')->access()) { ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('labelprint')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url('labelprint') ?>"><?php echo 'Label Print' ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>



        <!-- service menu start -->
        <?php if (
            $this->permission1->method('manage_service', 'read')->access()
            || $this->permission1->method('create_service', 'create')->access()
        ) {
            // one link: the manage screen (it has the "Add" button); users who may only add go straight to the form
            $nav_target = $this->permission1->method('manage_service', 'read')->access() ? 'manage_service' : 'add_service';
        ?>
            <li class="treeview <?php
                                if ($this->uri->segment('1') == ("add_service") || $this->uri->segment('1') == ("manage_service") || $this->uri->segment('1') == ("edit_service")) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="<?php echo base_url($nav_target) ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAFlElEQVR4AYxVC0yVZRh+3nPRg/OWzhRDZ2aJ87IszUydWbpMMVsKmSuVTBCngAiYhHQ0BaeiiGHSbPOyUlHnrXnJuxXVKknDYTZvmc45LVGu53D+t+c77qcjWXa2d9//vbfnvX7HgQf8LkxSz5UEfeVagmbcnKozy6fqW3fidH7VFI3ROG3xAHP8J8CviZqCpthX50C3OsW3dcCrPsFI3kssILzGwjbfFF2vsdrh34DuC3AyUV8sTdaf6gRCZycCDrgtF2bzvE7HVwJAYp0TD/sdmE7K9zvxqb6tk3Cf3z8Avpup4+tcmB4QLKejCJLfUpTetjCm/SqJbvuRzKoOw4iA4qgl+IBnoq8RYgg0gCAZDTHuAfgyVfsxyvEBJxJYjlg6OBaZL7O7fCg7eqySinPTtO/laTrd6UPf1oVyoGWhRFsOrNUAtlsWcvyCxwkyJhSkHkChQoWujLwp672JQFm98mSHrXxmhqZZTsxgOa4RfBwbv9jIWq6Ww6qI9buwodaN2cxklk7WVkZmqB5gfzomMeJ01nwTo5r3TK4cNwqGimdqGA2juqzExM4rZWvHApnGALpenqqPGHnzj+UM78ugyGDflvgcSDV8Q/UALMsbAQujWPPkgYvliBHaFKZoy+b+LhC1eXR4Hk5E2Hdmso22PdmvYuoONhUxsiDAHq82Z9q1ATc6UmmXEYTSk3m4xMza/ZykvQz/bLJ2pn7f8jYoMXebWOIilnAEsyipikNvww8CVPgRydpXccZjmN43RtCQKN9K2Y+nZuppn6KMUX7R/TQYz9+aLONxlrkfmadU0NVIggA+N676gRNs1iUBjiLkt9urTb5OxXZG9ai6ENmzBXoy0q7MCGfDsfdSgj5kq1fX4Soza8csrlGnneEHAQJ+dGJjZzCC6TVAJ4T8wqqwgsobn8uV9D5L5Jx4xeqdJxe7r5D5zHhJTSOssdUbuRHH3viYLScX0bfjNPIuwN0M9lCYpQ48bRvsfVc7MfLwIUtks80LPXuukAMEv1OWqEEb2j9B2o8mOEyQML8bziDAb8BFZtANFg7xCYiynbBs/akYsT9diw6SjqRpEZexqDhVi75P0aITyVrEendjWQYaG6cLifyeUFuNdQRe3rpATgcBvF6xGOmpWhfS6bS5UVaokHeR99RKbnaNA/EuJ+LhQnyAxNLEV3oQLx4Mv9EKBcamw3Kp5qi/ySx2RayS9YYXBDAffH/yuME/nHdi6LosHb0pE8cZfTSnYpRT8Tk3ILPKQp9bHtQOWiR/2uTwoLJZOeJOJuvR0iQ9xHepDZcx6Nz4deTna+P8ebqKb8kWLlurCMVubtPgGhdGRWdLyms5klTiwQCWYjvlA13VOHYoTR8zxsdTdeTtOziqAuH7FMXIvXz8xhuZTQ5fOV7myFXQwUrWL4UjljlpvqTEeuWWrdTdj+Xk9/YBm9mrffyOPZimu2gznH2L6rtMCio86MF7DsG22XbmdKgHB1jrQeLGDi5PFld9hBGEUrUbC6lzgyVLYhDPksK59RlDlsqMQGMoG7+asmS4EdMrT+5ZVEdamlRSWMh/p4SbinX8Hlq4UMNDASZ45frrC2XjmGyJH50jw6IWyeSXsqWUYxxda2EPy7JzQK6Me2qxXA21M9/BJtf6sZ5jNayFG20ZnddXh00F87S40Ksb1mTpxE+82t4o27R1jkbunKP7OAC9/vDjhedzZa8ta3gGAbxesRjFXG7xgpQsOcaNvkHApFonvGysh03PX5ulX23I1DWfZepG9mEpnEgamSNzYziaDZ2G3oMAhpGZKWYsm2Vn61iWqQknwp04V87Fvy+F73hlbJMy8AlGLjPMiFkoUaMXyC/G7kFUD2AUuQuJbHR/n+C9WZlSbHg2xWyRwIQFUka6YPP+z/kXAAAA//8W6jO1AAAABklEQVQDACwnhEE/sAfEAAAAAElFTkSuQmCC"
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('service') ?>
                    </span>
                </a>
            </li>
        <?php } ?>



        <!-- Payment Method Menu Start -->
        <?php if (
            $this->permission1->method('payment_method_list', 'read')->access()
            || $this->permission1->method('add_payment_method', 'create')->access()
        ) {
            // one link: the manage screen (it has the "Add" button); users who may only add go straight to the form
            $nav_target = $this->permission1->method('payment_method_list', 'read')->access() ? 'payment_method_list' : 'add_payment_method';
        ?>
            <li class="treeview <?php
                                if ($this->uri->segment('1') == ("add_payment_method") || $this->uri->segment('1') == ("payment_method_list")) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="<?php echo base_url($nav_target) ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAFSklEQVR4AZRVfUyVZRT/nRfQMNTVpmYZWtSGH6kpOO0P+wMtZyQqyXADm4aiAvJhAoLA5ap4RQE/LnHlIzNpORca1UxLbTnNyo8Ss+nUZrhMTbJmE+F+nH7vvUCIofPuPO9zvs9zznOecw1089tp0aBuRA/F/t8AVRad2eLGzR25+ml9jg54KI9dlO8JULFKnzI8qKKeP1ekS9FQv1ynEcf+ZRp5aKmuPZqhG0+ka/rpJTrjTJqOPrP4v2xvLNT4vxP1izsJutM1X2vuCqCqIi5spbPHuVxQHIOgtyjq92bqaSqHE6/xV2xUgzIgiIeJ6iFwNCZr3dXFuksFhbSdzH0W7afShmQb2K1YQnQyV9VFP/SMLZJxbkUYgB8gGKHAbBh4LLxMfgkrkcMvbJLtQzdJ4fPlEhdsl+gn3pGZUBwQ4CBXHfFfOwKUWnU4HdkouEBHF0M8sHxlUX/0RKMq0sjbTPmz3A+zRAWnUnTQmRQddjldA8nvANoHUj+EeqN5qEZvgC1bNIBELbUeoaAhwSpriZ+67sQaoxU7RVBAw1fJOwjFbu5DnAHIdPsh9h/FANKdoRd9DSYjhGXyZdDUhBAyRnOZMMz8xFulzgP08wjmkx7iCUR4xDp5ZWKJxEwolblhpbJk5AbJH7pBLlHeAXTakRFP78sgJ0fOQrGIWre5QqsLtMp8Bzx5Tz8P3iSvj18z3juQqScPLdPdR9O1jl30eUOqFp5P0X6Ud4Ch6NVOsFS+ACYjI18cUIwlfpydksB38CPxb5jubJatnMqBlH+nbmydUCbRlJmHyXcJfr6QrNsak7SEXVRL3VGUeYHZ+0rkpfhJschZGYiXqLSa5BA6LCX+Nfdj8Me8pkeRKgbG85KzvHLALR5Mdyoy3G5s9riwgPpxzDyLpcoKCMI5lomqnSAxUZy85BUCvEz2Ze5JNChgu/aOsUjrxPWSQ15fysYwU1uoXY4MLZemZxxy6clKud3PIZ/1cUhxr0opljJpvicADb0Qt1KOBDoxiiUyH1447+LkgWW6/9DbeounzKTS8ZY7MB8V0e6h2wCmSVSx3IpeLfOYrlnzZvIiuIK4WjwexIVVipP4feG+Adoto9bILvhhJC96H3nKkqWHb5RzxB8IRlGRhtpW6bHSVbp/k1XnVFg0tiZPs7fnafL7Fu3f7mFKkfw+ab1MQQAGjC+Rynb+g3bDDYygUhgUEby8eJ5O1A8HVXC+hxP2j3K04pNszd2bpe9ymn4MJyq+Tde5tLkL/pinva8s1pk3FmhoZ4EBxeB2hihqFxbIhwkW+X6OVfbFrpYYf0GRePAl+33ppHUy3TCQy0uORNvvbLKGXUhSR3MgttG+L4ehvU3k3QwVBHsxfmhoPh5iPqi16CC2ZwQE0RzRDnZRvUeR6DFgO56hixpSdQ/HdjTXOvLXM/vX6aPaZ+37ejNgaX4i2coTmJ1C1Ac93DAH2Ws8RCSDtND4KhQDDUUWs7rW2owoQ7CL/wk27pHOFiTwHezwWfu+BqMGM3odyb/o4Db3DohZKSeii2TWNJsMdwGp4oKVJ0jgsHuDL/piQCC20zaW4yMpuFxynq6RPzuM2xCzTQczSAHp/qxfnqNQs6rz9EWLhYmT2Q6RNrkZUSa/cZKN4Kj4gC2bwPGQwT+cpc855Hq7XtfdUIHpPBcKmznfxYMjDDgjxI3dO3K0ri5Xq9lFpeyirZymexSI8gOyx5ZJyhi7XOnqsCtt5C8Xe/YKKeI0XZ6WK9cSC+XwW1bJj18pUfzLjHYHIJNp2uk0LaJYppqzaFypXO7qqDv6XwAAAP//LyrzwgAAAAZJREFUAwAzZSUXccBgygAAAABJRU5ErkJggg=="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('payment_method') ?>
                    </span>
                </a>
            </li>
        <?php } ?>
        <!-- Payment Menu end -->




        <!-- supplier menu part -->
        <?php if (
            $this->permission1->method('manage_supplier', 'read')->access()
            || $this->permission1->method('add_supplier', 'create')->access()
        ) {
            // one link: the manage screen (it has the "Add" button); users who may only add go straight to the form
            $nav_target = $this->permission1->method('manage_supplier', 'read')->access() ? 'supplier_list' : 'add_supplier';
        ?>
            <li class="treeview <?php echo (($this->uri->segment(1) == "add_supplier" || $this->uri->segment(1) == "supplier_list" || $this->uri->segment(1) == "edit_supplier" || $this->uri->segment(1) == "supplier_ledgerdata" || $this->uri->segment(1) == "supplier_ledger" || $this->uri->segment(1) == "supplier_advance") ? "active" : '') ?>">
                <a href="<?php echo base_url($nav_target) ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAEPElEQVR4AcxUfWjUdRj/fO9+d7u7Lec5WW3qVi0SkvCPwHypKGLOGbLerKiQsdqWhhnutTbz2gt71ZRtNed0RnPQ/qk/ijMMHU6ITYqmDKUk0ShCimTLt93v7unzCBvX3c32T9DB577f3/P2eZ7P87tz4D/+/L8IGhok2NgozYlAX29TkzzEMztalLgJ2urlnvY6KZlBgzw2nZAUwbmaGlOdCPQttGzUeQTPt9XJXhExmhdH4BY8aQHLXECKZfC4K4yXNFBBmx4JQd8Fl+CH8h1mF2sMdTQgVwMd+hUNVwRgF+fZUZ47jB8ZPDXt99A3fY891ceca90B2UeiF5l7r8bEEXjCJLBx7I2AydsSMDuZpHG3kCS3joRf6tscMDXMK00G3nNH8E+JPt4pWf21EmTHhZZgH+8niONMXMwzeHiHFFKuFZ/USjOfu4lBolOfFS4by/k82F8jfc4QtpHg6cGALJiZwHsTbsozRIIUYoIB37mBU7R5iSGSJtM2lhSGauzj2cE4j8NGiyIJ+Jb+/cz5y+PAh8w55ryB+TMEDACd1yhRMRcWYAAoT783gjLavuEdtJ9lTDGLX97YZIaps5NylCioe86fv2OItjGnjSKND3tw2XG0SrKOVEoz2SuYuIHnc94QnuWi1yTZeJmyFLLTV5hY4LWxiGTzKNv6YJX8yiJLuVy/gndnlh+/0FfPu602/3UscOS2mEvrWk11fqspXd9i1updMenFGnZ1MQVojPWtazEP5LeYTNpXc6KjlmAov9kU0JZOZLBOldbQ2jMSxb4amVfxFgkmJYyB4Wrxc9LUk2WSO1wuu06WS+twhRSdqJBKSvk6SQqOB8QTW0OfExKMbpcllOROyrSYJJ+5Qtg+L4RRdrr8kXaU+1NQS51PUbojlHWCMZ/6JvCCFoxFQgIWf5ddDbhtpK7cbQ76Iuijpoe8YVwYexvvu/+AWf2BObOy3ZxmE5/7Qkjj7h6NLa7PcQRnt8padjTKzkrvuI5mDWKyi8u+QvsTXPTAlAPdpzeLX30PzsdXlCmPix0Z3yYPqy0acQQs5mZX+Uy6i6qWnd8q3c4I7udEm/iGBJd2mnNeoNpnYc9Pb0o2xmHoUynv45SroovrPY4gp8t8cSMNr/J1DLArF3X+nokl7Lwwp8N8qUnUv8YpaHICtT+no4e/j91s6jD9e9QfjTgCdS4LmCkWyKAsC4kcSjNgAfXjG8X92xbZa0XQxX1UsoEDnHQqu8N8vaTTnNHcWCQk0KCMj0yQRXr5S17EfaxyhzGSmYanuNAun+AdThgkQRPvfRo/G2Yl0ITUHjNC3as5QbrLYAX3s4myNFK2KU50N27imdRuM6qxs+G2BJrk7TGXkm0Us+OrJLD4nzXhs9GY0mPa/IfMFY25Hf6VQJPNQTNp9ZoiV6/ZYO03r5kD5qLa54I5Ecyl0GwxfwMAAP//1lMn3AAAAAZJREFUAwBp6dFAuICNBgAAAABJRU5ErkJggg=="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('supplier') ?>
                    </span>
                </a>
            </li>
        <?php } ?>

        <!-- customer menu start-->

        <?php
        $path = 'application/modules/';
        $map  = directory_map($path);
        $HmvcMenu   = array();
        if (is_array($map) && sizeof($map) > 0)
            foreach ($map as $key => $value) {
                $menu = str_replace("\\", '/', $path . $key . 'config/menu.php');
                if (file_exists($menu)) {

                    if (file_exists(APPPATH . 'modules/' . $key . '/assets/data/env')) {
                        @include($menu);
                    }
                }
            }
        ?>
        <?php if (
            $this->permission1->method('manage_customer', 'read')->access()
            || $this->permission1->method('add_customer', 'create')->access()
        ) {
            // one link: the manage screen (it has the "Add" button); users who may only add go straight to the form
            $nav_target = $this->permission1->method('manage_customer', 'read')->access() ? 'customer_list' : 'add_customer';
        ?>
            <li class="treeview <?php echo (($this->uri->segment(1) == "add_customer" || $this->uri->segment(1) == "customer_list" || $this->uri->segment(1) == "credit_customer" || $this->uri->segment(1) == "paid_customer" || $this->uri->segment(1) == "edit_customer" || $this->uri->segment(1) == "customer_ledgerdata" || $this->uri->segment(1) == "customer_ledger" || $this->uri->segment(1) == "advance_receipt" || $this->uri->segment(1) == "customer_advance") ? "active" : '') ?>">
                <a href="<?php echo base_url($nav_target) ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAD2klEQVR4AexUW2xUVRRd+86QphF/GmOaqiRgfRBjDT5IJIFg8cMYTY2PGk0gVNqBtNOqUChTHJgqMlJsgzKNrUKliRohwRTFfqgVwRQTtSMxvqhOrQox0ARISBs6M/du1pmZM0BC4YPwQcLNrO5z9l57r7P3PbcOrvBzTeCSA86PqK9eC75YqZ1fr9TR/Y3a/22TTrtkdo5wslqnj9fo3tPVOuou0U6NqD8XQl7AnYq6tIOlacENRLnnImZJxsaDWvJ7g677I6ibEkG92/gsXD/eTTmY7zqZ3KXeYdTYWF4gKShlYVi4wExLiq/SEkzBIItEPAeN5PwwXK+zbZz7W4lsLiuem8ttlsbEnexA80TBJ9kIMJFGFf3FBMiBKyhIAS8h99C/g8gKCLykD7tzobMjema9fJNWPMRTRonFB65DyJK4d84pkBUBCm285EasofCSlCBK8XlTu6Tfxhy7MLYyKvue2iDNFVHpiUTEMz6DpB8fUmCcsKfUtA89JmYg5N7UId3F70hzUacMGJ9FRuC9sN7XvVZ7e8I69NEaTewkdjXrwGchrTLEh9+QYZ5uLgU+4Ak/Jh4r2yy9JvZrg1YdqteB4aAm/qnTxJFaHTq6THtHA3qviTuxiN6e8mE/kyuYeBtbncH1DK7nsGj3p6s1YIgLNkl83puy8ME2ee6BdukzvvjLuow3pzvHzeQx19So8FjzWK2WOkkHC+n0kdRKa0dgbYKj+I0iiz9frW99uUq7+J107WvUjgPLtY4H+I7Yy0OZF29zjN3IWn4FFjkM3kyMUeArOk3Q4lRK8XQaiDH+Pq9eA4sFyAmQW0tfjLZPFY30H+I+L0JuP3ljzL3FcX1oSSrKT7NnOm1xMGk72zTd3ZPx821lrCDP4b6EV3Ijhdq4zt4uE2ctjq486aHF4X1+hIX28GvMEDJEkvhd/EiRcu4P08Zpz4tz/z8xSMxl/CBtVtgcxA+wbq87BfOdtSHpFAdlVPuLxPa0g21s7W0SDnJ9PRMHecI9tGcLCLsAfqJvN1HAr+k4bQexlSdvnRD8qYUoK90i26kHhEJyIhyWv5tekRXLw1LdEJEXayPyM2f5RMrDCgpli5vCBswyPs+PrbyB9x8ZwcicNgnObpeaWe3SNGuzjNzZKqfAh1T+neTHjmIs0saOzhegCN8dJjxU8xC7Cu9CwSQl2NxkEfqLZ2IBR7WB/yr+ZfsjxIkc/ksCv3B0OzCGOx6PyDjpF/xdtIPKSnE5ru/r1sm2QItMf+FVKVr0mhQ9v16mPfu6ND0ZlaFHt8jEBSvnnBcVyHEuy1z9AmcAAAD//wN0DmwAAAAGSURBVAMAFHzTQOg77d4AAAAASUVORK5CYII="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('customer') ?>
                    </span>
                </a>
            </li>
        <?php } ?>


        <!-- customer menu end -->

        <!-- Stock menu part -->
        <?php if (
            $this->permission1->method('add_stockbatch', 'create')->access()
            || $this->permission1->method('stockbatchlist', 'read')->access()
            || $this->permission1->method('new_stock', 'create')->access()
            || $this->permission1->method('new_inventory_transection', 'create')->access()
            || $this->permission1->method('new_inventory_request', 'create')->access()
            || $this->permission1->method('manage_inventory_request', 'read')->access()


        ) { ?>
            <li class="treeview <?php echo (

                                    $this->uri->segment(1) == "stockbatch_form"
                                    || $this->uri->segment(1) == "stockbatchlist"
                                    || $this->uri->segment(1) == "new_inventory_transection"
                                    || $this->uri->segment(1) == "manage_inventory_transection"
                                    || $this->uri->segment(1) == "new_inventory_request"
                                    || $this->uri->segment(1) == "manage_inventory_request"

                                    ? "active" : '') ?>">
                <a href="javascript:void(0)" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAEvklEQVR4AZRVfUzVVRh+3nsvEn5ManN+r83WljVzfazmmiWas3SWThfU5tIQNyBgQIDcIG4B2goppgYGlaZlQ4xR2YdDoH/6GOUkkbmcThRdTROCwHvv797f23MuXoRQurJ7znvOed/3ed6P8zs4cAt/Pcn6UF+SFvdt0CmRukVEcCZDp15I0Ro/kOd3oB0u1A9s0kzdpFH/RzQmwQmPjjuZpjkaxAEL+GRKJeJtB5wWhw1M89r4PpCkT49FclOC1ixd7u9BS9CB3jl/Ii4I/HMuFY0kmhtwYrVfoAEH+rh/xkrUBmZz142IbkoQEFRxdHudaGufgWrLiWwVbCT4GYLWE/zXSe/LsqDArO8O2siPmKDZo7dZgnMBFwptxReMfjqBqr0OfMTIZ9rRWOyzcexSstZzv9rvxHraS0QEhzbrGmsAzYw+ZsHbcpQRd7DmdWxuI8v1ndOJHZYfr9subPcCRXfskmRmdpkB3AgfQyX61K3317n12wDwaGA8llH2Gg9KY2MI6/xAdL/iuM+Fn+/cKU/NrpTfjI2PE8vGefTPOGN3od5nO/ELS/Ghz4ciy4d01vx2Y06CPVed+DzoxMC4WGxhKTrmVshBoxs+LNfw3fV1iIAgsXSsJWiCPR6miQE28W9jFlcmHyAKSy0b5WbP0hkxYrBUGDMDi58LAc8GFDmUnRzP0mFarUfHGaTHt8qlheXSZtYMxIgRw+/CZBJPHnF4bRPOAMwCoVoK2tnM/XRoc/rR0uDWFddscYILknMe/J1K0+j2DM2j7bv0v9yZqk1daTp/UDs4XyfgJQtOwBUaz+fVezIo+IyygplsacjXL7/K1bnGhXoj8EO2rupxoZn6K+xPGcs7j4Edsm0UXUzRqvOZGmMMQwSmQSaylHzp7pmIRVwf9QpOUyoBdzG6WoI0TvChnPsZzbn6NeVCXuFM2qxkaZdSv9K2sJfX+S+WcZXDwoNDBAQAr6DZIydH+pM84kksljbeKhAA/A566VTGaI9zfS/XvzNaYuA1ys1RsXjFr3jJG42DJNtPfY3BNIChDJjWWQIteatU13s8GjozSkYTuh2MzpCYPh0jaQVJppMsnvoyruf09cG8Wf0dF7Honh3SRKyQn8EIgRUUyIWYGCxm2rPGu3DknTd0gVESDKxtqPl8PcGXVPksTOT5SYJnKJBFoud5C1c8XC6Vzx0QqgAzjcjAgGVlyVW3W0r4wq9jiinlxbqXYLPY7CkcL/gYysRetNGxn81PZjB/8Owblqhh4ZvSbTDCg+TgOxba0i0kh6a8POnKLZR1LMt7BElghGtJ2GUyWL5dfAmlYhq7oDsareYs3LshAC4YxMgS8WzUL8ctP2YXyiPpRTKPRC2s9eywUUKJnN7gEa/PyXJwhM+NVPYw6MJsEht+jMrAGP13BCahkQ6+nR49XFOoD4T1ptY8D29xOEcfa+5Hk09xaiAWrUYREYHpT4ZH8vm/OJF9yagu0j3Vbp1qboshqX1VZ/Jj3M3vKV1tvPjENimJ84ipVGQZmEjMSC2Q8ykeWc/mfixRqGOz1/Jj28ha7CNZ1YqtEr9km3Qa2/CIKIOwcVi+XCRHugRxBK3lJdgXX4rFa0rlp7B+uPwXAAD//zU57PMAAAAGSURBVAMA4a8ZTO7v880AAAAASUVORK5CYII="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('stock') ?>
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('stockbatchlist', 'read')->access() || $this->permission1->method('add_stockbatch', 'create')->access()) {
                        $nav = $this->permission1->method('stockbatchlist', 'read')->access() ? 'stockbatchlist' : 'stockbatch_form'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('stockbatchlist', 'stockbatch_form')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Stock Batch</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_inventory_request', 'read')->access() || $this->permission1->method('new_inventory_request', 'create')->access()) {
                        $nav = $this->permission1->method('manage_inventory_request', 'read')->access() ? 'manage_inventory_request' : 'new_inventory_request'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_inventory_request', 'new_inventory_request')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Inventory Transaction Request</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_inventory_transection', 'read')->access() || $this->permission1->method('new_inventory_transection', 'create')->access()) {
                        $nav = $this->permission1->method('manage_inventory_transection', 'read')->access() ? 'manage_inventory_transection' : 'new_inventory_transection'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_inventory_transection', 'new_inventory_transection')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Inventory Transaction</a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>


        <?php if (
            $this->permission1->method('new_grn', 'create')->access()
            || $this->permission1->method('manage_grn', 'read')->access()
            || $this->permission1->method('new_gdn', 'create')->access()
            || $this->permission1->method('manage_gdn', 'read')->access()

        ) { ?>
            <li class="treeview <?php echo (
                                    $this->uri->segment(1) == "new_grn"
                                    || $this->uri->segment(1) == "new_gdn"
                                    || $this->uri->segment(1) == "manage_grn"
                                    || $this->uri->segment(1) == "manage_gdn"

                                    ? "active" : '') ?>">
                <a href="javascript:void(0)" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAACXBIWXMAAA7DAAAOwwHHb6hkAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAACZtJREFUeJztnVtsHFcZx//f2fVlvSEJARkTG1FaJHjghTpthUCCgNoIqVH7ApIfCMTekLrhJZIddtdILIq86xLzUKHEib3r5SYVwksRERe1ogIhyiVyVYR4CzTUpU0hCRCvdzf2zsfD7razu7P3uZyZOT/JimfOzswXz3/PdznnzAAKhUKhUCgUCoWikcunOXT5NIectsMKhNMGyE4mnj98Z7iw8b/hwl8y8cJnnbbHbMhpA2TlUjz33gCLpwB8Qb+fwFd2NTp18qnQPxwyzVSUAOpIJFiMF4oRIj4HYG+Tj+XAfHZzOPStRIJ27bTPbJQAdKQXth+EJi4C/NHOjqCXILQnIosjf7TWMutQAgCQTdzeXyoOJwGcRGNcVCTCNwGAGWcADNW1awAuBYYK8eOJd/7HemvNxfcCSC8UjrLGKwSM17cx8OsAxJPTqaG/AkA6WvggiM8DeMTgVDeIcGY6Ofx9ArHVdpuFbwXQz81MLxSOQuMLACYajiT8RrCYrYpGdnwngMunOfTfUOGrxIiisTtnAD8YwM7pL6b23mx1nmzi9n6tOPQNBp0CEKhr3iHwysBOKH5smXImmm86vhJAJp4/DMYFBj7c2Movs8ATJxZHft/NOdfjufs1FisAHjRo/hsRfWUmOfzz3iy2Hl8IoFlOXyEH5rN7r4WWP/9jKvVy/napo8y1A08LoJMbQwJPTi+OvGrG9ToRmmy1A88KwMmuuZ2rIWB2JjXyohXX7hbPCUCW4Cyb4OHdu4Vov8Gm1XhKAN3k9LbZJHntwBMCkP2PDMhbO3C1AMzK6e1CFvekx7UCsCKntwuZageuE4DVOb1dyFI7cI0A7M7p7cLp2oErBCBTl2kVTtUOpBaAjEGTlThRO5BWAOno9udA9G0A76lvcyqnt4s2ae0tZopFlobWzEhrpROAG3J6u7CjdiCNANyW09uF1W5QCgE4mdOnY/lX9NuRVOgeM9vNwqpAONi/ab1TTYGYHc3p329xuylMJ8MbiQR/rEkqfC8z/ywT2+66duCIAN7K6dlbOb3VJBKkAVi9FM/91Kh2wKBHAwKH09HtjmsHtgtgPZ67XysWVkCturKQnTn9dYvbTedkMvw6gGOZeD5r4DrDIFqaKOanMrHttrUD22IAv+X0dtFv7cAWAfg5p7eLXmsHlgpA5fT2023twBIBqJzeWbpxt6YLYDVeOCKYzwO4r7GV7gLaLYCKZl9XYQQPAeIAwIMGjdc0olOmZwGC+SKAe5oYNAjQmNnXVDSDUO5wDblPMF9UTwjxOVbXAXYBvGbxNRTdMQ7dfbdaAJuRVOgDFl9D0QXpWP7v0Llo5QJ8jhKAz1EC8DmWxgAMTNSPl1tFu3F6RRkGxvXFH0sFQOXz2zJeboBT15Wa+sqfcgE+x2oXsEvO1QFsH6d3AxUXYE8dgBysA1g1N8/tqDqAogYlAJ+jBOBzgmbny/o8s4c6wGYkFfqEmfYoWmN6nk61vztZB1B0gHIBPicI5/PlCTTOW1PYhONrAysxQtVNqAkk/dE2hqqrA7zi6NpAA1TMYDMqBvA5svUAygV0T18xlGwCeE3V8LujLobqGuUCfI4SgM9RAvA5ssUA42ouX9c0PBq/G2QTgKoD2IxyAT5Hth5A1QG6p2atX7fIJgBVB+gSVQdQ9IUSgM9RAvA5UsUAXK4DXOvlWALullg79uWl8J/MtstL1K/TkEoAlTmE9/ZyLAMQJJ5bj20fmU6N/MFcy7yL11zAPg30y/XY9kNOG+IWpOoBKmsJe3lL1hiAkcrv+zTgF6vR3CPKHbRHKgFQuQ5g8HzB1lSepf8cgAOVM+0XRLa4g3Qs/1sYPZWzNyxfF1FXN7juCRcwnQxvCNIeBnBLt9sudzCB8h/UjB+zhNQxnhAA4LgIXItnBABIIYISyuss9D/6lzbsGrQ7+oZTqWIAM5hOhjfW47mHa2OCt0RgdUyw2eRZRVWf2zDW0W8tv1881QNUkaAncA2eFACgRNApnhUAoETQCZ4WAKBE0A7PCwBQImiF57KAZjiVHcg+w8kXPUAV1RM0IlsPYPm6AK38BpUiAA1vfwHsqhNIh2wCcHJdwD4N9D0AH3Lo+o7gKxfQAUZv1/I0svUATqwL6Gtevdsx/TmBPaBf22b7uoD6Z+catLcb79fb30sM083xps8XUGvx2lMd7++Efv+ett8Pabu+dt8k2fNrtyDDcwKb+WAZe6YSgM26fXr7jWKYftstfY5i0OlvktPj4V3S73h/v+2mI60LgPM9ky+QVgBO90x+QRWCfE7bHsDsOoFZ3+zVaO4BIvE4AYcAHATK6wqYcJVIe3ZmMXzVjOt4nU5cgFQBWmYhd0jTxDIBn6xvY+AjYBxhFgvpWP4FQdrcdDK84YSdbkHaGMCITCw/wxouUGc1+8MaixfT0fxsZCm0bpVN/fZoTsc6nQhAimg8Hc1HGFgzbCT8GwDAeHddyyAImUw8r80kQ9+x1kJ30lYATisUANaiuUkQztftvsGg5O7Ozg9nl9/xJgCszN0ZHRgcmCLmOAOj1Q8y42I6mns5shR+yVbDXYArXACRWEZtt3+VqfToieSeG/rPVYTwdHZ+60elYPAKwJOVpqHKOT5jk8muQfo0cDWaewDAp3S7bhjdfD3Hz+15o0SlowD+Vd3HwKfXornJZsf4FekFIIR4TL9NRIutbn6Vk8nw6wRO1h4rHjfbPrdDkswHaDoYQqBRBocqm7yzsztW9flAY51CH7Nk57fGSsHAP1F5NxKB8gx+E7X0O1jTDquP7/b8+sGl67LNB2iwh8FvbxBu6m9+hab2Hz+35410LH8blWngFSG1+v+2+3s4Pd5vun3Su4AauJe3nJHjb0aTGRnmA7SkzgUcWJm7M1rXCzS1Pzu/NVYC79edy8gF+JlNx+cDtCMdy58F8LXKJg0MDkwBeLra3sr+0oCY0vcaDD4XSYW+bpWtbkR6F0BC+0nNNnM8O7811u64zJncQYDi+n2Cas+lcIEAKqN6L1S3GRgtBYNXWokgcyZ3kAOBK/rSMAPPq4GhRlxRCRSkzWksfgdgqLyHJ0vBwJ8zse2kRtoz1bpAdn5rrDQgphgUB7N+XKAotMCc/ZbLj2si5LVY/jgBzUb1blb+fVeT9i9FUqHvWmCW63GNAABgLbo9BaIMAaH2nwYAFBmYPZEKZS01zMVIHwPoObE08gyx9nECftXusww8T1rgIXXzW+OqHkDPWjQ3KYR4jDUcAuF9AADGqyRwlaA9qwI+hUKhUCgUCoVCoTDi/xCC5X6zDjuoAAAAAElFTkSuQmCC"
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('warehouse_management') ?>
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('manage_grn', 'read')->access() || $this->permission1->method('new_grn', 'create')->access()) {
                        $nav = $this->permission1->method('manage_grn', 'read')->access() ? 'manage_grn' : 'new_grn'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_grn', 'new_grn')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">GRN</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_gdn', 'read')->access() || $this->permission1->method('new_gdn', 'create')->access()) {
                        $nav = $this->permission1->method('manage_gdn', 'read')->access() ? 'manage_gdn' : 'new_gdn'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_gdn', 'new_gdn')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">GDN</a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>







        <!-- Purchase menu start -->
        <?php if (
            $this->permission1->method('add_purchase', 'create')->access()
            || $this->permission1->method('manage_purchase', 'read')->access()
            || $this->permission1->method('new_purchase_order', 'create')->access()
            || $this->permission1->method('manage_purchase_order', 'read')->access()
            || $this->permission1->method('new_purchase_return', 'create')->access()
            || $this->permission1->method('manage_purchase_return', 'read')->access()
        ) { ?>
            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("add_purchase")
                                    || $this->uri->segment('1') == ("purchase_edit")
                                    || $this->uri->segment('1') == ("purchase_list")
                                    || $this->uri->segment('1') == ("purchase_details")
                                    || $this->uri->segment('1') == ("new_purchase_order")
                                    || $this->uri->segment('1') == ("manage_purchase_order")
                                    || $this->uri->segment('1') == ("new_purchase_return")
                                    || $this->uri->segment('1') == ("manage_purchase_return")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="javascript:void(0)" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAADcElEQVR4AbSUX2wUVRTGvzP7b3abYpES40NjIESDGo0KxqbREq3gkwlqN8oLVIKEBzUajYTWZSOKWpMifZIGXyGxRqNPalTSBFHX1gQKJSRQCAmBkGVLWcJ2d2fm8J2FvjQ7pdCwyW/ud++593z33jOzDu7y7+4baKdGql260+vSPTf5xtug66rr9QXdoE3zPaBzpIxlYwU8ODaBAePoJfx2rIBe6neOFLBv3gYnruHC+GU8cHICe4zxSXx8ahL/kmXUse9aNTkfEyf9u0yuHZKVrwzJips8wfbVKcF/U8AVL47cvnYdvgP++LZNG0OLXFbkphTKdu86mhvUV98YwkrTFQfry4J/TBvUB0sONpo2KkDQGMALNxDkKgLxBE9PX1FVMDnQgQXWZwIYpo0q0Mz5edNZqFNRxNJ/Cz1tpA5SxGEfaOLClukwdR4Bmq1vyRk3WcPjuBvgknUWPYdHuLEx06En2DwiVQ8QJin2t2tv/yr9gvqxso+emla85ylaTRtMuLwIZE2r4BPGhsFfqAFj3CxGfUUpAM5z0aAv+IscNq2KXzl+qqYFg9T5ac01og5ylmNWAybJcaHNa3z3gIxIgFFSMB04GGPCi6YNzi1aa0Cw8P7FOG4LZzVwEtxFFBJEsMIms837MdRqUOuLPYHsKnU1gpL1+lo1GURRSg8KbxSY1YCf2JUggaVk4VdrtIPtUo3jSdN+As+oixbTDSm8HLhwTftN6A7iOGBmRqhBX6cmvQh+psmIJtCAFJ6ijjPpqGkk0Ux9qKYTWEL9i2lJ4syCFvRZcsOxRz18B6uZ+Cx33YBGPPvhj/LlXPjgB9m7eUD4Rt/IGm7gYoLXAJos5ufY9vmb2jFXeru0PdvJy6RHqMHUEhzksVM0KIiLTYjhrbnix/GZey+2MH94kbNZCRIpvIYE/mQBX6JZQNpoWGF/vA73Mb6cdbqH7SIvhe9nNbDg+7ukxDfiEOuwf9tueZ1mbwdJ/N+9W7bOhLHL0The3Pa1rOEm8j275JzlCL0iCxpRB6e5o4d3ZPV530WahiM2PhOeaLicxMZPM7qWG6r96dmcWxp81CtFjWJLEMNqJt+f2SFDtnAmDz2KnSxrkS/G434Em6bjtzSwiZmMjG7vlq3be+Qn69cjnRY/0yP9nJdl/eZ+gnrJbmfsOgAAAP//kgz90gAAAAZJREFUAwCVEeJAO4xjQQAAAABJRU5ErkJggg=="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        Purchase of Product
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('manage_purchase_order', 'read')->access() || $this->permission1->method('new_purchase_order', 'create')->access()) {
                        $nav = $this->permission1->method('manage_purchase_order', 'read')->access() ? 'manage_purchase_order' : 'new_purchase_order'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_purchase_order', 'new_purchase_order')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Purchase Order</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_purchase', 'read')->access() || $this->permission1->method('add_purchase', 'create')->access()) {
                        $nav = $this->permission1->method('manage_purchase', 'read')->access() ? 'purchase_list' : 'add_purchase'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('purchase_list', 'add_purchase')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Purchase</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_purchase_return', 'read')->access() || $this->permission1->method('new_purchase_return', 'create')->access()) {
                        $nav = $this->permission1->method('manage_purchase_return', 'read')->access() ? 'manage_purchase_return' : 'new_purchase_return'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_purchase_return', 'new_purchase_return')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Purchase Return</a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <!-- Purchase menu end -->

        <!-- Supplier Payment section -->
       








        <!-- Invoice menu start -->
        <?php if (
            $this->permission1->method('new_invoice', 'create')->access() ||
            $this->permission1->method('new_pos', 'create')->access()
            || $this->permission1->method('manage_invoice', 'read')->access()
            || $this->permission1->method('new_quotation', 'create')->access()
            || $this->permission1->method('manage_quotation', 'read')->access()
            || $this->permission1->method('new_sales_return', 'create')->access()
            || $this->permission1->method('manage_sales_return', 'read')->access()
        ) { ?>
            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("add_invoice") || $this->uri->segment('1') == ("new_pos") || $this->uri->segment('1') == ("touch_invoice") || $this->uri->segment('1') == ("invoice_list")
                                    || $this->uri->segment('1') == ("invoice_details") || $this->uri->segment('1') == ("invoice_pad_print")
                                    || $this->uri->segment('1') == ("pos_print") || $this->uri->segment('1') == ("invoice_edit")
                                    || $this->uri->segment('1') == ("new_quotation") || $this->uri->segment('1') == ("manage_quotation")
                                    || $this->uri->segment('1') == ("new_sales_return") || $this->uri->segment('1') == ("manage_sales_return")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="javascript:void(0)" style="display:flex; align-items:center;">

                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAE+UlEQVR4AZxVbWxTVRh+33NbWta7tYDJEA34x6j8MMKiMRoRf1QJGhMSWnQO4urYmLKvENbbQvHafXR3It0YMC5CRVFDmJIogppOxSgKhuEvgvGj/FAWFsV+rLPddu85nlugdsNthJvznHvec9/zPOec977nELiJp6UqXSlvGP458HIy6qtLRr31yairezBa3zEUbW+5Eo0EktHj3nT0XGM2Olg7foZVs3uv084o0O4ZWQ8I5QDsdMseuzPUY3cqO+3OvoYFzp1SqdMfmOf0tNidTyuic2mX1YmAQdBh1U0JdLzIyRku1wVh/fUBhW++gh2ynJxb2IeMZbht5siVKVfwxrpMBZ+NjxE4RnS6IuddULnDlzyAcJ9F0I4VdN/QnFIAGbzEvVUCdBECsxPEA9wuKPg3N2wM4CBM80wpwAcy78EixXdAVAJvisq23uKvJ/AgLAaGuxBgs1rN8lsywYcbUwrwb9MWImgH+RbJFFh3zT4cn8r5lgUO1y0cZIK+2r913p6pyI3+GQXaqtL3B6tGyoK1I2WB2kSZVH8VPA/KBGqy9vUBeU9KzPlEGik7W59ZaJAWYloBWeb/EMXViMxFKXMhIS7CiIsicTGGLsr01bFYXMwIeDfh34lAHikkN9ozCCDdErFt40GWZFWUgrtLpPaeEqmzu0T6oPF2aad3vl+S5iar2uw/rOwUpaVhy2GDtBAzCDDS5hnZ3Vo9rMo1w2rglZTq35hSmxtTqis8qDZ0Du0NhRJzIoHhx080D6s/NmRuSMgZBJAyDds1zawA0RWBUoWaqUKQKjoIikb0kM/niKMgnqGE+6C1r3D2RntaAcNBE2cnLZZs3AIQz3JAFuIGrLxvXLMmDB/beeC/aTYu6mAcE0ZXHtMKGEGelc3s1cbNapYKqgmJiiai0llE1cbMqhXH9hlblL4n+QShZjVtGt2QZ77W+F8BBownKJgXnwf0R4oqAvtt7tfUYnfrnhJ3qKfEzYPsPtK4wM2DvMbYIk+ro39lp829JGztvsabf00Q4DM29a7TA93lo98A4F8XbZmvQpXprh1NbPar1ZmFW2tTXn9dwis1JLzurkveOmVos6L8WfyWHH/gRHPae64h+yxMeiYIFKfo20gh1fC+5bFNh6yr+Fm0DAg5lU6PfIhF2RQAGeADBoDAADAYYJQNOBy38dCYLxPKbcRfJvFz12s9wRBbwpt0w7tCNwI/S7nx+lpm0xF+52f8WRgVlrf2iv2tPY7+jrCj/0jTHf27fPO/rKnB8UpZvLxiu9hf1mW5wIdNKHxCV21O+TAA+XhvhfZ8+IWMJ0cu/HMUgRYzIB/xGS8LbEw96iu4MvkWfWYEObIt+dRxfmUONI42X2X7r84LUIAxhjDr8h9CHxd7Etnot0Cha8t+MUoAzfzk1Ft2lZwKFVyZPd7SFbkgB+2fG1cmX0GnzkDg9JQjV0iu5hUDOAXA1sgnUUsMWisExHLpHdun/BMwpM/x6qHJmVyvDKltrVfUSCCl5jK5MavyTQ8Cgy+McQbyArIff+IH1q+9a/Xti+4CU9Mhy4UjLia0edKbgOECQL1yciYLFBSiCQoIPMN5JmsIigamlRjB7w1yA3kBw2gKE04GvyXHx05uX5v57mJR5jRhaNXvLCqXex0xAzzAMQNHm0pjYV9pzCc7Yh6OZzqssQfD1tiiXowbXNfxLwAAAP//ScI8JQAAAAZJREFUAwDNYj390PKE5AAAAABJRU5ErkJggg=="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('invoice') ?>
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('manage_quotation', 'read')->access() || $this->permission1->method('new_quotation', 'create')->access()) {
                        $nav = $this->permission1->method('manage_quotation', 'read')->access() ? 'manage_quotation' : 'new_quotation'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_quotation', 'new_quotation')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Sales Order</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('new_invoice', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("add_invoice")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a
                                href="<?php echo base_url('add_invoice') ?>"><?php echo display('new_invoice') ?></a></li>
                    <?php } ?>
                    <?php if ($this->permission1->method('new_pos', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("new_pos")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a
                                href="<?php echo base_url('new_pos') ?>"><?php echo display('new_pos') ?></a></li>
                    <?php } ?>
                    <?php if ($this->permission1->method('touch_invoice', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("touch_invoice")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a
                                href="<?php echo base_url('touch_invoice') ?>">New GUI POS</a></li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_invoice', 'read')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("invoice_list")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>"><a
                                href="<?php echo base_url('invoice_list') ?>"><?php echo display('manage_invoice') ?></a></li>
                    <?php } ?>

                    <?php if ($this->permission1->method('manage_sales_return', 'read')->access() || $this->permission1->method('new_sales_return', 'create')->access()) {
                        $nav = $this->permission1->method('manage_sales_return', 'read')->access() ? 'manage_sales_return' : 'new_sales_return'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_sales_return', 'new_sales_return')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Sales Return</a>
                        </li>
                    <?php } ?>

                </ul>
            </li>
        <?php } ?>

        <!-- service menu start -->
        <!-- service menu start -->
        <?php if (
            $this->permission1->method('service_invoice', 'create')->access()
            || $this->permission1->method('manage_service_invoice', 'read')->access()
            || $this->permission1->method('serviceorder_invoice', 'create')->access()
            || $this->permission1->method('manage_serviceorder_invoice', 'read')->access()
        ) { ?>

            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("add_service_invoice") || $this->uri->segment('1') == ("service_details") || $this->uri->segment('1') == ("manage_service_invoice")
                                    || $this->uri->segment('1') == ("edit_service_invoice")
                                    || $this->uri->segment('1') == ("serviceorder_invoice") || $this->uri->segment('1') == ("manage_serviceorder_invoice")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="javascript:void(0)" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAE2klEQVR4AbxUf0yVVRh+3u/CBZpWyh9WlmuZa5X0U22oS8Wcc5iWP1DMEYpKGIioyFXh+oEKNK8imKRohc5MJfrHlClqbaFlmZlL3ZhTZ7K5mpZuqffe796359yErstN/6Du7rNzvnPe93me9z3f+Sz8x7//X+DIPH3+2zx9tLMK66jgy0J96KsF+lFAUKwx+OzoXC05XKAJ9yoUmKn9nRk6QTM0MTrH2pOncV94tCjgws6QCw0hC6eI60ELA2KB0z/m68TohDvNf39HbRrLcQQPh9zYpVn6ZHucdaMLfCEgKyjY7ABehzWFBNeIi3zO43P9sQKdfLxAe7YW6BMGZ/N0x4VcnWRIzmVqvAJDutTJ9PiNsjasmMvcOWbPwKLbLsSHFKkj4SWOyXRfzSCH6zMJn6O4Pwx4AoqiYAhFjBtALFFbY5yuUJrjo6EDmKuspOPZIiHo8icS1nPsx82unC8nmgavkjGMbKWIvlQleX3XSPazNZLNtZnc7y+2OH3Wij8IfPdrjm66kq2zKVB7U7DubznA4gICIVwgcQ/iF7ppCrvgTVkpe0yQMRBm275ZoEO/L9BNJ3K0m+Pgh1AC3Gb//Lv6JveHM+83csVT+IwIVl3N0u5m37QIwThcT1sho0XxOt3BwGwaBF0sm5OQooYGUv1x2BAiAn5sOJ2vIwMWssSNYT1rZVGPD2R14npJZ6xPY/G5TlSXxfKaEMaWLSU6ZFyFXKYb3CShbau1x6PT6SiXgkesMF5hq/L7r5G0F4nnqmUK1z3qRuZjVXKDKR3/7nXSQjP7ryZivDWrVHbCQhqTMzaX6A4S9oIgOSmAgyz7kasBpLzmkxMDSTLYx9hbNDvpjiS9AkF8emqONrfmafPZXG2+MFub2wi2NpXoy+4C02y5NK1UshwXVvKg+5D4qVAYM0hwyZ0An6mEd6XbLe7IkNYgIVbSO6lGRjxD8O0yZ+GROIxhu0awXclslzciYDLeL9dEJ4C2qWUYSuImJwZbOcZQtJZtdAuwfe9C3d28UAv3L/rntrYs1K7H87UiHIu9fkX6dQe7DV87IgJVZZqpQTSG3WjctBT7eQ5POzcxfNIKWT9hmZwcWynrUytlJN+SqWrhIkKoV6i0zNe3XQ720cSxpGoZdMMPL9vyQDu5GSMCvERvcOMTnkMNXbdllkpJhk/+NAEGDYt1VONiPcy96qCiNw0kHSjEQZ7X46pI6bdaGvjtSo1JwIFwDGxE/SICdOZj7wflemU7Se5bZ+sLUTGYWC5N48tlIG9yGd2eZ8tsxGLyEJ+U+mOR+PV8bSBHqgpG9a2SXdG5EQFPsbTQTbyvTF+mQ09YUGnOZMNS7RUdnFYpZ8aWy1a2q97lxuV9RTqPlW/jS+FLXi2zeeh/RMebeUTATOhuEauomGfLGQqc5Jkc4PjexqV6qN6rtVuKddzHtj5oYhuX6KvX/DjISxa+cg7DUnxyxKzfCR0CJSVyjlUcq1iu48NhLOP8co4t6TNsDCZRHc+ntyuErduK9RBdv+W3kD66QtaY1/VOxO1rHQJmIRhEOV0X+mNQRZKfzZqI6Cxbjk8rk5UZy2T0lOUyiJ+VbKLN7N8NtwnYtlxjPzP4CVg33yv5d0u+l/3bBEyCvVhai4vlqJl3Bv4l0Bmk0Rx/AQAA//87xP1yAAAABklEQVQDADT1Ok+57jfbAAAAAElFTkSuQmCC"
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('sales_of_service') ?>
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('manage_serviceorder_invoice', 'read')->access() || $this->permission1->method('serviceorder_invoice', 'create')->access()) {
                        $nav = $this->permission1->method('manage_serviceorder_invoice', 'read')->access() ? 'manage_serviceorder_invoice' : 'serviceorder_invoice'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_serviceorder_invoice', 'serviceorder_invoice')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Service Order</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_service_invoice', 'read')->access() || $this->permission1->method('service_invoice', 'create')->access()) {
                        $nav = $this->permission1->method('manage_service_invoice', 'read')->access() ? 'manage_service_invoice' : 'add_service_invoice'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_service_invoice', 'add_service_invoice')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Service</a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>

         <?php if (
            $this->permission1->method('new_supplier_payment', 'create')->access()
            || $this->permission1->method('manage_supplier_payment', 'read')->access()
        ) { ?>
            <li class="treeview <?php echo ($this->uri->segment('1') == 'new_supplier_payment' || $this->uri->segment('1') == 'manage_supplier_payment') ? 'active' : ''; ?>">
                <?php $nav_target = $this->permission1->method('manage_supplier_payment', 'read')->access() ? 'manage_supplier_payment' : 'new_supplier_payment'; ?>
                <a href="<?php echo base_url($nav_target) ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <!-- banknote -->
                        <rect x="2" y="8" width="14" height="9.5" rx="1.5" fill="none" stroke="#9b7fd4" stroke-width="1.6"/>
                        <circle cx="9" cy="12.75" r="2.1" fill="none" stroke="#9b7fd4" stroke-width="1.4"/>
                        <line x1="4" y1="10.3" x2="4" y2="10.3" stroke="#9b7fd4" stroke-width="1.6" stroke-linecap="round"/>
                        <line x1="14" y1="15.2" x2="14" y2="15.2" stroke="#9b7fd4" stroke-width="1.6" stroke-linecap="round"/>
                        <!-- outgoing arrow -->
                        <line x1="15" y1="9" x2="21" y2="3" stroke="#c4a8f0" stroke-width="1.8" stroke-linecap="round"/>
                        <polyline points="21,7 21,3 17,3" fill="none" stroke="#c4a8f0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span style="margin-left: 10px;">
                        Supplier Payment
                    </span>
                </a>
            </li>
        <?php } ?>

        <!-- Customer Receive section -->
        <?php if (
            $this->permission1->method('new_customer_receive', 'create')->access()
            || $this->permission1->method('manage_customer_receive', 'read')->access()
        ) { ?>
            <li class="treeview <?php echo ($this->uri->segment('1') == 'new_customer_receive' || $this->uri->segment('1') == 'manage_customer_receive') ? 'active' : ''; ?>">
                <?php $nav_target = $this->permission1->method('manage_customer_receive', 'read')->access() ? 'manage_customer_receive' : 'new_customer_receive'; ?>
                <a href="<?php echo base_url($nav_target) ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <!-- person head -->
                        <circle cx="12" cy="5" r="2.5" fill="#9b7fd4"/>
                        <!-- person body -->
                        <path d="M8.5 10.5C8.5 9.12 10.07 8 12 8s3.5 1.12 3.5 2.5V13H8.5v-2.5z" fill="#9b7fd4"/>
                        <!-- downward arrow (receiving) -->
                        <line x1="12" y1="14" x2="12" y2="20" stroke="#9b7fd4" stroke-width="1.8" stroke-linecap="round"/>
                        <polyline points="9,17.5 12,20.5 15,17.5" fill="none" stroke="#9b7fd4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <!-- coin/money circle -->
                        <circle cx="19" cy="9" r="3" fill="none" stroke="#c4a8f0" stroke-width="1.5"/>
                        <text x="19" y="12.5" text-anchor="middle" font-size="5" fill="#c4a8f0" font-family="sans-serif" dy="-2">$</text>
                    </svg>
                    <span style="margin-left: 10px;">
                        Customer Receive
                    </span>
                </a>
            </li>
        <?php } ?>

        <!-- Voucher part -->
        <?php if (
            $this->permission1->method('payment_receipt_type_form', 'create')->access() ||
            $this->permission1->method('payment_receipt_type_list', 'read')->access() ||
            $this->permission1->method('new_payment_voucher', 'create')->access() ||
            $this->permission1->method('manage_payment_voucher', 'read')->access() ||
            $this->permission1->method('new_receipt_voucher', 'create')->access() ||
            $this->permission1->method('manage_receipt_voucher', 'read')->access() ||
            $this->permission1->method('new_contra_voucher', 'create')->access() ||
            $this->permission1->method('manage_contra_voucher', 'read')->access()
        ) { ?>
            <li class="treeview <?php echo ((
                                    $this->uri->segment('1') == ("new_payment_voucher") || $this->uri->segment('1') == ("manage_payment_voucher") ||
                                    $this->uri->segment('1') == ("new_receipt_voucher") || $this->uri->segment('1') == ("manage_receipt_voucher") ||
                                    $this->uri->segment('1') == ("new_contra_voucher") || $this->uri->segment('1') == ("manage_contra_voucher") ||
                                    $this->uri->segment('1') == ("payment_receipt_type_form") || $this->uri->segment('1') == ("payment_receipt_type_list")) ? "active" : '') ?>">
                <a href="javascript:void(0)" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 24 24">
                        <image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAADv0lEQVR4AeyUXWwUVRTH/+fuVxPigyFBbP1ImvAiER9IJEIpWqyBiNWUCDRAEWI7RS0uEJh9KGYSN8qgki5V6vBQ0aKJrhSjFkuhUWh9sG8uJvK0SeNH0lRtjdjQ7s4c/1MSrUlXX8oDCZv57dyPc8//nHPvXIMb/Lsl8L8FNvqRRv44pC/9kFIvd0C9i0n1Pn1evfea1evYqV56m3r7N6u3q169jU+o9/h6vtept400P6reHnJwrXrOI+q5a9TLVKvnke5VesiBGoMreFAVdwWAiwJcboprAriRItyCwC2y7Uc4x3llGwaush/QtqhwxeB9Uaymj+7Ah+vTJkaY2j33rcQKgwAJNfj53sOSX9Yu+dVkQ6fkt3ZJfh+ZLMNY/CrGunok39Mr+V4SviWOX7AAY5l+ueQLtghwxI9jat+Q5HcRCH7yDRIMmFolHucZLYtfw8BkAv3bn9S9dRu0r2699m1ap3ujPs7FJvGV87CWvT4gOUTQEvORPVqjFbPd/adAmY9FNP5++nZUi2CJCs4xsn6WZEniV1SzXN/9Fp2xgUMRlrYlWkS286F/REoKdDZqRcTgDAXK4+M4q0AVFM3sN1GoamohzgpQbhQ9B2u1nOM4MCi5QNESjSJL25lMSgrEo1hKB2fSH0jtm1mppUCa0Xdwozu4OP1uv9S+TSj2CQIsDQVCkhTxFbvZfoowyfB/DpjuMJ2tamvQyy88recp1kbHrWLQSqG2HY/peatWL7NcK1m+4dkuWobkWwZ0PBy7noFySdibxc6TMpEIsJlD+VIZKJDnydniXpDfafevR4ThcMSQ0PtzP9r6QNieb2YEGMnHrOXx3H5dfkMEmMJowaCe72ODe+ZXZCaDMOrKV2RUCqhnJpnPd18XWTyNPyl6JzfZ4yY3saoNrG0Dx5q4yeHY4oUrcDVcX4q/BUKD+zMyOg1s5L2UOfmsLt+UFT9aQA2vEo+GKZ7xJG+vJE9Xygi8SUGN4wjNw9VzY2AwxaNWMZLSylxSKxOCBYwwdNDdvkOrEcOiWAQT/OjGE8B4gfgxjE8VMcGr4Y7kWq1MEYe8WqWVR0kXYbYV9HvN8CP5RoARA9h0ZjMcmwLbySCdbo0a2BGfc5znN2DT3hb2TQx2VGAHIZwLx02EtrTnftoqGLnyNYaNOBLc9rKk7z4s1rLXxFrTLlbdW2I1nhCr9R2x2k6J9caHYvE2tU5/JlbvF2Kd7hPrFDlxQaxj5MiAWM6XYtkXxXrxklgWaRyStAMJGPjctZuv0Ztf4C8AAAD//6oMy1EAAAAGSURBVAMAqlmcQFzxA7YAAAAASUVORK5CYII=" x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        Vouchers
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('payment_receipt_type_list', 'read')->access() || $this->permission1->method('payment_receipt_type_form', 'create')->access()) {
                        $nav = $this->permission1->method('payment_receipt_type_list', 'read')->access() ? 'payment_receipt_type_list' : 'payment_receipt_type_form'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('payment_receipt_type_list', 'payment_receipt_type_form')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Payment/Receipt Type</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_payment_voucher', 'read')->access() || $this->permission1->method('new_payment_voucher', 'create')->access()) {
                        $nav = $this->permission1->method('manage_payment_voucher', 'read')->access() ? 'manage_payment_voucher' : 'new_payment_voucher'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_payment_voucher', 'new_payment_voucher')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Payment Voucher</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_receipt_voucher', 'read')->access() || $this->permission1->method('new_receipt_voucher', 'create')->access()) {
                        $nav = $this->permission1->method('manage_receipt_voucher', 'read')->access() ? 'manage_receipt_voucher' : 'new_receipt_voucher'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_receipt_voucher', 'new_receipt_voucher')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Receipt Voucher</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_contra_voucher', 'read')->access() || $this->permission1->method('new_contra_voucher', 'create')->access()) {
                        $nav = $this->permission1->method('manage_contra_voucher', 'read')->access() ? 'manage_contra_voucher' : 'new_contra_voucher'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('manage_contra_voucher', 'new_contra_voucher')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Contra Voucher</a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <!-- voucher menu end -->










        <!-- human resource management menu start -->
        <?php if (
            $this->permission1->method('add_designation', 'create')->access() || $this->permission1->method('manage_designation', 'read')->access()
            || $this->permission1->method('add_employee', 'create')->access() || $this->permission1->method('manage_employee', 'read')->access()
        ) { ?>
            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("designation_form") || $this->uri->segment('1') == ("designation_list")
                                    || $this->uri->segment('1') == ("employee_form") || $this->uri->segment('1') == ("employee_list")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="javascript:void(0)" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAEl0lEQVR4AZRVS2xUZRg9/z8tTQk6C0VpZaGBRFwYoJSY4EITiAkSXUnHR1qBMYRHGxsxlEgYBpXGBVgaASlaEzAR+4ipCNUEWUhdlOiOYlFs3SjtIMWQVJjXnd9z7tx7WxcmOJkz97vnO9/5/te9YxF8HJzxNrktxAG3wT0c0Hd9cUm32Eu6g8Wke212UdQguxlPF2L4kNiRq8Th2aK7iXOWtRZvFC0+up10T4Y1UQMmXMEAPiwq8D8/BSCq9+xMfdRg3gJc4OhP5i2uEmkEn5ub3eM3t7r2qS2uh/gsQI845QIZ8hVIse6qPOY9hKGQ9xtMNrtHrmfQmgduFSxOcDSJia1u7eX1bg6n/ljWw8n7jpkE8XKARNbg01wFHpVGWtWoVh7ykqea2D9a3JqCw6/sfpCCFi7Ru7y2cskG75mPszXHTG/tcXNF4t+2uQXjr7sHFdceNaM1R03/vQ9gUFrVBLUtvhc9f9/uVtscsDRvYAhHYBY8Fi6XmTDe7HaXLK45DxNjzW6XOIGmy1jjEbNr5WXYaJnlEoDBAEdwmkAIcklCHj5otIE5E2CjT/JHGiJJPqplfJrcgLxtkSLPoEgDAbz68IAcY8e0/2XBNd77ORpM+CR/yHFSvracM9C1KE95Wwp+yhmUaKCZzCAGxZfo4X/zMbRR82eAmSWyGGFO2n+Bo/eovWzrDpmv2WBjoYTtJJ4tAYuEWAxf1cWxRu7DO9wKno6nuF+rBMXilFsex2ppVSNwwOvkRd2mFR3mG/+Yruowd57oxPWiQdPKDjMuLD1g/v7+DhYOvelGWfQjN/y9Ugx1gmJxykkjrWoELl+jvOSpAfgNFOxL+yeJg9BdGTkPa3k6lhAgbuWAuKCYELdEmrK6/Eu+JK/yHRA1SKdNiWvZf67NxcMkR1NFhKdjjE/pYt4vIsaIkK8K9arlKvTJK+SiBoM73ULu+hE+oX+daXNvSVDkRnMppliU4jXDParmIOYqDrgpaaQ9s8vtUS01R77c6WrFCVGD2zHUM1lDmFwMzyvJ6Tre54lp4hmu/XlBMSEuL420PDXPkTNErVeJleKEqEGpCsMc2TQBju68kpxRhjFYrLMd4+npEqiJiVOO8aS0NP6WsXTTWYdhcULUYD5wgw/XOo6wkf8HnUo2tKOPTV7km/Isl6aBaAnQ4HMWicR+9EtbqMAh1cqjZg6mxAm2K+3u797rToyXcCMbw3d8Jj7mOZ7sTrkfulN44ZX95sKrafNL09umbzbENb5jhj7Zg/XSqka18pCXPOVtYZDg1Jp4KuKEToZOjmFczxG939XlKnt7Hbdb45mBOOV4svQWrqdeNaqVR1yefM80WG5SNW9GuZ4/84oIFgOMqwsZ1GVGceqDfe7zw2nXKSgWp5yvKWuj2sDrCpvO1btIJ2WE0xvhRkXvE/6B9PDeNafMxZa9pqE5hZeKlWgXFJc5c1GaQBvVBl6X2NxZnoo8O2pa0QiYADdTHNPlZTHGuNbdJiMoLrPwNb7WMA5hAXmyOG+z1TjFjl9wOudIHg/BkY3xIdoWGv3XVRpfaxDVykue8v4HAAD//0EE8fkAAAAGSURBVAMAYIm0G2lsHjIAAAAASUVORK5CYII="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('hrm_management') ?>
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('manage_designation', 'read')->access() || $this->permission1->method('add_designation', 'create')->access()) {
                        $nav = $this->permission1->method('manage_designation', 'read')->access() ? 'designation_list' : 'designation_form'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('designation_list', 'designation_form')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Designation</a>
                        </li>
                    <?php } ?>
                    <?php if ($this->permission1->method('manage_employee', 'read')->access() || $this->permission1->method('add_employee', 'create')->access()) {
                        $nav = $this->permission1->method('manage_employee', 'read')->access() ? 'employee_list' : 'employee_form'; ?>
                        <li class="<?php echo (in_array($this->uri->segment(1), array('employee_list', 'employee_form')) ? 'active' : '') ?>">
                            <a href="<?php echo base_url($nav) ?>">Employee</a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <!-- Human resource management menu end -->
        <?php if ($this->permission1->method('data_uploader', 'create')->access()) { ?>
            <li class="treeview <?php echo (($this->uri->segment(1) == "data_uploader") ? "active" : '') ?>">
                <a href="<?php echo base_url('data_uploader') ?>" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAADVUlEQVR4AcSUW0hUURSG19ozXirsalb4ZFRUDyWRERRSQUQXiKAoDR8ircwuFnTzYTxdoIKiyIjCLCKK6qEeukAX7B4JlhkYQVk9JFoGpYkzZ87o6l+WedQjow/RwMesWetf6997nz3H0D/+/F8DyZIBsloWgg0gU3Ikta8b9tyBWOK3s6XQjqc629ANUAQu2EQVdo48D66V9N4adTOQNRLTWEvXQz6yMPS6QzTLiVAS4nHIrQkZSgwT3WtYJyt7Y9LN4JuhfWGmeVht9qBTnJlQzA8TznI94neDT3JxJJamwOAJKP6aK8u+5MokXVRPZp0MajfKcKx0MwxKkk5xiVfTsCJubPJRJnTN0F1xmCpr/PShJldWeek7GYRaaUHYUByO4piXuD2XcoLrnFiaGvZRumNoBXqqbR+dqc6TXe2a9u9OBrafUrEyZ3QiVbULevpOOcqfRhfx45TjfDklkeag7yIMd7/dJLPdPW0GZXkyrCJfboWY8iH8yBa3ukXRYtUHpa33By5Bqc56nStDtM9UWRLL8XQPW53t+GhrqIXStOBFOW6Y4lWbUsT1kTgaqzN0Vqgf3X+2RfqZ70203mZKhfPStMN8ZDoeoteAR/kyKjiQKhWNvTRTD3KDztBZOInJLViwwbGsxE14kH6Ib3o1aU4HOjFUilszQdFYc1rzQmdB9xBkGNyA8TB54SXU3M1tMvJnHJViRcng8x+SNac11XiBUymHdozBeQXDfhrkJRISdvx0G5pkMB+Lea9oDJK1phqvXjyLwdA2GLg8gNuSq5YkdRUyseB+Xwv6ae7i/fwUDaRorDmtqaZr38UCGYHnsATcN3YMHYDJwKYWuqSFruLl+9jK2MtlmkcDKRprTmsau9EZER9dwswE7HK/ybL4JR5GDv4kM5tj6E2xJadPW3LAReDEzt93Wlev6EDUh4IA6NAWSonOwKwZmLk6ay9Xtv3RsvfwObzcpsH1LgqLcGQ7XBS09KeJOhSNpGgcNDQBmgLQoTV41TDdaTaUhpnnVddmoEGexa/yCnkFGAnYRfzGAD9VDRZBisaagyYeuLWj1lucsSnAlapR/hroj2hghxElms5d75MBjme74h4QLe6TQWAXVyjRhrrrfTJwN/Y2/gUAAP//NE7SKAAAAAZJREFUAwBa3pNAuXUE2wAAAABJRU5ErkJggg=="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('datauploader') ?>
                    </span>
                </a>
            </li>
        <?php } ?>



        <!-- Report menu start -->


        <?php if (
            $this->permission1->method('closing_report', 'read')->access() || $this->permission1->method('todays_report', 'read')->access()
            || $this->permission1->method('income_statement_form', 'read')->access()
            || $this->permission1->method('expenditure_statement', 'read')->access()
            || $this->permission1->method('receipt_payment', 'read')->access()
            || $this->permission1->method('balance_sheet', 'read')->access() || $this->permission1->method('product_wise_sales_report', 'read')->access()
            || $this->permission1->method('fixedasset_schedule', 'read')->access()
            || $this->permission1->method('bank_reconciliation_report', 'read')->access()
            || $this->permission1->method('cheque_flow__report', 'create')->access() || $this->permission1->method('stock', 'read')->access() || $this->permission1->method('stock_report', 'read')->access()
            || $this->permission1->method('todays_customer_receipt', 'read')->access() || $this->permission1->method('todays_sales_report', 'read')->access() || $this->permission1->method('due_report', 'read')->access() || $this->permission1->method('todays_purchase_report', 'read')->access() || $this->permission1->method('purchase_report_category_wise', 'read')->access() || $this->permission1->method('product_sales_reports_date_wise', 'read')->access() || $this->permission1->method('sales_report_category_wise', 'read')->access()
            || $this->permission1->method('shipping_cost_report', 'read')->access()
            || $this->permission1->method('live_stock_report', 'read')->access()
            || $this->permission1->method('stock_audit_report', 'read')->access()
            || $this->permission1->method('sales_order_report', 'read')->access()
            || $this->permission1->method('sales_return_report', 'read')->access()   || $this->permission1->method('purchase_order_report', 'read')->access()
            || $this->permission1->method('purchase_return_report', 'read')->access()   || $this->permission1->method('grn_report', 'read')->access()
            || $this->permission1->method('gdn_report', 'read')->access()   || $this->permission1->method('gross_profit_report', 'read')->access()
            || $this->permission1->method('profit_report_invoicewise', 'read')->access()
            || $this->permission1->method('gross_profit_category_report', 'read')->access()
            || $this->permission1->method('service_report', 'read')->access()
            || $this->permission1->method('product_batch_summary_report', 'read')->access()
            || $this->permission1->method('service_order_report', 'read')->access()
            || $this->permission1->method('purchase_report_productwise', 'read')->access()

            || $this->permission1->method('payment_report', 'read')->access()
            || $this->permission1->method('receipt_report', 'read')->access()
            || $this->permission1->method('contra_voucher_report', 'read')->access()
            || $this->permission1->method('customer_receive_report', 'read')->access()
            || $this->permission1->method('customer_due_report', 'read')->access()
            || $this->permission1->method('supplier_payment_report', 'read')->access()
            || $this->permission1->method('supplier_due_report', 'read')->access()



        ) { ?>

            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("cash_book") || $this->uri->segment('1') == ("cash_book_report")
                                    || $this->uri->segment('1') == ("service_report")
                                    || $this->uri->segment('1') == ("day_book") || $this->uri->segment('1') == ("day_book_report")
                                    || $this->uri->segment('1') == ("bank_book") || $this->uri->segment('1') == ("bank_book_report") ||
                                    $this->uri->segment('1') == ("general_ledger") || $this->uri->segment('1') == ("sub_ledger") || $this->uri->segment('1') == ("sub_ledger_report") || $this->uri->segment('1') == ("trial_balance") || $this->uri->segment('1') == ("coa_print")
                                    || $this->uri->segment('1') == ("trial_balance_report") || $this->uri->segment('1') == ("accounts_report_search") ||
                                    $this->uri->segment('1') == ("income_statement_form") || $this->uri->segment('1') == ("income_statement")
                                    ||  $this->uri->segment('1') == ("expenditure_statement") ||  $this->uri->segment('1') == ("expenditure_statement_report")
                                    || $this->uri->segment('1') == ("receipt_payment") || $this->uri->segment('1') == ("receipt_payment_report")
                                    || $this->uri->segment('1') == ("profit_loss_report_search") || $this->uri->segment('1') == ("profit_loss_report")
                                    || $this->uri->segment('1') == ("balance_sheet") ||  $this->uri->segment('1') == ("fixedasset_schedule")
                                    || $this->uri->segment('1') == ("bank_reconciliation_report") ||
                                    $this->uri->segment('1') == ("chequeflowreport") || $this->uri->segment('1') == ("stock") ||
                                    $this->uri->segment('1') == ("closing_report") || $this->uri->segment('1') == ("closing_report_search")  || $this->uri->segment('1') == ("live_stock_report") || $this->uri->segment('1') == ("todays_report") || $this->uri->segment('1') == ("todays_customer_received") || $this->uri->segment('1') == ("todays_customerwise_received") || $this->uri->segment('1') == ("sales_report") || $this->uri->segment('1') == ("datewise_sales_report") || $this->uri->segment('1') == ("userwise_sales_report") || $this->uri->segment('1') == ("invoice_wise_due_report") || $this->uri->segment('1') == ("shipping_cost_report") || $this->uri->segment('1') == ("purchase_report") || $this->uri->segment('1') == ("purchase_report_categorywise") || $this->uri->segment('1') == ("product_wise_sales_report") || $this->uri->segment('1') == ("category_sales_report") || $this->uri->segment('1') == ("sales_return") || $this->uri->segment('1') == ("supplier_returns") || $this->uri->segment('1') == ("tax_report") || $this->uri->segment('1') == ("profit_report")
                                    || $this->uri->segment('1') == ("stock_audit_report")
                                    || $this->uri->segment('1') == ("sales_order_report")  || $this->uri->segment('1') == ("sales_return_report") || $this->uri->segment('1') == ("purchase_order_report") || $this->uri->segment('1') == ("purchase_return_report") || $this->uri->segment('1') == ("grn_report") || $this->uri->segment('1') == ("gdn_report")
                                    || $this->uri->segment('1') == ("gross_profit_report") || $this->uri->segment('1') == ("gross_profit_category_report")
                                    || $this->uri->segment('1') == ("product_batch_summary_report") || $this->uri->segment('1') == ("service_order_report")
                                    || $this->uri->segment('1') == ("profit_report_invoicewise")
                                    || $this->uri->segment('1') == ("purchase_report_productwise")
                                    || $this->uri->segment('1') == ("payment_report") || $this->uri->segment('1') == ("receipt_report")
                                    || $this->uri->segment('1') == ("contra_voucher_report")
                                    || $this->uri->segment('1') == ("customer_receive_report") || $this->uri->segment('1') == ("customer_due_report")
                                    || $this->uri->segment('1') == ("supplier_payment_report") || $this->uri->segment('1') == ("supplier_due_report")

                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="javascript:void(0)" style="display:flex; align-items:center;">

                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAED0lEQVR4AbSUb2hVdRjHv79zttrKJg5iRGEUOfunviip9E2U9UKYFJFSRo6Vu8vQcIS5Xt1CZy/CIUUxJSaxUQtd9UJfVK5JCkLQm4KMiKwIRJsr2d313nPv/fV5jveczqJBCl7O9zzP7/t8f8/z/J5zzg2U+fn1vsV3+bX+eX8Htsd3+kWZ8GW5aYHyRt9Zbtav5UCjZadl2HfLDfqhtNG/57f65svKzqa4wHTOP1tyGiLpfKAygdgGchTrKhQ06uUd9CVfwZlNfh5JBqJAShFmfHiKdfyZ05pLzs6GwNf0CAVagVIQSH36Np8TroOe8zrX7eeffsF3J5jc7FtMHDDnhWnndGs+HTdVnDbg5xKUQ43ahrkw3aB2tIMJCl7tpg0ir3MRXWZRCfQGOBtJffCDBtZP24a5gFbo/kFdGERXaZyOK0AZtJUCDbFhE9wpIEZ0pL7nP02lUdn9soImDBYPuN+Z8VskUxZwbYxliDfqRfhDtVbtV/337Uv+APjJ8N0Wv81omvDojoGzQBEFjWfqVC7qFZKNIJrVBes2Ztp39x513JV31LIt6AO1E7vVUGrQQmNroSbJMYz+fXilJ7DgvXtddM9u90wl1MME9tL9EewYD7qH0Tzq5LzpEsQdOgo5vY3uyxO9/uNiTT/ibyF5IY7XxfEJ6r7ue9ON3z/gcg/sdquwT/iqPqmGWvnVy37V0V6/ItHRiOhUJLqers1/jHUImmnqAlazRpRs/LctNOopTvO5gaRfJPESDpzibr2Wx77TH9hGYkWsOA0qadYJYiZzqzTIxd2g4jVNI+bDf4o9EYXahj/FKDtI3IlfAfpfBehGjCEGm9MCcCMkGMO+A4qc5HGKVcEYBV+DE/FYT2/S4c3+6oN9/kPwUYztfrlF2WBjSGFcPu8Dug2Z/X4SnyfZ6jW73FFLyLqFRlqxKmZf0+lr1MSx1oEnDcz7NkvGxyYSJDg+nPc33R7pMzQ7SfQ1dgOFlprWHmpGKytofHwCc6xqCv5NjWNDFe4QSdaWQh2vVXWS9RKS95J8BPsB68WmrXDDT087q8AUQZIJROAkX9Rp+/8nyTeM6WfE+0jwILGtxVC3YB8Ce8A8YuyWzLJWiuyItABBoMMkW/pboCW1Rp0azGs9BRYxpglsF7Fjz73u9uXyboYHeTOcvf8xrEI8IuaR8kYCKO5cHHc1D+/7VqeZC1Ut68m7Yd4i6/AAsYPEepHFF2tlYeQMtyxnJ4K6+B2cD2X/ppNUnwLTUYOqFqwGmmFtnOGMcQZOk2in+HL/Mo5TFTNa0xeND3bs8DdWyvE4XuUhbTfQ7Q27dvpuxhPauo5+4wzw9nAvakP9YhzPaEVdl/Ar+/v9nUHLuJqum9CCK4FrJ9T8NwAAAP//IlvYVgAAAAZJREFUAwDMuTwYIOke5gAAAABJRU5ErkJggg=="
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('report') ?>
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($this->permission1->method('cash_book', 'read')->access()) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("cash_book")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <span>Books</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('cash_book', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("cash_book")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('cash_book') ?>"><?php echo display('cash_book'); ?></a></li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>
                    <?php if (
                        $this->permission1->method('todays_purchase_report', 'read')->access()
                        || $this->permission1->method('purchase_order_report', 'read')->access() ||
                        $this->permission1->method('purchase_report_productwise', 'read')->access() ||
                        $this->permission1->method('purchase_report_category_wise', 'read')->access() ||
                        $this->permission1->method('purchase_return_report', 'read')->access()
                    ) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("purchase_report") ||
                                                $this->uri->segment('1') == ("purchase_order_report") ||
                                                $this->uri->segment('1') == ("purchase_report_productwise") ||
                                                $this->uri->segment('1') == ("purchase_report_categorywise") ||
                                                $this->uri->segment('1') == ("purchase_return_report")
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">

                                <span>
                                    Purchase Reports
                                </span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('purchase_order_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("purchase_order_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('purchase_order_report') ?>"><?php echo display('purchase_order_report') ?></a>
                                    </li>
                                <?php } ?>



                                <?php if ($this->permission1->method('todays_purchase_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("purchase_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('purchase_report') ?>"><?php echo display('purchase_report') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('purchase_report_productwise', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("purchase_report_productwise")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('purchase_report_productwise') ?>"><?php echo display('purchase_report_productwise') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('purchase_report_category_wise', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("purchase_report_categorywise")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('purchase_report_categorywise') ?>"><?php echo display('purchase_report_category_wise') ?></a>
                                    </li>
                                <?php } ?>


                                <?php if ($this->permission1->method('purchase_return_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("purchase_return_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('purchase_return_report') ?>"><?php echo display('purchase_return_report') ?></a>
                                    </li>
                                <?php } ?>


                            </ul>
                        </li>
                    <?php } ?>

                  
                    <?php if (
                        $this->permission1->method('sales_order_report', 'read')->access() ||
                        $this->permission1->method('todays_sales_report', 'read')->access()
                        || $this->permission1->method('user_wise_sales_report', 'read')->access() ||
                        $this->permission1->method('product_wise_sales_report', 'read')->access() ||
                        $this->permission1->method('sales_report_category_wise', 'read')->access() ||
                        $this->permission1->method('sales_return_report', 'read')->access()
                    ) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("sales_order_report") ||
                                                $this->uri->segment('1') == ("sales_report") ||
                                                $this->uri->segment('1') == ("userwise_sales_report") ||
                                                $this->uri->segment('1') == ("product_wise_sales_report") ||
                                                $this->uri->segment('1') == ("category_sales_report") ||
                                                $this->uri->segment('1') == ("sales_return_report")
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">


                                <span>
                                    Sales Report
                                </span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('sales_order_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("sales_order_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('sales_order_report') ?>"><?php echo display('sales_order_report') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('todays_sales_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("sales_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('sales_report') ?>"><?php echo display('sales_report') ?></a>
                                    </li>


                                <?php } ?>
                                <?php if ($this->permission1->method('user_wise_sales_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("userwise_sales_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('userwise_sales_report') ?>"><?php echo display('user_wise_sales_report') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('product_wise_sales_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("product_wise_sales_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('product_wise_sales_report') ?>"><?php echo display('product_wise_sales_report') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('sales_report_category_wise', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("category_sales_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('category_sales_report') ?>"><?php echo display('sales_report_category_wise') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('sales_return_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("sales_return_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('sales_return_report') ?>"><?php echo display('sales_return_report') ?></a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>

                     <?php if (
                        $this->permission1->method('service_order_report', 'read')->access() ||
                        $this->permission1->method('service_report', 'read')->access()
                    ) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("service_order_report") ||
                                                $this->uri->segment('1') == ("service_report")
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <span>Service Report</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('service_order_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("service_order_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('service_order_report') ?>"><?php echo display('service_order_report') ?></a>
                                    </li>
                                <?php } ?>


                                <?php if ($this->permission1->method('service_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("service_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('service_report') ?>"><?php echo display('service_report') ?></a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>

                      <?php if (
                        $this->permission1->method('supplier_payment_report', 'read')->access() ||
                        $this->permission1->method('supplier_due_report', 'read')->access()
                    ) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("supplier_payment_report") ||
                                                $this->uri->segment('1') == ("supplier_due_report")
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <span>Supplier Payment Reports</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('supplier_payment_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("supplier_payment_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('supplier_payment_report') ?>"><?php echo display('supplier_payment_report') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('supplier_due_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("supplier_due_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('supplier_due_report') ?>"><?php echo display('supplier_due_report') ?></a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>


                    <?php if (
                        $this->permission1->method('customer_receive_report', 'read')->access() ||
                        $this->permission1->method('customer_due_report', 'read')->access()
                    ) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("customer_receive_report") ||
                                                $this->uri->segment('1') == ("customer_due_report")
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <span>Customer Receive Reports</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('customer_receive_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("customer_receive_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('customer_receive_report') ?>"><?php echo display('customer_receive_report') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('customer_due_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("customer_due_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('customer_due_report') ?>"><?php echo display('customer_due_report') ?></a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>

                   

                    <?php if (
                           $this->permission1->method('payment_report', 'read')->access() ||
                           $this->permission1->method('receipt_report', 'read')->access()||
                           $this->permission1->method('contra_voucher_report', 'read')->access()
                    ) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("payment_report") ||
                                                $this->uri->segment('1') == ("receipt_report") ||
                                                $this->uri->segment('1') == ("contra_voucher_report") 
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <span>Voucher Report</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('payment_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("payment_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('payment_report') ?>"><?php echo display('payment_report') ?></a>
                                    </li>
                                <?php } ?>


                                <?php if ($this->permission1->method('receipt_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("receipt_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('receipt_report') ?>"><?php echo display('receipt_report') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('contra_voucher_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("contra_voucher_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('contra_voucher_report') ?>"><?php echo display('contra_voucher_report') ?></a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>


                    <?php if (
                        $this->permission1->method('grn_report', 'read')->access() ||
                        $this->permission1->method('gdn_report', 'read')->access()
                    ) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("grn_report") ||
                                                $this->uri->segment('1') == ("gdn_report")
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <span> Warehouse Reports</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('grn_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("grn_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('grn_report') ?>"><?php echo display('grn_report') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('gdn_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("gdn_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('gdn_report') ?>"><?php echo display('gdn_report') ?></a>
                                    </li>
                                <?php } ?>


                            </ul>
                        </li>
                    <?php } ?>


                    <?php if (
                        $this->permission1->method('stock_report', 'read')->access() ||
                        $this->permission1->method('live_stock_report', 'read')->access() ||
                        $this->permission1->method('stock_audit_report', 'read')->access() ||
                        $this->permission1->method('product_batch_summary_report', 'read')->access()


                    ) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("stock") ||
                                                $this->uri->segment('1') == ("live_stock_report") ||
                                                $this->uri->segment('1') == ("stock_audit_report") ||
                                                $this->uri->segment('1') == ("product_batch_summary_report")

                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <span> Inventory Reports</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('stock_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("stock")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('stock') ?>"><?php echo display('stock_report') ?></a></li>
                                <?php } ?>

                                <?php if ($this->permission1->method('live_stock_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("live_stock_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('live_stock_report') ?>"><?php echo display('live_stock_report') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('stock_audit_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("stock_audit_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('stock_audit_report') ?>"><?php echo display('stock_audit_report') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('product_batch_summary_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("product_batch_summary_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('product_batch_summary_report') ?>"><?php echo display('product_batch_summary_report') ?></a>
                                    </li>
                                <?php } ?>


                            </ul>
                        </li>
                    <?php } ?>

                    <?php if (
                        $this->permission1->method('gross_profit_report', 'read')->access() ||
                        $this->permission1->method('gross_profit_category_report', 'read')->access() ||
                        $this->permission1->method('profit_report_invoicewise', 'read')->access()
                    ) { ?>
                        <!-- Supplier menu start -->
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("gross_profit_report") ||
                                                $this->uri->segment('1') == ("gross_profit_category_report") ||
                                                $this->uri->segment('1') == ("profit_report_invoicewise")
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <span> Gross Profit Reports</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('gross_profit_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("gross_profit_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('gross_profit_report') ?>"><?php echo display('gross_profit_report') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('gross_profit_category_report', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("gross_profit_category_report")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('gross_profit_category_report') ?>"><?php echo display('gross_profit_category_report') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('profit_report_invoicewise', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("profit_report_invoicewise")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a href="<?php echo base_url('profit_report_invoicewise') ?>">Profit Report (Invoice Wise)</a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </li>
                    <?php } ?>










                </ul>
            </li>
        <?php } ?>



        <!-- Report menu end -->








        <!-- Comission end -->





        <!-- Software Settings menu start -->
        <?php if (
            $this->permission1->method('manage_company', 'read')->access() || $this->permission1->method('manage_company', 'create')->access()
            || $this->permission1->method('add_company', 'create')->access()
            || $this->permission1->method('add_user', 'create')->access() || $this->permission1->method('add_user', 'read')->access() || $this->permission1->method('add_language', 'create')->access() || $this->permission1->method('add_currency', 'create')->access() || $this->permission1->method('soft_setting', 'create')->access() || $this->permission1->method('add_role', 'create')->access() || $this->permission1->method('role_list', 'read')->access() || $this->permission1->method('user_assign', 'create')->access()
            || $this->permission1->method('sms_configure', 'create')->access() || $this->permission1->method('add_incometax', 'create')->access() || $this->permission1->method('manage_income_tax', 'read')->access() || $this->permission1->method('tax_settings', 'create')->access()
            || $this->permission1->method('tax_report', 'read')->access() || $this->permission1->method('invoice_wise_tax_report', 'read')->access() || $this->permission1->method('tax_settings', 'read')->access()
            || $this->permission1->method('vat_tax_setting', 'read')->access() || $this->permission1->method('add_incometax', 'create')->access() || $this->permission1->method('manage_income_tax', 'read')->access() || $this->permission1->method('tax_settings', 'create')->access() || $this->permission1->method('tax_report', 'read')->access() || $this->permission1->method('invoice_wise_tax_report', 'read')->access() || $this->permission1->method('tax_settings', 'read')->access() || $this->permission1->method('vat_tax_setting', 'read')->access()
            || $this->permission1->method('user_assign_branch', 'read')->access()   || $this->permission1->method('user_assign_store', 'read')->access()
        ) { ?>
            <li class="treeview <?php
                                if (
                                    $this->uri->segment('1') == ("company_list") || $this->uri->segment('1') == ("edit_company")
                                    || $this->uri->segment('1') == ("add_company")
                                    || $this->uri->segment('1') == ("add_user") || $this->uri->segment('1') == ("user_list") || $this->uri->segment('1') == ("language")
                                    || $this->uri->segment('1') == ("currency_form") || $this->uri->segment('1') == ("currency_list") || $this->uri->segment('1') == ("settings")
                                    || $this->uri->segment('1') == ("mail_setting") || $this->uri->segment('1') == ("app_setting") || $this->uri->segment('1') == ("add_role") || $this->uri->segment('1') == ("role_list")
                                    || $this->uri->segment('1') == ("edit_role") || $this->uri->segment('1') == ("assign_role") || $this->uri->segment('1') == ("sms_setting") || $this->uri->segment('1') == ("restore")
                                    || $this->uri->segment('1') == ("db_import") || $this->uri->segment('1') == ("editPhrase") || $this->uri->segment('1') == ("phrases") || $this->uri->segment('1') == ("invoice_wise_tax_report") || $this->uri->segment('1') == ("tax_setting")
                                    || $this->uri->segment('1') == ("income_tax") || $this->uri->segment('1') == ("manage_income_tax")
                                    || $this->uri->segment('1') == ("tax_reports") || $this->uri->segment('1') == ("update_tax_setting")
                                    || $this->uri->segment('1') == ("vat_tax_setting") || $this->uri->segment('1') == ("user_assign_branch")
                                    || $this->uri->segment('1') == ("user_assign_store")
                                ) {
                                    echo "active";
                                } else {
                                    echo " ";
                                }
                                ?>">
                <a href="javascript:void(0)" style="display:flex; align-items:center;">

                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24"
                        height="24" viewBox="0 0 24 24">
                        <image
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAE3UlEQVR4AaRVa2xURRT+zt3tbkttEYsRNVQJlKAomgAJQqMENwrRkFAEwiMiCSAPgS2hhdKW3rJtoRRawFJEUMEYSwM+CUoFUmnRBEManyiKEJWngKaFlm539x6/u7CrhbX9wWbmnpnvnPm+mTMzOwa6+O3O0QEfLlPPPta6bB3WRfgt7i4FRLCFQR5Y8FiK7bVLNPEWlk4Aju3o3VSoi7aaOtJGq3O1N61j7GpZNnqNLGO7LlHwFC2OLtTU771aeuoljbf7/1c7CKxfqYWi6MuauSNf96tgDdu5/xm8lisZfSRTDxoGdhK/0J6E3Z2JRAXKitRLsh7zTFl4WjAuLoRpU4pkclBxZs9SnVKbrdPp7zkiCV6HCxlWKzyPrJdyYm/hDrxLsZglKgDF18z3A6aphmmKFYiDuzpP9zgMFKqQAgDt5MNXUR9qx6Ahr0srIViCfsQb7XasGhXIypfPFeiWAvTaUqCpUNSEBMvHl8hUcWCv5cDP6oTPEcJYEG/06hibUIAJcgVr7XasGhVYVxTe0EsLTDnrBDbxxMye5pPvFJxfCPViIV+C+CDowBINYBIUK37K1iSSbkcSxtHGLIZZov2Li/UQCau53C2bTO3HlTTPuEEuEOXMPcTu9rfheQH8cGIW+9sDfkzkDGso9vKZeXroz7k64WYVwyG4j2AdU5SeyTRBMJQD6nfk6v01uTjxcZ72aTuO30kUvHoeLed+QxH3KoOihzhuaNqrcrFPpYy0DBSoYCCxDoXjOvTJD5cY8DuA3nQeD1qoSeiLw5zx5om7JGRXjggkJOEk7XusnRZDQrBPw8Qyn1Zt9Kl9B34k2eOuOG4qkBjvgufar0gfXSpvR5g404a2Zix/dIPst+/AqVe0lHtkcuUtkZiINfLy5KtAAENgYC8DvBeAo3QOuebnYgRf+NuReWwgPQQjZdg6ySeS/I1XTX939Oe+NKsfGfdslrJITMQadsM0pTUrV/ZawMPdgWQNIUeceCe+HatU0TK4DQ37lmrDwWwtsuPtOrhCFvOSJdOf8WClFPd+Q/6y8ZtrWMAGVxdoKjsJwUQEZvmkgWnY2OrCZwpcEQsze/6NUYy7Ur9EKxT0sjNogyymcf+yQFfTxizkvI47HZhPst1ZWRLO4/SV8mm3djxDb5CXbNHFHhj19BopZTrOH1mM8ojIQxslB+z8MT+2SFTgqoUCko3aXKjjt63Qpfyza7zmxnSXGx+NXSVzxpRKbZ2p8bCwjxNpavSinPHhklYpOUxV8Nw8rb80R79snq1ZamqYO/yxo0xT2pItvMALNxUG4jjr4ZaFy7xMu3Djpy0oZHKee6JcTMY0fbtIK264kFolefdWyZMpvZDOCXRrPQuf7YsK2J0ZFJlvSsZMU4rs9pRiqeZZckReNJIPc1p4zY4dTBH2m44t/HfjbRw/QIJuVHDf0nWu9uggEA649VPFoPCLpora4RUSPS2PrReTJ2lEZAhT9OLlFBx2BnCAt70l0I40jo24Y9uMEtkZedE8ZVISI8qtpho6QXn5MTflMlMUxCSmqX+c4nSXAjEIO0KC909eQv6JXrDfamdTd6TeuU1OJWxFmrwpZ29bYMBd9pGF5bbwCYCLIScOKJQZYvII3LYAd8HqVyk+VwKe5cFcwZOXLrhOTn78AwAA//8Uo7Q4AAAABklEQVQDAOrd/5Tut8Z+AAAAAElFTkSuQmCC"
                            x="0" y="0" width="24" height="24" />
                    </svg>
                    <span style="margin-left: 10px;">
                        <?php echo display('settings_preferences') ?>
                    </span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <!-- Software Settings menu start -->
                    <?php if (
                        $this->permission1->method('manage_company', 'read')->access() || $this->permission1->method('manage_company', 'create')->access()
                        || $this->permission1->method('add_company', 'create')->access()
                        || $this->permission1->method('add_language', 'create')->access() || $this->permission1->method('add_currency', 'create')->access() || $this->permission1->method('soft_setting', 'create')->access() || $this->permission1->method('back_up', 'create')->access() || $this->permission1->method('back_up', 'read')->access() || $this->permission1->method('restore', 'create')->access() || $this->permission1->method('sql_import', 'create')->access()
                    ) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("company_list") || $this->uri->segment('1') == ("add_company")  || $this->uri->segment('1') == ("edit_company")  || $this->uri->segment('1') == ("language") || $this->uri->segment('1') == ("currency_form") || $this->uri->segment('1') == ("currency_list") || $this->uri->segment('1') == ("settings") || $this->uri->segment('1') == ("mail_setting") || $this->uri->segment('1') == ("app_setting") || $this->uri->segment('1') == ("editPhrase") || $this->uri->segment('1') == ("phrases")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <i class="ti-settings"></i> <span><?php echo display('web_settings') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('add_company', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("add_company")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('add_company') ?>"><?php echo display('add_company') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('manage_company', 'read')->access() || $this->permission1->method('manage_company', 'update')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("company_list")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('company_list') ?>"><?php echo display('company_list') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('add_language', 'create')->access() || $this->permission1->method('add_language', 'update')->access()) { ?>
                                    <li
                                        class="<?php echo (($this->uri->segment(1) == "language" || $this->uri->segment('1') == ("editPhrase") || $this->uri->segment('1') == ("phrases")) ? "active" : '') ?>">
                                        <a href="<?php echo base_url('language') ?>">

                                            <?php echo display('language') ?>

                                        </a>

                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('soft_setting', 'create')->access() || $this->permission1->method('soft_setting', 'update')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("settings")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>">
                                        <a href="<?php echo base_url('settings') ?>"
                                            class="<?php echo (($this->uri->segment(1) == "settings") ? "active" : null) ?>">

                                            <?php echo display('settings') ?>

                                        </a>

                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('mail_setting', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("mail_setting")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('mail_setting') ?>"><?php echo display('mail_setting') ?> </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>


                    <!-- Tax permission start -->
                    <?php if ($this->permission1->method('add_incometax', 'create')->access() || $this->permission1->method('manage_income_tax', 'read')->access() || $this->permission1->method('tax_settings', 'create')->access() || $this->permission1->method('tax_report', 'read')->access() || $this->permission1->method('invoice_wise_tax_report', 'read')->access() || $this->permission1->method('tax_settings', 'read')->access() || $this->permission1->method('vat_tax_setting', 'read')->access()) { ?>
                        <li class="treeview <?php
                                            if (($this->uri->segment('1') == ("invoice_wise_tax_report") || $this->uri->segment('1') == ("tax_setting")
                                                || $this->uri->segment('1') == ("income_tax") || $this->uri->segment('1') == ("manage_income_tax")
                                                || $this->uri->segment('1') == ("tax_reports") || $this->uri->segment('1') == ("update_tax_setting")
                                                || $this->uri->segment('1') == ("vat_tax_setting"))) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <i class="fa fa-money"></i> <span><?php echo display('tax') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('tax_settings', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("vat_tax_setting")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('vat_tax_setting') ?>"><?php echo display('vat_tax_setting') ?></a>
                                    </li>
                                <?php } ?>


                            </ul>
                        </li>
                    <?php } ?>
                    <!-- Tax permission End -->


                    <!-- Role permission start -->
                    <?php if (
                        $this->permission1->method('add_role', 'create')->access()
                        || $this->permission1->method('role_list', 'read')->access() || $this->permission1->method('add_user', 'create')->access() || $this->permission1->method('manage_user', 'read')->access()
                        || $this->permission1->method('edit_role', 'create')->access() || $this->permission1->method('assign_role', 'create')->access()
                        || $this->permission1->method('user_assign_branch', 'read')->access() || $this->permission1->method('user_assign_store', 'read')->access()
                    ) { ?>
                        <li class="treeview <?php
                                            if (
                                                $this->uri->segment('1') == ("add_role") || $this->uri->segment('1') == ("role_list") || $this->uri->segment('1') == ("edit_role") || $this->uri->segment('1') == ("assign_role")
                                                || $this->uri->segment('1') == ("add_user") || $this->uri->segment('1') == ("user_list") || $this->uri->segment('1') == ("user_assign_branch") || $this->uri->segment('1') == ("user_assign_store")
                                            ) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <i class="ti-key"></i> <span><?php echo display('area_responsibility') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if ($this->permission1->method('add_user', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("add_user")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('add_user') ?>"><?php echo display('add_user') ?></a></li>
                                <?php } ?>
                                <?php if ($this->permission1->method('manage_user', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("user_list")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('user_list') ?>"><?php echo display('manage_users') ?> </a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('add_role', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("add_role")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('add_role') ?>"><?php echo display('add_role') ?></a></li>
                                <?php } ?>
                                <?php if ($this->permission1->method('role_list', 'read')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("role_list")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('role_list') ?>"><?php echo display('role_list') ?></a></li>
                                <?php } ?>
                                <?php if ($this->permission1->method('user_assign', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("assign_role")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('assign_role') ?>"><?php echo display('user_assign_role') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->permission1->method('user_assign_store', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("user_assign_store")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('user_assign_store') ?>"><?php echo display('user_assign_store') ?></a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission1->method('user_assign_branch', 'create')->access()) { ?>
                                    <li class="treeview <?php if ($this->uri->segment('1') == ("user_assign_branch")) {
                                                            echo "active";
                                                        } else {
                                                            echo " ";
                                                        } ?>"><a
                                            href="<?php echo base_url('user_assign_branch') ?>"><?php echo display('user_assign_branch') ?></a>
                                    </li>
                                <?php } ?>


                            </ul>
                        </li>
                    <?php } ?>
                    <!-- Role permission End -->

                    <!-- Synchronizer setting start -->
                    <?php if ($this->permission1->method('restore', 'create')->access() || $this->permission1->method('sql_import', 'create')->access() || $this->permission1->method('sql_import', 'create')->access()) { ?>
                        <li class="treeview <?php
                                            if ($this->uri->segment('1') == ("restore") || $this->uri->segment('1') == ("db_import")) {
                                                echo "active";
                                            } else {
                                                echo " ";
                                            }
                                            ?>">
                            <a href="#">
                                <i class="ti-reload"></i> <span><?php echo display('data_synchronizer') ?></span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">



                                <li class="treeview <?php if ($this->uri->segment('2') == ("backup_create")) {
                                                        echo "active";
                                                    } else {
                                                        echo " ";
                                                    } ?>"><a
                                        href="<?php echo base_url('dashboard/backup_restore/download_backup') ?>"><?php echo display('backup') ?></a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                    <!-- Synchronizer setting end -->

                </ul>
            </li>
        <?php } ?>
        <!-- Software Settings menu end -->





    </ul>
</div> <!-- /.sidebar -->
<script>
/* Collapsed (icons-only) sidebar: hover or click an icon to see its name / its group's sub-items.
   The panel lives on <body> (position: fixed), so the sidebar's scroll area cannot clip it. */
(function () {
    var fly, tip, current = null, pinned = false, hideTimer = null, showTimer = null;

    function collapsed() { return document.body.classList.contains('sidebar-collapse') && window.innerWidth > 767; }
    function sidebar()   { return document.querySelector('.main-sidebar'); }
    function ensure() {
        if (fly) return;
        fly = document.createElement('div'); fly.id = 'sbFly';
        tip = document.createElement('div'); tip.id = 'sbTip';
        document.body.appendChild(fly); document.body.appendChild(tip);
        fly.addEventListener('mouseenter', function () { clearTimeout(hideTimer); });
        fly.addEventListener('mouseleave', function () { if (!pinned) scheduleHide(); });
    }
    function labelOf(li) {
        var s = li.querySelector(':scope > a > span:not(.pull-right-container)');
        return s ? s.textContent.replace(/\s+/g, ' ').trim() : '';
    }
    function subOf(li) { return li.querySelector(':scope > .treeview-menu'); }

    function place(el, li) {
        var r = li.getBoundingClientRect(), sb = sidebar().getBoundingClientRect();
        el.style.left = Math.round(sb.right + 8) + 'px';
        el.style.top = Math.round(r.top) + 'px';
        var h = el.offsetHeight, max = window.innerHeight - 12;
        var top = r.top;
        if (top + h > max) top = Math.max(12, max - h);       // keep it on screen
        el.style.top = Math.round(top) + 'px';
    }
    function clearMark() { if (current) current.classList.remove('fly-hover'); }

    function open(li, pin) {
        ensure(); clearTimeout(hideTimer);
        if (current && current !== li) clearMark();
        current = li; pinned = !!pin; li.classList.add('fly-hover');
        var sub = subOf(li), title = labelOf(li);
        if (sub) {                                              // a group: title + its sub-items
            tip.classList.remove('show');
            var head = document.createElement('div'); head.className = 'fly-title'; head.textContent = title;
            var clone = sub.cloneNode(true);
            clone.removeAttribute('style');
            clone.querySelectorAll('[style]').forEach(function (n) { n.removeAttribute('style'); });
            clone.querySelectorAll('.pull-right-container').forEach(function (n) { n.remove(); });
            clone.querySelectorAll('a[href="#"], a[href^="javascript"]').forEach(function (a) { a.removeAttribute('href'); a.classList.add('fly-head'); });
            fly.textContent = ''; fly.appendChild(head); fly.appendChild(clone);
            fly.classList.add('show'); place(fly, li);
        } else {                                                // a plain link: just its name
            fly.classList.remove('show');
            tip.textContent = title; tip.classList.add('show'); place(tip, li);
        }
    }
    function hide() {
        clearTimeout(hideTimer); clearTimeout(showTimer); pinned = false;
        if (fly) fly.classList.remove('show');
        if (tip) tip.classList.remove('show');
        clearMark(); current = null;
    }
    function scheduleHide() { clearTimeout(hideTimer); hideTimer = setTimeout(hide, 180); }

    // hover: show for the icon under the pointer, hide shortly after leaving both the icon and the panel
    document.addEventListener('mouseover', function (e) {
        if (!collapsed() || !e.target.closest) return;
        var li = e.target.closest('.main-sidebar .sidebar-menu > li');
        if (li) {
            clearTimeout(hideTimer);
            // hovering the icon that is already open (even one opened by a click) changes nothing - in particular it must not un-pin it
            if (li !== current) { clearTimeout(showTimer); showTimer = setTimeout(function () { if (li !== current) open(li, false); }, 60); }
        }
        else if (!e.target.closest('#sbFly') && !pinned) scheduleHide();
    });
    // click: a group icon pins its panel open (clicking again closes it); a plain link navigates as usual
    document.addEventListener('click', function (e) {
        if (!collapsed() || !e.target.closest) return;
        if (e.target.closest('#sbFly')) { hide(); return; }                       // a sub-item was chosen: it navigates
        var a = e.target.closest('.main-sidebar .sidebar-menu > li > a');
        if (a) {
            var li = a.parentNode;
            if (subOf(li)) {
                e.preventDefault(); e.stopImmediatePropagation();                  // do not let the old tree script try to expand it
                if (pinned && current === li) hide(); else open(li, true);
            } else { hide(); }
            return;
        }
        hide();
    }, true);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') hide(); });
    window.addEventListener('resize', hide);
    var sb = sidebar(); if (sb) sb.addEventListener('scroll', hide, { passive: true });
    new MutationObserver(function () { if (!collapsed()) hide(); }).observe(document.body, { attributes: true, attributeFilter: ['class'] });
})();
</script>