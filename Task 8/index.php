<?php
// PHP Backend for Simulation 
// Set time limit to avoid timeout during simulation
set_time_limit(60);

// Configuration
$target_password = "superman";
$simulation_name = "Node-Alpha-7";

// Wordlist (Hardcoded for strict PHP compliance)
$wordlist = [
    "password", "123456", "admin", "guest", "welcome",
    "qwerty", "letmein", "secret", "football", "superman",
    "dragon", "master", "shadow", "hunter", "justice"
];

$is_simulating = isset($_POST['start_simulation']);
$results = [];
$found_match = null;
$attempts = 0;

// If simulating, we will use output flushing for a "live" feel
// Note: This requires the server to support flushing and no output compression.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictionary Attack Simulator | PHP Only</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Orbitron:wght@400;700&family=Fira+Code&display=swap" rel="stylesheet">
    <!-- Main Styles -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .php-badge {
            position: fixed;
            top: 10px;
            right: 10px;
            font-size: 0.7rem;
            color: #4CAF50;
            border: 1px solid #4CAF50;
            padding: 2px 6px;
            border-radius: 4px;
            opacity: 0.8;
            font-family: 'Fira Code', monospace;
        }
        /* Custom scroll behavior for terminal when flushed */
        .terminal-content {
            display: flex;
            flex-direction: column;
        }
        .btn-start {
            cursor: pointer;
            border: none;
        }
        .btn-start:hover {
            opacity: 0.9;
        }
        .match-card {
            background: rgba(0, 255, 157, 0.1);
            border: 1px solid #00ff9d;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="php-badge">MODERN PHP CORE</div>
    <div class="container">
        <h1>Dictionary Attack Simulation</h1>
        <p class="description">Educational simulation using Pure PHP to demonstrate wordlist attacks. (No JavaScript, No JSON)</p>

        <?php if ($is_simulating): ?>
            <div class="simulation-panel">
                <div class="control-card">
                    <div class="input-group">
                        <span class="input-label">Simulation Status</span>
                        <div class="password-display" id="target-display">SIMULATING...</div>
                    </div>
                </div>

                <div class="terminal-card">
                    <div class="terminal-header">
                        <div class="dot red"></div>
                        <div class="dot yellow"></div>
                        <div class="dot green"></div>
                    </div>
                    <div class="terminal-content" id="terminal-log">
                        <?php
    // Start simulation output
    echo "<div class='progress-wrapper' style='margin-bottom: 20px;'>
                                <div style='display: flex; justify-content: space-between; margin-bottom: 8px;'>
                                    <span class='input-label'>Attack Progress</span>
                                    <span class='input-label' style='color: var(--primary-color);' id='progress-text'>0%</span>
                                </div>
                                <div class='progress-container'>
                                    <div class='progress-bar' id='attack-progress' style='width: 0%; transition: width 0.3s ease;'></div>
                                </div>
                              </div>";

    echo "<div class='log-line'>> Initializing PHP Simulation Engine...</div>";
    echo "<div class='log-line'>> Target system: $simulation_name</div>";
    flush();
    ob_flush();
    sleep(1);

    $total_words = count($wordlist);

    $start_time = microtime(true);
    foreach ($wordlist as $index => $word) {
        $attempts++;
        $is_match = ($word === $target_password);

        $percent = round(($attempts / $total_words) * 100);

        // Output style to update progress bar visually without JS
        echo "<style>
                                #attack-progress { width: {$percent}% !important; }
                                #progress-text { font-size: 0; }
                                #progress-text::before { content: '{$percent}%'; font-size: 0.8rem; }
                            </style>";

        echo "<div class='log-line typing-effect'>
                                    <span class='prompt'>></span> 
                                    Testing: <span class='test-word'>" . str_pad($word, 10, " ", STR_PAD_RIGHT) . "</span>... 
                                    " . ($is_match ? "<span class='status-success'>[ MATCH FOUND ]</span>" : "<span class='status-error'>[ DENIED ]</span>") . "
                                  </div>";

        // Force output update in browser
        flush();
        ob_flush();

        if ($is_match) {
            $found_match = $word;
            break;
        }
        // Added slightly larger visual delay so students can clearly see the step-by-step
        usleep(300000); // 0.3 seconds
    }
    $end_time = microtime(true);
    $time_taken = round($end_time - $start_time, 2);

    if ($found_match) {
        echo "<br><div class='log-line success typing-effect'>> System breached successfully with: $found_match</div>";
    }
    else {
        echo "<br><div class='log-line error typing-effect'>> Dictionary exhausted. Attack failed.</div>";
    }
?>
                    </div>
                </div>
            </div>
            
            <?php if ($found_match): ?>
                <div class="match-card">
                    <h2 style="color: #00ff9d; margin-bottom: 10px;">BREACH SUCCESSFUL</h2>
                    <p style="margin-bottom: 5px;">• Password found: <strong><?php echo $found_match; ?></strong></p>
                    <p style="margin-bottom: 5px;">• Number of attempts: <strong><?php echo $attempts; ?></strong></p>
                    <p style="margin-bottom: 15px;">• Time taken: <strong><?php echo $time_taken; ?> seconds</strong></p>
                    <a href="index.php" style="color: #fff; text-decoration: underline; font-size: 0.9rem;">Reset Simulation</a>
                </div>
            <?php
    else: ?>
                <div style="text-align: center; margin-top: 2rem;">
                    <div class="match-card" style="background: rgba(255, 77, 77, 0.1); border-color: #ff4d4d;">
                        <h2 style="color: #ff4d4d; margin-bottom: 10px;">ATTACK FAILED</h2>
                        <p style="margin-bottom: 5px;">• Password found: <strong>None</strong></p>
                        <p style="margin-bottom: 5px;">• Number of attempts: <strong><?php echo $attempts; ?></strong></p>
                        <p style="margin-bottom: 15px;">• Time taken: <strong><?php echo $time_taken; ?> seconds</strong></p>
                    </div>
                    <a href="index.php" class="btn-start" style="text-decoration: none; display: inline-block; margin-top: 1rem;">Back</a>
                </div>
            <?php
    endif; ?>

        <?php
else: ?>
            <div class="simulation-panel">
                <div class="control-card">
                    <div class="input-group">
                        <span class="input-label">Target Password</span>
                        <div class="password-display">********</div>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo count($wordlist); ?></span>
                            <span class="stat-label">Words</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">PHP</span>
                            <span class="stat-label">Engine</span>
                        </div>
                    </div>

                    <form method="POST">
                        <button type="submit" name="start_simulation" class="btn-start" style="width: 100%;">Start Simulation</button>
                    </form>
                </div>

                <div class="terminal-card">
                    <div class="terminal-header">
                        <div class="dot red"></div>
                        <div class="dot yellow"></div>
                        <div class="dot green"></div>
                    </div>
                    <div class="terminal-content">
                        <div class="log-line">> PHP Backend Ready.</div>
                        <div class="log-line">> Awaiting user command...</div>
                    </div>
                </div>
            </div>
        <?php
endif; ?>
    </div>

    <div class="container awareness-container">
        <h2>Understanding Password Security</h2>
        <p class="description">Learn how a Dictionary Attack works and how to protect yourself.</p>
        
        <div class="awareness-section">
            <div class="awareness-card feature">
                <div class="icon">📖</div>
                <h3>What is a Dictionary Attack?</h3>
                <p>A dictionary attack is like trying every key on a huge keychain until one opens the door. Hackers use a massive list of common words, names, and patterns (a "dictionary") to guess your password. If your password is in their list, they're in!</p>
            </div>
            
            <div class="awareness-card risk">
                <div class="icon">⚠️</div>
                <h3>The Risk of Weak Passwords</h3>
                <p>Using passwords like <strong>"123456"</strong> or <strong>"password"</strong> is incredibly dangerous. Hackers program their tools to try these first. A simple password can be cracked within milliseconds.</p>
            </div>
        </div>
        
        <div class="best-practices-card">
            <h3>🛡️ Best Practices for Strong Passwords</h3>
            <ul class="practices-list">
                <li><strong>Mix it up:</strong> Use uppercase and lowercase letters.</li>
                <li><strong>Add variety:</strong> Include numbers and special characters like <code>@ # $ %</code>.</li>
                <li><strong>Be unique:</strong> Avoid common words, pet names, or birthdates.</li>
                <li><strong>Go long:</strong> A password should ideally be at least 12 characters long.</li>
            </ul>
        </div>
        
        <div class="warning-banner">
            <strong>Stay Safe:</strong> Use strong, unique passwords for every account to keep your digital life secure!
        </div>
    </div>
</body>
</html>
