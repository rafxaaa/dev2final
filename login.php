<?php
require 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: home.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = $mysqli->prepare(
            "SELECT user_id, full_name, password_hash, security_level
             FROM users
             WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($uid, $name, $hash, $level);

        if ($stmt->fetch()) {
            if (password_verify($pass, $hash)) {
                $_SESSION['user_id']        = $uid;
                $_SESSION['full_name']      = $name;
                $_SESSION['security_level'] = $level;
                header("Location: home.php");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>SafeRoute — Log in</title>
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

  /* NAV (same structure as home/map) */
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

  /* Auth layout */
  .auth-wrap{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:48px 16px 72px;
    background:
      radial-gradient(circle at 20% 20%, rgba(0,0,0,.03), transparent 60%),
      radial-gradient(circle at 80% 80%, rgba(0,0,0,.03), transparent 60%);
  }
  .auth-inner{
    width:100%;
    max-width:1100px;
    display:grid;
    grid-template-columns:minmax(0,1.2fr) minmax(0,1fr);
    gap:48px;
  }
  @media (max-width:880px){
    .auth-inner{
      grid-template-columns:1fr;
      gap:32px;
    }
  }

  .auth-copy h1{
    margin:0 0 10px;
    font-size:30px;
    letter-spacing:.02em;
  }
  .auth-copy p{
    margin:0;
    max-width:420px;
    font-size:15px;
    line-height:1.65;
    color:#555;
  }
  .auth-pill{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:4px 10px;
    border-radius:999px;
    border:1px solid #eee;
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.14em;
    color:#777;
    margin-bottom:14px;
  }
  .auth-pill-dot{
    width:7px;height:7px;border-radius:999px;
    background:#18a4ff;
  }

  .auth-card{
    background:#fff;
    border-radius:18px;
    border:1px solid #eee;
    box-shadow:0 16px 40px rgba(0,0,0,0.06);
    padding:24px 22px 22px;
  }
  .auth-card h2{
    margin:0 0 4px;
    font-size:20px;
  }
  .auth-card .sub{
    margin:0 0 18px;
    font-size:13px;
    color:#777;
  }

  form.auth-form{
    display:flex;
    flex-direction:column;
    gap:12px;
  }
  .field label{
    display:block;
    font-size:12px;
    font-weight:500;
    margin-bottom:4px;
    color:#444;
  }
  .field input{
    width:100%;
    border-radius:10px;
    border:1px solid #ddd;
    padding:9px 10px;
    font-size:13px;
    outline:none;
    transition:border-color .15s, box-shadow .15s;
  }
  .field input:focus{
    border-color:#18a4ff;
    box-shadow:0 0 0 1px rgba(24,164,255,0.25);
  }

  .auth-actions{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-top:10px;
    gap:12px;
  }
  .auth-actions button{
    border:none;
    border-radius:999px;
    padding:9px 20px;
    font-size:13px;
    font-weight:500;
    cursor:pointer;
    background:#111;
    color:#fff;
  }
  .auth-actions button:hover{
    background:#333;
  }
  .auth-actions a{
    font-size:12px;
    color:#555;
    text-decoration:underline;
    text-decoration-thickness:1px;
    text-underline-offset:2px;
  }

  .error-box{
    border-radius:10px;
    padding:10px 11px;
    background:#fff3f3;
    border:1px solid #f3b5b5;
    color:#a11b1b;
    font-size:12px;
    margin-bottom:12px;
  }
  .error-box p{
    margin:0;
  }

  footer{
    text-align:center;
    padding:18px 16px 28px;
    font-size:11px;
    color:#777;
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
      <?php if (isLoggedIn()): ?>
        <?php if (isAdmin()): ?>
          <a href="admin.php">Admin</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="signup.php">Sign up</a>
        <a href="login.php" class="active">Log in</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="auth-wrap">
  <div class="auth-inner">
    <section class="auth-copy">
      <div class="auth-pill">
        <span class="auth-pill-dot"></span>
        <span>Account access</span>
      </div>
      <h1>Pick up where you last felt safe.</h1>
      <p>
        Log into SafeRoute to keep using your preferred routes and filters, and to help us
        tune the experience for how you actually move around campus at night.
      </p>
    </section>

    <section class="auth-card">
      <h2>Log in</h2>
      <p class="sub">Use the email and password you created for your SafeRoute account.</p>

      <?php if ($errors): ?>
        <div class="error-box">
          <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
      <?php endif; ?>

      <form method="post" class="auth-form" autocomplete="off">
        <div class="field">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            required
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
          >
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            required
          >
        </div>

        <div class="auth-actions">
          <button type="submit">Log in</button>
          <a href="signup.php">Create an account instead</a>
        </div>
      </form>
    </section>
  </div>
</main>

<footer>SafeRoute — USC Campus Safety Visualization</footer>

</body>
</html>