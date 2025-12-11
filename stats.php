<?php require 'config.php'; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>SafeRoute — Analytics</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
  .nav-links a.active{opacity:1;color:#111}

  /* Main content */
  .main-content{
    flex:1;
    max-width:var(--maxw);
    margin:0 auto;
    padding:48px 24px;
    width:100%;
  }

  h1{
    margin:0 0 8px;
    font-size:28px;
    letter-spacing:.02em;
  }

  .subtitle{
    margin:0 0 32px;
    color:#777;
    font-size:15px;
  }

  .stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));
    gap:24px;
    margin-bottom:48px;
  }

  .stat-card{
    background:#fff;
    border:1px solid #eee;
    border-radius:14px;
    padding:20px;
  }

  .stat-card h3{
    margin:0 0 8px;
    font-size:13px;
    text-transform:uppercase;
    letter-spacing:.1em;
    color:#777;
    font-weight:600;
  }

  .stat-value{
    font-size:36px;
    font-weight:700;
    color:#111;
    margin:0;
  }

  .stat-label{
    font-size:12px;
    color:#999;
    margin-top:4px;
  }

  .chart-container{
    background:#fff;
    border:1px solid #eee;
    border-radius:14px;
    padding:24px;
    margin-bottom:32px;
  }

  .chart-container h2{
    margin:0 0 20px;
    font-size:18px;
    font-weight:600;
  }

  .chart-wrapper{
    position:relative;
    height:300px;
  }

  @media (max-width:720px){
    .stats-grid{
      grid-template-columns:1fr;
    }
    .nav-links{ display:none; }
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
      <a href="stats.php" class="active">Analytics</a>
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

  <main class="main-content">
    <h1>SafeRoute Analytics</h1>
    <p class="subtitle">User behavior insights and route preferences</p>

    <?php
    // Get statistics
    $stats = [];
    $tables_exist = true;
    
    // Check if tables exist, if not show setup message
    $table_check = $mysqli->query("SHOW TABLES LIKE 'route_requests'");
    if ($table_check->num_rows == 0) {
        $tables_exist = false;
    }
    
    if ($tables_exist) {
        // Total route requests
        $result = $mysqli->query("SELECT COUNT(*) as total FROM route_requests");
        $stats['total_routes'] = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
    
    // Safe route requests
    $result = $mysqli->query("SELECT COUNT(*) as total FROM route_requests WHERE route_type = 'safe'");
    $stats['safe_routes'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Fastest route requests
    $result = $mysqli->query("SELECT COUNT(*) as total FROM route_requests WHERE route_type = 'fastest'");
    $stats['fastest_routes'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Walking vs Driving
    $result = $mysqli->query("SELECT COUNT(*) as total FROM route_requests WHERE travel_mode = 'walking'");
    $stats['walking_routes'] = $result->fetch_assoc()['total'] ?? 0;
    
    $result = $mysqli->query("SELECT COUNT(*) as total FROM route_requests WHERE travel_mode = 'driving'");
    $stats['driving_routes'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Total searches
    $result = $mysqli->query("SELECT COUNT(*) as total FROM search_queries");
    $stats['total_searches'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Unique users
    $result = $mysqli->query("SELECT COUNT(DISTINCT user_id) as total FROM route_requests WHERE user_id IS NOT NULL");
    $stats['unique_users'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Routes by day (last 30 days)
    $routes_by_day = [];
    $result = $mysqli->query("
        SELECT DATE(created_at) as day, COUNT(*) as count 
        FROM route_requests 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(created_at)
        ORDER BY day ASC
    ");
    while ($row = $result->fetch_assoc()) {
        $routes_by_day[] = $row;
    }
    
    // Route type breakdown
    $route_types = [];
    $result = $mysqli->query("
        SELECT route_type, COUNT(*) as count 
        FROM route_requests 
        GROUP BY route_type
    ");
    while ($row = $result->fetch_assoc()) {
        $route_types[] = $row;
    }
    
    // Travel mode breakdown
    $travel_modes = [];
    $result = $mysqli->query("
        SELECT travel_mode, COUNT(*) as count 
        FROM route_requests 
        GROUP BY travel_mode
    ");
    while ($row = $result->fetch_assoc()) {
        $travel_modes[] = $row;
    }
    
    // Top search terms
    $top_searches = [];
    $result = $mysqli->query("
        SELECT search_term, COUNT(*) as count 
        FROM search_queries 
        WHERE search_term IS NOT NULL AND search_term != ''
        GROUP BY search_term 
        ORDER BY count DESC 
        LIMIT 10
    ");
    while ($row = $result->fetch_assoc()) {
        $top_searches[] = $row;
    }
    } else {
        // Tables don't exist - show setup message
        $stats = [
            'total_routes' => 0,
            'safe_routes' => 0,
            'fastest_routes' => 0,
            'walking_routes' => 0,
            'driving_routes' => 0,
            'total_searches' => 0,
            'unique_users' => 0
        ];
        $routes_by_day = [];
        $route_types = [];
        $travel_modes = [];
        $top_searches = [];
    }
    ?>
    
    <?php if (!$tables_exist): ?>
    <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:10px;padding:16px;margin-bottom:24px;">
      <strong>Setup Required:</strong> The tracking tables haven't been created yet. 
      Please run <code>setup_tracking.php</code> once to create the necessary database tables.
    </div>
    <?php endif; ?>

    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Route Requests</h3>
        <p class="stat-value"><?php echo number_format($stats['total_routes']); ?></p>
        <p class="stat-label">All time</p>
      </div>
      
      <div class="stat-card">
        <h3>Safe Route Requests</h3>
        <p class="stat-value"><?php echo number_format($stats['safe_routes']); ?></p>
        <p class="stat-label"><?php echo $stats['total_routes'] > 0 ? round(($stats['safe_routes'] / $stats['total_routes']) * 100) : 0; ?>% of all routes</p>
      </div>
      
      <div class="stat-card">
        <h3>Walking Routes</h3>
        <p class="stat-value"><?php echo number_format($stats['walking_routes']); ?></p>
        <p class="stat-label"><?php echo ($stats['walking_routes'] + $stats['driving_routes']) > 0 ? round(($stats['walking_routes'] / ($stats['walking_routes'] + $stats['driving_routes'])) * 100) : 0; ?>% prefer walking</p>
      </div>
      
      <div class="stat-card">
        <h3>Total Searches</h3>
        <p class="stat-value"><?php echo number_format($stats['total_searches']); ?></p>
        <p class="stat-label">Crime log searches</p>
      </div>
      
      <div class="stat-card">
        <h3>Active Users</h3>
        <p class="stat-value"><?php echo number_format($stats['unique_users']); ?></p>
        <p class="stat-label">Users who requested routes</p>
      </div>
    </div>

    <div class="chart-container">
      <h2>Route Requests Over Time (Last 30 Days)</h2>
      <div class="chart-wrapper">
        <canvas id="routesOverTimeChart"></canvas>
      </div>
    </div>

    <div class="chart-container">
      <h2>Route Type Preference</h2>
      <div class="chart-wrapper">
        <canvas id="routeTypeChart"></canvas>
      </div>
    </div>

    <div class="chart-container">
      <h2>Travel Mode Preference</h2>
      <div class="chart-wrapper">
        <canvas id="travelModeChart"></canvas>
      </div>
    </div>

    <?php if (!empty($top_searches)): ?>
    <div class="chart-container">
      <h2>Top Search Terms</h2>
      <div class="chart-wrapper">
        <canvas id="searchTermsChart"></canvas>
      </div>
    </div>
    <?php endif; ?>

  </main>

<script>
// Routes over time chart
const routesOverTimeData = <?php echo json_encode($routes_by_day); ?>;
const routesOverTimeLabels = routesOverTimeData.map(d => {
  const date = new Date(d.day);
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});
const routesOverTimeCounts = routesOverTimeData.map(d => parseInt(d.count));

new Chart(document.getElementById('routesOverTimeChart'), {
  type: 'line',
  data: {
    labels: routesOverTimeLabels,
    datasets: [{
      label: 'Route Requests',
      data: routesOverTimeCounts,
      borderColor: '#18a4ff',
      backgroundColor: 'rgba(24, 164, 255, 0.1)',
      tension: 0.4,
      fill: true
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});

// Route type chart
const routeTypeData = <?php echo json_encode($route_types); ?>;
const routeTypeLabels = routeTypeData.map(d => d.route_type.charAt(0).toUpperCase() + d.route_type.slice(1));
const routeTypeCounts = routeTypeData.map(d => parseInt(d.count));

new Chart(document.getElementById('routeTypeChart'), {
  type: 'doughnut',
  data: {
    labels: routeTypeLabels,
    datasets: [{
      data: routeTypeCounts,
      backgroundColor: ['#18ff9c', '#18a4ff']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});

// Travel mode chart
const travelModeData = <?php echo json_encode($travel_modes); ?>;
const travelModeLabels = travelModeData.map(d => d.travel_mode.charAt(0).toUpperCase() + d.travel_mode.slice(1));
const travelModeCounts = travelModeData.map(d => parseInt(d.count));

new Chart(document.getElementById('travelModeChart'), {
  type: 'bar',
  data: {
    labels: travelModeLabels,
    datasets: [{
      label: 'Route Requests',
      data: travelModeCounts,
      backgroundColor: ['#18a4ff', '#18ff9c']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});

<?php if (!empty($top_searches)): ?>
// Top search terms chart
const searchTermsData = <?php echo json_encode($top_searches); ?>;
const searchTermsLabels = searchTermsData.map(d => d.search_term.length > 20 ? d.search_term.substring(0, 20) + '...' : d.search_term);
const searchTermsCounts = searchTermsData.map(d => parseInt(d.count));

new Chart(document.getElementById('searchTermsChart'), {
  type: 'bar',
  data: {
    labels: searchTermsLabels,
    datasets: [{
      label: 'Searches',
      data: searchTermsCounts,
      backgroundColor: '#18a4ff'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: 'y',
    plugins: {
      legend: { display: false }
    },
    scales: {
      x: { beginAtZero: true }
    }
  }
});
<?php endif; ?>
</script>
</body>
</html>

