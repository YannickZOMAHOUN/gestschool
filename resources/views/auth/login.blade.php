<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SchoolManager — Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=DM+Mono:wght@300;400;500&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   FONDATIONS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

:root {
    --ink:       #09090e;
    --ink-2:     #0f0f16;
    --ink-3:     #18181f;
    --ink-4:     #23232e;
    --gold:      #c9a84c;
    --gold-dim:  #a07b2e;
    --gold-glow: rgba(201,168,76,.2);
    --gold-faint:rgba(201,168,76,.07);
    --rim:       rgba(201,168,76,.12);
    --rim-hot:   rgba(201,168,76,.45);
    --text:      #ede9de;
    --text-2:    #8a8475;
    --text-3:    #48453d;
    --error:     #e5614a;
    --success:   #5db87a;
    --serif:     'Cormorant Garamond', Georgia, serif;
    --sans:      'Outfit', sans-serif;
    --mono:      'DM Mono', monospace;
}

html, body {
    min-height: 100vh;
    background: var(--ink);
    color: var(--text);
    font-family: var(--sans);
    overflow-x: hidden;
}

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   ARRIÈRE-PLAN
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.scene {
    position: fixed; inset: 0; z-index: 0; overflow: hidden;
    pointer-events: none;
}

/* Grain SVG */
.grain {
    position: absolute; inset: 0;
    opacity: .038;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    background-size: 200px 200px;
    animation: grain-shift 6s steps(2) infinite;
}
@keyframes grain-shift {
    0%   { transform: translate(0,0); }
    20%  { transform: translate(-1%,-2%); }
    40%  { transform: translate(2%,1%); }
    60%  { transform: translate(-1%,2%); }
    80%  { transform: translate(1%,-1%); }
    100% { transform: translate(0,0); }
}

/* Grille de points */
.dot-grid {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(201,168,76,.14) 1px, transparent 1px);
    background-size: 38px 38px;
    mask-image: radial-gradient(ellipse 75% 75% at 50% 50%, black 0%, transparent 100%);
}

/* Grand orbe doré central */
.orb-main {
    position: absolute;
    width: 1000px; height: 1000px; border-radius: 50%;
    background: radial-gradient(circle, rgba(201,168,76,.07) 0%, rgba(201,168,76,.025) 40%, transparent 65%);
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    animation: breathe 12s ease-in-out infinite alternate;
}
@keyframes breathe {
    from { transform: translate(-50%,-50%) scale(1); }
    to   { transform: translate(-50%,-50%) scale(1.14); }
}

/* Orbe haut-gauche bleuté */
.orb-cool {
    position: absolute;
    width: 480px; height: 480px; border-radius: 50%;
    background: radial-gradient(circle, rgba(70,90,180,.06) 0%, transparent 65%);
    top: -150px; left: -120px;
    animation: drift 18s ease-in-out infinite alternate;
}
@keyframes drift { to { transform: translate(50px, 70px); } }

/* Ligne verticale dorée */
.vert-line {
    position: absolute;
    top: 0; bottom: 0;
    right: calc(520px - 1px);
    width: 1px;
    background: linear-gradient(to bottom,
        transparent 0%,
        var(--rim) 20%,
        rgba(201,168,76,.18) 50%,
        var(--rim) 80%,
        transparent 100%
    );
}

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   LAYOUT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.stage {
    position: relative; z-index: 1;
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 520px;
}

/* ━━━ PANNEAU GAUCHE ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.col-left {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 3rem 5.5rem;
}

/* Logo */
.brand {
    display: flex; align-items: center; gap: .85rem;
}
.brand-mark {
    width: 38px; height: 38px;
    border: 1px solid var(--rim-hot);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, rgba(201,168,76,.1), transparent);
    position: relative;
}
.brand-mark i { color: var(--gold); font-size: .88rem; }
.brand-name {
    font-family: var(--serif);
    font-size: 1.2rem; font-weight: 400; letter-spacing: .06em;
    color: var(--text);
}
.brand-name b { font-weight: 600; color: var(--gold); }
.brand-tag {
    font-family: var(--mono);
    font-size: .58rem; letter-spacing: .1em;
    color: var(--text-3);
    border: 1px solid var(--ink-4);
    border-radius: 3px;
    padding: .15rem .4rem;
    margin-left: .35rem;
}

/* Editorial */
.editorial {
    flex: 1;
    display: flex; flex-direction: column; justify-content: center;
    padding: 3rem 0;
}

.eyebrow {
    display: flex; align-items: center; gap: .75rem;
    font-family: var(--mono);
    font-size: .62rem;
    text-transform: uppercase; letter-spacing: .18em;
    color: var(--gold);
    margin-bottom: 1.6rem;
}
.eyebrow::before {
    content: ''; width: 28px; height: 1px;
    background: linear-gradient(to right, var(--gold), transparent);
}

.display {
    font-family: var(--serif);
    font-size: clamp(3rem, 4vw, 5.2rem);
    font-weight: 300;
    line-height: 1.06;
    letter-spacing: -.01em;
    color: var(--text);
    margin-bottom: 2.2rem;
}
.display em { font-style: italic; color: var(--gold); }

.body-text {
    font-size: .875rem; line-height: 2;
    color: var(--text-2);
    max-width: 360px;
    border-left: 1px solid var(--rim-hot);
    padding-left: 1.3rem;
}

/* Métriques */
.metrics {
    display: flex; gap: 3rem;
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid var(--rim);
}
.metric { display: flex; flex-direction: column; gap: .2rem; }
.m-val {
    font-family: var(--serif);
    font-size: 2.2rem; font-weight: 300;
    color: var(--text); line-height: 1;
}
.m-val sup { font-size: 1rem; color: var(--gold); }
.m-key {
    font-family: var(--mono);
    font-size: .58rem; text-transform: uppercase; letter-spacing: .14em;
    color: var(--text-3); margin-top: .2rem;
}

/* Pied gauche */
.col-left-foot {
    display: flex; align-items: center; justify-content: space-between;
}
.copy-txt {
    font-family: var(--mono);
    font-size: .62rem; color: var(--text-3);
}
.pulse-dot {
    display: flex; align-items: center; gap: .5rem;
    font-family: var(--mono); font-size: .62rem; color: var(--text-3);
}
.pulse-dot::before {
    content: ''; width: 6px; height: 6px; border-radius: 50%;
    background: var(--success);
    box-shadow: 0 0 8px rgba(93,184,122,.5);
    animation: pulse-blink 2.5s ease-in-out infinite;
}
@keyframes pulse-blink { 0%,100%{opacity:1} 50%{opacity:.3} }

/* ━━━ PANNEAU DROIT (formulaire) ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.col-right {
    display: flex; align-items: center; justify-content: center;
    padding: 3rem 3.5rem;
    background: rgba(10,10,16,.8);
    backdrop-filter: blur(20px);
    border-left: 1px solid var(--rim);
    position: relative;
}

/* Lueur dorée en haut de la carte */
.col-right::before {
    content: '';
    position: absolute;
    top: 0; left: 12%; right: 12%; height: 1px;
    background: linear-gradient(90deg, transparent, var(--gold-dim), transparent);
    opacity: .55;
}

/* Coin décoratif */
.col-right::after {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 80px; height: 80px;
    border-top: 1px solid var(--rim-hot);
    border-right: 1px solid var(--rim-hot);
    pointer-events: none;
}

.form-box { width: 100%; max-width: 370px; }

/* En-tête formulaire */
.f-eyebrow {
    font-family: var(--mono);
    font-size: .58rem; text-transform: uppercase; letter-spacing: .18em;
    color: var(--gold);
    margin-bottom: .85rem;
    display: flex; align-items: center; gap: .55rem;
}
.f-eyebrow::after {
    content: ''; flex: 1; height: 1px;
    background: linear-gradient(to right, var(--rim-hot), transparent);
}

.f-title {
    font-family: var(--serif);
    font-size: 2.7rem; font-weight: 300; line-height: 1.1;
    color: var(--text); margin-bottom: .55rem;
}
.f-sub {
    font-size: .82rem; color: var(--text-2); line-height: 1.75;
    margin-bottom: 2.4rem;
}

/* ── Champ ── */
.field { margin-bottom: 1.2rem; }
.f-label {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .5rem;
}
.f-label span {
    font-size: .67rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .1em;
    color: var(--text-3);
}
.f-label a {
    font-family: var(--mono);
    font-size: .62rem; letter-spacing: .06em;
    color: var(--gold-dim); text-decoration: none;
    transition: color .2s;
}
.f-label a:hover { color: var(--gold); }

.f-wrap { position: relative; }
.f-ico {
    position: absolute; left: 1rem; top: 50%;
    transform: translateY(-50%);
    font-size: .76rem; color: var(--text-3);
    pointer-events: none; transition: color .25s;
}
.f-wrap:focus-within .f-ico { color: var(--gold); }

.f-input {
    width: 100%;
    padding: .86rem 1rem .86rem 2.55rem;
    background: rgba(24,24,31,.7);
    border: 1px solid var(--ink-4);
    border-radius: 8px;
    color: var(--text);
    font-family: var(--sans); font-size: .86rem;
    outline: none;
    transition: border-color .25s, box-shadow .25s, background .25s;
}
.f-input::placeholder { color: var(--text-3); }
.f-input:focus {
    border-color: var(--gold-dim);
    background: rgba(24,24,31,1);
    box-shadow: 0 0 0 3px var(--gold-faint);
}

.eye-toggle {
    position: absolute; right: .8rem; top: 50%;
    transform: translateY(-50%);
    background: none; border: none;
    color: var(--text-3); font-size: .76rem;
    cursor: pointer; padding: .3rem;
    transition: color .2s;
}
.eye-toggle:hover { color: var(--gold); }

.f-err {
    margin-top: .4rem; font-family: var(--mono);
    font-size: .68rem; color: var(--error);
    display: flex; align-items: center; gap: .35rem;
}

/* Remember */
.chk-row {
    display: flex; align-items: center; gap: .55rem;
    margin-bottom: 1.75rem; cursor: pointer;
}
.chk-row input[type="checkbox"] {
    appearance: none; -webkit-appearance: none;
    width: 14px; height: 14px;
    border: 1px solid var(--ink-4);
    border-radius: 3px; background: rgba(24,24,31,.7);
    cursor: pointer; position: relative;
    transition: border-color .2s, background .2s;
    flex-shrink: 0;
}
.chk-row input:checked { background: var(--gold-dim); border-color: var(--gold-dim); }
.chk-row input:checked::after {
    content: ''; position: absolute;
    top: 1px; left: 3px;
    width: 4px; height: 7px;
    border: 1.5px solid var(--ink); border-top: none; border-left: none;
    transform: rotate(45deg);
}
.chk-row span { font-size: .77rem; color: var(--text-2); user-select: none; }

/* Bouton principal — effet de balayage doré */
.btn-cta {
    width: 100%; padding: .95rem;
    background: transparent;
    border: 1px solid var(--gold-dim);
    border-radius: 8px;
    color: var(--gold);
    font-family: var(--sans); font-size: .86rem; font-weight: 500;
    letter-spacing: .07em;
    cursor: pointer;
    position: relative; overflow: hidden;
    transition: color .3s, border-color .3s;
    display: flex; align-items: center; justify-content: center; gap: .55rem;
}
.btn-cta::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(110deg, var(--gold-dim) 0%, #7a5a1a 100%);
    transform: translateY(101%);
    transition: transform .38s cubic-bezier(.22,1,.36,1);
    z-index: 0;
}
.btn-cta:hover { color: #0d0c09; border-color: var(--gold); }
.btn-cta:hover::before { transform: translateY(0); }
.btn-cta span { position: relative; z-index: 1; display: flex; align-items: center; gap: .5rem; }
.btn-cta:active { transform: scale(.99); }

/* Séparateur */
.sep {
    display: flex; align-items: center; gap: .9rem;
    margin: 1.4rem 0;
}
.sep::before,.sep::after { content: ''; flex:1; height:1px; background: var(--ink-4); }
.sep span {
    font-family: var(--mono); font-size: .58rem;
    letter-spacing: .14em; text-transform: uppercase; color: var(--text-3);
}

/* Bouton parents */
.btn-ghost {
    width: 100%; padding: .86rem;
    background: rgba(24,24,31,.5);
    border: 1px solid var(--ink-4);
    border-radius: 8px;
    color: var(--text-2); font-family: var(--sans); font-size: .8rem;
    cursor: pointer; text-decoration: none;
    display: flex; align-items: center; justify-content: center; gap: .55rem;
    transition: border-color .22s, color .22s, background .22s;
}
.btn-ghost:hover {
    border-color: rgba(201,168,76,.2);
    color: var(--text); background: rgba(24,24,31,.9);
    text-decoration: none;
}
.btn-ghost i { color: var(--text-3); transition: color .22s; font-size: .78rem; }
.btn-ghost:hover i { color: var(--gold-dim); }

/* Pied du formulaire */
.f-foot {
    margin-top: 2rem;
    text-align: center;
    font-family: var(--mono); font-size: .6rem; color: var(--text-3);
    letter-spacing: .06em;
}

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   ANIMATIONS D'ENTRÉE — Cascade fluide
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.r {
    opacity: 0;
    transform: translateY(24px);
    animation: rise .6s cubic-bezier(.22,1,.36,1) forwards;
}
@keyframes rise { to { opacity: 1; transform: translateY(0); } }

/* Gauche */
.brand      { animation-delay: .05s; }
.eyebrow    { animation-delay: .18s; }
.display    { animation-delay: .27s; }
.body-text  { animation-delay: .36s; }
.metrics    { animation-delay: .44s; }
.col-left-foot { animation-delay: .52s; }

/* Droite */
.f-eyebrow  { animation-delay: .1s;  }
.f-title    { animation-delay: .2s;  }
.f-sub      { animation-delay: .29s; }
.field-1    { animation-delay: .37s; }
.field-2    { animation-delay: .44s; }
.chk-row    { animation-delay: .51s; }
.btn-cta    { animation-delay: .58s; }
.sep        { animation-delay: .64s; }
.btn-ghost  { animation-delay: .70s; }
.f-foot     { animation-delay: .78s; }

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   RESPONSIVE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
@media (max-width: 920px) {
    .stage { grid-template-columns: 1fr; }
    .col-left { display: none; }
    .col-right { border-left: none; background: var(--ink); min-height:100vh; }
    .vert-line { display: none; }
}
@media (max-width: 420px) {
    .col-right { padding: 2rem 1.25rem; }
}

    </style>
</head>
<body>

<!-- Scène -->
<div class="scene">
    <div class="grain"></div>
    <div class="dot-grid"></div>
    <div class="orb-main"></div>
    <div class="orb-cool"></div>
    <div class="vert-line"></div>
</div>

<!-- Layout -->
<div class="stage">

    <!-- ═══ GAUCHE ═══════════════════════════════════════ -->
    <div class="col-left">

        <div class="brand r">
            <div class="brand-mark"><i class="fas fa-graduation-cap"></i></div>
            <div class="brand-name">School<b>Manager</b></div>
            <span class="brand-tag">v2</span>
        </div>

        <div class="editorial">
            <p class="eyebrow r">Plateforme de gestion scolaire</p>
            <h1 class="display r">
                L'excellence<br>
                <em>au service</em><br>
                de l'éducation.
            </h1>
            <p class="body-text r">
                Centralisez notes, classes, enseignants et bulletins
                dans un espace sécurisé, pensé pour chaque rôle
                de votre établissement scolaire.
            </p>

            <div class="metrics r">
                <div class="metric">
                    <div class="m-val">∞</div>
                    <div class="m-key">Classes</div>
                </div>
                <div class="metric">
                    <div class="m-val">100<sup>%</sup></div>
                    <div class="m-key">Sécurisé</div>
                </div>
                <div class="metric">
                    <div class="m-val">24<sup>/7</sup></div>
                    <div class="m-key">Disponible</div>
                </div>
            </div>
        </div>

        <div class="col-left-foot r">
            <span class="copy-txt">&copy; {{ date('Y') }} SchoolManager</span>
            <span class="pulse-dot">Système opérationnel</span>
        </div>

    </div>

    <!-- ═══ DROITE — FORMULAIRE ══════════════════════════ -->
    <div class="col-right">
        <div class="form-box">

            <p class="f-eyebrow r">Accès sécurisé</p>
            <h2 class="f-title r">Connexion</h2>
            <p class="f-sub r">Identifiez-vous pour accéder à votre espace de gestion.</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="field field-1 r">
                    <div class="f-label">
                        <span>Adresse e-mail</span>
                    </div>
                    <div class="f-wrap">
                        <i class="fas fa-at f-ico"></i>
                        <input
                            type="email" id="email" name="email"
                            class="f-input"
                            placeholder="votre@email.com"
                            value="{{ old('email') }}"
                            required autofocus
                        >
                    </div>
                    @error('email')
                        <div class="f-err">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="field field-2 r">
                    <div class="f-label">
                        <span>Mot de passe</span>
                        <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                    </div>
                    <div class="f-wrap">
                        <i class="fas fa-lock f-ico"></i>
                        <input
                            type="password" id="password" name="password"
                            class="f-input"
                            placeholder="••••••••••"
                            required
                        >
                        <button type="button" class="eye-toggle" id="eyeBtn">
                            <i class="fas fa-eye" id="eyeIco"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="f-err">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember -->
                <label class="chk-row r">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Rester connecté sur cet appareil</span>
                </label>

                <!-- CTA principal -->
                <button type="submit" class="btn-cta r">
                    <span>
                        <i class="fas fa-arrow-right-to-bracket"></i>
                        Accéder à mon espace
                    </span>
                </button>

                <!-- Séparateur -->
                <div class="sep r"><span>ou</span></div>

                <!-- Espace parents -->
                <a href="{{ route('parents.dashboard') }}" class="btn-ghost r">
                    <i class="fas fa-chart-line"></i>
                    Espace parents — Consulter un résultat
                </a>

            </form>

            <div class="f-foot r">SchoolManager &mdash; {{ date('Y') }} &mdash; Tous droits réservés</div>

        </div>
    </div>

</div>

<script>
/* Toggle visibilité mot de passe */
const eyeBtn = document.getElementById('eyeBtn');
const eyeIco = document.getElementById('eyeIco');
const pwdInp = document.getElementById('password');

eyeBtn.addEventListener('click', () => {
    const visible = pwdInp.type === 'text';
    pwdInp.type      = visible ? 'password' : 'text';
    eyeIco.className = visible ? 'fas fa-eye' : 'fas fa-eye-slash';
});
</script>

</body>
</html>
