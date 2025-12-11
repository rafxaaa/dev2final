<?php require 'config.php'; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>SafeRoute — Recent Incidents</title>
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

  /* Incident cards */
  .incidents-list{
    display:flex;
    flex-direction:column;
    gap:16px;
    margin-bottom:32px;
  }

  .incident-card{
    border:1px solid #eee;
    border-radius:12px;
    padding:20px;
    background:#fff;
    transition:box-shadow .2s, border-color .2s;
  }

  .incident-card:hover{
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    border-color:#ddd;
  }

  .incident-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:12px;
    gap:16px;
  }

  .incident-title{
    font-size:18px;
    font-weight:600;
    color:#111;
    margin:0;
    flex:1;
  }

  .incident-date{
    font-size:13px;
    color:#777;
    white-space:nowrap;
  }

  .incident-location{
    font-size:14px;
    color:#555;
    margin-bottom:8px;
  }

  .incident-meta{
    display:flex;
    gap:16px;
    flex-wrap:wrap;
    font-size:12px;
    color:#888;
  }

  .incident-meta span{
    display:flex;
    align-items:center;
    gap:4px;
  }

  .badge{
    display:inline-block;
    padding:3px 8px;
    border-radius:4px;
    font-size:11px;
    font-weight:500;
    background:#f0f0f0;
    color:#555;
  }

  .badge.primary{
    background:#18a4ff;
    color:#fff;
  }

  /* Pagination */
  .pagination{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    margin-top:32px;
    padding:20px 0;
    border-top:1px solid #eee;
  }

  .pagination button{
    padding:8px 14px;
    border-radius:8px;
    border:1px solid #ddd;
    background:#fff;
    color:#111;
    font-size:13px;
    cursor:pointer;
    transition:all .2s;
  }

  .pagination button:hover:not(:disabled){
    background:#f5f5f5;
    border-color:#bbb;
  }

  .pagination button:disabled{
    opacity:0.4;
    cursor:not-allowed;
  }

  .pagination button.active{
    background:#111;
    color:#fff;
    border-color:#111;
  }

  .pagination-info{
    margin:0 16px;
    font-size:13px;
    color:#777;
  }

  .page-numbers{
    display:flex;
    gap:4px;
  }

  .empty-state{
    text-align:center;
    padding:60px 20px;
    color:#999;
  }

  .empty-state p{
    margin:8px 0;
    font-size:15px;
  }

  @media (max-width:720px){
    .nav-links{ display:none; }
    .incident-header{
      flex-direction:column;
    }
    .pagination{
      flex-wrap:wrap;
    }
    .page-numbers{
      order:3;
      width:100%;
      justify-content:center;
      margin-top:8px;
    }
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
    <h1>Recent Incidents</h1>
    <p class="subtitle">Browse recent crime and fire log entries from campus</p>

    <?php
    $api_url = 'https://rafaelv8.webdev.iyaserver.com/acad276/testM2.php';
    $per_page = 5;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $per_page;
    
    // Build query string
    $query_params = ['limit' => $per_page, 'offset' => $offset];
    $query_string = http_build_query($query_params);
    
    // Fetch data
    $ch = curl_init($api_url . '?' . $query_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $incidents = [];
    $total_count = 0;
    
    if ($http_code === 200 && $response) {
        $data = json_decode($response, true);
        if ($data && isset($data['rows'])) {
            $incidents = $data['rows'];
            // Estimate total (API might not return total count, so we'll use a workaround)
            // If we get fewer results than per_page, we're on the last page
            if (count($incidents) < $per_page) {
                $total_count = $offset + count($incidents);
            } else {
                // Fetch next page to check if there are more
                $next_offset = $offset + $per_page;
                $next_query = http_build_query(['limit' => 1, 'offset' => $next_offset]);
                $next_ch = curl_init($api_url . '?' . $next_query);
                curl_setopt($next_ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($next_ch, CURLOPT_TIMEOUT, 5);
                $next_response = curl_exec($next_ch);
                curl_close($next_ch);
                
                if ($next_response) {
                    $next_data = json_decode($next_response, true);
                    if ($next_data && isset($next_data['rows']) && count($next_data['rows']) > 0) {
                        // There are more results, estimate total
                        $total_count = $offset + $per_page + 1; // At least this many
                    } else {
                        $total_count = $offset + count($incidents);
                    }
                } else {
                    $total_count = $offset + count($incidents) + 1; // Unknown, assume more
                }
            }
        }
    }
    
    $total_pages = $total_count > 0 ? ceil($total_count / $per_page) : ($page + 1);
    ?>

    <?php if (empty($incidents)): ?>
      <div class="empty-state">
        <p>No incidents found.</p>
        <p><a href="map2.php">View incidents on the map</a></p>
      </div>
    <?php else: ?>
      <div class="incidents-list">
        <?php foreach ($incidents as $incident): ?>
          <div class="incident-card">
            <div class="incident-header">
              <h3 class="incident-title">
                <?php echo htmlspecialchars($incident['offense'] ?? 'Incident'); ?>
              </h3>
              <?php if (!empty($incident['date_reported'])): ?>
                <div class="incident-date">
                  <?php echo htmlspecialchars($incident['date_reported']); ?>
                </div>
              <?php endif; ?>
            </div>
            
            <?php if (!empty($incident['location']) || !empty($incident['address_for_map'])): ?>
              <div class="incident-location">
                📍 <?php echo htmlspecialchars($incident['location'] ?? $incident['address_for_map'] ?? ''); ?>
              </div>
            <?php endif; ?>
            
            <div class="incident-meta">
              <?php if (!empty($incident['disposition'])): ?>
                <span>
                  <span class="badge"><?php echo htmlspecialchars($incident['disposition']); ?></span>
                </span>
              <?php endif; ?>
              <span>
                <a href="map2.php" style="color:#18a4ff;text-decoration:underline;">View on map →</a>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <button 
          onclick="window.location.href='?page=1'" 
          <?php echo $page <= 1 ? 'disabled' : ''; ?>
        >
          First
        </button>
        <button 
          onclick="window.location.href='?page=<?php echo $page - 1; ?>'" 
          <?php echo $page <= 1 ? 'disabled' : ''; ?>
        >
          Prev
        </button>
        
        <div class="pagination-info">
          Page <?php echo $page; ?> 
          <?php if ($total_count > 0): ?>
            of <?php echo $total_pages; ?> 
            (Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_count); ?> of ~<?php echo $total_count; ?>)
          <?php endif; ?>
        </div>
        
        <div class="page-numbers">
          <?php
          // Show page numbers (max 5 pages around current)
          $start_page = max(1, $page - 2);
          $end_page = min($total_pages, $start_page + 4);
          
          if ($end_page - $start_page < 4) {
              $start_page = max(1, $end_page - 4);
          }
          
          for ($i = $start_page; $i <= $end_page; $i++):
          ?>
            <button 
              onclick="window.location.href='?page=<?php echo $i; ?>'"
              class="<?php echo $i === $page ? 'active' : ''; ?>"
            >
              <?php echo $i; ?>
            </button>
          <?php endfor; ?>
        </div>
        
        <button 
          onclick="window.location.href='?page=<?php echo $page + 1; ?>'" 
          <?php echo $page >= $total_pages ? 'disabled' : ''; ?>
        >
          Next
        </button>
        <button 
          onclick="window.location.href='?page=<?php echo $total_pages; ?>'" 
          <?php echo $page >= $total_pages ? 'disabled' : ''; ?>
        >
          Last
        </button>
      </div>
    <?php endif; ?>

  </main>

</body>
</html>

