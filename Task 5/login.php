<?php
// ── JSON Mock Database ──────────────
$users_db_file = __DIR__ . '/users.json';
$users_data = [];
if (file_exists($users_db_file)) {
    $users_data = json_decode(file_get_contents($users_db_file), true);
} else {
    // Fallback if JSON missing
    $users_data = [['username' => 'admin', 'password' => 'admin123']];
}

$result_type    = '';  
$result_title   = '';
$result_msg     = '';
$query_display  = '';
$explain        = false;
$submitted_user = '';
$submitted_pass = '';
$original_query = "SELECT * FROM users WHERE username='[username]' AND password='[password]';";
$found_users    = [];

// ── Process Form Submission ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_user = $_POST['username'] ?? '';
    $submitted_pass = $_POST['password'] ?? '';
    $secure_demo    = isset($_POST['secure_demo']);

    // Queries for Educational Display
    $original_query = "SELECT * FROM users WHERE username='[username]' AND password='[password]';";
    $query_display  = "SELECT * FROM users WHERE username='" . $submitted_user . "' AND password='" . $submitted_pass . "';";

    $found_users    = [];
    $is_injection   = false;
    $custom_msg     = '';

    if ($secure_demo) {
        // Simulate Prepared Statement Logic
        foreach ($users_data as $user) {
            if ($user['username'] === $submitted_user && $user['password'] === $submitted_pass) {
                $found_users[] = $user;
                break;
            }
        }
        $query_display = "SELECT * FROM users WHERE username = ? AND password = ?;";
    } else {
        $u_low = strtolower(trim($submitted_user));
        $p_low = strtolower(trim($submitted_pass));

        // ── SIMULATED SQL ENGINE ──
        $true_patterns = ["' or 1=1", "' or 'a'='a", "' or '1'='1", "' or true"];
        $has_true = false;
        foreach ($true_patterns as $tp) {
            if (strpos($u_low, $tp) !== false || strpos($p_low, $tp) !== false) {
                $has_true = true;
                break;
            }
        }

        if ($has_true) {
            $is_injection = true;
            $found_users = $users_data; 
            $custom_msg = "The 'OR 1=1' condition made the entire WHERE clause evaluate to TRUE. The database returned every single row it found.";
        } 
        elseif (strpos($u_low, "' --") !== false || strpos($u_low, "' #") !== false) {
            $is_injection = true;
            $user_part = trim(explode("'", $submitted_user)[0]);
            if ($user_part !== "") {
                foreach ($users_data as $user) {
                    if (strtolower($user['username']) === strtolower($user_part)) {
                        $found_users[] = $user;
                    }
                }
            } else {
                $found_users = $users_data;
            }
            $custom_msg = "The '--' or '#' symbols commented out the rest of the query, so the database never even checked the password.";
        }
        elseif (strpos($u_low, "' union select") !== false || strpos($p_low, "' union select") !== false) {
            $is_injection = true;
            $found_users = $users_data;
            $custom_msg = "A UNION SELECT statement was used to merge other table data (simulated as the entire user list here) into the results.";
        }
        else {
            foreach ($users_data as $user) {
                if ($user['username'] === $submitted_user && $user['password'] === $submitted_pass) {
                    $found_users[] = $user;
                    break;
                }
            }
        }
    }

    // Results Mapping
    if ($secure_demo) {
        $result_type  = 'secure';
        $result_title = '🛡️ SECURE: PREPARED STATEMENT';
        $result_msg   = "Input was treated as data, not code. Access denied.";
    } elseif ($is_injection) {
        $result_type  = 'bypass';
        $result_title = '⚠️ BYPASS SUCCESSFUL!';
        $result_msg   = $custom_msg;
        $explain      = true;
    } elseif (count($found_users) > 0) {
        $result_type  = 'granted';
        $result_title = '✅ ACCESS GRANTED';
        $result_msg   = 'Match found! Welcome back.';
    } elseif ($submitted_user !== '' || $submitted_pass !== '') {
        $result_type  = 'denied';
        $result_title = '❌ ACCESS DENIED';
        $result_msg   = 'Zero rows returned. Invalid credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vulnerable Login — SQL Injection Demo</title>
  <meta name="description" content="Simulated vulnerable login page to demonstrate SQL Injection authentication bypass." />
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Highlight injection in the query display */
    .inj-highlight { color:#f87171; background:rgba(239,68,68,.15); border-radius:3px; padding:0 2px; }
    .sql-kw  { color:#c084fc; font-weight:600; }
    .sql-str { color:#34d399; }
    .sql-cmt { color:#6b7280; }
    .hint-box {
      background:rgba(124,58,237,.06);
      border:1px dashed rgba(124,58,237,.3);
      border-radius:var(--radius-sm);
      padding:0.9rem 1.1rem;
      font-size:0.83rem;
      color:var(--text-muted);
      margin-bottom:1.2rem;
    }
    .hint-box code { color:var(--pink); background:rgba(236,72,153,.1); padding:1px 5px; border-radius:4px; }
    .explain-section {
      margin-top: 1.4rem;
      border-top: 1px solid var(--border);
      padding-top: 1.2rem;
    }
    .explain-section h4 {
      font-family: 'Orbitron', sans-serif;
      font-size: 0.85rem;
      color: var(--purple);
      margin-bottom: 0.8rem;
      letter-spacing: .05em;
    }
    .query-annotated { line-height: 2; }
  </style>
</head>
<body>


<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav class="navbar">
  <a class="navbar-brand" href="index.php">
    <span>SQL</span><em>INJECT</em>
  </a>
  <ul class="navbar-links">
    <li><a href="index.php">HOME</a></li>
    <li><a href="index.php#how-it-works">HOW IT WORKS</a></li>
    <li><a href="secure.php">SECURE VERSION</a></li>
    <li><a href="login.php" class="btn-outline-nav" style="color:var(--red)!important;border-color:var(--red);">⚠️ VULNERABLE</a></li>
  </ul>
</nav>

<!-- SQL Injection Attack Simulation Lab -->
<div class="login-wrapper" style="margin-bottom: 0; padding-bottom: 0;">
  <div class="attack-lab" style="width: 100%; max-width: 900px; margin: 2rem auto 0 auto;">
    <h2 class="section-title gradient-text" style="text-align:center;">SQL Injection Attack Simulation Lab</h2>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 2rem;">Click an attack button to automatically test common SQL Injection payloads.</p>
    
    <div class="attack-grid">
      <!-- 1️⃣ Username Auth Bypass -->
      <div class="attack-card">
        <h4>1️⃣ User Bypass</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">Payload: <code>' OR 1=1 --</code> in Username</p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('username').value='\' OR 1=1 --'; document.getElementById('password').value='anything';">💉 Pre-fill Attack</button>
      </div>
      
      <!-- 2️⃣ Password Auth Bypass -->
      <div class="attack-card" style="border-left-color: var(--purple);">
        <h4 style="color:var(--purple);">2️⃣ Pass Bypass</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">Payload: <code>' OR '1'='1</code> in Password</p>
        <button type="button" class="btn btn-danger btn-sm" style="background:var(--purple);" onclick="document.getElementById('username').value='admin'; document.getElementById('password').value='\' OR \'1\'=\'1';">💉 Pre-fill Attack</button>
      </div>
      
      <!-- 3️⃣ Comment Injection -->
      <div class="attack-card">
        <h4>3️⃣ Comment Inject</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">Payload: <code>admin' --</code></p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('username').value='admin\' --'; document.getElementById('password').value='wrong';">💉 Pre-fill Attack</button>
      </div>
      
      <!-- 4️⃣ Union Extraction -->
      <div class="attack-card">
        <h4>4️⃣ Data Leak</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">Payload: <code>' UNION SELECT...</code></p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('username').value='\' UNION SELECT username,password FROM users --'; document.getElementById('password').value='anything';">💉 Pre-fill Attack</button>
      </div>
      
      <!-- 5️⃣ Secure Query Example -->
      <div class="attack-card" style="border-left-color: var(--green);">
        <h4 style="color:var(--green);">5️⃣ Secure Demo</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">See it blocked!</p>
        <a href="secure.php" class="btn btn-success btn-sm" style="text-align: center; text-decoration: none;">🛡️ Open Safe Demo</a>
      </div>

      <!-- 6️⃣ Secure Query Example (Corrected) -->
      <div class="attack-card" style="border-left-color: var(--green);">
        <h4 style="color:var(--green);">6️⃣ Parameterized</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">See how it's done</p>
        <a href="secure.php" class="btn btn-success btn-sm" style="text-align: center; text-decoration: none;">🛡️ Open Safe Demo</a>
      </div>
    </div>
  </div>
</div>

<!-- ── Login Panel ──────────────────────────────────────────── -->
<div class="login-wrapper" style="padding-top: 0; min-height: auto;">
  <div style="width:100%;max-width:900px;display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">

    <!-- Left: Login Form -->
    <div class="login-card">
      <span class="login-badge">⚠️ Vulnerable System</span>
      <h2>Simulated Login</h2>
      <p class="subtitle">This form is intentionally vulnerable to SQL Injection for educational purposes.</p>

      <!-- Hint box -->
      <div class="hint-box">
        💡 Hardcoded credentials: <code>admin</code> / <code>admin123</code><br>
        💉 Try injection: <code>' OR 1=1 --</code> as username
      </div>

      <!-- Quick-fill buttons -->
      <div class="quick-fill" style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('username').value='admin'; document.getElementById('password').value='admin123';">✅ Normal Login</button>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('username').value='\' OR 1=1 --'; document.getElementById('password').value='anything';">💉 SQL Inject</button>
        <button type="button" class="btn btn-sm" style="background:#6b7280;color:#fff;" onclick="document.getElementById('username').value='wrong_user'; document.getElementById('password').value='wrong_pass';">❌ Wrong Creds</button>
      </div>

      <!-- Main login form -->
      <form method="post" action="login.php">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username"
                 placeholder="Enter username or payload…"
                 value="<?php echo htmlspecialchars($submitted_user, ENT_QUOTES); ?>" />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password"
                 placeholder="Enter password…" />
        </div>
        <button type="submit" class="btn btn-primary w-full">LOGIN →</button>
      </form>

      <!-- Result Panel -->
      <?php if ($result_type): ?>
      <div class="result-panel show <?php echo $result_type; ?>">
        <div class="result-title"><?php echo $result_title; ?></div>
        <div style="font-size:0.88rem; color:var(--text-muted); line-height:1.5;">
            <?php echo $result_msg; ?>
        </div>

        <?php if ($explain): ?>
        <div class="explain-section">
          <h4>💡 EDUCATIONAL BREAKDOWN</h4>
          <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:.6rem;">The injection payload "broke out" of the SQL string, changing the query logic:</p>
          <div class="query-box" style="font-size: 0.73rem; background: rgba(0,0,0,0.2);">
            <span class="sql-kw">SELECT</span> * <span class="sql-kw">FROM</span> users 
            <span class="sql-kw">WHERE</span> username = '<span class="inj-highlight"><?php echo htmlspecialchars($submitted_user); ?></span>' 
            <span class="sql-kw">AND</span> password = '<span class="inj-highlight"><?php echo htmlspecialchars($submitted_pass); ?></span>'
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="right-side">
      <div class="card">
        <h3 style="font-size:0.9rem;margin-bottom:.8rem;color:var(--purple); text-transform:uppercase;">SQL Execution Trace</h3>
        
        <div class="mb-3">
            <p style="font-size:0.72rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:0.3rem;">1. Original Query Template</p>
            <div class="query-box" style="opacity:0.7; font-size: 0.75rem;"><?php echo $original_query; ?></div>
        </div>

        <div class="mb-3">
            <p style="font-size:0.72rem; font-weight:700; color:var(--pink); text-transform:uppercase; margin-bottom:0.3rem;">2. Injected Query String</p>
            <div class="query-box" style="font-size: 0.75rem;">
              <?php
              $display = htmlspecialchars($query_display, ENT_QUOTES);
              $display = preg_replace('/\b(SELECT|FROM|WHERE|AND|OR|UNION)\b/', '<span class="sql-kw">$1</span>', $display);
              // Highlight the user inputs
              if (!$secure_demo && ($submitted_user !== '' || $submitted_pass !== '')) {
                  if ($submitted_user !== '') $display = str_replace(htmlspecialchars($submitted_user), "<span class='inj-highlight'>".htmlspecialchars($submitted_user)."</span>", $display);
                  if ($submitted_pass !== '') $display = str_replace(htmlspecialchars($submitted_pass), "<span class='inj-highlight'>".htmlspecialchars($submitted_pass)."</span>", $display);
              }
              echo $display;
              ?>
            </div>
        </div>

        <?php if (count($found_users) > 0 || $submitted_user !== '' || $submitted_pass !== ''): ?>
        <div class="mb-3">
            <p style="font-size:0.72rem; font-weight:700; color:var(--green); text-transform:uppercase; margin-bottom:0.3rem;">3. Data Results (Returned Rows)</p>
            <?php if (count($found_users) > 0): ?>
                <div class="results-table-wrapper">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>username</th>
                                <th>password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($found_users as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><code style="color:var(--text-muted);"><?php echo htmlspecialchars($row['password']); ?></code></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size:0.7rem; color:var(--green); margin-top:0.4rem;">✔ <?php echo count($found_users); ?> row(s) returned.</p>
            <?php else: ?>
                <div class="alert alert-danger" style="padding:0.6rem 1rem; margin:0; font-size:0.75rem;">
                    Empty result set. (0 rows returned)
                </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
          <span class="alert-icon">ℹ️</span>
          <div style="font-size:0.8rem;">Submit the form to see the SQL execution and returned data results.</div>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>



<footer class="footer">
  <p>Built for <strong>educational purposes only</strong> · SQL Injection Demo · <span style="color:var(--pink);">No real database used</span></p>
</footer>

</body>
</html>
