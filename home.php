<?php require 'config.php'; ?>
<!doctype html>
<html lang="en">
<head>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-F57J36XCNL"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-F57J36XCNL');
</script>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>SafeRoute</title>
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
    min-height:72vh;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    padding:48px 16px 120px;
    text-align:center;
  }
  /* soft ambient backdrop */
  .hero::before{
    content:"";position:absolute;inset:0;pointer-events:none;z-index:0;
    background:radial-gradient(circle at 50% 45%, rgba(0,0,0,.06), transparent 65%);
  }

  /* Main eye */
  #eye{
    width:min(44vw,420px);
    cursor:pointer;
    animation:autoBlink var(--blink-speed) infinite;
    transform-origin:center;
    position:relative;z-index:1;
  }
  @keyframes autoBlink{0%,98%,100%{transform:scaleY(1)}99%{transform:scaleY(.05)}}
  .blink{animation:clickBlink var(--blink-duration) forwards}
  @keyframes clickBlink{0%{transform:scaleY(1)}50%{transform:scaleY(.05)}100%{transform:scaleY(1)}}

  /* Mini eyes (faded, scattered) */
  .satellite{
    position:absolute;inset:0;pointer-events:none;z-index:0;
  }
  .eye-small{
    position:absolute;opacity:.08;filter:blur(.1px);
    width: clamp(42px, 6vw, 84px);
    transform-origin:center;
    animation:autoBlink var(--blink-speed) infinite;
  }
  /* positions + staggered delays */
  .eye-small.e1{top:12%;left:18%;animation-delay:.2s}
  .eye-small.e2{top:22%;right:14%;animation-delay:1.1s}
  .eye-small.e3{bottom:26%;left:10%;animation-delay:2.0s}
  .eye-small.e4{bottom:18%;right:16%;animation-delay:2.8s}
  .eye-small.e5{top:40%;left:6%;animation-delay:3.6s}
  .eye-small.e6{top:14%;right:35%;animation-delay:4.4s}

  /* Fixed CTA */
  .go-btn{
    position:fixed;left:50%;bottom:64px;transform:translateX(-50%);
    padding:12px 34px;border-radius:999px;background:#111;color:#fff;
    font-size:14px;letter-spacing:.2px;box-shadow:0 6px 18px rgba(0,0,0,.08);
    transition:.2s;z-index:25;
  }
  .go-btn:hover{background:#333}

  /* Sections */
  .section{max-width:var(--maxw);margin:0 auto;padding:72px 24px}
  .lead{font-size:18px;line-height:1.55;color:#444;margin:0 auto 22px;max-width:760px}
  .muted{color:#777;font-size:13px}
  .grid{display:grid;gap:28px;grid-template-columns:repeat(3,minmax(0,1fr))}
  @media (max-width:900px){.grid{grid-template-columns:1fr}}
  .card{border:1px solid #eee;border-radius:14px;padding:22px 18px}
  .card h3{margin:0 0 8px;font-size:16px}
  .card p{margin:0;color:#555;line-height:1.55;font-size:14px}
  footer{text-align:center;padding:26px 16px 90px;font-size:12px;color:#777}

  /* --- Blink-linked screen dimmer --- */
  .dimmer{
    position:fixed;inset:0;background:#000;opacity:0;pointer-events:none;z-index:15;
    animation:autoDim var(--blink-speed) infinite;
    transition:opacity .18s ease;
  }
  /* auto dim that peaks at the same moment as autoBlink 99% */
  @keyframes autoDim{0%,98%,100%{opacity:0}99%{opacity:.08}}
  /* extra quick dim on manual click blink */
  .dimmer.flash{opacity:.12;transition:none}
  
  .tagline {
  margin-top: 24px;
  font-size: 20px;
  letter-spacing: 0px;
  color: #111;
  opacity: 0.75;
  font-weight: bold;
  transition: opacity .3s ease;
}

.tagline:hover {
  opacity: 1;
}

</style>
</head>
<body>

  <!-- NAV -->
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

  <!-- Dim overlay (auto + click flash) -->
  <div id="dimmer" class="dimmer" aria-hidden="true"></div>

  <!-- HERO -->
  <main class="hero">
    <!-- Mini eyes layer -->
    <div class="satellite" aria-hidden="true">
      <img class="eye-small e1" src="Saferoute2.png" alt="">
      <img class="eye-small e2" src="Saferoute2.png" alt="">
      <img class="eye-small e3" src="Saferoute2.png" alt="">
      <img class="eye-small e4" src="Saferoute2.png" alt="">
      <img class="eye-small e5" src="Saferoute2.png" alt="">
      <img class="eye-small e6" src="Saferoute2.png" alt="">
    </div>
    
    <!-- Main focal eye -->
    <img id="eye" src="Saferoute2.png" alt="SafeRoute logo" />
    
     <p class="tagline">Alternative routes for your safety</p>
     
  </main>

  <!-- ABOUT / WHY -->
  <section id="about" class="section">
    <h2 style="margin:0 0 8px;font-size:22px;letter-spacing:.1px">Why SafeRoute</h2>
    <p class="lead">
      SafeRoute turns raw campus incident logs into a clean, filterable map so students can
      quickly understand <em>where</em> and <em>when</em> incidents cluster. It’s minimal,
      fast, and built to help you make smarter choices in the moment.
    </p>
    <p class="muted">Data source: daily crime &amp; fire logs • Location strings normalized for mapping</p>
  </section>

  <!-- FEATURES -->
  <section class="section">
  <div class="grid">
    <div class="card"><h3>Live Filtering</h3><p>Search by offense, disposition, and date range to surface only the events you care about.</p></div>
    <div class="card"><h3>Clean Geocoding</h3><p>Messy location text is normalized into map-ready addresses (e.g., “2800 Block of Orchard Ave”).</p></div>
    <div class="card"><h3>Time Patterns</h3><p>See late-night hot spots vs. daytime incidents to plan safer routes across campus.</p></div>
    <div class="card"><h3>Mobile First</h3><p>Minimal UI that loads fast on any device—no clutter, just the signal.</p></div>
    <div class="card"><h3>Privacy-Respecting</h3><p>No personal data collected. We visualize publicly available reports only.</p></div>
    <div class="card"><h3>Smart Routing</h3><p>SafeRoute analyzes recent incidents to highlight safer pathways across campus, helping you choose better routes in real time.</p></div>
  </div>
</section>

  <!-- RECENT INCIDENTS -->
  <section class="section">
    <h2 style="margin:0 0 8px;font-size:22px;letter-spacing:.1px">Recent Incidents</h2>
    <p class="lead">
      Browse recent crime and fire log entries from campus. Stay informed about what's happening around you.
    </p>
    <div style="margin-top:24px;">
      <a href="incidents.php" style="display:inline-block;padding:12px 24px;background:#111;color:#fff;border-radius:8px;font-size:14px;font-weight:500;transition:background .2s;">
        View All Incidents →
      </a>
    </div>
  </section>



  <footer>SafeRoute — USC Campus Safety Visualization</footer>

  <!-- Fixed CTA -->
  <a class="go-btn" href="map2.php" aria-label="Enter Map">Enter Map</a>

<script>
  const eye = document.getElementById('eye');
  const dim = document.getElementById('dimmer');

  eye.addEventListener('click', () => {
    // manual blink
    eye.classList.add('blink');
    // quick dim “flash”
    dim.classList.add('flash');
    setTimeout(() => {
      eye.classList.remove('blink');
      dim.classList.remove('flash');
    }, 300); // keep in sync with --blink-duration
  });
</script>
</body>
</html>
