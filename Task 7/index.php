<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHOENIX | Email Header Analyzer</title>
    <meta name="description" content="Cybersecurity educational tool for analyzing email headers.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-brand">PHOENIX</a>
        <div class="nav-links-header">
            <a href="index.php">Analyzer</a>
            <a href="explanation.php">Security Guide</a>
        </div>
    </nav>

    <div class="dashboard">
        <header class="app-header">
            <h1>Email Header Analyzer</h1>
            <p>Deconstruct email headers to verify authenticity, trace origins, and identify cyber threats.</p>
        </header>

        <main class="main-content">
            <div class="glass analyzer-card">
                <div class="input-area">
                    <label for="header-input" class="input-label">PASTE RAW HEADERS</label>
                    <textarea id="header-input" name="header-input" class="header-textarea" placeholder="Paste full raw email headers here (including Received, From, To, etc.)..."></textarea>
                    
                    <div class="actions" style="margin-top: 2rem;">
                        <button type="button" id="analyze-btn" class="analyze-btn">
                            <span class="btn-text">INITIALIZE ANALYSIS</span>
                        </button>
                    </div>
                </div>
            </div>

            <div id="results-container" style="display : none;"></div>

            <div class="nav-links">
                <a href="explanation.php" class="nav-link">
                    <span>&#9670;</span> LEARN MORE ABOUT HEADERS
                </a>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>
