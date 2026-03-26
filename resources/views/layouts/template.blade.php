{{-- ======================================================
     LAYOUT PRINCIPAL — Clean Sage
     Gestschool · Lycée Technique de Bohicon
     Usage : @extends('layouts.template')

     ARCHITECTURE :
     Tout le CSS du layout (sidebar, header, main) est ici dans
     le <head> pour éviter le bug d'ordre Blade où les @push
     des @include arrivent après le @stack('styles').
     Les partials sidebar.blade.php et header.blade.php
     ne contiennent que du HTML pur.
     ====================================================== --}}

<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School Manager')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logoicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fonts : Lora (titres serif) + DM Sans (UI) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

    {{-- CSS legacy (Bootstrap, FontAwesome, DataTables…) --}}
    @include("layouts.css")

    {{-- Hooks pages enfants --}}
    @yield("another_CSS")
    @stack('styles')

    <style>
        /* ==============================================================
           0. RESET CONFLITS LEGACY (style.css)
           Neutralise les anciens sélecteurs qui cassent le layout.
           ============================================================== */
        .sidebar,
        .toggle-sidebar .sidebar                { all: unset !important; }
        #main, #footer,
        .toggle-sidebar #main,
        .toggle-sidebar #footer                 { margin-left: 0 !important; }
        @media (min-width: 1200px) {
            #main, #footer                      { margin-left: 0 !important; }
        }

        /* ==============================================================
           1. DESIGN TOKENS — Clean Sage
           ============================================================== */
        :root {
            /* Couleurs */
            --c-bg:           #f6f7f3;
            --c-bg-2:         #eef0ea;
            --c-surface:      #ffffff;
            --c-surface-2:    #f1f3ee;
            --c-surface-3:    #e8ebe3;

            --c-accent:       #3a6b35;
            --c-accent-mid:   #5a9a52;
            --c-accent-light: #eef4ec;
            --c-accent-bg:    rgba(58,107,53,.07);
            --c-accent-bg2:   rgba(58,107,53,.13);
            --c-accent-glow:  rgba(58,107,53,.18);

            --c-text:         #1c2318;
            --c-text-2:       #4a5544;
            --c-text-3:       #8a9880;

            --c-border:       #e2e5da;
            --c-border-2:     #cbd2c2;
            --c-border-3:     #b5bfaa;

            --c-success:      #2e7d4f;
            --c-success-bg:   rgba(46,125,79,.08);
            --c-danger:       #a84040;
            --c-danger-bg:    rgba(168,64,64,.07);
            --c-warning:      #9a6a1a;
            --c-warning-bg:   rgba(154,106,26,.08);

            /* Layout */
            --sb-width:           260px;
            --sb-width-collapsed: 72px;
            --hdr-height:         62px;

            /* --sb-actual = variable de layout unique.
               Toujours lire celle-ci dans header/main/footer.
               body.lk-sidebar-collapsed la bascule vers --sb-width-collapsed. */
            --sb-actual: var(--sb-width);

            /* Typo */
            --font-display: 'Lora', Georgia, serif;
            --font-ui:      'DM Sans', system-ui, sans-serif;

            /* Misc */
            --radius:    10px;
            --radius-sm: 7px;
            --radius-xs: 5px;
            --t:         .17s cubic-bezier(.4,0,.2,1);
            --sh-xs: 0 1px 2px rgba(28,35,24,.04);
            --sh-sm: 0 2px 8px rgba(28,35,24,.06), 0 1px 2px rgba(28,35,24,.03);
            --sh-md: 0 4px 18px rgba(28,35,24,.09), 0 1px 4px rgba(28,35,24,.04);
        }

        html[data-theme="dark"] {
            --c-bg:           #131710;
            --c-bg-2:         #191e16;
            --c-surface:      #1e2419;
            --c-surface-2:    #252c20;
            --c-surface-3:    #2d3527;
            --c-accent:       #6abf60;
            --c-accent-mid:   #85d47a;
            --c-accent-light: rgba(106,191,96,.12);
            --c-accent-bg:    rgba(106,191,96,.09);
            --c-accent-bg2:   rgba(106,191,96,.16);
            --c-accent-glow:  rgba(106,191,96,.20);
            --c-text:         #e4ead8;
            --c-text-2:       #adb9a0;
            --c-text-3:       #627057;
            --c-border:       rgba(255,255,255,.08);
            --c-border-2:     rgba(255,255,255,.13);
            --c-border-3:     rgba(255,255,255,.19);
        }

        /* Collapsed → bascule --sb-actual */
        body.lk-sidebar-collapsed { --sb-actual: var(--sb-width-collapsed); }

        /* ==============================================================
           2. BASE
           ============================================================== */
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            font-family: var(--font-ui) !important;
            color: var(--c-text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: var(--c-bg);
        }

        /* Grain papier subtil */
        body::before {
            content: ''; position: fixed; inset: 0; z-index: 0;
            pointer-events: none; opacity: .55;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
        }

        :focus         { outline: none; }
        :focus-visible { outline: 2px solid var(--c-accent); outline-offset: 2px; border-radius: 3px; }

        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: .001ms !important; transition-duration: .001ms !important; }
        }

        /* ==============================================================
           3. SPINNER
           ============================================================== */
        #lk-spinner {
            position: fixed; inset: 0; z-index: 99999;
            display: flex; align-items: center; justify-content: center;
            transition: opacity .28s ease;
        }
        #lk-spinner::before {
            content: ''; position: fixed; inset: 0;
            background: rgba(246,247,243,.88);
            backdrop-filter: blur(3px);
        }
        html[data-theme="dark"] #lk-spinner::before { background: rgba(19,23,16,.88); }

        .lk-spin-ring {
            width: 38px; height: 38px; position: relative; z-index: 1;
            border: 2.5px solid var(--c-border-2);
            border-top-color: var(--c-accent);
            border-radius: 50%;
            animation: lk-spin .7s linear infinite;
        }
        @keyframes lk-spin { to { transform: rotate(360deg); } }

        /* ==============================================================
           4. SIDEBAR — Clean Sage
           ============================================================== */
        .lk-sidebar, .lk-sidebar * { font-family: var(--font-ui); box-sizing: border-box; }

        .lk-sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sb-width);
            height: 100vh;
            background: var(--c-surface);
            border-right: 1px solid var(--c-border);
            display: flex; flex-direction: column;
            z-index: 1050;
            overflow-y: auto; overflow-x: hidden;
            box-shadow: 2px 0 10px rgba(28,35,24,.05);
            transition: transform var(--t), width var(--t);
        }
        html[data-theme="dark"] .lk-sidebar { box-shadow: 2px 0 18px rgba(0,0,0,.2); }
        .lk-sidebar::-webkit-scrollbar       { width: 3px; }
        .lk-sidebar::-webkit-scrollbar-thumb { background: var(--c-border-2); border-radius: 99px; }

        /* Brand */
        .lk-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 15px 14px 14px;
            border-bottom: 1px solid var(--c-border);
            position: sticky; top: 0; z-index: 10;
            background: var(--c-surface);
        }
        .lk-brand-icon {
            width: 36px; height: 36px; border-radius: var(--radius-sm); flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: var(--c-accent); color: #fff; font-size: .88rem;
            box-shadow: 0 4px 12px var(--c-accent-glow);
        }
        .lk-brand-text   { line-height: 1.2; flex: 1; min-width: 0; }
        .lk-brand-name   {
            display: block; font-family: var(--font-display);
            font-size: .9rem; font-weight: 700;
            color: var(--c-text); letter-spacing: -.01em;
        }
        .lk-brand-accent { color: var(--c-accent); font-style: italic; }
        .lk-brand-sub    {
            display: block; font-size: .59rem; font-weight: 500;
            letter-spacing: .13em; text-transform: uppercase;
            color: var(--c-text-3); margin-top: 2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .lk-ico-btn {
            width: 27px; height: 27px; border-radius: var(--radius-xs); flex-shrink: 0;
            border: 1px solid var(--c-border); background: var(--c-surface-2);
            color: var(--c-text-3); cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: .65rem; transition: all var(--t);
        }
        .lk-ico-btn:hover { background: var(--c-surface-3); color: var(--c-text); }
        .lk-close-btn    { display: none; }
        .lk-collapse-btn { display: flex; }

        /* User pill */
        .lk-user-pill {
            display: flex; align-items: center; gap: 9px;
            margin: 10px 10px 4px; padding: 9px 10px;
            background: var(--c-accent-light);
            border: 1px solid rgba(58,107,53,.15);
            border-radius: var(--radius);
            transition: border-color var(--t);
        }
        html[data-theme="dark"] .lk-user-pill { border-color: rgba(106,191,96,.15); }
        .lk-user-pill:hover { border-color: rgba(58,107,53,.35); }

        .lk-avatar {
            width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: .64rem; font-weight: 700; color: #fff;
            background: var(--c-accent);
            box-shadow: 0 3px 10px var(--c-accent-glow);
        }
        .lk-user-info { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
        .lk-user-name { font-size: .75rem; font-weight: 600; color: var(--c-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .lk-user-role { display: flex; align-items: center; gap: 4px; font-size: .63rem; color: var(--c-text-3); }
        .lk-role-dot  {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--c-success);
            box-shadow: 0 0 5px rgba(46,125,79,.55);
            animation: lk-dot 2.8s ease-in-out infinite;
        }
        @keyframes lk-dot { 0%,100%{ opacity:1; transform:scale(1); } 50%{ opacity:.4; transform:scale(.65); } }

        /* Nav */
        .lk-nav { flex: 1; padding: 6px 8px 8px; display: flex; flex-direction: column; gap: 1px; }

        .lk-nav-section {
            display: flex; align-items: center; gap: 8px; padding: 14px 6px 4px;
        }
        .lk-nav-section span {
            font-size: .57rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .14em;
            color: var(--c-text-3); white-space: nowrap;
        }
        .lk-nav-section::after { content: ''; flex: 1; height: 1px; background: var(--c-border); }

        .lk-nav-link {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 10px; border-radius: var(--radius-sm);
            color: var(--c-text-2); font-size: .79rem; font-weight: 400;
            text-decoration: none; border: 1px solid transparent;
            background: transparent; cursor: pointer;
            width: 100%; text-align: left;
            transition: all var(--t); position: relative;
        }
        .lk-nav-link:hover  { background: var(--c-surface-2); color: var(--c-text); }
        .lk-nav-link.active {
            background: var(--c-accent-bg2); color: var(--c-accent); font-weight: 600;
            border-color: rgba(58,107,53,.17);
        }
        html[data-theme="dark"] .lk-nav-link.active { border-color: rgba(106,191,96,.17); }
        .lk-nav-link.active::before {
            content: ''; position: absolute; left: 0; top: 22%; bottom: 22%;
            width: 2.5px; border-radius: 0 3px 3px 0; background: var(--c-accent);
        }

        .lk-nav-icon {
            width: 15px; height: 15px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            color: var(--c-text-3); transition: color var(--t);
        }
        .lk-nav-link:hover .lk-nav-icon,
        .lk-nav-link.active .lk-nav-icon { color: var(--c-accent); }
        .lk-nav-label { flex: 1; }

        .lk-badge {
            font-size: .54rem; font-weight: 700; padding: 1.5px 6px; border-radius: 20px;
            background: var(--c-accent-bg2); border: 1px solid rgba(58,107,53,.2);
            color: var(--c-accent); letter-spacing: .04em;
        }
        .lk-chevron { font-size: .54rem; color: var(--c-text-3); transition: transform var(--t); margin-left: auto; }
        .lk-collapsible.open .lk-chevron { transform: rotate(90deg); }

        /* Sub-menu */
        .lk-sub-menu {
            display: none; flex-direction: column; gap: 1px;
            padding: 2px 0 4px 10px;
            margin-left: 16px; border-left: 1.5px solid var(--c-border-2);
        }
        .lk-sub-menu.open { display: flex; animation: lk-sub-in .13s ease both; }
        @keyframes lk-sub-in { from{ opacity:0; transform:translateY(-4px); } to{ opacity:1; transform:translateY(0); } }

        .lk-sub-link {
            display: flex; align-items: center; gap: 7px;
            padding: 5.5px 10px; border-radius: var(--radius-xs);
            color: var(--c-text-3); font-size: .76rem; text-decoration: none;
            transition: all var(--t);
        }
        .lk-sub-link:hover  { background: var(--c-surface-2); color: var(--c-text-2); }
        .lk-sub-link.active { color: var(--c-accent); background: var(--c-accent-bg); font-weight: 600; }
        .lk-sub-link i      { width: 13px; text-align: center; font-size: .69rem; color: var(--c-text-3); transition: color var(--t); }
        .lk-sub-link:hover i,
        .lk-sub-link.active i { color: var(--c-accent); }

        /* Footer sidebar */
        .lk-sidebar-footer {
            border-top: 1px solid var(--c-border); padding: 10px;
            position: sticky; bottom: 0; background: var(--c-surface);
        }
        .lk-logout-btn {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            width: 100%; padding: 7.5px; border-radius: var(--radius-sm);
            border: 1px solid var(--c-border); background: transparent;
            color: var(--c-text-3); font-family: var(--font-ui);
            font-size: .76rem; font-weight: 500; text-decoration: none; cursor: pointer;
            transition: all var(--t);
        }
        .lk-logout-btn:hover { color: var(--c-danger); border-color: rgba(168,64,64,.25); background: var(--c-danger-bg); }

        /* Overlay mobile */
        .lk-overlay {
            display: none; position: fixed; inset: 0; z-index: 1040;
            background: rgba(28,35,24,.42); backdrop-filter: blur(3px);
            animation: lk-fade .18s ease;
        }
        .lk-overlay.active { display: block; }
        @keyframes lk-fade { from{ opacity:0; } to{ opacity:1; } }

        /* Collapsed */
        body.lk-sidebar-collapsed .lk-sidebar                { width: var(--sb-width-collapsed); }
        body.lk-sidebar-collapsed .lk-brand-text,
        body.lk-sidebar-collapsed .lk-user-info,
        body.lk-sidebar-collapsed .lk-nav-label,
        body.lk-sidebar-collapsed .lk-badge,
        body.lk-sidebar-collapsed .lk-chevron,
        body.lk-sidebar-collapsed .lk-nav-section span,
        body.lk-sidebar-collapsed .lk-nav-section::after,
        body.lk-sidebar-collapsed .lk-sidebar-footer span    { display: none !important; }
        body.lk-sidebar-collapsed .lk-nav-link               { justify-content: center; padding: 9px 5px; }
        body.lk-sidebar-collapsed .lk-nav-icon               {
            width: 34px; height: 34px; border-radius: var(--radius-sm);
            background: var(--c-surface-2); border: 1px solid var(--c-border);
        }
        body.lk-sidebar-collapsed .lk-nav-link.active .lk-nav-icon {
            background: var(--c-accent-bg2); border-color: rgba(58,107,53,.2);
        }
        body.lk-sidebar-collapsed .lk-user-pill { justify-content: center; padding: 8px 5px; }

        /* ==============================================================
           5. HEADER — Clean Sage
           Lit --sb-actual : se synchronise avec l'état collapsed.
           ============================================================== */
        .lk-header, .lk-header * { font-family: var(--font-ui); box-sizing: border-box; }

        .lk-header {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--hdr-height); z-index: 1030;
            display: flex; align-items: center; gap: 12px;
            padding: 0 1.5rem 0 calc(var(--sb-actual) + 1.25rem);
            background: rgba(255,255,255,.95);
            border-bottom: 1px solid var(--c-border);
            backdrop-filter: blur(10px) saturate(130%);
            -webkit-backdrop-filter: blur(10px) saturate(130%);
            box-shadow: var(--sh-xs);
            transition: padding var(--t);
        }
        html[data-theme="dark"] .lk-header {
            background: rgba(30,36,25,.94);
            box-shadow: 0 2px 14px rgba(0,0,0,.2);
        }
        /* Liseré vert en bas */
        .lk-header::after {
            content: ""; position: absolute; left: 0; right: 0; bottom: -1px; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(58,107,53,.28), rgba(90,154,82,.18), transparent);
            pointer-events: none;
        }

        .lk-hdr-left   { display: flex; align-items: center; gap: .75rem; flex-shrink: 0; }
        .lk-hdr-center { flex: 1; display: flex; justify-content: center; }
        .lk-hdr-right  { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }

        /* Hamburger */
        .lk-toggle-btn {
            width: 32px; height: 32px; border-radius: var(--radius-sm);
            border: 1px solid var(--c-border); background: transparent; cursor: pointer;
            display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 4px;
            transition: all var(--t);
        }
        .lk-toggle-btn span { display: block; height: 1.5px; border-radius: 2px; background: var(--c-text-3); transition: all var(--t); }
        .lk-toggle-btn span:nth-child(1) { width: 14px; }
        .lk-toggle-btn span:nth-child(2) { width: 18px; }
        .lk-toggle-btn span:nth-child(3) { width: 10px; }
        .lk-toggle-btn:hover { background: var(--c-surface-2); border-color: var(--c-border-2); }
        .lk-toggle-btn:hover span { background: var(--c-accent); }

        /* Breadcrumb */
        .lk-breadcrumb {
            display: flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 999px;
            border: 1px solid var(--c-border); background: var(--c-surface-2);
            font-size: .69rem; font-weight: 500; color: var(--c-text-2); white-space: nowrap;
        }
        .lk-breadcrumb i { color: var(--c-accent); font-size: .66rem; }

        /* Badge centre */
        .lk-app-badge {
            display: flex; align-items: center; gap: 6px;
            padding: 5px 14px; border-radius: 999px;
            border: 1px solid rgba(58,107,53,.2); background: var(--c-accent-light);
            color: var(--c-accent);
            font-family: var(--font-display); font-size: .73rem; font-weight: 600;
            letter-spacing: .01em; transition: background var(--t);
        }
        html[data-theme="dark"] .lk-app-badge { border-color: rgba(106,191,96,.2); }
        .lk-app-badge:hover { background: var(--c-accent-bg2); }
        .lk-app-badge i { font-size: .64rem; }

        /* Boutons action */
        .lk-action-btn {
            width: 32px; height: 32px; border-radius: var(--radius-sm);
            border: 1px solid var(--c-border); background: transparent;
            color: var(--c-text-3); cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all var(--t);
        }
        .lk-action-btn:hover { background: var(--c-accent-light); color: var(--c-accent); border-color: rgba(58,107,53,.2); }
        html[data-theme="dark"] .lk-action-btn:hover { border-color: rgba(106,191,96,.2); }

        .lk-hdiv { width: 1px; height: 18px; background: var(--c-border-2); }

        /* Profile */
        .lk-profile-wrap { position: relative; }
        .lk-profile-btn {
            display: flex; align-items: center; gap: 7px;
            padding: 4px 9px 4px 5px; border-radius: var(--radius-sm);
            border: 1px solid var(--c-border); background: var(--c-surface-2);
            cursor: pointer; transition: all var(--t);
        }
        .lk-profile-btn:hover { background: var(--c-surface-3); border-color: var(--c-border-2); }

        .lk-hdr-avatar {
            width: 26px; height: 26px; border-radius: 7px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: .59rem; font-weight: 700; color: #fff;
            background: var(--c-accent); box-shadow: 0 3px 8px var(--c-accent-glow);
        }
        .lk-profile-text { display: flex; flex-direction: column; gap: 1px; }
        .lk-profile-name { font-size: .72rem; font-weight: 600; color: var(--c-text); white-space: nowrap; }
        .lk-profile-role { font-size: .59rem; color: var(--c-text-3); white-space: nowrap; }
        .lk-chevron-hdr  { font-size: .52rem; color: var(--c-text-3); transition: transform var(--t); }
        .lk-profile-wrap.open .lk-chevron-hdr { transform: rotate(180deg); }

        /* Dropdown */
        .lk-dropdown {
            position: absolute; top: calc(100% + .55rem); right: 0;
            min-width: 220px; border-radius: 11px; padding: .38rem;
            border: 1px solid var(--c-border); background: var(--c-surface);
            box-shadow: var(--sh-md); display: none; z-index: 9999;
            animation: lkDrop .13s cubic-bezier(.4,0,.2,1) both;
        }
        .lk-dropdown.open { display: block; }
        @keyframes lkDrop { from{ opacity:0; transform:scale(.97) translateY(-7px); } to{ opacity:1; transform:scale(1) translateY(0); } }

        .lk-dd-header { display: flex; align-items: center; gap: 9px; padding: .5rem .62rem .58rem; }
        .lk-dd-avatar {
            width: 36px; height: 36px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: .67rem;
            background: var(--c-accent); box-shadow: 0 4px 10px var(--c-accent-glow);
        }
        .lk-dd-name    { font-size: .79rem; font-weight: 600; color: var(--c-text); font-family: var(--font-display); }
        .lk-dd-role    { font-size: .63rem; color: var(--c-text-3); margin-top: 1px; }
        .lk-dd-divider { height: 1px; background: var(--c-border); margin: .22rem .08rem; }

        .lk-dd-item {
            display: flex; align-items: center; gap: 8px;
            padding: .46rem .68rem; border-radius: 6px;
            color: var(--c-text-2); font-size: .75rem; font-weight: 400;
            text-decoration: none; transition: all var(--t);
        }
        .lk-dd-item:hover   { background: var(--c-surface-2); color: var(--c-text); }
        .lk-dd-item i       { width: 13px; text-align: center; font-size: .69rem; color: var(--c-text-3); transition: color var(--t); }
        .lk-dd-item:hover i { color: var(--c-accent); }
        .lk-dd-danger       { color: var(--c-danger) !important; }
        .lk-dd-danger:hover { background: var(--c-danger-bg) !important; color: var(--c-danger) !important; }
        .lk-dd-danger i     { color: var(--c-danger) !important; }

        /* ==============================================================
           6. MAIN + FOOTER
           ============================================================== */
        #lk-main {
            margin-left: var(--sb-actual);
            padding-top: calc(var(--hdr-height) + 1.5rem);
            padding-left: 1.75rem; padding-right: 1.75rem; padding-bottom: 2.5rem;
            min-height: 100vh; position: relative; z-index: 1;
            transition: margin-left var(--t);
        }
        #lk-footer {
            margin-left: var(--sb-actual);
            background: var(--c-surface); border-top: 1px solid var(--c-border);
            padding: .85rem 1.75rem; transition: margin-left var(--t);
            position: relative; z-index: 1;
        }
        #lk-footer p           { font-size: .69rem; color: var(--c-text-3); margin: 0; text-align: center; }
        #lk-footer strong span { color: var(--c-accent); font-weight: 600; }

        /* ==============================================================
           7. COMPOSANTS GLOBAUX
           ============================================================== */

        /* Titres de page */
        .lk-page-title {
            font-family: var(--font-display);
            font-size: 1.3rem; font-weight: 600; color: var(--c-text);
            letter-spacing: -.02em; margin-bottom: .2rem;
        }
        .lk-page-sub { font-size: .76rem; color: var(--c-text-3); margin-bottom: 1.5rem; }

        /* Cards */
        .lk-card {
            background: var(--c-surface); border: 1px solid var(--c-border);
            border-radius: var(--radius); padding: 1.1rem 1.2rem; box-shadow: var(--sh-xs);
        }
        .lk-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding-bottom: .75rem; margin-bottom: .75rem; border-bottom: 1px solid var(--c-border);
        }
        .lk-card-title { font-family: var(--font-display); font-size: .88rem; font-weight: 600; color: var(--c-text); }

        /* Stat cards */
        .lk-stat {
            background: var(--c-surface); border: 1px solid var(--c-border);
            border-radius: var(--radius); padding: 1rem 1.1rem;
        }
        .lk-stat.accent { border-left: 3px solid var(--c-accent); border-radius: 0 var(--radius) var(--radius) 0; }
        .lk-stat-label { font-size: .61rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: var(--c-text-3); margin-bottom: .32rem; }
        .lk-stat-val   { font-family: var(--font-display); font-size: 1.55rem; font-weight: 700; color: var(--c-text); letter-spacing: -.04em; }
        .lk-stat.accent .lk-stat-val { color: var(--c-accent); }
        .lk-stat-sub   { font-size: .67rem; margin-top: .28rem; color: var(--c-success); }
        .lk-stat-sub.neg { color: var(--c-danger); }

        /* Badges statuts */
        .lk-badge-ok   { display: inline-flex; font-size: .62rem; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: var(--c-success-bg); border: 1px solid rgba(46,125,79,.2); color: var(--c-success); }
        .lk-badge-warn { display: inline-flex; font-size: .62rem; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: var(--c-warning-bg); border: 1px solid rgba(154,106,26,.2); color: var(--c-warning); }
        .lk-badge-err  { display: inline-flex; font-size: .62rem; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: var(--c-danger-bg); border: 1px solid rgba(168,64,64,.2); color: var(--c-danger); }

        /* Boutons */
        .lk-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .46rem .95rem; border-radius: var(--radius-sm);
            font-family: var(--font-ui); font-size: .77rem; font-weight: 500;
            cursor: pointer; border: 1px solid; text-decoration: none; transition: all var(--t);
        }
        .lk-btn-primary { background: var(--c-accent); border-color: var(--c-accent); color: #fff; }
        .lk-btn-primary:hover { background: var(--c-accent-mid); border-color: var(--c-accent-mid); color: #fff; }
        .lk-btn-outline { background: transparent; border-color: var(--c-border-2); color: var(--c-text-2); }
        .lk-btn-outline:hover { background: var(--c-surface-2); color: var(--c-text); }

        /* Tables */
        .lk-table-wrap { overflow-x: auto; }
        .lk-table { width: 100%; border-collapse: collapse; font-size: .77rem; }
        .lk-table thead th {
            font-size: .59rem; text-transform: uppercase; letter-spacing: .12em;
            color: var(--c-text-3); padding: .5rem .85rem;
            border-bottom: 1px solid var(--c-border); font-weight: 700; text-align: left;
        }
        .lk-table tbody td { padding: .62rem .85rem; color: var(--c-text-2); border-bottom: 1px solid var(--c-bg-2); }
        .lk-table tbody tr:last-child td { border-bottom: none; }
        .lk-table tbody tr:hover td { background: var(--c-surface-2); }
        .lk-table .td-primary { color: var(--c-text); font-weight: 500; }

        /* Formulaires */
        .lk-form-group { margin-bottom: 1.1rem; }
        .lk-label { display: block; font-size: .71rem; font-weight: 600; color: var(--c-text-2); margin-bottom: .33rem; }
        .lk-input, .lk-select, .lk-textarea {
            width: 100%; padding: .5rem .78rem;
            border: 1px solid var(--c-border-2); border-radius: var(--radius-sm);
            background: var(--c-surface); color: var(--c-text);
            font-family: var(--font-ui); font-size: .79rem;
            transition: border-color var(--t), box-shadow var(--t);
        }
        .lk-input:focus, .lk-select:focus, .lk-textarea:focus {
            outline: none; border-color: var(--c-accent);
            box-shadow: 0 0 0 3px var(--c-accent-bg);
        }
        .lk-input::placeholder, .lk-textarea::placeholder { color: var(--c-text-3); }

        /* Alerts */
        .lk-alert {
            display: flex; align-items: flex-start; gap: .6rem;
            padding: .72rem .95rem; border-radius: var(--radius-sm);
            font-size: .78rem; margin-bottom: 1rem; border: 1px solid;
        }
        .lk-alert-success { background: var(--c-success-bg); border-color: rgba(46,125,79,.2); color: var(--c-success); }
        .lk-alert-danger  { background: var(--c-danger-bg);  border-color: rgba(168,64,64,.2);  color: var(--c-danger); }
        .lk-alert-warning { background: var(--c-warning-bg); border-color: rgba(154,106,26,.2); color: var(--c-warning); }

        /* ==============================================================
           8. RESPONSIVE
           ============================================================== */
        @media (max-width: 992px) {
            .lk-sidebar              { transform: translateX(-100%); width: 260px !important; box-shadow: none; }
            .lk-sidebar.open         { transform: translateX(0); box-shadow: 4px 0 22px rgba(28,35,24,.12); }
            .lk-header               { padding: 0 1rem; }
            #lk-main, #lk-footer     { margin-left: 0 !important; }
            .lk-close-btn            { display: flex; }
            .lk-collapse-btn         { display: none; }
            .lk-breadcrumb span,
            .lk-app-badge span,
            .lk-profile-text,
            .lk-chevron-hdr          { display: none; }
            .lk-app-badge            { padding: 5px 8px; }
            body.lk-sidebar-collapsed { --sb-actual: 0px; }
        }
        @media (max-width: 576px) {
            #lk-main       { padding-left: 1rem; padding-right: 1rem; }
            #lk-footer     { padding-left: 1rem; padding-right: 1rem; }
            .lk-hdr-center { display: none; }
        }
    </style>
</head>

<body>

    {{-- Spinner --}}
    <div id="lk-spinner" aria-hidden="true">
        <div class="lk-spin-ring"></div>
    </div>

    {{-- Sidebar + Overlay --}}
    @include("layouts.sidebar")

    {{-- Header --}}
    @include("layouts.header")

    {{-- Contenu principal --}}
    <main id="lk-main" role="main">
        @yield("content")
    </main>

    {{-- Footer --}}
    <footer id="lk-footer" role="contentinfo">
        <p>
            © <time datetime="2026">2026</time>
            <strong><span>SCHOOL MANAGER</span></strong> —
            Powered by <strong><span>Yannick ZOMAHOUN</span></strong>.
            Tous droits réservés.
        </p>
    </footer>

    {{-- JS legacy --}}
    @include("layouts.js")

    {{-- Hooks pages enfants --}}
    @yield("another_JS")
    @stack('scripts')

    {{-- ================================================================
         JS GLOBAL — sidebar, dropdown profil, thème
         Tout ici pour éviter le bug d'ordre Blade avec les @push.
         ================================================================ --}}
    <script>
    (function () {
        'use strict';

        /* ── Spinner ──────────────────────────────────────────────── */
        window.addEventListener('load', function () {
            var s = document.getElementById('lk-spinner');
            if (!s) return;
            s.style.opacity = '0';
            setTimeout(function () { s.remove(); }, 300);
        });

        window.showLoader = function () {
            var s = document.getElementById('lk-spinner');
            if (s) { document.body.appendChild(s); s.style.display = 'flex'; s.style.opacity = '1'; }
        };

        /* ── Sidebar ──────────────────────────────────────────────── */
        var sidebar     = document.getElementById('lk-sidebar');
        var overlay     = document.getElementById('lkSidebarOverlay');
        var closeBtn    = document.getElementById('lkSidebarCloseBtn');
        var collapseBtn = document.getElementById('lkSidebarCollapseBtn');

        if (!sidebar) return;

        var K = 'lk_sidebar_collapsed';
        var isMobile = function () { return window.matchMedia('(max-width: 992px)').matches; };

        function openMobile() {
            sidebar.classList.add('open');
            if (overlay) overlay.classList.add('active');
            document.body.classList.add('lk-mobile-open');
        }
        function closeMobile() {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
            document.body.classList.remove('lk-mobile-open');
        }
        function toggleCollapsed() {
            document.body.classList.toggle('lk-sidebar-collapsed');
            var c = document.body.classList.contains('lk-sidebar-collapsed');
            if (collapseBtn) {
                collapseBtn.innerHTML = c
                    ? '<i class="fas fa-angle-double-right"></i>'
                    : '<i class="fas fa-angle-double-left"></i>';
            }
            try { localStorage.setItem(K, c ? '1' : '0'); } catch (e) {}
        }

        /* Hamburger header */
        document.querySelectorAll('.toggle-sidebar-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                isMobile()
                    ? (sidebar.classList.contains('open') ? closeMobile() : openMobile())
                    : toggleCollapsed();
            });
        });

        if (closeBtn)    closeBtn.addEventListener('click', closeMobile);
        if (overlay)     overlay.addEventListener('click', closeMobile);
        if (collapseBtn) collapseBtn.addEventListener('click', toggleCollapsed);

        /* Restauration état collapsed — desktop uniquement (FIX 4) */
        try {
            if (localStorage.getItem(K) === '1' && !isMobile()) {
                document.body.classList.add('lk-sidebar-collapsed');
                if (collapseBtn) collapseBtn.innerHTML = '<i class="fas fa-angle-double-right"></i>';
            }
        } catch (e) {}

        /* Accordéon — conserve les menus avec lien actif (FIX 5) */
        document.querySelectorAll('.lk-collapsible').forEach(function (btn) {
            var sub = document.getElementById(btn.dataset.target);
            if (!sub) return;

            btn.addEventListener('click', function () {
                var wasOpen   = sub.classList.contains('open');
                var hasActive = !!sub.querySelector('.lk-sub-link.active');

                /* Fermer tous les menus sans lien actif */
                document.querySelectorAll('.lk-sub-menu.open').forEach(function (s) {
                    if (!s.querySelector('.lk-sub-link.active')) s.classList.remove('open');
                });
                document.querySelectorAll('.lk-collapsible.open').forEach(function (b) {
                    var t = document.getElementById(b.dataset.target);
                    if (!t || !t.querySelector('.lk-sub-link.active')) {
                        b.classList.remove('open');
                        b.setAttribute('aria-expanded', 'false');
                    }
                });

                if (!wasOpen) {
                    sub.classList.add('open');
                    btn.classList.add('open');
                    btn.setAttribute('aria-expanded', 'true');
                } else if (!hasActive) {
                    sub.classList.remove('open');
                    btn.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        });

        /* Scroll vers le lien actif */
        var active = sidebar.querySelector('.lk-nav-link.active, .lk-sub-link.active');
        if (active) setTimeout(function () { active.scrollIntoView({ block: 'nearest' }); }, 300);

        /* Fermeture mobile sur clic lien */
        sidebar.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () { if (isMobile()) closeMobile(); });
        });

        /* ── Dropdown profil ──────────────────────────────────────── */
        var wrap  = document.getElementById('lkProfileWrap');
        var pbtn  = document.getElementById('lkProfileBtn');
        var pmenu = document.getElementById('lkProfileMenu');

        function closeDD() {
            if (!wrap) return;
            wrap.classList.remove('open');
            if (pmenu) pmenu.classList.remove('open');
            if (pbtn)  pbtn.setAttribute('aria-expanded', 'false');
        }

        if (pbtn && pmenu && wrap) {
            pbtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var o = wrap.classList.toggle('open');
                pmenu.classList.toggle('open', o);
                pbtn.setAttribute('aria-expanded', o ? 'true' : 'false');
            });
            document.addEventListener('click',   function (e) { if (!wrap.contains(e.target)) closeDD(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDD(); });
        }

        /* ── Thème light / dark ───────────────────────────────────── */
        var themeBtn = document.getElementById('lkThemeBtn');
        var root     = document.documentElement;
        var TK       = 'lk_theme';

        function applyTheme(t) {
            root.dataset.theme = t;
            if (themeBtn) {
                var icon = themeBtn.querySelector('i');
                if (icon) icon.className = t === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
            try { localStorage.setItem(TK, t); } catch (e) {}
        }

        try { var sv = localStorage.getItem(TK); if (sv) applyTheme(sv); } catch (e) {}

        if (themeBtn) {
            themeBtn.addEventListener('click', function () {
                applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark');
            });
        }

    })();
    </script>

</body>
</html>
