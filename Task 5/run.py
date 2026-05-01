"""
run.py — SQL Injection Educational Demo Server
Uses Python's built-in http.server (NO external libraries needed).
Just run:  python run.py
"""

import http.server
import urllib.parse
import webbrowser
import threading
import os

PORT = 8080
VALID_USER = "admin"
VALID_PASS = "admin123"

# ── SQLi pattern detection (mirrors the PHP logic) ───────────
SQLI_PATTERNS = [
    "' or 1=1", "' or '1'='1", "' or 1=1 --", "' or 1=1--",
    "admin'--", "' or 'x'='x", "or 1=1", "1=1",
]

def is_sqli(value: str) -> bool:
    v = value.lower().strip()
    return any(p in v for p in SQLI_PATTERNS)


# ══════════════════════════════════════════════════════════════
#  HTML TEMPLATES
# ══════════════════════════════════════════════════════════════

NAVBAR = """
<nav class="navbar">
  <a class="navbar-brand" href="/index.html">
    <span>SQL</span><em>INJECT</em>
  </a>
  <ul class="navbar-links">
    {links}
  </ul>
</nav>
"""

FOOTER = """
<footer class="footer">
  <p>Built for <strong>educational purposes only</strong> &middot; SQL Injection Demo &middot;
  <span style="color:var(--pink);">No real database used</span></p>
</footer>"""

HEAD = lambda title, extra="": f"""<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>{title}</title>
  <link rel="stylesheet" href="/style.css"/>
  {extra}
</head>
<body>"""

# ── INDEX PAGE ───────────────────────────────────────────────
def page_index():
    nav = NAVBAR.format(links="""
        <li><a href="/index.html#intro">INTRO</a></li>
        <li><a href="/index.html#how-it-works">HOW IT WORKS</a></li>
        <li><a href="/index.html#objectives">OBJECTIVES</a></li>
        <li><a href="/login" class="btn-outline-nav">TRY DEMO</a></li>
    """)
    return HEAD("SQL Injection — Educational Demo") + nav + """
<section class="hero">
  <span class="hero-label">&#9888;&#65039; Educational Demo Only</span>
  <h1 class="gradient-text">HACK IN<br>PLAIN SIGHT</h1>
  <p>Understand how SQL Injection bypasses authentication in vulnerable systems.
     A fully simulated, safe environment for students and developers.</p>
  <div class="flex gap-2 flex-wrap justify-center">
    <a href="#intro" class="btn btn-primary">DISCOVER MORE</a>
    <a href="/login" class="btn btn-outline" style="border:2px solid var(--purple);color:var(--purple);">OPEN DEMO &rarr;</a>
  </div>
</section>

<section class="section" id="intro">
  <h2 class="section-title gradient-text">What is SQL Injection?</h2>
  <span class="section-title-line"></span>
  <div class="card mb-2" style="margin-bottom:1.5rem;">
    <p style="font-size:1rem;line-height:1.8;">
      <strong>SQL Injection (SQLi)</strong> is one of the most critical and widespread web security vulnerabilities.
      It occurs when <em>user-supplied input</em> is directly embedded into a SQL query without proper sanitisation,
      allowing an attacker to manipulate the query&#39;s logic.
    </p>
    <p class="mt-2" style="font-size:0.95rem;color:var(--text-muted);">
      By injecting special SQL characters and logic, an attacker can:<br>
      <strong>bypass authentication</strong>, read sensitive database contents, modify or delete data, and even execute commands.
    </p>
  </div>
  <div class="card-grid">
    <div class="info-card">
      <span class="icon">&#127919;</span>
      <h3>Authentication Bypass</h3>
      <p>The most common SQLi attack &mdash; tricking a login form into granting access without valid credentials.</p>
    </div>
    <div class="info-card">
      <span class="icon">&#128194;</span>
      <h3>Data Exfiltration</h3>
      <p>Attackers use UNION-based or blind SQLi to extract entire database tables, including passwords and emails.</p>
    </div>
    <div class="info-card">
      <span class="icon">&#128163;</span>
      <h3>Data Destruction</h3>
      <p>DROP TABLE or DELETE statements can be injected to wipe out entire databases in seconds.</p>
    </div>
    <div class="info-card">
      <span class="icon">&#128272;</span>
      <h3>Prevention</h3>
      <p>Parameterized queries, prepared statements, and input validation are the gold standard defences.</p>
    </div>
  </div>
</section>

<section style="background:rgba(124,58,237,.03);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
<div class="section" id="how-it-works">
  <h2 class="section-title gradient-text">How the Attack Works</h2>
  <span class="section-title-line"></span>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">
    <div>
      <div class="steps">
        <div class="step"><div class="step-num">1</div>
          <div class="step-body"><h4>The Vulnerable Query</h4>
          <p>A developer writes a login check that directly inserts user input into a SQL string.</p></div>
        </div>
        <div class="step"><div class="step-num">2</div>
          <div class="step-body"><h4>Attacker Enters the Payload</h4>
          <p>Instead of a real username, the attacker types: <code style="color:var(--pink)">' OR 1=1 --</code></p></div>
        </div>
        <div class="step"><div class="step-num">3</div>
          <div class="step-body"><h4>The Query is Broken</h4>
          <p>The single quote closes the string. <code>OR 1=1</code> always evaluates TRUE. <code>--</code> comments out the rest.</p></div>
        </div>
        <div class="step"><div class="step-num">4</div>
          <div class="step-body"><h4>Access Granted!</h4>
          <p>The database returns the first user row, and the application thinks login succeeded — no password needed.</p></div>
        </div>
      </div>
    </div>
    <div>
      <p style="font-size:.82rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.6rem;">&#9888;&#65039; VULNERABLE CODE</p>
      <div class="code-block">
        <span class="code-label">Python/PHP</span>
        <span class="kw">username</span> = request.form[<span class="str">'username'</span>]<br>
        <span class="kw">password</span> = request.form[<span class="str">'password'</span>]<br><br>
        <span class="kw">sql</span> = <span class="str">f"SELECT * FROM users<br>
        &nbsp; WHERE username = '<span class="inj">{username}</span>'<br>
        &nbsp; AND password = '<span class="inj">{password}</span>'"</span><br><br>
        <span class="cmt"># &#9888;&#65039; Directly using user input!</span>
      </div>
      <p style="font-size:.82rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);margin:.8rem 0 .6rem;">&#128137; WITH INJECTION PAYLOAD</p>
      <div class="code-block">
        <span class="code-label">SQL</span>
        <span class="kw">SELECT</span> * <span class="kw">FROM</span> users<br>
        &nbsp;<span class="kw">WHERE</span> username = '<span class="inj">' OR 1=1 --</span>'<br>
        &nbsp;<span class="kw">AND</span> password = '<span class="str">anything</span>'<br><br>
        <span class="cmt">-- 1=1 is always TRUE</span><br>
        <span class="cmt">-- Rest is commented out by --</span><br>
        <span class="cmt">-- Login BYPASSED &#9989;</span>
      </div>
    </div>
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
          <span class="flow-badge" style="background:rgba(59,130,246,.15); color:#60a5fa; border-color:#3b82f6;">Python/PHP Backend</span>
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
      <div class="flow-icon" style="border-color:var(--pink); background:rgba(236,72,153,.1); box-shadow:0 0 20px rgba(236,72,153,.4);">&#128137;</div>
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
          <span class="flow-badge">MySQL/SQLite Engine</span>
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
      <div class="flow-icon" style="border-color:var(--green); background:rgba(16,185,129,.1); box-shadow:0 0 20px rgba(16,185,129,.4);">&#128275;</div>
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

  <div class="alert alert-warning mt-3">
    <span class="alert-icon">&#9888;&#65039;</span>
    <div><strong>Educational Notice:</strong> All demonstrations are <strong>100% simulated with no real database</strong>.
    Never use these techniques on real systems without permission.</div>
  </div>
</div>
</section>

<section class="section" id="objectives">
  <h2 class="section-title gradient-text">Learning Objectives</h2>
  <span class="section-title-line"></span>
  <div class="card-grid">
    <div class="info-card" style="border-left:3px solid var(--purple);">
      <span class="icon">&#129504;</span><h3>Understand SQLi Logic</h3>
      <p>Grasp why SQL strings constructed from user input are dangerous and how boolean logic is exploited.</p>
    </div>
    <div class="info-card" style="border-left:3px solid var(--pink);">
      <span class="icon">&#128275;</span><h3>Authentication Bypass</h3>
      <p>See live how a login form can be circumvented without knowing any real credentials.</p>
    </div>
    <div class="info-card" style="border-left:3px solid #f59e0b;">
      <span class="icon">&#9881;&#65039;</span><h3>Backend Validation</h3>
      <p>Understand why frontend checks are useless — only server-side parameterized queries truly protect you.</p>
    </div>
    <div class="info-card" style="border-left:3px solid var(--green);">
      <span class="icon">&#128737;&#65039;</span><h3>Safe Demo Environment</h3>
      <p>Practice understanding attack patterns safely, without risking any real data.</p>
    </div>
  </div>
  <div class="text-center mt-3" style="margin-top:3rem;">
    <p style="color:var(--text-muted);margin-bottom:1.2rem;">Ready to see SQL Injection in action?</p>
    <div class="flex gap-2 justify-center flex-wrap">
      <a href="/login" class="btn btn-primary">&#128137; TRY VULNERABLE LOGIN</a>
      <a href="/secure" class="btn btn-success">&#128737;&#65039; TRY SECURE LOGIN</a>
    </div>
  </div>
</section>
""" + FOOTER + "</body></html>"


# ── LOGIN PAGE ────────────────────────────────────────────────
def page_login(username="", password="", result_type="", result_title="", result_msg="", explain=False, query_display=""):
    nav = NAVBAR.format(links="""
        <li><a href="/index.html">HOME</a></li>
        <li><a href="/index.html#how-it-works">HOW IT WORKS</a></li>
        <li><a href="/secure">SECURE VERSION</a></li>
        <li><a href="/login" class="btn-outline-nav" style="color:var(--red)!important;border-color:var(--red)!important;">&#9888;&#65039; VULNERABLE</a></li>
    """)

    result_html = ""
    if result_type:
        explain_html = ""
        if explain:
            explain_html = """
            <div class="explain-section">
              <h4>&#128161; WHY DID THIS WORK?</h4>
              <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:.6rem;">The injected string broke out of the SQL string and added a condition that is always TRUE:</p>
              <div class="query-box query-annotated">
                <span class="sql-kw">SELECT</span> * <span class="sql-kw">FROM</span> users<br>
                &nbsp;<span class="sql-kw">WHERE</span> username = '<span class="inj-highlight">' OR 1=1 --</span>'<br>
                &nbsp;<span class="sql-kw">AND</span> password = '<span class="sql-str">anything</span>'
                <span class="sql-cmt">&nbsp;&larr; commented out!</span>
              </div>
              <p style="font-size:.8rem;color:var(--text-muted);margin-top:.7rem;">
                <code style="color:#c084fc">'</code> closes the string &middot;
                <code style="color:#34d399">OR 1=1</code> is always TRUE &middot;
                <code style="color:#6b7280">--</code> discards the password check
              </p>
            </div>"""

        result_html = f"""
        <div class="result-panel show {result_type}">
          <div class="result-title">{result_title}</div>
          <p>{result_msg}</p>
          {explain_html}
        </div>"""

    # Query display in right panel
    query_panel = """<div class="alert alert-info">
      <span class="alert-icon">&#8505;&#65039;</span>
      <div>Submit the form above and the resulting SQL query will appear here in real-time.</div>
    </div>"""

    # Results data simulation
    results_html = ""
    if result_type and result_type != "denied":
        rows = []
        if result_type == "bypass" or result_type == "granted":
            # Just simulate a couple of users for the table
            rows = [
                {"user": "admin", "pass": "admin123"},
                {"user": "guest", "pass": "guest123"}
            ]
        
        if rows:
            table_rows = "".join([f"<tr><td>{r['user']}</td><td><code>{r['pass']}</code></td></tr>" for r in rows])
            results_html = f"""
            <div class="mb-3">
                <p style="font-size:0.72rem; font-weight:700; color:var(--green); text-transform:uppercase; margin-bottom:0.3rem;">3. Data Results (Returned Rows)</p>
                <div class="results-table-wrapper">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>username</th>
                                <th>password</th>
                            </tr>
                        </thead>
                        <tbody>
                            {table_rows}
                        </tbody>
                    </table>
                </div>
                <p style="font-size:0.7rem; color:var(--green); margin-top:0.4rem;">✔ {len(rows)} row(s) returned.</p>
            </div>"""
    elif result_type == "denied":
        results_html = """
        <div class="mb-3">
            <p style="font-size:0.72rem; font-weight:700; color:var(--green); text-transform:uppercase; margin-bottom:0.3rem;">3. Data Results (Returned Rows)</p>
            <div class="alert alert-danger" style="padding:0.6rem 1rem; margin:0; font-size:0.75rem;">
                Empty result set. (0 rows returned)
            </div>
        </div>"""

    if query_display:
        import html as html_lib
        q = html_lib.escape(query_display)
        # Highlight SQL keywords
        for kw in ["SELECT", "FROM", "WHERE", "AND", "OR", "UNION"]:
            q = q.replace(kw, f'<span class="sql-kw">{kw}</span>')

        alert_html = ""
        if result_type == "bypass":
            # Highlight injection in query
            # We want to highlight the submitted user/pass if they caused injection
            u_esc = html_lib.escape(username)
            p_esc = html_lib.escape(password)
            if u_esc and u_esc in q: q = q.replace(u_esc, f'<span class="inj-highlight">{u_esc}</span>')
            if p_esc and p_esc in q: q = q.replace(p_esc, f'<span class="inj-highlight">{p_esc}</span>')
            
            alert_html = """<div class="alert alert-danger" style="margin-top:.8rem;">
              <span class="alert-icon">&#128137;</span>
              <div><strong>Injection Detected!</strong> The red text above is attacker-controlled input that escaped the string context and altered the query logic.</div>
            </div>"""
        elif result_type == "granted":
            alert_html = """<div class="alert alert-success" style="margin-top:.8rem;">
              <span class="alert-icon">&#9989;</span>
              <div><strong>Legitimate login.</strong> Correct username and password matched the hardcoded credentials.</div>
            </div>"""
        elif result_type == "denied":
            alert_html = """<div class="alert alert-info" style="margin-top:.8rem;">
              <span class="alert-icon">&#128683;</span>
              <div><strong>No rows returned.</strong> The WHERE clause found no matching user &mdash; login refused.</div>
            </div>"""

        query_panel = f"""
        <h3 style="font-size:0.9rem;margin-bottom:.8rem;color:var(--purple); text-transform:uppercase;">SQL Execution Trace</h3>
        <div class="mb-3">
            <p style="font-size:0.72rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:0.3rem;">1. Original Query Template</p>
            <div class="query-box" style="opacity:0.7; font-size: 0.75rem;">SELECT * FROM users WHERE username='[username]' AND password='[password]';</div>
        </div>
        <div class="mb-3">
            <p style="font-size:0.72rem; font-weight:700; color:var(--pink); text-transform:uppercase; margin-bottom:0.3rem;">2. Injected Query String</p>
            <div class="query-box" style="font-size: 0.75rem;">{q}</div>
        </div>
        {results_html}
        {alert_html}"""

    u_safe = username.replace('"', '&quot;')

    return HEAD("Vulnerable Login — SQL Injection Demo", """
    <style>
      .inj-highlight{color:#f87171;background:rgba(239,68,68,.15);border-radius:3px;padding:0 2px;}
      .sql-kw{color:#c084fc;font-weight:600;} .sql-str{color:#34d399;} .sql-cmt{color:#6b7280;}
      .hint-box{background:rgba(124,58,237,.06);border:1px dashed rgba(124,58,237,.3);border-radius:var(--radius-sm);padding:.9rem 1.1rem;font-size:.83rem;color:var(--text-muted);margin-bottom:1.2rem;}
      .hint-box code{color:var(--pink);background:rgba(236,72,153,.1);padding:1px 5px;border-radius:4px;}
      .explain-section{margin-top:1.4rem;border-top:1px solid var(--border);padding-top:1.2rem;}
      .explain-section h4{font-family:'Orbitron',sans-serif;font-size:.85rem;color:var(--purple);margin-bottom:.8rem;letter-spacing:.05em;}
      .query-annotated{line-height:2;}
      /* Compact Attack Lab Override */
      .attack-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; margin-top: 1rem; }
      .attack-card { background: rgba(124,58,237,.03); border: 1px solid rgba(236,72,153,0.15); border-left: 3px solid var(--pink); border-radius: var(--radius-sm); padding: 0.75rem; display: flex; flex-direction: column; justify-content: space-between; gap: 0.6rem; }
      .attack-card h4 { color: var(--pink); font-size: 0.85rem; margin: 0; font-family: 'Orbitron', sans-serif; }
      .attack-card p { font-size: 0.65rem; color: var(--text-muted); margin: 0.2rem 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
      .attack-card .btn { width: 100%; padding: 0.4rem; font-size: 0.7rem; }
    </style>""") + nav + f"""
<div class="login-wrapper" style="margin-bottom: 0; padding-bottom: 0;">
  <div class="attack-lab" style="width: 100%; max-width: 900px; margin: 1rem auto 0 auto;">
    <h2 class="section-title gradient-text" style="text-align:center; font-size: 1.5rem;">SQL Injection Attack Simulation Lab</h2>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">Click an attack button to automatically test common SQL Injection payloads.</p>
    
    <div class="attack-grid">
      <div class="attack-card">
        <h4>1️⃣ User Bypass</h4>
        <p>Payload: ' OR 1=1 --</p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('username').value='\\' OR 1=1 --'; document.getElementById('password').value='anything';">💉 Pre-fill Attack</button>
      </div>
      <div class="attack-card" style="border-left-color: var(--purple);">
        <h4 style="color:var(--purple);">2️⃣ Pass Bypass</h4>
        <p>Payload: ' OR '1'='1'</p>
        <button type="button" class="btn btn-danger btn-sm" style="background:var(--purple);" onclick="document.getElementById('username').value='admin'; document.getElementById('password').value='\\' OR \\'1\\'=\\'1';">💉 Pre-fill Attack</button>
      </div>
      <div class="attack-card">
        <h4>3️⃣ Comment Inject</h4>
        <p>Payload: admin' --</p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('username').value='admin\\' --'; document.getElementById('password').value='wrong';">💉 Pre-fill Attack</button>
      </div>
      <div class="attack-card">
        <h4>4️⃣ Data Leak</h4>
        <p>Payload: ' UNION SELECT...</p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('username').value='\\' UNION SELECT username,password FROM users --'; document.getElementById('password').value='anything';">💉 Pre-fill Attack</button>
      </div>
      <div class="attack-card" style="border-left-color: var(--green);">
        <h4 style="color:var(--green);">5️⃣ Secure Demo</h4>
        <p>See it blocked!</p>
        <a href="/secure" class="btn btn-success btn-sm" style="text-align: center; text-decoration: none;">🛡️ Open Safe Demo</a>
      </div>
      <div class="attack-card" style="border-left-color: var(--green);">
        <h4 style="color:var(--green);">6️⃣ Parameterized</h4>
        <p>How it's done</p>
        <a href="/secure" class="btn btn-success btn-sm" style="text-align: center; text-decoration: none;">🛡️ Open Safe Demo</a>
      </div>
    </div>
  </div>
</div>
<div class="login-wrapper" style="padding-top: 0; min-height: auto;">
  <div style="width:100%;max-width:900px;display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">

    <div class="login-card">
      <span class="login-badge">&#9888;&#65039; Vulnerable System</span>
      <h2>Simulated Login</h2>
      <p class="subtitle">This form is intentionally vulnerable to SQL Injection for educational purposes.</p>
      <div class="hint-box">
        &#128161; Credentials: <code>admin</code> / <code>admin123</code><br>
        &#128137; Try injection: <code>' OR 1=1 --</code> as username
      </div>
      <div class="quick-fill">
        <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('username').value='admin'; document.getElementById('password').value='admin123';">&#9989; Normal Login</button>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('username').value='\\' OR 1=1 --'; document.getElementById('password').value='anything';">&#128137; SQL Inject</button>
        <button type="button" class="btn btn-sm" style="background:#6b7280;color:#fff;" onclick="document.getElementById('username').value='wrong_user'; document.getElementById('password').value='wrong_pass';">&#10060; Wrong Creds</button>
      </div>
      <form method="post" action="/login">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter username or payload&hellip;" value="{u_safe}"/>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter password&hellip;"/>
        </div>
        <button type="submit" class="btn btn-primary w-full">LOGIN &rarr;</button>
      </form>
      {result_html}
    </div>

    <div>
      <div class="card">
        <h3 style="font-size:1rem;margin-bottom:.8rem;color:var(--purple);">&#128269; Under the Hood</h3>
        <p style="font-size:.87rem;color:var(--text-muted);margin-bottom:1rem;">
          A vulnerable application builds its SQL query by directly concatenating user input:
        </p>
        <div class="code-block" style="margin-bottom:1rem;">
          <span class="code-label">VULNERABLE CODE</span>
          <span class="kw">sql</span> = <span class="str">f"SELECT * FROM users<br>
          &nbsp; WHERE username = '<span class="inj">{{username}}</span>'<br>
          &nbsp; AND password = '<span class="inj">{{password}}</span>'"</span>
        </div>
        {query_panel}
        <div style="margin-top:1.2rem;border-top:1px solid var(--border);padding-top:1rem;">
          <p style="font-size:.82rem;color:var(--text-muted);">Want to see how a <strong>secure</strong> system handles the same payload?</p>
          <a href="/secure" class="btn btn-success btn-sm" style="margin-top:.6rem;display:inline-block;">&#128737;&#65039; Try Secure Version &rarr;</a>
        </div>
      </div>
    </div>

  </div>
</div>
""" + FOOTER + "</body></html>"


# ── SECURE PAGE ───────────────────────────────────────────────
def page_secure(username="", password="", result_type="", result_title="", result_msg="", attempted_sqli=False):
    nav = NAVBAR.format(links="""
        <li><a href="/index.html">HOME</a></li>
        <li><a href="/index.html#how-it-works">HOW IT WORKS</a></li>
        <li><a href="/login">VULNERABLE VERSION</a></li>
        <li><a href="/secure" class="btn-outline-nav" style="color:var(--green)!important;border-color:var(--green)!important;">&#128737;&#65039; SECURE</a></li>
    """)

    result_html = ""
    if result_type:
        blocked_detail = ""
        if attempted_sqli and result_type == "denied":
            blocked_detail = """
            <div style="margin-top:.8rem;padding-top:.8rem;border-top:1px solid rgba(16,185,129,.2);">
              <p style="font-size:.82rem;color:var(--green);"><strong>How it was blocked:</strong></p>
              <p style="font-size:.82rem;color:var(--text-muted);">The <code style="color:#c084fc">?</code> placeholder kept the query structure fixed.
              The payload <code style="color:#f87171">' OR 1=1 --</code> was passed as a <em>literal string value</em> &mdash;
              not SQL code &mdash; to the database engine. No such user exists &rarr; access denied.</p>
            </div>"""

        result_html = f"""
        <div class="result-panel show {result_type}">
          <div class="result-title">{result_title}</div>
          <p>{result_msg}</p>
          {blocked_detail}
        </div>"""

    import html as html_lib
    u_safe = html_lib.escape(username)

    param_display = ""
    if username:
        param_display = f"""
        <p style="font-size:.82rem;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--text-muted);margin-bottom:.4rem;">Query Template (Fixed Structure):</p>
        <div class="query-box">
          <span style="color:#c084fc">SELECT</span> * <span style="color:#c084fc">FROM</span> users<br>
          &nbsp;<span style="color:#c084fc">WHERE</span> username = <span class="param-tag">?</span><br>
          &nbsp;<span style="color:#c084fc">AND</span> password = <span class="param-tag">?</span>
        </div>
        <p style="font-size:.8rem;color:var(--text-muted);margin-top:.6rem;">
          Bound values: <span class="param-tag">{u_safe}</span>&nbsp;&nbsp;<span class="param-tag">[password]</span>
        </p>
        <div class="alert alert-success" style="margin-top:.8rem;">
          <span class="alert-icon">&#9989;</span>
          <div>The <code>?</code> placeholders keep SQL structure fixed. Input is escaped and treated as plain text data.</div>
        </div>"""
    else:
        param_display = """<div class="alert alert-info">
          <span class="alert-icon">&#8505;&#65039;</span>
          <div>Submit the form to see how the parameterized query handles your input safely.</div>
        </div>"""

    return HEAD("Secure Login — SQL Injection Demo", """
    <style>
      .param-tag{display:inline-block;background:rgba(16,185,129,.12);color:var(--green);border:1px solid rgba(16,185,129,.3);border-radius:4px;padding:1px 6px;font-family:'Fira Code',monospace;font-size:.82rem;}
      .compare-row{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:2rem;}
      @media(max-width:700px){.compare-row{grid-template-columns:1fr;}}
      .compare-block{border-radius:var(--radius);padding:1.5rem;border:1px solid var(--border);}
      .compare-block.vuln{background:rgba(239,68,68,.04);border-color:rgba(239,68,68,.25);}
      .compare-block.safe{background:rgba(16,185,129,.04);border-color:rgba(16,185,129,.25);}
      .compare-block h3{font-family:'Orbitron',sans-serif;font-size:.9rem;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:2px solid;}
      .compare-block.vuln h3{color:var(--red);border-color:rgba(239,68,68,.3);}
      .compare-block.safe h3{color:var(--green);border-color:rgba(16,185,129,.3);}
      /* Compact Attack Lab Override */
      .attack-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem; }
      .attack-card { padding: 0.75rem; gap: 0.6rem; }
      .attack-card h4 { font-size: 0.85rem; }
      .attack-card p { font-size: 0.65rem; margin: 0.2rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
      .attack-card .btn { padding: 0.4rem; font-size: 0.7rem; }
    </style>""") + nav + f"""
<div class="login-wrapper" style="margin-bottom: 0; padding-bottom: 0;">
  <div class="attack-lab" style="width: 100%; max-width: 900px; margin: 1rem auto 0 auto;">
    <h3 class="section-title gradient-text" style="text-align:center; font-size:1.5rem;">Secure Demo — Attack Lab</h3>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 1.5rem; font-size:0.9rem;">Test common payloads to see how they are neutralized by parameterized queries.</p>
    
    <div class="attack-grid">
      <div class="attack-card">
        <h4>1️⃣ User Bypass</h4>
        <p>Payload: ' OR 1=1 --</p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('s_username').value='\\' OR 1=1 --'; document.getElementById('s_password').value='anything';">💉 Pre-fill Attack</button>
      </div>
      <div class="attack-card" style="border-left-color: var(--purple);">
        <h4 style="color:var(--purple);">2️⃣ Pass Bypass</h4>
        <p>Payload: ' OR '1'='1</p>
        <button type="button" class="btn btn-danger btn-sm" style="background:var(--purple);" onclick="document.getElementById('s_username').value='admin'; document.getElementById('s_password').value='\\' OR \\'1\\'=\\'1';">💉 Pre-fill Attack</button>
      </div>
      <div class="attack-card">
        <h4>3️⃣ Comment Inject</h4>
        <p>Payload: admin' --</p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('s_username').value='admin\\' --'; document.getElementById('s_password').value='wrong';">💉 Pre-fill Attack</button>
      </div>
      <div class="attack-card">
        <h4>4️⃣ Data Leak</h4>
        <p>Payload: ' UNION SELECT...</p>
        <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('s_username').value='\\' UNION SELECT username,password FROM users --'; document.getElementById('s_password').value='anything';">💉 Pre-fill Attack</button>
      </div>
      <div class="attack-card" style="border-left-color: var(--green);">
        <h4 style="color:var(--green);">5️⃣ Valid User</h4>
        <p>Payload: admin123</p>
        <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('s_username').value='admin'; document.getElementById('s_password').value='admin123';">✅ Pre-fill Valid</button>
      </div>
    </div>
  </div>
</div>
<div class="login-wrapper" style="padding-top: 0; min-height: auto;">
  <div style="width:100%;max-width:900px;display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">

    <div class="login-card" style="border-color:rgba(16,185,129,.3);">
      <span class="login-badge secure-badge">&#128737;&#65039; Secure System</span>
      <h2>Secure Login</h2>
      <p class="subtitle">This form uses <strong>parameterized queries</strong> to safely handle user input.</p>
      <div class="hint-box" style="background:rgba(16,185,129,.06);border-color:rgba(16,185,129,.25);">
        &#128161; Try the same SQL injection payload: <code style="color:var(--green);">' OR 1=1 --</code><br>
        Watch it get completely blocked! &#9989;
      </div>
      <form method="post" action="/secure">
        <div class="form-group">
          <label for="s_username">Username</label>
          <input type="text" id="s_username" name="username" placeholder="Enter username&hellip;" value="{u_safe}"/>
        </div>
        <div class="form-group">
          <label for="s_password">Password</label>
          <input type="password" id="s_password" name="password" placeholder="Enter password&hellip;"/>
        </div>
        <button type="submit" class="btn btn-success w-full">LOGIN &rarr;</button>
      </form>
      {result_html}
    </div>

    <div>
      <div class="card" style="border-color:rgba(16,185,129,.2);">
        <h3 style="font-size:1rem;margin-bottom:.8rem;color:var(--green);">&#128737;&#65039; How Parameterized Queries Work</h3>
        <p style="font-size:.87rem;color:var(--text-muted);margin-bottom:1rem;">
          The query structure is compiled <em>before</em> user input is bound.
          The database treats bound values as <strong>pure data</strong>, never as SQL syntax.
        </p>
        <p style="font-size:.82rem;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--green);margin-bottom:.4rem;">Secure Code</p>
        <div class="code-block" style="margin-bottom:1rem;">
          <span class="code-label">Python / sqlite3</span>
          <span class="kw">cursor</span>.execute(<br>
          &nbsp;&nbsp;<span class="str">"SELECT * FROM users<br>
          &nbsp;&nbsp;&nbsp;WHERE username = <span style="color:#34d399">?</span><br>
          &nbsp;&nbsp;&nbsp;AND password = <span style="color:#34d399">?</span>"</span>,<br>
          &nbsp;&nbsp;(<span class="kw">username</span>, <span class="kw">password</span>)<br>
          )<br>
          <span class="cmt"># &#9989; Input is NEVER part of the SQL string</span>
        </div>
        {param_display}
        <div style="margin-top:1.2rem;border-top:1px solid var(--border);padding-top:1rem;">
          <p style="font-size:.82rem;color:var(--text-muted);">See what happens on a <strong>vulnerable</strong> system?</p>
          <a href="/login" class="btn btn-danger btn-sm" style="margin-top:.6rem;display:inline-block;">&#9888;&#65039; Try Vulnerable Login &rarr;</a>
        </div>
      </div>
    </div>

  </div>
</div>

<div style="background:rgba(124,58,237,.03);border-top:1px solid var(--border);">
<div class="section">
  <h2 class="section-title gradient-text">Vulnerable vs. Secure</h2>
  <span class="section-title-line"></span>
  <div class="compare-row">
    <div class="compare-block vuln">
      <h3>&#10060; VULNERABLE (Concatenation)</h3>
      <div class="code-block">
        <span class="code-label">PYTHON</span>
        <span class="kw">sql</span> = (<br>
        &nbsp;<span class="str">f"SELECT * FROM users<br>
        &nbsp;WHERE username='<span class="inj">{{username}}</span>'<br>
        &nbsp;AND password='<span class="inj">{{password}}</span>'"</span><br>
        )<br><br>
        <span class="cmt"># &#9888;&#65039; Input injected directly &rarr; DANGER</span>
      </div>
      <div class="alert alert-danger" style="margin-top:.8rem;">
        <span class="alert-icon">&#128137;</span>
        <div>User input becomes part of SQL code. Attacker can break out of the string context.</div>
      </div>
    </div>
    <div class="compare-block safe">
      <h3>&#9989; SECURE (Parameterized)</h3>
      <div class="code-block">
        <span class="code-label">PYTHON / sqlite3</span>
        <span class="kw">cursor</span>.execute(<br>
        &nbsp;<span class="str">"SELECT * FROM users<br>
        &nbsp;WHERE username = ?<br>
        &nbsp;AND password = ?"</span>,<br>
        &nbsp;(<span class="kw">username</span>, <span class="kw">password</span>)<br>
        )
      </div>
      <div class="alert alert-success" style="margin-top:.8rem;">
        <span class="alert-icon">&#128737;&#65039;</span>
        <div>Input bound separately. Special characters are automatically escaped &mdash; no injection possible.</div>
      </div>
    </div>
  </div>
  <div class="secure-coding-section" style="margin-top:3rem;">
    <h2 class="section-title gradient-text" style="text-align:center; font-size:1.8rem; margin-bottom: 2rem;">&#128737;&#65039; Secure Coding Section</h2>
    
    <div class="card mb-3" style="border-left:4px solid var(--green);">
      <h3 style="color:var(--green); font-size:1.2rem; margin-bottom:0.8rem; display:flex; align-items:center; gap:10px;">
        <span class="icon">&#129529;</span> 1. Input Validation
      </h3>
      <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:1rem; line-height:1.6;">
        Input validation is your application's first line of defense. Before any user-supplied data reaches your database logic, it must be rigorously checked against expected formats, lengths, and types. Implementing <strong>allow-listing</strong> is far more secure than block-listing. Validation ensures that garbage or malicious data never even touches your database engine.
      </p>
      <div class="code-block">
        <span class="code-label">Python Example: Filtering Input</span>
        <span class="cmt"># Ensure the input matches an expected email format or regex</span><br>
        <span class="kw">import</span> re<br>
        <span class="kw">if not</span> re.match(<span class="str">r"^[A-Za-z0-9_]+$"</span>, username):<br>
        &nbsp;&nbsp;<span class="kw">raise</span> ValueError(<span class="str">"Invalid username format"</span>)
      </div>
    </div>

    <div class="card mb-3" style="border-left:4px solid var(--green);">
      <h3 style="color:var(--green); font-size:1.2rem; margin-bottom:0.8rem; display:flex; align-items:center; gap:10px;">
        <span class="icon">&#128268;</span> 2. Parameterized Queries
      </h3>
      <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:0.5rem; line-height:1.6;">
        If input validation is the first line of defense, parameterized queries are the ultimate fail-safe. Using parameterization completely separates the <strong>logic</strong> of your SQL statement from the <strong>data</strong> supplied by the user. 
      </p>
      <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:1rem; line-height:1.6;">
        Instead of dynamically concatenating variables into a single SQL string, the query is sent to the database with placeholders like <code>?</code> (sqlite) or <code>%s</code> (psycopg2). The user-provided data is then sent completely separately. Because of this strict separation, it is mathematically impossible for user data to break out and alter the SQL structure.
      </p>
    </div>

    <div class="card mb-3" style="border-left:4px solid var(--green);">
      <h3 style="color:var(--green); font-size:1.2rem; margin-bottom:0.8rem; display:flex; align-items:center; gap:10px;">
        <span class="icon">&#128274;</span> 3. Prepared Statements
      </h3>
      <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:1rem; line-height:1.6;">
        Prepared statements restrict user input strictly literal data handling. The database engine compiles and optimizes the SQL template <em>before</em> any user data is bound to it. By completing the query's structural phase early, if an attacker inputs <code>' OR 1=1 --</code>, the database simply interprets it as a bizarrely named user, neutralizing the attack completely.
      </p>
      <div class="code-block">
        <span class="code-label">Python sqlite3 Example</span>
        <span class="kw">cursor</span>.execute(<span class="str">"SELECT id FROM users WHERE username = ?"</span>, (username,))<br>
        <span class="kw">user</span> = <span class="kw">cursor</span>.fetchone()
      </div>
    </div>
  </div>
</div>
</div>
""" + FOOTER + "</body></html>"


# ══════════════════════════════════════════════════════════════
#  REQUEST HANDLER
# ══════════════════════════════════════════════════════════════

class SQLiHandler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=os.path.dirname(os.path.abspath(__file__)), **kwargs)

    def log_message(self, format, *args):
        # Clean console output
        print(f"  [{self.command}] {self.path}")

    def send_html(self, html_content, status=200):
        body = html_content.encode("utf-8")
        self.send_response(status)
        self.send_header("Content-Type", "text/html; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def parse_post_body(self):
        length = int(self.headers.get("Content-Length", 0))
        body = self.rfile.read(length).decode("utf-8")
        return urllib.parse.parse_qs(body, keep_blank_values=True)

    def do_GET(self):
        path = self.path.split("?")[0].rstrip("/")
        if path in ("", "/", "/index.html", "/index"):
            self.send_html(page_index())
        elif path == "/login":
            self.send_html(page_login())
        elif path == "/secure":
            self.send_html(page_secure())
        else:
            # Serve static files (style.css, etc.)
            super().do_GET()

    def do_POST(self):
        path = self.path.split("?")[0].rstrip("/")
        params = self.parse_post_body()
        username = params.get("username", [""])[0]
        password = params.get("password", [""])[0]

        if path == "/login":
            # Build simulated SQL query string
            query_display = (
                f"SELECT * FROM users WHERE username = '{username}'"
                f" AND password = '{password}';"
            )
            
            # More robust check for simulation
            u_low = username.lower()
            p_low = password.lower()
            
            is_injection = False
            custom_msg = ""
            
            if "' or 1=1" in u_low or "' or '1'='1" in p_low or "or true" in u_low or "' or 'a'='a" in u_low:
                is_injection = True
                custom_msg = "The 'OR 1=1' condition made the entire WHERE clause evaluate to TRUE. The database returned every single row it found."
            elif "' --" in u_low or "' #" in u_low:
                is_injection = True
                custom_msg = "The '--' or '#' symbols commented out the rest of the query, so the database never even checked the password."
            elif "union select" in u_low:
                is_injection = True
                custom_msg = "A UNION SELECT statement was used to merge other table data into the results."

            if is_injection:
                html = page_login(
                    username=username, password=password,
                    result_type="bypass",
                    result_title="&#9888;&#65039; BYPASS SUCCESSFUL — ACCESS GRANTED!",
                    result_msg=custom_msg,
                    explain=True,
                    query_display=query_display,
                )
            elif username == VALID_USER and password == VALID_PASS:
                html = page_login(
                    username=username, password=password,
                    result_type="granted",
                    result_title="&#9989; ACCESS GRANTED",
                    result_msg="Correct credentials! Welcome, <strong>admin</strong>. In a real system you would now be redirected to the dashboard.",
                    query_display=query_display,
                )
            else:
                html = page_login(
                    username=username, password=password,
                    result_type="denied",
                    result_title="&#10060; ACCESS DENIED",
                    result_msg="Invalid username or password. The query returned zero rows, so access was refused.",
                    query_display=query_display,
                )
            self.send_html(html)

        elif path == "/secure":
            attempted_sqli = is_sqli(username)
            if username == VALID_USER and password == VALID_PASS:
                html = page_secure(
                    username=username, password=password,
                    result_type="granted",
                    result_title="&#9989; ACCESS GRANTED",
                    result_msg="Correct credentials matched. Logged in as <strong>admin</strong>.",
                )
            else:
                msg = (
                    "&#128737;&#65039; SQL Injection attempt <strong>blocked!</strong> "
                    "The payload was treated as a plain text string, not SQL syntax. "
                    "Parameterized queries prevent this attack entirely."
                    if attempted_sqli else
                    "Invalid credentials. The parameterized query returned no matching rows."
                )
                html = page_secure(
                    username=username, password=password,
                    result_type="denied",
                    result_title="&#10060; ACCESS DENIED",
                    result_msg=msg,
                    attempted_sqli=attempted_sqli,
                )
            self.send_html(html)
        else:
            self.send_response(404)
            self.end_headers()


# ══════════════════════════════════════════════════════════════
#  MAIN
# ══════════════════════════════════════════════════════════════

if __name__ == "__main__":
    url = f"http://localhost:{PORT}"
    print("=" * 55)
    print("  SQL Injection Educational Demo")
    print("=" * 55)
    print(f"  Server running at: {url}")
    print(f"  Pages:")
    print(f"    Home       → {url}/")
    print(f"    Vulnerable → {url}/login")
    print(f"    Secure     → {url}/secure")
    print("  Press Ctrl+C to stop.")
    print("=" * 55)

    # Open browser automatically after short delay
    def open_browser():
        import time
        time.sleep(1.0)
        webbrowser.open(url)

    threading.Thread(target=open_browser, daemon=True).start()

    server = http.server.HTTPServer(("", PORT), SQLiHandler)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\n  Server stopped.")
