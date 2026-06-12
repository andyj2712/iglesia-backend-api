<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Josué API — Backend Platform</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --purple:       #4A2C8F;
      --purple-mid:   #6B48B8;
      --purple-light: #8B5CF6;
      --gold:         #D4A843;
      --gold-light:   #F0C96A;
      --cream:        #FAF7F2;
      --cream-dark:   #F0EBE3;
      --text:         #1A1A2E;
      --text-soft:    #5A5A7A;
      --card-bg:      #FFFFFF;
      --border:       rgba(74, 44, 143, 0.12);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--cream);
      color: var(--text);
      line-height: 1.6;
      overflow-x: hidden;
    }

    /* ── HERO ── */
    .hero {
      background: linear-gradient(140deg, #1A0A3D 0%, #2E1260 40%, #4A2C8F 75%, #6B48B8 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 80px 24px 60px;
      position: relative;
      overflow: hidden;
    }

    /* Fondo estrellado sutil */
    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image:
        radial-gradient(circle at 20% 30%, rgba(212,168,67,0.06) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(139,92,246,0.1) 0%, transparent 50%);
      pointer-events: none;
    }

    .hero-dots {
      position: absolute;
      inset: 0;
      overflow: hidden;
      pointer-events: none;
    }

    .hero-dots span {
      position: absolute;
      width: 2px;
      height: 2px;
      background: rgba(255,255,255,0.4);
      border-radius: 50%;
      animation: twinkle 3s ease-in-out infinite;
    }

    @keyframes twinkle {
      0%, 100% { opacity: 0.2; }
      50% { opacity: 0.8; }
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(212,168,67,0.15);
      border: 1px solid rgba(212,168,67,0.35);
      color: var(--gold-light);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 6px 16px;
      border-radius: 20px;
      margin-bottom: 28px;
      position: relative;
      z-index: 1;
    }

    .badge-dot {
      width: 6px;
      height: 6px;
      background: var(--gold);
      border-radius: 50%;
      animation: pulse-dot 2s ease-in-out infinite;
    }

    @keyframes pulse-dot {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.4); opacity: 0.7; }
    }

    .hero-title {
      font-size: clamp(42px, 8vw, 80px);
      font-weight: 900;
      color: #FFFFFF;
      text-align: center;
      line-height: 1.05;
      letter-spacing: -2px;
      position: relative;
      z-index: 1;
      margin-bottom: 8px;
    }

    .hero-title .accent {
      background: linear-gradient(90deg, var(--gold), var(--gold-light));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-subtitle-line {
      font-size: clamp(14px, 2.5vw, 18px);
      color: rgba(255,255,255,0.5);
      text-align: center;
      font-family: 'JetBrains Mono', monospace;
      font-weight: 400;
      letter-spacing: 0.5px;
      position: relative;
      z-index: 1;
      margin-bottom: 48px;
    }

    .hero-subtitle-line .hl { color: rgba(212,168,67,0.8); }

    /* Stack pills */
    .stack-pills {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      position: relative;
      z-index: 1;
      margin-bottom: 64px;
    }

    .pill {
      display: flex;
      align-items: center;
      gap: 7px;
      background: rgba(255,255,255,0.07);
      border: 1px solid rgba(255,255,255,0.12);
      color: rgba(255,255,255,0.85);
      font-size: 13px;
      font-weight: 600;
      padding: 7px 14px;
      border-radius: 8px;
      backdrop-filter: blur(8px);
      transition: background 0.2s ease, border-color 0.2s ease;
    }

    .pill:hover {
      background: rgba(255,255,255,0.12);
      border-color: rgba(212,168,67,0.4);
    }

    .pill-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    /* ── DIAGRAMA DE ARQUITECTURA (el elemento firma) ── */
    .arch-diagram {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      gap: 0;
      flex-wrap: wrap;
      justify-content: center;
      row-gap: 16px;
    }

    .arch-node {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
    }

    .arch-box {
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.14);
      border-radius: 12px;
      padding: 12px 18px;
      text-align: center;
      backdrop-filter: blur(12px);
      min-width: 90px;
    }

    .arch-box .icon { font-size: 20px; display: block; margin-bottom: 4px; }
    .arch-box .name {
      font-size: 11px;
      font-weight: 700;
      color: rgba(255,255,255,0.9);
      white-space: nowrap;
    }

    .arch-box .tech {
      font-family: 'JetBrains Mono', monospace;
      font-size: 9px;
      color: rgba(212,168,67,0.7);
      white-space: nowrap;
    }

    .arch-arrow {
      display: flex;
      align-items: center;
      padding: 0 4px;
    }

    .arch-arrow svg {
      width: 32px;
      height: 12px;
      opacity: 0.5;
    }

    .arch-label {
      font-size: 9px;
      font-weight: 600;
      letter-spacing: 0.5px;
      color: rgba(255,255,255,0.35);
      text-transform: uppercase;
    }

    /* Ola de transición */
    .hero-wave {
      position: absolute;
      bottom: -1px;
      left: 0;
      right: 0;
      height: 60px;
      line-height: 0;
    }

    .hero-wave svg { width: 100%; height: 60px; }

    /* ── SECCIÓN PRINCIPAL ── */
    .main { max-width: 1100px; margin: 0 auto; padding: 80px 24px; }

    .section-eyebrow {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--purple-mid);
      margin-bottom: 10px;
    }

    .section-title {
      font-size: clamp(28px, 5vw, 42px);
      font-weight: 900;
      color: var(--text);
      letter-spacing: -1px;
      line-height: 1.1;
      margin-bottom: 16px;
    }

    .section-desc {
      font-size: 16px;
      color: var(--text-soft);
      line-height: 1.7;
      max-width: 560px;
    }

    /* ── MÓDULOS ── */
    .modules-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-top: 48px;
    }

    .module-card {
      background: var(--card-bg);
      border-radius: 18px;
      padding: 28px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 20px rgba(74,44,143,0.06);
      position: relative;
      overflow: hidden;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .module-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 32px rgba(74,44,143,0.12);
    }

    .module-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      border-radius: 18px 18px 0 0;
    }

    .module-card.purple::before { background: linear-gradient(90deg, var(--purple), var(--purple-light)); }
    .module-card.gold::before   { background: linear-gradient(90deg, var(--gold), var(--gold-light)); }
    .module-card.green::before  { background: linear-gradient(90deg, #10B981, #34D399); }
    .module-card.orange::before { background: linear-gradient(90deg, #F97316, #FB923C); }
    .module-card.red::before    { background: linear-gradient(90deg, #EF4444, #F87171); }
    .module-card.blue::before   { background: linear-gradient(90deg, #3B82F6, #60A5FA); }

    .module-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      margin-bottom: 16px;
    }

    .module-card.purple .module-icon { background: #F0EAFF; }
    .module-card.gold   .module-icon { background: #FEF3C7; }
    .module-card.green  .module-icon { background: #D1FAE5; }
    .module-card.orange .module-icon { background: #FFEDD5; }
    .module-card.red    .module-icon { background: #FEE2E2; }
    .module-card.blue   .module-icon { background: #DBEAFE; }

    .module-title {
      font-size: 17px;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 8px;
      letter-spacing: -0.3px;
    }

    .module-desc {
      font-size: 14px;
      color: var(--text-soft);
      line-height: 1.6;
    }

    .module-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 14px;
    }

    .tag {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      font-weight: 600;
      padding: 3px 8px;
      border-radius: 6px;
      background: var(--cream);
      color: var(--purple);
      border: 1px solid var(--border);
    }

    /* ── STACK TÉCNICO ── */
    .stack-section {
      background: var(--text);
      color: #FFFFFF;
      padding: 80px 24px;
      position: relative;
      overflow: hidden;
    }

    .stack-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at 80% 50%, rgba(107,72,184,0.2) 0%, transparent 60%);
      pointer-events: none;
    }

    .stack-inner { max-width: 1100px; margin: 0 auto; position: relative; z-index: 1; }

    .stack-header { margin-bottom: 48px; }

    .stack-header .section-eyebrow { color: var(--gold); }
    .stack-header .section-title   { color: #FFFFFF; }
    .stack-header .section-desc    { color: rgba(255,255,255,0.55); }

    .stack-cols {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px;
    }

    .stack-col-title {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 16px;
      padding-bottom: 8px;
      border-bottom: 1px solid rgba(212,168,67,0.2);
    }

    .stack-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 12px 0;
      border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .stack-item:last-child { border-bottom: none; }

    .stack-item-icon {
      width: 36px;
      height: 36px;
      background: rgba(255,255,255,0.06);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      flex-shrink: 0;
    }

    .stack-item-name {
      font-size: 14px;
      font-weight: 700;
      color: #FFFFFF;
    }

    .stack-item-desc {
      font-size: 12px;
      color: rgba(255,255,255,0.45);
      line-height: 1.4;
    }

    /* ── ENDPOINTS ── */
    .endpoints-section { padding: 80px 24px; background: var(--cream-dark); }
    .endpoints-inner { max-width: 1100px; margin: 0 auto; }

    .endpoints-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 16px;
      margin-top: 48px;
    }

    .endpoint-group {
      background: var(--card-bg);
      border-radius: 14px;
      padding: 20px;
      border: 1px solid var(--border);
    }

    .endpoint-group-title {
      font-size: 13px;
      font-weight: 800;
      color: var(--purple);
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .endpoint-row {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 7px 0;
      border-bottom: 1px solid var(--border);
      font-size: 13px;
    }

    .endpoint-row:last-child { border-bottom: none; padding-bottom: 0; }

    .method {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 5px;
      min-width: 46px;
      text-align: center;
      flex-shrink: 0;
    }

    .method.get    { background: #DBEAFE; color: #1D4ED8; }
    .method.post   { background: #D1FAE5; color: #065F46; }
    .method.put    { background: #FEF3C7; color: #92400E; }
    .method.delete { background: #FEE2E2; color: #991B1B; }

    .endpoint-path {
      font-family: 'JetBrains Mono', monospace;
      font-size: 12px;
      color: var(--text-soft);
    }

    /* ── ROLES ── */
    .roles-section { padding: 80px 24px; }
    .roles-inner { max-width: 1100px; margin: 0 auto; }

    .roles-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
      margin-top: 48px;
    }

    .role-card {
      background: var(--card-bg);
      border-radius: 18px;
      padding: 28px;
      border: 1px solid var(--border);
      text-align: center;
    }

    .role-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      margin: 0 auto 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
    }

    .role-card.admin .role-avatar  { background: linear-gradient(135deg, #4A2C8F, #8B5CF6); }
    .role-card.lider .role-avatar  { background: linear-gradient(135deg, #D4A843, #F0C96A); }
    .role-card.server .role-avatar { background: linear-gradient(135deg, #10B981, #34D399); }

    .role-name {
      font-size: 18px;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 8px;
    }

    .role-desc {
      font-size: 14px;
      color: var(--text-soft);
      line-height: 1.6;
      margin-bottom: 16px;
    }

    .role-perms {
      display: flex;
      flex-direction: column;
      gap: 6px;
      text-align: left;
    }

    .perm-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: var(--text-soft);
    }

    .perm-check {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: #D1FAE5;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 9px;
      color: #065F46;
      flex-shrink: 0;
    }

    /* ── FOOTER ── */
    .footer {
      background: #0F0A1E;
      color: rgba(255,255,255,0.4);
      text-align: center;
      padding: 48px 24px;
      font-size: 13px;
      line-height: 1.8;
    }

    .footer .footer-logo {
      font-size: 22px;
      font-weight: 900;
      color: #FFFFFF;
      letter-spacing: -0.5px;
      margin-bottom: 8px;
    }

    .footer .footer-logo .acc { color: var(--gold); }

    .footer-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(16,185,129,0.1);
      border: 1px solid rgba(16,185,129,0.2);
      color: #34D399;
      font-size: 11px;
      font-weight: 600;
      padding: 4px 12px;
      border-radius: 20px;
      margin: 16px auto 0;
    }

    .status-dot {
      width: 6px;
      height: 6px;
      background: #10B981;
      border-radius: 50%;
      animation: pulse-dot 2s ease-in-out infinite;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 600px) {
      .arch-diagram { gap: 0; }
      .arch-arrow { display: none; }
      .arch-node { flex-basis: calc(50% - 8px); }
    }

    @media (prefers-reduced-motion: reduce) {
      * { animation: none !important; transition: none !important; }
    }
  </style>
</head>
<body>

  <!-- ══════════ HERO ══════════ -->
  <section class="hero">
    <div class="hero-dots" aria-hidden="true">
      <span style="top:12%;left:8%;animation-delay:0s"></span>
      <span style="top:25%;left:22%;animation-delay:.4s"></span>
      <span style="top:8%;left:65%;animation-delay:.7s"></span>
      <span style="top:40%;left:90%;animation-delay:.2s"></span>
      <span style="top:70%;left:5%;animation-delay:1s"></span>
      <span style="top:80%;left:45%;animation-delay:.6s"></span>
      <span style="top:55%;left:78%;animation-delay:.9s"></span>
      <span style="top:18%;left:50%;animation-delay:.3s"></span>
    </div>

    <div class="hero-badge">
      <span class="badge-dot" aria-hidden="true"></span>
      API v1.0 · Activa en producción
    </div>

    <h1 class="hero-title">
      Iglesia<br><span class="accent">Josué</span> API
    </h1>

    <p class="hero-subtitle-line">
      <span class="hl">Laravel 9</span> · RESTful · <span class="hl">Railway</span> · MySQL · <span class="hl">FCM</span>
    </p>

    <div class="stack-pills" role="list">
      <span class="pill" role="listitem"><span class="pill-dot" style="background:#FF4A4A"></span>Laravel 9</span>
      <span class="pill" role="listitem"><span class="pill-dot" style="background:#4479A1"></span>MySQL</span>
      <span class="pill" role="listitem"><span class="pill-dot" style="background:#FF6B35"></span>Firebase FCM</span>
      <span class="pill" role="listitem"><span class="pill-dot" style="background:#42B883"></span>Vue.js + Ionic</span>
      <span class="pill" role="listitem"><span class="pill-dot" style="background:#D4A843"></span>Sanctum Auth</span>
      <span class="pill" role="listitem"><span class="pill-dot" style="background:#6366F1"></span>Capacitor Android</span>
      <span class="pill" role="listitem"><span class="pill-dot" style="background:#3ECF8E"></span>Railway Cloud</span>
    </div>

    <!-- Diagrama de arquitectura -->
    <div class="arch-diagram" role="img" aria-label="Diagrama de flujo: App Móvil hacia API Laravel hacia MySQL y FCM hacia Notificaciones">
      <div class="arch-node">
        <div class="arch-box">
          <span class="icon" aria-hidden="true">📱</span>
          <div class="name">App Móvil</div>
          <div class="tech">Vue + Ionic</div>
        </div>
        <span class="arch-label">Android</span>
      </div>
      <div class="arch-arrow" aria-hidden="true">
        <svg viewBox="0 0 32 12" fill="none"><path d="M0 6h28M22 1l6 5-6 5" stroke="#D4A843" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div class="arch-node">
        <div class="arch-box" style="border-color:rgba(212,168,67,0.4);background:rgba(212,168,67,0.08);">
          <span class="icon" aria-hidden="true">⚡</span>
          <div class="name">API REST</div>
          <div class="tech">Laravel 9</div>
        </div>
        <span class="arch-label">Railway</span>
      </div>
      <div class="arch-arrow" aria-hidden="true">
        <svg viewBox="0 0 32 12" fill="none"><path d="M0 6h28M22 1l6 5-6 5" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div class="arch-node">
        <div class="arch-box">
          <span class="icon" aria-hidden="true">🗄️</span>
          <div class="name">Base de Datos</div>
          <div class="tech">MySQL</div>
        </div>
        <span class="arch-label">Railway</span>
      </div>
      <div class="arch-arrow" aria-hidden="true">
        <svg viewBox="0 0 32 12" fill="none"><path d="M0 6h28M22 1l6 5-6 5" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div class="arch-node">
        <div class="arch-box">
          <span class="icon" aria-hidden="true">🔔</span>
          <div class="name">Push Alerts</div>
          <div class="tech">Firebase FCM</div>
        </div>
        <span class="arch-label">Google Cloud</span>
      </div>
    </div>

    <div class="hero-wave" aria-hidden="true">
      <svg viewBox="0 0 1440 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,30 C240,60 480,0 720,30 C960,60 1200,10 1440,30 L1440,60 L0,60 Z" fill="#FAF7F2"/>
      </svg>
    </div>
  </section>

  <!-- ══════════ MÓDULOS ══════════ -->
  <section class="main" aria-labelledby="modules-title">
    <p class="section-eyebrow">Funcionalidades</p>
    <h2 class="section-title" id="modules-title">Una plataforma completa<br>para la comunidad</h2>
    <p class="section-desc">No es una app de lectura. Es un sistema de gestión comunitaria con roles, flujos en tiempo real y lógica de negocio real.</p>

    <div class="modules-grid">

      <div class="module-card purple">
        <div class="module-icon" aria-hidden="true">🔐</div>
        <h3 class="module-title">Autenticación y Roles</h3>
        <p class="module-desc">Acceso seguro mediante tokens Sanctum. Jerarquía de tres niveles que controla qué pantallas, botones y endpoints son accesibles para cada persona.</p>
        <div class="module-tags">
          <span class="tag">Sanctum</span><span class="tag">Middleware</span><span class="tag">Policies</span>
        </div>
      </div>

      <div class="module-card gold">
        <div class="module-icon" aria-hidden="true">📣</div>
        <h3 class="module-title">Muro Comunitario</h3>
        <p class="module-desc">Feed interactivo de anuncios y reflexiones. Las publicaciones se segmentan por ministerio y soportan imágenes con carga y visualización dinámica.</p>
        <div class="module-tags">
          <span class="tag">Announcements</span><span class="tag">Storage</span><span class="tag">Media</span>
        </div>
      </div>

      <div class="module-card blue">
        <div class="module-icon" aria-hidden="true">🔔</div>
        <h3 class="module-title">Notificaciones Push</h3>
        <p class="module-desc">Integración con Firebase Cloud Messaging. El servidor registra tokens de dispositivo y dispara alertas directas al teléfono, incluso con la app cerrada.</p>
        <div class="module-tags">
          <span class="tag">FCM</span><span class="tag">Capacitor</span><span class="tag">Tokens</span>
        </div>
      </div>

      <div class="module-card green">
        <div class="module-icon" aria-hidden="true">⛪</div>
        <h3 class="module-title">Gestión de Ministerios</h3>
        <p class="module-desc">Estructura organizacional con ministerios (Música, Juvenil, Niños, Intercesión...). Los líderes dirigen sus publicaciones a audiencias específicas o a toda la iglesia.</p>
        <div class="module-tags">
          <span class="tag">Ministries</span><span class="tag">Segmentation</span>
        </div>
      </div>

      <div class="module-card orange">
        <div class="module-icon" aria-hidden="true">📅</div>
        <h3 class="module-title">Turnos y Horarios</h3>
        <p class="module-desc">Sistema de programación de servicios para colaboradores. Cada servidor ve sus próximas responsabilidades con fecha, hora y estado de confirmación.</p>
        <div class="module-tags">
          <span class="tag">Schedules</span><span class="tag">Calendar</span><span class="tag">Status</span>
        </div>
      </div>

      <div class="module-card red">
        <div class="module-icon" aria-hidden="true">👥</div>
        <h3 class="module-title">Gestión de Usuarios</h3>
        <p class="module-desc">Panel de administración completo: crear, editar y eliminar usuarios, asignarles rol y vincularlos a uno o más ministerios simultáneamente.</p>
        <div class="module-tags">
          <span class="tag">CRUD</span><span class="tag">Roles</span><span class="tag">Pivot</span>
        </div>
      </div>

    </div>
  </section>

  <!-- ══════════ STACK TÉCNICO ══════════ -->
  <section class="stack-section" aria-labelledby="stack-title">
    <div class="stack-inner">
      <div class="stack-header">
        <p class="section-eyebrow">Tecnologías</p>
        <h2 class="section-title" id="stack-title">El ecosistema técnico</h2>
        <p class="section-desc">Arquitectura desacoplada: backend y frontend viven en repositorios y entornos independientes, comunicados únicamente por la API.</p>
      </div>

      <div class="stack-cols">
        <div>
          <p class="stack-col-title">Backend</p>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🔴</div>
            <div>
              <p class="stack-item-name">Laravel 9</p>
              <p class="stack-item-desc">Framework PHP. Routing, Eloquent ORM, Migrations, Sanctum, Jobs.</p>
            </div>
          </div>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🗄️</div>
            <div>
              <p class="stack-item-name">MySQL</p>
              <p class="stack-item-desc">Base de datos relacional. Tablas pivot para relaciones muchos-a-muchos.</p>
            </div>
          </div>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🟣</div>
            <div>
              <p class="stack-item-name">Railway</p>
              <p class="stack-item-desc">Cloud hosting. Variables de entorno enlazadas, dominio HTTPS automático.</p>
            </div>
          </div>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🔑</div>
            <div>
              <p class="stack-item-name">Laravel Sanctum</p>
              <p class="stack-item-desc">Autenticación por tokens para SPAs y apps móviles. Sin estado.</p>
            </div>
          </div>
        </div>

        <div>
          <p class="stack-col-title">Frontend / Móvil</p>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🟢</div>
            <div>
              <p class="stack-item-name">Vue.js 3</p>
              <p class="stack-item-desc">Composition API, ref, onMounted. Lógica reactiva sin complejidad innecesaria.</p>
            </div>
          </div>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🔵</div>
            <div>
              <p class="stack-item-name">Ionic Framework</p>
              <p class="stack-item-desc">Componentes nativos multiplataforma: modales, FABs, cards, selects.</p>
            </div>
          </div>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🤖</div>
            <div>
              <p class="stack-item-name">Capacitor + Android</p>
              <p class="stack-item-desc">Compilación nativa en APK. Acceso a APIs del dispositivo (permisos push).</p>
            </div>
          </div>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🌐</div>
            <div>
              <p class="stack-item-name">Axios</p>
              <p class="stack-item-desc">Cliente HTTP. Interceptores de token, manejo de errores centralizado.</p>
            </div>
          </div>
        </div>

        <div>
          <p class="stack-col-title">Servicios externos</p>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🔔</div>
            <div>
              <p class="stack-item-name">Firebase FCM</p>
              <p class="stack-item-desc">Push notifications. El servidor dispara alertas a tokens de dispositivo registrados.</p>
            </div>
          </div>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🐙</div>
            <div>
              <p class="stack-item-name">GitHub</p>
              <p class="stack-item-desc">Repositorio del proyecto. Railway se conecta al repo para auto-deploy en cada push.</p>
            </div>
          </div>
          <div class="stack-item">
            <div class="stack-item-icon" aria-hidden="true">🖼️</div>
            <div>
              <p class="stack-item-name">Laravel Storage</p>
              <p class="stack-item-desc">Almacenamiento de imágenes de publicaciones. URLs públicas servidas por la API.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══════════ ENDPOINTS ══════════ -->
  <section class="endpoints-section" aria-labelledby="endpoints-title">
    <div class="endpoints-inner">
      <p class="section-eyebrow">Referencia</p>
      <h2 class="section-title" id="endpoints-title">Endpoints de la API</h2>
      <p class="section-desc">Todos los recursos bajo <code style="font-family:'JetBrains Mono',monospace;font-size:14px;background:var(--cream);padding:2px 6px;border-radius:4px;">/api/</code> requieren token Bearer excepto login y register.</p>

      <div class="endpoints-grid">
        <div class="endpoint-group">
          <p class="endpoint-group-title"><span aria-hidden="true">🔐</span> Autenticación</p>
          <div class="endpoint-row"><span class="method post">POST</span><span class="endpoint-path">/login</span></div>
          <div class="endpoint-row"><span class="method post">POST</span><span class="endpoint-path">/register</span></div>
          <div class="endpoint-row"><span class="method post">POST</span><span class="endpoint-path">/logout</span></div>
          <div class="endpoint-row"><span class="method get">GET</span><span class="endpoint-path">/user-profile</span></div>
        </div>

        <div class="endpoint-group">
          <p class="endpoint-group-title"><span aria-hidden="true">📣</span> Anuncios</p>
          <div class="endpoint-row"><span class="method get">GET</span><span class="endpoint-path">/announcements</span></div>
          <div class="endpoint-row"><span class="method post">POST</span><span class="endpoint-path">/announcements</span></div>
          <div class="endpoint-row"><span class="method put">PUT</span><span class="endpoint-path">/announcements/{id}</span></div>
          <div class="endpoint-row"><span class="method delete">DEL</span><span class="endpoint-path">/announcements/{id}</span></div>
        </div>

        <div class="endpoint-group">
          <p class="endpoint-group-title"><span aria-hidden="true">⛪</span> Ministerios</p>
          <div class="endpoint-row"><span class="method get">GET</span><span class="endpoint-path">/ministries</span></div>
          <div class="endpoint-row"><span class="method post">POST</span><span class="endpoint-path">/ministries</span></div>
          <div class="endpoint-row"><span class="method get">GET</span><span class="endpoint-path">/ministries/{id}</span></div>
          <div class="endpoint-row"><span class="method put">PUT</span><span class="endpoint-path">/ministries/{id}</span></div>
        </div>

        <div class="endpoint-group">
          <p class="endpoint-group-title"><span aria-hidden="true">👥</span> Usuarios</p>
          <div class="endpoint-row"><span class="method get">GET</span><span class="endpoint-path">/users</span></div>
          <div class="endpoint-row"><span class="method post">POST</span><span class="endpoint-path">/users</span></div>
          <div class="endpoint-row"><span class="method put">PUT</span><span class="endpoint-path">/users/{id}</span></div>
          <div class="endpoint-row"><span class="method delete">DEL</span><span class="endpoint-path">/users/{id}</span></div>
        </div>

        <div class="endpoint-group">
          <p class="endpoint-group-title"><span aria-hidden="true">📅</span> Turnos</p>
          <div class="endpoint-row"><span class="method get">GET</span><span class="endpoint-path">/schedules/my-schedules</span></div>
          <div class="endpoint-row"><span class="method post">POST</span><span class="endpoint-path">/schedules</span></div>
          <div class="endpoint-row"><span class="method put">PUT</span><span class="endpoint-path">/schedules/{id}</span></div>
          <div class="endpoint-row"><span class="method delete">DEL</span><span class="endpoint-path">/schedules/{id}</span></div>
        </div>

        <div class="endpoint-group">
          <p class="endpoint-group-title"><span aria-hidden="true">🔔</span> Notificaciones</p>
          <div class="endpoint-row"><span class="method post">POST</span><span class="endpoint-path">/fcm-token</span></div>
          <div class="endpoint-row"><span class="method post">POST</span><span class="endpoint-path">/send-notification</span></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══════════ ROLES ══════════ -->
  <section class="roles-section" aria-labelledby="roles-title">
    <div class="roles-inner">
      <p class="section-eyebrow">Jerarquía</p>
      <h2 class="section-title" id="roles-title">Sistema de roles y permisos</h2>
      <p class="section-desc">Cada rol desbloquea un conjunto diferente de funcionalidades tanto en el frontend como en la capa de autorización de Laravel.</p>

      <div class="roles-grid">
        <div class="role-card admin">
          <div class="role-avatar" aria-hidden="true">🛡️</div>
          <h3 class="role-name">Administrador</h3>
          <p class="role-desc">Control total del sistema. Gestiona toda la plataforma desde usuarios hasta ministerios.</p>
          <ul class="role-perms" role="list">
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Crear y eliminar usuarios</li>
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Gestionar ministerios</li>
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Publicar avisos generales</li>
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Asignar turnos a cualquier equipo</li>
          </ul>
        </div>

        <div class="role-card lider">
          <div class="role-avatar" aria-hidden="true">🎖️</div>
          <h3 class="role-name">Líder</h3>
          <p class="role-desc">Gestiona su ministerio: publica, asigna turnos y se comunica con su equipo.</p>
          <ul class="role-perms" role="list">
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Publicar en su ministerio</li>
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Asignar turnos a su equipo</li>
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Ver su calendario de servicios</li>
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Ver publicaciones de la iglesia</li>
          </ul>
        </div>

        <div class="role-card server">
          <div class="role-avatar" aria-hidden="true">🙌</div>
          <h3 class="role-name">Servidor</h3>
          <p class="role-desc">El colaborador de base. Recibe notificaciones, ve el muro y consulta sus responsabilidades.</p>
          <ul class="role-perms" role="list">
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Ver el muro comunitario</li>
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Consultar sus turnos asignados</li>
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Recibir notificaciones push</li>
            <li class="perm-item"><span class="perm-check" aria-hidden="true">✓</span> Gestionar su perfil</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- ══════════ FOOTER ══════════ -->
  <footer class="footer">
    <p class="footer-logo">Iglesia <span class="acc">Josué</span> API</p>
    <p>Backend RESTful construido con Laravel 9 · Desplegado en Railway</p>
    <p>Sistema de gestión comunitaria para equipos de servicio</p>
    <div class="footer-status" role="status" aria-live="polite">
      <span class="status-dot" aria-hidden="true"></span>
      API operativa
    </div>
  </footer>

</body>
</html>