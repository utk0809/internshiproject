<?php
// index.php — SQL Injection Educational Demo — Landing Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SQL Injection — Educational Demo</title>
  <meta name="description" content="Learn how SQL Injection attacks work in a safe, simulated educational environment. Understand authentication bypass and how to prevent it." />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav class="navbar">
  <a class="navbar-brand" href="index.php">
    <span>SQL</span><em>INJECT</em>
  </a>
  <ul class="navbar-links">
    <li><a href="#intro">INTRO</a></li>
    <li><a href="#how-it-works">HOW IT WORKS</a></li>
    <li><a href="#objectives">OBJECTIVES</a></li>
    <li><a href="login.php" class="btn-outline-nav">TRY DEMO</a></li>
  </ul>
</nav>

<!-- ── Hero ────────────────────────────────────────────────── -->
<section class="hero">
  <span class="hero-label">⚠️ Educational Demo Only</span>
  <h1 class="gradient-text">HACK IN<br>PLAIN SIGHT</h1>
  <p>
    Understand how SQL Injection bypasses authentication in vulnerable systems.
    A fully simulated, safe environment for students and developers.
  </p>
  <div class="flex gap-2 flex-wrap justify-center">
    <a href="#intro" class="btn btn-primary">DISCOVER MORE</a>
    <a href="login.php" class="btn btn-outline" style="border:2px solid var(--purple);color:var(--purple);">OPEN DEMO →</a>
  </div>
</section>

<!-- ── Intro Section ────────────────────────────────────────── -->
<section class="section" id="intro">
  <h2 class="section-title gradient-text">What is SQL Injection?</h2>
  <span class="section-title-line"></span>

  <div class="card mb-2" style="margin-bottom:1.5rem;">
    <p style="font-size:1rem;line-height:1.8;">
      <strong>SQL Injection (SQLi)</strong> is one of the most critical and widespread web security vulnerabilities. 
      It occurs when <em>user-supplied input</em> is directly embedded into a SQL query without proper sanitisation, 
      allowing an attacker to manipulate the query's logic.
    </p>
    <p class="mt-2" style="font-size:0.95rem;color:var(--text-muted);">
      By injecting special SQL characters and logic, an attacker can:<br>
      <strong>bypass authentication</strong>, read sensitive database contents, modify or delete data, and even execute system commands on some databases.
    </p>
  </div>

  <div class="card-grid">
    <div class="info-card">
      <span class="icon">🎯</span>
      <h3>Authentication Bypass</h3>
      <p>The most common SQLi attack — tricking a login form into granting access without valid credentials.</p>
    </div>
    <div class="info-card">
      <span class="icon">📂</span>
      <h3>Data Exfiltration</h3>
      <p>Attackers use UNION-based or blind SQLi to extract entire database tables, including passwords and emails.</p>
    </div>
    <div class="info-card">
      <span class="icon">💣</span>
      <h3>Data Destruction</h3>
      <p>DROP TABLE or DELETE statements can be injected to wipe out entire databases in seconds.</p>
    </div>
    <div class="info-card">
      <span class="icon">🔐</span>
      <h3>Prevention</h3>
      <p>Parameterized queries, prepared statements, and input validation are the gold standard defences.</p>
    </div>
  </div>
</section>

<!-- ── How It Works ─────────────────────────────────────────── -->
<section style="background:rgba(124,58,237,.03);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
<div class="section" id="how-it-works">
  <h2 class="section-title gradient-text">How the Attack Works</h2>
  <span class="section-title-line"></span>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">
    <div>
      <div class="steps">
        <div class="step">
          <div class="step-num">1</div>
          <div class="step-body">
            <h4>The Vulnerable Query</h4>
            <p>A developer writes a login check that directly inserts user input into a SQL string.</p>
          </div>
        </div>
        <div class="step">
          <div class="step-num">2</div>
          <div class="step-body">
            <h4>Attacker Enters the Payload</h4>
            <p>Instead of a real username, the attacker types: <code style="color:var(--pink)">' OR 1=1 --</code></p>
          </div>
        </div>
        <div class="step">
          <div class="step-num">3</div>
          <div class="step-body">
            <h4>The Query is Broken</h4>
            <p>The single quote <code>'</code> closes the string. <code>OR 1=1</code> always evaluates to TRUE. <code>--</code> comments out the rest.</p>
          </div>
        </div>
        <div class="step">
          <div class="step-num">4</div>
          <div class="step-body">
            <h4>Access Granted!</h4>
            <p>The database returns the first user row, and the application thinks login succeeded — no password needed.</p>
          </div>
        </div>
      </div>
    </div>

    <div>
      <p style="font-size:0.82rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.6rem;">⚠️ VULNERABLE PHP CODE</p>
      <div class="code-block">
        <span class="code-label">PHP</span>
        <span class="kw">$username</span> = <span class="str">$_POST</span>[<span class="str">'username'</span>];<br>
        <span class="kw">$password</span> = <span class="str">$_POST</span>[<span class="str">'password'</span>];<br><br>
        <span class="kw">$sql</span> = <span class="str">"SELECT * FROM users<br>
        &nbsp;&nbsp;WHERE username = '<span class="inj">$username</span>'<br>
        &nbsp;&nbsp;AND password = '<span class="inj">$password</span>'"</span>;<br><br>
        <span class="cmt">// ⚠️ Directly using user input!</span><br>
        <span class="kw">$result</span> = mysqli_query(<span class="kw">$conn</span>, <span class="kw">$sql</span>);
      </div>

      <p style="font-size:0.82rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);margin:.8rem 0 .6rem;">💉 WITH INJECTION PAYLOAD</p>
      <div class="code-block">
        <span class="code-label">SQL</span>
        <span class="kw">SELECT</span> * <span class="kw">FROM</span> users<br>
        &nbsp;<span class="kw">WHERE</span> username = '<span class="inj">' OR 1=1 --</span>'<br>
        &nbsp;<span class="kw">AND</span> password = '<span class="str">anything</span>'<br><br>
        <span class="cmt">-- Result: 1=1 is always TRUE</span><br>
        <span class="cmt">-- The rest is commented out by --</span><br>
        <span class="cmt">-- Login BYPASSED ✅</span>
      </div>
    </div>
  </div>

  <div class="alert alert-warning mt-3">
    <span class="alert-icon">⚠️</span>
    <div>
      <strong>Educational Notice:</strong> All demonstrations on this site are <strong>100% simulated in PHP without a real database</strong>. 
      The goal is to understand the attack pattern so you can prevent it. Never use these techniques on real systems.
    </div>
  </div>

  <!-- ── Task 4: Advanced Visual Flow Animation Masterclass ────────────────────────── -->
  <div class="sql-flow-container mt-3" id="advanced-demo">
    
    <div style="text-align:center; margin-bottom: 2.5rem; position: relative; z-index: 2;">
      <span class="hero-label" style="background: rgba(236,72,153,.15); color: var(--pink); border-color: var(--pink);">Masterclass Module</span>
      <h3 style="font-size: 1.8rem; color: #fff; margin-top: 1rem; margin-bottom: 0.8rem; text-transform: uppercase; font-family: 'Orbitron', sans-serif; letter-spacing: 0.05em; text-shadow: 0 0 15px rgba(236,72,153,.4);">
        Anatomy of an Attack
      </h3>
      <p style="color: #94a3b8; font-size: 0.95rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
        Watch carefully as we dissect the mechanics of an authentication bypass. 
        Observe how raw string concatenation transforms data into operational code.
      </p>
    </div>

    <div style="margin-bottom: 3.5rem; text-align: center;">
      <img src="sqli_diagram_1773407391766.png" alt="SQL Injection Architectural Diagram" class="diagram-photo">
      <p class="photo-caption">Visual representation of an attacker overriding an authentication database via injection.</p>
    </div>

    <!-- Step 1 -->
    <div class="flow-step">
      <div class="flow-icon" style="border-color:#3b82f6; box-shadow:0 0 20px rgba(59,130,246,.4);">1</div>
      <div class="flow-box">
        <div class="flow-header">
          <span class="flow-title">Initialization</span>
          <span class="flow-badge" style="background:rgba(59,130,246,.15); color:#60a5fa; border-color:#3b82f6;">PHP Backend</span>
        </div>
        <div style="margin-bottom: 1rem;">
          <span class="kw">SELECT</span> * <span class="kw">FROM</span> users <span class="kw">WHERE</span> username='<span class="anim-typing delay-1" style="color:#64748b;">[USER_INPUT]</span>' <span class="kw">AND</span> password='<span class="anim-typing delay-1" style="color:#64748b;">[PASS_INPUT]</span>'<span class="kw">;</span>
        </div>
        <div class="tech-details" style="border-color:#3b82f6;">
          <strong style="color:#fff;">The Flaw:</strong> The system takes the empty template and naively waits for user input. Because there are no prepared statements, the database engine cannot distinguish between the <em>structure</em> of the query and the <em>data</em> being fed into it.
        </div>
      </div>
    </div>

    <div class="flow-connector connector-1"></div>

    <!-- Step 2 -->
    <div class="flow-step">
      <div class="flow-icon" style="border-color:var(--pink); background:rgba(236,72,153,.1); box-shadow:0 0 20px rgba(236,72,153,.4);">💉</div>
      <div class="flow-box">
        <div class="flow-header">
          <span class="flow-title" style="color:#f472b6;">The Injection</span>
          <span class="flow-badge" style="background:rgba(236,72,153,.15); color:#f472b6; border-color:var(--pink);">Attacker Console</span>
        </div>
        <div style="margin-bottom: 1rem;">
          <span class="kw">SELECT</span> * <span class="kw">FROM</span> users <span class="kw">WHERE</span> username='<span class="inj anim-typing delay-2" style="background:transparent; padding:0;">' OR 1=1 --</span>' <span class="kw">AND</span> password='<span class="str">anything</span>'<span class="kw">;</span>
        </div>
        <div class="tech-details" style="border-color:var(--pink);">
          The attacker deliberately inserts the payload: <code>' OR 1=1 --</code> into the username field. 
          <ul>
            <li>The first <code>'</code> artificially closes the username data string.</li>
            <li>The <code>OR 1=1</code> is injected directly into the SQL boolean logic.</li>
            <li>The <code>--</code> acts as a SQL comment indicator.</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="flow-connector connector-2"></div>

    <!-- Step 3 -->
    <div class="flow-step">
      <div class="flow-icon" style="border-color:var(--purple); box-shadow:0 0 20px rgba(124,58,237,.4);">⚙️</div>
      <div class="flow-box">
        <div class="flow-header">
          <span class="flow-title">Database Compilation</span>
          <span class="flow-badge">MySQL Engine</span>
        </div>
        <div style="margin-bottom: 1rem; position: relative;">
          <!-- Using the fadeout and highlight animations triggered via CSS delay -->
          <span class="kw">SELECT</span> * <span class="kw">FROM</span> users <span class="kw">WHERE</span> username='' <span class="anim-highlight">OR 1=1</span> <span class="anim-fadeout">--' AND password='anything';</span>
        </div>
        <div class="tech-details">
          As the database engine parses the concatenated raw string:
          <ul>
            <li><strong style="color:#f87171;">Structural Breakout:</strong> The engine interprets the attacker's string as legitimate SQL commands.</li>
            <li><strong style="color:#f87171;">Logic Alteration:</strong> <code>1=1</code> evaluates to <strong>TRUE</strong>. Because of the <code>OR</code> operator, the entire <code>WHERE</code> clause evaluates to <strong>TRUE</strong> for every single row in the database.</li>
            <li><strong style="color:#94a3b8;">Truncation:</strong> The <code>--</code> sequence instructs the syntax parser to permanently ignore everything that follows it, blinding the password check entirely.</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="flow-connector connector-3"></div>

    <!-- Step 4 -->
    <div class="flow-step">
      <div class="flow-icon" style="border-color:var(--green); background:rgba(16,185,129,.1); box-shadow:0 0 20px rgba(16,185,129,.4);">🔓</div>
      <div class="flow-box anim-pulse" style="border-color:rgba(16,185,129,.4); background: rgba(16,185,129,.05);">
        <div class="flow-header" style="border-bottom-color: rgba(16,185,129,.2);">
          <span class="flow-title" style="color:#34d399;">Execution & Bypass</span>
          <span class="flow-badge" style="background:rgba(16,185,129,.15); color:#34d399; border-color:var(--green);">Critical Breach</span>
        </div>
        <div style="text-align:center; padding: 1.5rem 0;">
          <h4 style="font-family:'Orbitron',sans-serif; color:var(--green); font-size:1.4rem; letter-spacing:0.05em; margin-bottom: 0.5rem;">ACCESS GRANTED</h4>
          <p style="color:#94a3b8; font-family:'Inter',sans-serif; font-size:0.95rem;">
            The query was treated as: <code style="background:rgba(0,0,0,.5); color:#a7f3d0; padding:4px 8px; border-radius:4px;">SELECT * FROM users WHERE TRUE;</code>
          </p>
        </div>
        <div class="tech-details" style="border-color:var(--green); background: rgba(0,0,0,.4);">
          With the condition always evaluating to TRUE, the database returns the entire user dataset. The application's login logic takes the first row of data (almost always the "Admin" or root user) and automatically logs the attacker into the administrative dashboard—without ever requiring a valid password.
        </div>
      </div>
    </div>

  </div>

</div>
</section>

<!-- ── Learning Objectives ──────────────────────────────────── -->
<section class="section" id="objectives">
  <h2 class="section-title gradient-text">Learning Objectives</h2>
  <span class="section-title-line"></span>

  <div class="card-grid">
    <div class="info-card" style="border-left:3px solid var(--purple);">
      <span class="icon">🧠</span>
      <h3>Understand SQLi Logic</h3>
      <p>Grasp why SQL strings constructed from user input are dangerous and how boolean logic is exploited.</p>
    </div>
    <div class="info-card" style="border-left:3px solid var(--pink);">
      <span class="icon">🔓</span>
      <h3>Authentication Bypass</h3>
      <p>See live how a login form can be completely circumvented without knowing any real credentials.</p>
    </div>
    <div class="info-card" style="border-left:3px solid #f59e0b;">
      <span class="icon">⚙️</span>
      <h3>Backend Validation</h3>
      <p>Understand why frontend checks are useless — only server-side parameterized queries truly protect you.</p>
    </div>
    <div class="info-card" style="border-left:3px solid var(--green);">
      <span class="icon">🛡️</span>
      <h3>Safe Demo Environment</h3>
      <p>Practice destructive SQL techniques safely, without risking any real data or system damage.</p>
    </div>
  </div>

  <div class="text-center mt-3" style="margin-top:3rem;">
    <p style="color:var(--text-muted);margin-bottom:1.2rem;">Ready to see SQL Injection in action?</p>
    <div class="flex gap-2 justify-center flex-wrap">
      <a href="login.php" class="btn btn-primary">💉 TRY VULNERABLE LOGIN</a>
      <a href="secure.php" class="btn btn-success">🛡️ TRY SECURE LOGIN</a>
    </div>
  </div>
</section>

<!-- ── Footer ───────────────────────────────────────────────── -->
<footer class="footer">
  <p>Built for <strong>educational purposes only</strong> · SQL Injection Demo · 
  <span style="color:var(--pink);">No real database used</span></p>
</footer>

</body>
</html>
