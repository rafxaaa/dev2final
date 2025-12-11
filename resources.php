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
<title>SafeRoute — Resources</title>
<style>
  :root{
    --brand-tracking:-0.05em;
    --maxw: 980px;
  }

  html,body{height:100%}
  body{
    margin:0;
    font-family:"Helvetica Neue",Helvetica,Arial,system-ui,sans-serif;
    background:#fff;color:#111;
    display:flex;flex-direction:column;
    overflow-x:hidden;
  }
  a{color:inherit;text-decoration:none}

  .top-nav{
    position:sticky;top:0;z-index:20;
    height:52px;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:0 16px;
    backdrop-filter:blur(18px);
    background:linear-gradient(to bottom, rgba(255,255,255,0.94), rgba(255,255,255,0.8));
    border-bottom:1px solid #eee;
  }
  .top-nav-inner{
    display:flex;
    align-items:center;
    justify-content:space-between;
    width:100%;
    max-width:1100px;
  }
  .brand{
    display:flex;
    align-items:center;
    gap:8px;
    font-weight:800;
    letter-spacing:var(--brand-tracking);
    font-size:18px;
    color:#111;
  }
  .brand-dot{
    width:10px;
    height:10px;
    border-radius:999px;
    background:radial-gradient(circle at 30% 20%, #4bffba, #18a4ff);
  }
  .nav-links{
    display:flex;
    align-items:center;
    gap:18px;
    font-size:14px;
    letter-spacing:.2px;
  }
  .nav-links a{
    opacity:.75;
    transition:.2s;
    position:relative;
  }
  .nav-links a:hover{
    opacity:1;
    color:#111;
  }
  .nav-links a.active{
    opacity:1;
    color:#111;
  }
  .nav-links a.active::after{
    content:"";
    position:absolute;
    left:0;right:0;
    bottom:-4px;
    height:2px;
    border-radius:999px;
    background:#18a4ff;
  }

  .hero{
    max-width:var(--maxw);
    margin:0 auto;
    padding:48px 24px 24px;
  }
  .eyebrow{
    text-transform:uppercase;
    letter-spacing:.18em;
    font-size:11px;
    color:#777;
    margin-bottom:8px;
  }
  .hero h1{
    margin:0 0 10px;
    font-size:28px;
    letter-spacing:.02em;
  }
  .hero-lead{
    margin:0;
    max-width:620px;
    font-size:15px;
    line-height:1.6;
    color:#555;
  }

  .section{
    max-width:var(--maxw);
    margin:0 auto;
    padding:28px 24px 8px;
  }
  .section h2{
    margin:0 0 10px;
    font-size:18px;
    letter-spacing:.06em;
    text-transform:uppercase;
  }
  .section p{
    margin:0 0 12px;
    font-size:14px;
    line-height:1.6;
    color:#444;
  }
  .muted{color:#777;font-size:13px}

  .pill-list{
    display:grid;
    gap:10px;
    margin-top:8px;
  }
  .pill{
    border-radius:12px;
    border:1px solid #eee;
    padding:10px 12px;
    background:#fafafa;
    font-size:13px;
  }
  .pill-label{
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.15em;
    color:#888;
    margin-bottom:2px;
  }
  .pill-strong{
    font-weight:600;
    color:#111;
  }

  footer{
    text-align:center;
    padding:26px 16px 36px;
    font-size:12px;
    color:#777;
    margin-top:auto;
  }
</style>
</head>
<body>

<header class="top-nav">
  <div class="top-nav-inner">
    <div class="brand">
      <span class="brand-dot"></span>
      SAFEROUTE
    </div>
    <nav class="nav-links">
      <a href="home.php">Home</a>
      <a href="map2.php">Map</a>
      <a href="about.php">About</a>
      <a href="resources.php" class="active">Resources</a>
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
  </div>
</header>

<main>
  <section class="hero">
    <div class="eyebrow">Resources</div>
    <h1>Who to call and what to use when you need help.</h1>
    <p class="hero-lead">
      SafeRoute is just a visualization layer. When something feels off, or if you need
      real-time help, these are the services and tools that actually move things in the
      real world.
    </p>
  </section>

  <section class="section">
    <h2>Emergency contacts</h2>
    <div class="pill-list">
      <div class="pill">
        <div class="pill-label">Life-threatening or urgent</div>
        <div class="pill-strong">Call 911 first.</div>
        <p>If you or someone around you is in immediate danger, always treat 911 as your
        first call. SafeRoute can help you plan routes, but it cannot respond to emergencies.</p>
      </div>

      <div class="pill">
        <div class="pill-label">Campus safety</div>
        <div class="pill-strong">USC Department of Public Safety (DPS)</div>
        <p>Plug your campus safety numbers into your phone contacts so you don’t have to
        search for them when you’re stressed. Use them to report suspicious activity,
        ask for help, or follow up on an incident.</p>
      </div>

      <div class="pill">
        <div class="pill-label">Non-emergency</div>
        <div class="pill-strong">Local non-emergency police line</div>
        <p>For situations that are not an active emergency but still matter, your local
        non-emergency line can document issues and dispatch help when appropriate.</p>
      </div>
    </div>
  </section>

  <section class="section">
    <h2>Safe ride options</h2>
    <p>
      Many campuses partner with rideshare services or run their own shuttles to help
      students get home safely at night. SafeRoute is intended to work alongside these,
      not replace them.
    </p>
    <p class="muted">
      Update this section with the exact details for USC’s Late Night Safe Ride programs,
      current Lyft zones, and hours once you have them confirmed.
    </p>
    <div class="pill-list">
      <div class="pill">
        <div class="pill-label">Lyft / Safe ride</div>
        <div class="pill-strong">Free or discounted night rides</div>
        <p>Use the school-provided Lyft codes or safe-ride program instead of walking long
        distances alone late at night when that option is available.</p>
      </div>
      <div class="pill">
        <div class="pill-label">Campus shuttles</div>
        <div class="pill-strong">Shuttle stops and loops</div>
        <p>Check your campus transit map and see how your normal routes line up with shuttle
        stops, especially for late classes or study sessions that end after dark.</p>
      </div>
    </div>
  </section>

  <section class="section">
    <h2>Practical route tips</h2>
    <p>
      The map surfaces patterns, but your choices in the moment still matter. These are
      small habits that, combined with better route awareness, can reduce risk.
    </p>
    <div class="pill-list">
      <div class="pill">
        <div class="pill-label">Stay visible</div>
        <div class="pill-strong">Prefer lit, main corridors</div>
        <p>Even if a route is slightly longer, staying on well-lit, busier streets is often
        safer than cutting through empty alleys or isolated spaces.</p>
      </div>
      <div class="pill">
        <div class="pill-label">Travel together</div>
        <div class="pill-strong">Walk with a friend when you can</div>
        <p>Coordinate with friends, classmates, or roommates when leaving the library,
        campus events, or late-night study spots.</p>
      </div>
      <div class="pill">
        <div class="pill-label">Be reachable</div>
        <div class="pill-strong">Share your route if it helps</div>
        <p>Use your phone’s built-in location sharing with people you trust, especially if
        you’re taking an unfamiliar path or heading home late.</p>
      </div>
    </div>
  </section>

  <section class="section">
    <h2>Support after an incident</h2>
    <p>
      If you’ve experienced or witnessed an incident, safety is only one part of the story.
      Emotional, academic, and legal follow-up all matter.
    </p>
    <div class="pill-list">
      <div class="pill">
        <div class="pill-label">Counseling</div>
        <div class="pill-strong">Mental health and support services</div>
        <p>Most campuses have counseling centers that can help process what happened and
        connect you with long-term support.</p>
      </div>
      <div class="pill">
        <div class="pill-label">Reporting</div>
        <div class="pill-strong">Title IX / campus reporting offices</div>
        <p>For incidents like harassment or assault, campus reporting offices can explain
        your options, rights, and possible next steps.</p>
      </div>
    </div>
  </section>
</main>

<footer>SafeRoute — USC Campus Safety Visualization</footer>

</body>
</html>
