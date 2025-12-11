<?php
require 'config.php';

// Require admin access
if (!isAdmin()) {
    header('Location: home.php');
    exit();
}

$current_user = getCurrentUser();
$page = $_GET['page'] ?? 'dashboard';

// Handle admin actions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_user_level') {
        $user_id = intval($_POST['user_id'] ?? 0);
        $new_level = intval($_POST['security_level'] ?? 0);
        
        $stmt = $mysqli->prepare("UPDATE users SET security_level = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $new_level, $user_id);
        if ($stmt->execute()) {
            $message = 'User security level updated successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error updating user security level.';
            $message_type = 'error';
        }
        $stmt->close();
    } elseif ($action === 'delete_user') {
        $user_id = intval($_POST['user_id'] ?? 0);
        // Don't allow deleting yourself
        if ($user_id != $current_user['id']) {
            $stmt = $mysqli->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $message = 'User deleted successfully.';
                $message_type = 'success';
            } else {
                $message = 'Error deleting user.';
                $message_type = 'error';
            }
            $stmt->close();
        } else {
            $message = 'You cannot delete your own account.';
            $message_type = 'error';
        }
    }
}

// Get all users for user management
$users = [];
$stmt = $mysqli->prepare("SELECT user_id, full_name, email, security_level FROM users ORDER BY user_id DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();
?>
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
<title>SafeRoute — Admin</title>
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

  .admin-container{
    max-width:var(--maxw);
    margin:0 auto;
    padding:32px 24px;
    flex:1;
  }

  .admin-header{
    margin-bottom:32px;
  }
  .admin-header h1{
    margin:0 0 8px;
    font-size:28px;
    letter-spacing:.02em;
  }
  .admin-header p{
    margin:0;
    color:#777;
    font-size:14px;
  }

  .admin-tabs{
    display:flex;
    gap:8px;
    border-bottom:1px solid #eee;
    margin-bottom:24px;
  }
  .admin-tabs a{
    padding:10px 16px;
    font-size:13px;
    color:#666;
    border-bottom:2px solid transparent;
    margin-bottom:-1px;
  }
  .admin-tabs a:hover{
    color:#111;
  }
  .admin-tabs a.active{
    color:#111;
    border-bottom-color:#18a4ff;
  }

  .message{
    padding:12px 16px;
    border-radius:8px;
    margin-bottom:20px;
    font-size:13px;
  }
  .message.success{
    background:#e8f5e9;
    color:#2e7d32;
    border:1px solid #c8e6c9;
  }
  .message.error{
    background:#ffebee;
    color:#c62828;
    border:1px solid #ffcdd2;
  }

  .admin-card{
    background:#fafafa;
    border:1px solid #eee;
    border-radius:12px;
    padding:20px;
    margin-bottom:20px;
  }
  .admin-card h2{
    margin:0 0 16px;
    font-size:18px;
  }

  table{
    width:100%;
    border-collapse:collapse;
    font-size:13px;
  }
  table th{
    text-align:left;
    padding:10px;
    border-bottom:2px solid #eee;
    font-weight:600;
    color:#666;
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.1em;
  }
  table td{
    padding:12px 10px;
    border-bottom:1px solid #f0f0f0;
  }
  table tr:hover{
    background:#fafafa;
  }

  .badge{
    display:inline-block;
    padding:3px 8px;
    border-radius:999px;
    font-size:11px;
    font-weight:500;
  }
  .badge.admin{
    background:#e3f2fd;
    color:#1976d2;
  }
  .badge.user{
    background:#f3e5f5;
    color:#7b1fa2;
  }

  .btn{
    padding:6px 12px;
    border-radius:6px;
    border:1px solid #ddd;
    background:#fff;
    font-size:12px;
    cursor:pointer;
    transition:.2s;
  }
  .btn:hover{
    background:#f5f5f5;
  }
  .btn.danger{
    border-color:#f44336;
    color:#f44336;
  }
  .btn.danger:hover{
    background:#ffebee;
  }

  select{
    padding:4px 8px;
    border:1px solid #ddd;
    border-radius:6px;
    font-size:12px;
  }

  .stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
    gap:16px;
    margin-bottom:32px;
  }
  .stat-card{
    background:#fff;
    border:1px solid #eee;
    border-radius:12px;
    padding:20px;
  }
  .stat-card h3{
    margin:0 0 8px;
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.1em;
    color:#777;
  }
  .stat-card .value{
    font-size:32px;
    font-weight:600;
    color:#111;
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
      <a href="resources.php">Resources</a>
      <a href="stats.php">Analytics</a>
      <a href="admin.php" class="active">Admin</a>
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="admin-container">
  <div class="admin-header">
    <h1>Admin Panel</h1>
    <p>Welcome, <?php echo htmlspecialchars($current_user['name']); ?> — Manage users and system settings</p>
  </div>

  <?php if ($message): ?>
    <div class="message <?php echo $message_type; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <div class="admin-tabs">
    <a href="?page=dashboard" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
    <a href="?page=users" class="<?php echo $page === 'users' ? 'active' : ''; ?>">User Management</a>
  </div>

  <?php if ($page === 'dashboard'): ?>
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Users</h3>
        <div class="value"><?php echo count($users); ?></div>
      </div>
      <div class="stat-card">
        <h3>Admins</h3>
        <div class="value"><?php echo count(array_filter($users, fn($u) => $u['security_level'] >= 1)); ?></div>
      </div>
      <div class="stat-card">
        <h3>Regular Users</h3>
        <div class="value"><?php echo count(array_filter($users, fn($u) => $u['security_level'] == 0)); ?></div>
      </div>
    </div>

    <div class="admin-card">
      <h2>Quick Actions</h2>
      <p style="margin:0;color:#666;font-size:13px;">
        Use the User Management tab to view and manage all registered users, update security levels, and remove accounts.
      </p>
    </div>

  <?php elseif ($page === 'users'): ?>
    <div class="admin-card">
      <h2>User Management</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Security Level</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo $user['user_id']; ?></td>
              <td><?php echo htmlspecialchars($user['full_name']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td>
                <span class="badge <?php echo $user['security_level'] >= 1 ? 'admin' : 'user'; ?>">
                  <?php echo $user['security_level'] >= 1 ? 'Admin' : 'User'; ?>
                </span>
              </td>
              <td>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="update_user_level">
                  <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                  <select name="security_level" onchange="this.form.submit()">
                    <option value="0" <?php echo $user['security_level'] == 0 ? 'selected' : ''; ?>>User (0)</option>
                    <option value="1" <?php echo $user['security_level'] == 1 ? 'selected' : ''; ?>>Admin (1)</option>
                  </select>
                </form>
                <?php if ($user['user_id'] != $current_user['id']): ?>
                  <form method="POST" style="display:inline;margin-left:8px;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    <button type="submit" class="btn danger">Delete</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</main>

<footer style="text-align:center;padding:18px 16px 28px;font-size:11px;color:#777;">
  SafeRoute — USC Campus Safety Visualization
</footer>

</body>
</html>

