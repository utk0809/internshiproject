<?php
// ── Hardcoded credentials (simulated "database") ──────────────
define('VALID_USER', 'admin');
define('VALID_PASS', 'admin123');

$result_type   = '';
$result_title  = '';
$result_msg    = '';
$blocked_query = '';
$submitted_user = '';
$submitted_pass = '';

// ── Secure Login Logic (parameterized simulation) ─────────────
// A real secure system uses PDO prepared statements:
//   $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
//   $stmt->execute([$username, $password]);
// We simulate the same: the user input is NEVER inserted into a SQL string.
// Special characters are treated as plain text, not SQL code.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_user = $_POST['username'] ?? '';
    $submitted_pass = $_POST['password'] ?? '';

    // Show what the parameterized query looks like:
    // The input is bound separately — never concatenated
    $blocked_query = "SELECT * FROM users WHERE username = ? AND password = ?";
    // Values bound separately (shown below in the UI)

    // Secure check: strict equality only, input is treated as pure data
    if ($submitted_user === VALID_USER && $submitted_pass === VALID_PASS) {
        $result_type  = 'granted';
        $result_title = '✅ ACCESS GRANTED';
        $result_msg   = 'Correct credentials matched. Logged in as <strong>admin</strong>.';
    } else {
        $result_type  = 'denied';
        $result_title = '❌ ACCESS DENIED';
        // Check if it looks like the attacker tried SQLi
        $sqli_patterns = ["' or 1=1", "' or '1'='1", "admin'--", "' or 'x'='x"];
        $u_lower = strtolower(trim($submitted_user));
        $attempted_sqli = false;
        foreach ($sqli_patterns as $p) {
            if (strpos($u_lower, strtolower($p)) !== false) { $attempted_sqli = true; break; }
        }
        if ($attempted_sqli) {
            $result_msg = '🛡️ SQL Injection attempt <strong>blocked!</strong> The payload was treated as a plain text string, not SQL syntax. Parameterized queries prevent this attack entirely.';
        } else {
            $result_msg = 'Invalid credentials. The parameterized query returned no matching rows.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Secure Login — SQL Injection Demo</title>
  <meta name="description" content="Secure login page demonstrating how parameterized queries prevent SQL Injection." />
  <link rel="stylesheet" href="style.css" />
  <style>
    .param-tag {
      display:inline-block;
      background:rgba(16,185,129,.12);
      color:var(--green);
      border:1px solid rgba(16,185,129,.3);
      border-radius:4px;
      padding:1px 6px;
      font-family:'Fira Code',monospace;
      font-size:0.82rem;
    }
    .compare-row {
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:1.5rem;
      margin-top:2.5rem;
    }
    @media(max-width:700px){ .compare-row{grid-template-columns:1fr;} }
    .compare-block {
      border-radius:var(--radius);
      padding:1.5rem;
      border:1px solid var(--border);
    }
    .compare-block.vuln { background:rgba(239,68,68,.04); border-color:rgba(239,68,68,.25); }
    .compare-block.safe { background:rgba(16,185,129,.04); border-color:rgba(16,185,129,.25); }
    .compare-block h3 {
      font-family:'Orbitron',sans-serif;
      font-size:0.9rem;
      margin-bottom:1rem;
      padding-bottom:.5rem;
      border-bottom:2px solid;
    }
    .compare-block.vuln h3 { color:var(--red); border-color:rgba(239,68,68,.3); }
    .compare-block.safe h3 { color:var(--green); border-color:rgba(16,185,129,.3); }
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
    <li><a href="login.php">VULNERABLE VERSION</a></li>
    <li><a href="secure.php" class="btn-outline-nav" style="color:var(--green)!important;border-color:var(--green)!important;">🛡️ SECURE</a></li>
  </ul>
</nav>

<!-- SQL Injection Attack Simulation Lab -->
<div class="login-wrapper" style="margin-bottom: 0; padding-bottom: 0;">
  <div class="attack-lab" style="width: 100%; max-width: 900px; margin: 2rem auto 0 auto;">
    <h2 class="section-title gradient-text" style="text-align:center;">Secure System Attack Lab</h2>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 2rem;">Pre-fill common SQL Injection payloads to see how parameterized queries neutralize them.</p>
    
    <div class="attack-grid">
      <!-- 1️⃣ Username Auth Bypass -->
      <div class="attack-card">
        <h4>1️⃣ User Bypass</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">Payload: <code>' OR 1=1 --</code></p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('s_username').value='\' OR 1=1 --'; document.getElementById('s_password').value='anything';">💉 Pre-fill Attack</button>
      </div>
      
      <!-- 2️⃣ Password Auth Bypass -->
      <div class="attack-card" style="border-left-color: var(--purple);">
        <h4 style="color:var(--purple);">2️⃣ Pass Bypass</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">Payload: <code>' OR '1'='1</code></p>
        <button type="button" class="btn btn-danger btn-sm" style="background:var(--purple);" onclick="document.getElementById('s_username').value='admin'; document.getElementById('s_password').value='\' OR \'1\'=\'1';">💉 Pre-fill Attack</button>
      </div>
      
      <!-- 3️⃣ Comment Injection -->
      <div class="attack-card">
        <h4>3️⃣ Comment Inject</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">Payload: <code>admin' --</code></p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('s_username').value='admin\' --'; document.getElementById('s_password').value='wrong';">💉 Pre-fill Attack</button>
      </div>
      
      <!-- 4️⃣ Union Extraction -->
      <div class="attack-card">
        <h4>4️⃣ Data Leak</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">Payload: <code>' UNION SELECT...</code></p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('s_username').value='\' UNION SELECT username,password FROM users --'; document.getElementById('s_password').value='anything';">💉 Pre-fill Attack</button>
      </div>
      
      <!-- 5️⃣ Normal Auth Example -->
      <div class="attack-card" style="border-left-color: var(--green);">
        <h4 style="color:var(--green);">5️⃣ Valid User</h4>
        <p style="font-size:0.75rem; color:var(--text-muted);">Payload: <code>admin123</code></p>
        <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('s_username').value='admin'; document.getElementById('s_password').value='admin123';">✅ Pre-fill Valid</button>
      </div>
    </div>
  </div>
</div>

<!-- ── Login Panel ──────────────────────────────────────────── -->
<div class="login-wrapper" style="padding-top: 0; min-height: auto;">
  <div style="width:100%;max-width:900px;display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">

    <!-- Left: Secure Login Form -->
    <div class="login-card" style="border-color:rgba(16,185,129,.3);">
      <span class="login-badge secure-badge">🛡️ Secure System</span>
      <h2>Secure Login</h2>
      <p class="subtitle">This form uses <strong>parameterized queries</strong> to safely handle user input.</p>

      <div class="hint-box" style="background:rgba(16,185,129,.06);border-color:rgba(16,185,129,.25);">
        💡 Try the same SQL injection payload: <code style="color:var(--green);">' OR 1=1 --</code><br>
        Watch it get completely blocked! ✅
      </div>

      <form method="post" action="secure.php">
        <div class="form-group">
          <label for="s_username">Username</label>
          <input type="text" id="s_username" name="username"
                 placeholder="Enter username…"
                 value="<?php echo htmlspecialchars($submitted_user, ENT_QUOTES); ?>" />
        </div>
        <div class="form-group">
          <label for="s_password">Password</label>
          <input type="password" id="s_password" name="password"
                 placeholder="Enter password…" />
        </div>
        <button type="submit" class="btn btn-success w-full">LOGIN →</button>
      </form>

      <!-- Result Panel -->
      <?php if ($result_type): ?>
      <div class="result-panel show <?php echo $result_type; ?>">
        <div class="result-title"><?php echo $result_title; ?></div>
        <p><?php echo $result_msg; ?></p>
        <?php if ($result_type === 'denied' && strpos($result_msg, 'Injection') !== false): ?>
        <div style="margin-top:.8rem;padding-top:.8rem;border-top:1px solid rgba(16,185,129,.2);">
          <p style="font-size:0.82rem;color:var(--green);"><strong>How it was blocked:</strong></p>
          <p style="font-size:0.82rem;color:var(--text-muted);">The <code style="color:#c084fc">?</code> placeholder kept the query structure fixed. The payload <code style="color:#f87171">' OR 1=1 --</code> was passed as a literal string value — not SQL code — to the database engine, which searched for a user literally named <code>' OR 1=1 --</code>. No such user exists → access denied.</p>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Right: Explanation & Code Comparison -->
    <div>
      <div class="card" style="border-color:rgba(16,185,129,.2);">
        <h3 style="font-size:1rem;margin-bottom:.8rem;color:var(--green);">🛡️ How Parameterized Queries Work</h3>
        <p style="font-size:0.87rem;color:var(--text-muted);margin-bottom:1rem;">
          The query structure is compiled <em>before</em> user input is bound. 
          The database treats bound values as <strong>pure data</strong>, never as SQL syntax.
        </p>

        <p style="font-size:0.82rem;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--green);margin-bottom:.4rem;">Secure PHP Code</p>
        <div class="code-block" style="margin-bottom:1rem;">
          <span class="code-label">PHP / PDO</span>
          <span class="kw">$stmt</span> = <span class="kw">$pdo</span>->prepare(<br>
          &nbsp;&nbsp;<span class="str">"SELECT * FROM users<br>
          &nbsp;&nbsp;&nbsp;WHERE username = <span style="color:#34d399">?</span><br>
          &nbsp;&nbsp;&nbsp;AND password = <span style="color:#34d399">?</span>"</span><br>
          );<br>
          <span class="kw">$stmt</span>->execute([<span class="kw">$username</span>, <span class="kw">$password</span>]);<br>
          <span class="cmt">// ✅ Input is NEVER part of the SQL string</span>
        </div>

        <?php if ($blocked_query): ?>
        <p style="font-size:0.82rem;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.4rem;">Query Template (Fixed Structure):</p>
        <div class="query-box">
          <span style="color:#c084fc">SELECT</span> * <span style="color:#c084fc">FROM</span> users<br>
          &nbsp;<span style="color:#c084fc">WHERE</span> username = <span class="param-tag">?</span><br>
          &nbsp;<span style="color:#c084fc">AND</span> password = <span class="param-tag">?</span>
        </div>
        <p style="font-size:0.8rem;color:var(--text-muted);margin-top:.6rem;">
          Bound values: <span class="param-tag"><?php echo htmlspecialchars($submitted_user, ENT_QUOTES); ?></span>
          &nbsp;&nbsp;<span class="param-tag">[password]</span>
        </p>
        <div class="alert alert-success" style="margin-top:.8rem;">
          <span class="alert-icon">✅</span>
          <div>The <code>?</code> placeholders keep SQL structure fixed. Even if the user types SQL characters, they are escaped and treated as plain text data by the database engine.</div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
          <span class="alert-icon">ℹ️</span>
          <div>Submit the form above to see how the parameterized query handles your input safely.</div>
        </div>
        <?php endif; ?>

        <div style="margin-top:1.2rem;border-top:1px solid var(--border);padding-top:1rem;">
          <p style="font-size:0.82rem;color:var(--text-muted);">See what happens on a <strong>vulnerable</strong> system with the same payload?</p>
          <a href="login.php" class="btn btn-danger btn-sm" style="margin-top:.6rem;display:inline-block;">⚠️ Try Vulnerable Login →</a>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ── Side-by-side Comparison ─────────────────────────────── -->
<div style="background:rgba(124,58,237,.03);border-top:1px solid var(--border);">
<div class="section">
  <h2 class="section-title gradient-text">Vulnerable vs. Secure</h2>
  <span class="section-title-line"></span>

  <div class="compare-row">
    <div class="compare-block vuln">
      <h3>❌ VULNERABLE (String Concatenation)</h3>
      <div class="code-block">
        <span class="code-label">PHP</span>
        <span class="kw">$sql</span> = <span class="str">"SELECT * FROM users<br>
        &nbsp;WHERE username='<span class="inj">$username</span>'<br>
        &nbsp;AND password='<span class="inj">$password</span>'"</span>;<br><br>
        <span class="cmt">// Input injected directly → DANGER</span>
      </div>
      <div class="alert alert-danger" style="margin-top:.8rem;">
        <span class="alert-icon">💉</span>
        <div>User input becomes part of SQL code. Attacker can break out of the string context.</div>
      </div>
    </div>
    <div class="compare-block safe">
      <h3>✅ SECURE (Parameterized Query)</h3>
      <div class="code-block">
        <span class="code-label">PHP / PDO</span>
        <span class="kw">$stmt</span> = <span class="kw">$pdo</span>->prepare(<br>
        &nbsp;&nbsp;<span class="str">"SELECT * FROM users<br>
        &nbsp;&nbsp;&nbsp;WHERE username = ?<br>
        &nbsp;&nbsp;&nbsp;AND password = ?"</span>);<br>
        <span class="kw">$stmt</span>->execute([<span class="kw">$u</span>, <span class="kw">$p</span>]);
      </div>
      <div class="alert alert-success" style="margin-top:.8rem;">
        <span class="alert-icon">🛡️</span>
        <div>Input bound separately. Special characters are automatically escaped — no injection possible.</div>
      </div>
    </div>
  </div>

  <div class="secure-coding-section" style="margin-top:3rem;">
    <h2 class="section-title gradient-text" style="text-align:center; font-size:1.8rem; margin-bottom: 2rem;">🛡️ Secure Coding Section</h2>
    
    <div class="card mb-3" style="border-left:4px solid var(--green);">
      <h3 style="color:var(--green); font-size:1.2rem; margin-bottom:0.8rem; display:flex; align-items:center; gap:10px;">
        <span class="icon">🧹</span> 1. Input Validation
      </h3>
      <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:1rem; line-height:1.6;">
        Input validation is your application's first line of defense. Before any user-supplied data reaches your database logic, it must be rigorously checked against expected formats, lengths, and types. Implementing <strong>allow-listing</strong> (only accepting explicitly safe input like alphanumeric characters for a username) is far more secure than block-listing (attempting to filter out 'bad' characters). Validation ensures that garbage or malicious data never even touches your database engine.
      </p>
      <div class="code-block">
        <span class="code-label">PHP Example: Filtering Input</span>
        <span class="cmt">// Ensure the input matches an expected email format</span><br>
        <span class="kw">$email</span> = filter_input(INPUT_POST, <span class="str">'email'</span>, FILTER_VALIDATE_EMAIL);<br>
        <span class="kw">if</span> (<span class="kw">$email</span> === false) {<br>
        &nbsp;&nbsp;<span class="kw">die</span>(<span class="str">"Invalid email format"</span>);<br>
        }
      </div>
    </div>

    <div class="card mb-3" style="border-left:4px solid var(--green);">
      <h3 style="color:var(--green); font-size:1.2rem; margin-bottom:0.8rem; display:flex; align-items:center; gap:10px;">
        <span class="icon">🔌</span> 2. Parameterized Queries
      </h3>
      <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:0.5rem; line-height:1.6;">
        If input validation is the first line of defense, parameterized queries are the ultimate fail-safe. Using parameterization completely separates the <strong>logic</strong> of your SQL statement from the <strong>data</strong> supplied by the user. 
      </p>
      <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:1rem; line-height:1.6;">
        Instead of dynamically concatenating variables into a single SQL string (as seen in the vulnerable version), the query is sent to the database with placeholders like <code>?</code>. The user-provided data is then sent completely separately. Because of this strict separation, it is mathematically impossible for user data to break out and alter the SQL structure.
      </p>
    </div>

    <div class="card mb-3" style="border-left:4px solid var(--green);">
      <h3 style="color:var(--green); font-size:1.2rem; margin-bottom:0.8rem; display:flex; align-items:center; gap:10px;">
        <span class="icon">🔒</span> 3. Prepared Statements
      </h3>
      <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:1rem; line-height:1.6;">
        Prepared statements are the mechanism by which parameterized queries are implemented in drivers like PDO or MySQLi. When you use a prepared statement, the database compiles and optimizes the SQL template <em>before</em> any user data is bound to it. By completing the query's structural phase early, the database strictly treats any subsequent variables as literal string data. If an attacker inputs <code>' OR 1=1 --</code>, the database simply interprets it as a bizarrely named user, neutralizing the attack completely.
      </p>
      <div class="code-block">
        <span class="code-label">PHP PDO Example</span>
        <span class="kw">$stmt</span> = <span class="kw">$pdo</span>->prepare(<span class="str">"SELECT id FROM users WHERE username = ?"</span>);<br>
        <span class="kw">$stmt</span>->execute([<span class="kw">$_POST</span>[<span class="str">'username'</span>]]);<br>
        <span class="kw">$user</span> = <span class="kw">$stmt</span>->fetch();
      </div>
    </div>
  </div>
</div>
</div>

<footer class="footer">
  <p>Built for <strong>educational purposes only</strong> · SQL Injection Demo · <span style="color:var(--green);">Parameterized queries protect you</span></p>
</footer>

</body>
</html>
