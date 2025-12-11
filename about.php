<?php require 'config.php'; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>SafeRoute — About</title>
<style>
  :root{
    --brand-tracking:-0.05em;    /* Helvetica “-50” feel */
    --maxw: 980px;
    --blink-speed: 6s;           /* master timing for auto blink + dimmer */
    --blink-duration: .3s;       /* click blink timing */
  }

  html,body{height:100%}
  body{
    margin:0;
    font-family:"Helvetica Neue",Helvetica,Arial,system-ui,sans-serif;
    background:#fff;color:#111;
    display:flex;flex-direction:column;
    overflow-x:hidden;           /* no horizontal scroll */
  }
  a{color:inherit;text-decoration:none}

  /* NAV */
  .nav{
    position:sticky;top:0;z-index:20;
    display:flex;align-items:center;justify-content:space-between;
    padding:18px 28px;background:#fff;border-bottom:1px solid #eee;
  }
  .brand{font-weight:800;letter-spacing:var(--brand-tracking);font-size:18px;user-select:none}
  .nav-links{display:flex;gap:28px;font-size:14px;letter-spacing:.2px}
  .nav-links a{opacity:.8;transition:.2s}
  .nav-links a:hover{opacity:1}

  /* HERO */
  .hero{
    position:relative;
    min-height:52vh;
    display:flex;flex-direction:column;align-items:flex-start;justify-content:center;
    padding:56px 16px 40px;
  }
  .hero::before{
  content:"";
  position:absolute;
  inset:0;
  pointer-events:none;
  z-index:0;

  background:
 
    url("/mnt/data/USC-Village-aerial.jpg");

  background-size:cover;
  background-position:center;
  background-repeat:no-repeat;
  opacity:0.32;        /* adjust fade level */
  filter:blur(1px);    /* optional — softens it more */
}
  .hero-inner{
    position:relative;z-index:1;
    max-width:var(--maxw);
    margin:0 auto;
  }
  .eyebrow{
    text-transform:uppercase;
    letter-spacing:.18em;
    font-size:11px;
    color:#777;
    margin-bottom:8px;
  }
  .hero h1{
    margin:0 0 12px;
    font-size:32px;
    letter-spacing:.02em;
  }
  .hero-lead{
    margin:0;
    max-width:640px;
    font-size:16px;
    line-height:1.6;
    color:#555;
  }

  /* Sections */
  .section{max-width:var(--maxw);margin:0 auto;padding:40px 24px 32px}
  .section h2{margin:0 0 10px;font-size:20px;letter-spacing:.05em;text-transform:uppercase;font-weight:600}
  .section p{margin:0 0 14px;font-size:14px;line-height:1.6;color:#444}
  .muted{color:#777;font-size:13px}

  .grid{display:grid;gap:24px;grid-template-columns:repeat(3,minmax(0,1fr))}
  @media (max-width:900px){.grid{grid-template-columns:1fr}}
  .card{border:1px solid #eee;border-radius:14px;padding:18px 16px}
  .card h3{margin:0 0 6px;font-size:15px}
  .card p{margin:0;color:#555;line-height:1.55;font-size:14px}

  footer{text-align:center;padding:26px 16px 36px;font-size:12px;color:#777;margin-top:auto}

  /* Dimmer (not really used here but kept from home css) */
  .dimmer{
    position:fixed;inset:0;background:#000;opacity:0;pointer-events:none;z-index:15;
    animation:autoDim var(--blink-speed) infinite;
    transition:opacity .18s ease;
  }
  @keyframes autoDim{0%,98%,100%{opacity:0}99%{opacity:.08}}
  .dimmer.flash{opacity:.12;transition:none}
</style>
</head>
<body>

  <!-- NAV (same CSS as home, About active) -->
  <header class="nav">
    <div class="brand">SAFEROUTE</div>
    <nav class="nav-links">
      <a href="home.php">Home</a>
      <a href="map2.php">Map</a>
      <a href="about.php">About</a>
      <a href="resources.php">Resources</a>
      <a href="stats.php">Analytics</a>
      <?php if (isLoggedIn()): ?>
        <?php if (isAdmin()): ?>
          <a href="admin.php">Admin</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Log in</a>
        <a href="signup.php">Sign up</a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- optional dimmer kept for css parity -->
  <div id="dimmer" class="dimmer" aria-hidden="true"></div>

  <!-- HERO / INTRO -->
  <main class="hero" id="about">
    <div class="hero-inner">
      <div class="eyebrow">About</div>
      <h1>Built to make campus movement feel safer.</h1>
      <p class="hero-lead">
        SafeRoute started as a simple question: if all this crime and fire data already exists,
        why is it so hard for students to actually <strong>use</strong> it to move smarter?
        This project is our attempt to turn static logs into something that feels alive, visual,
        and actually helpful when you’re deciding how to get home.
      </p>
    </div>
  </main>

  <!-- ORIGIN / GOAL -->
  <section class="section">
    <h2>What SafeRoute does</h2>
    <p>
      SafeRoute ingests campus incident logs and plots them on an interactive map that’s
      filterable by date, type of incident, and disposition. On top of that, it layers a
      routing demo that can compare different paths and highlight options that avoid nearby
      incidents.
    </p>
    <p class="muted">
      It’s not a replacement for official safety resources or emergency services — it’s a
      visual tool to help you build better intuition about where incidents cluster and how
      your daily routes move through those patterns.
    </p>
  </section>

  <!-- PRINCIPLES -->
  <section class="section">
    <h2>Principles</h2>
    <div class="grid">
      <div class="card">
        <h3>Clarity over fear</h3>
        <p>
          The goal isn’t to scare you with red dots — it’s to give you a clearer picture so
          decisions like “which way should I walk?” feel less random.
        </p>
      </div>
      <div class="card">
        <h3>Simple, readable UI</h3>
        <p>
          No cluttered dashboards. Just a map, clean filters, and routing options that feel
          familiar if you’ve ever used a maps app.
        </p>
      </div>
      <div class="card">
        <h3>Respect for data</h3>
        <p>
          SafeRoute visualizes public reports only and doesn’t track individual users. The
          emphasis is on patterns, not people.
        </p>
      </div>
    </div>
  </section>

  <!-- HOW TO USE -->
  <section class="section">
    <h2>How to use it</h2>
    <p>
      Start on the home page, tap <strong>Enter Map</strong>, and you’ll land on the campus
      map. From there, you can:
    </p>
    <p>
      • Filter incidents by date range to see what’s been happening recently.<br>
      • Search by offense or location keyword.<br>
      • Set a start and end point to compare the fastest route vs. a route that avoids nearby incidents.
    </p>
    <p class="muted">
      Everything is a prototype — the idea is to prove that students actually find this useful,
      then keep iterating.
    </p>
  </section>

  <footer>SafeRoute — USC Campus Safety Visualization</footer>

</body>
</html>