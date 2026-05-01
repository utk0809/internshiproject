<?php
// Educational demonstration - URL Spoofing Task
// Built strictly for EDUCATIONAL PURPOSES as per project requirements
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Spoofing Demonstration – Cybersecurity Awareness</title>
    <link rel="stylesheet" href="url_spoofing dashboard/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="description"
        content="Discover how URL spoofing works and learn how to protect yourself from phishing attacks with this interactive educational guide.">
    <style>
        /* Back navigation bar - blends with the spoofing dashboard theme */
        .back-nav {
            background: #0a0f1a;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 14px 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .back-nav a {
            color: #3b82f6;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.2s;
        }
        .back-nav a:hover {
            color: #60a5fa;
        }
        .back-nav .badge {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>

<body>
    <!-- Back Navigation -->
    <div class="back-nav">
        <a href="awareness.php"><i class="fa-solid fa-arrow-left"></i> Back to Awareness Dashboard</a>
        <span class="badge"><i class="fa-solid fa-graduation-cap"></i> Educational Purposes Only</span>
    </div>

    <header>
        <div class="container">
            <h1>URL Spoofing <span class="highlight-blue">Demonstration</span></h1>
            <p class="subtitle">Cybersecurity Awareness & Educational Guide</p>
        </div>
    </header>

    <main class="container">
        <section id="what-is">
            <h2>What is URL Spoofing?</h2>
            <div class="glass-card">
                <p>URL spoofing is a technique used by cybercriminals to create <strong>fake URLs</strong> that mimic legitimate
                    websites. The goal is to deceive users into believing they are visiting a trusted site, such as
                    their bank or social media profile, to steal sensitive information like passwords, credit card
                    numbers, or personal data.</p>
                <p>Attackers rely on the fact that most users don't look closely at the address bar before entering
                    their credentials.</p>
            </div>
        </section>

        <section id="examples">
            <h2>Real vs Fake URL Examples</h2>
            <div class="examples-grid">
                <div class="example-box real">
                    <h3>Legitimate Website</h3>
                    <code class="url-display">https://www.<span class="domain-green">paypal</span>.com</code>
                    <p class="label">Green means the domain is correct and trusted.</p>
                </div>

                <div class="example-box fake">
                    <h3>Spelling Trick</h3>
                    <code class="url-display">https://www.<span class="suspicious-red">paypa1</span>.com</code>
                    <p class="label">Note the number <span class="suspicious-red">1</span> instead of the letter 'l'.
                    </p>
                </div>

                <div class="example-box fake">
                    <h3>Subdomain Trick</h3>
                    <code
                        class="url-display">https://paypal.<span class="suspicious-red">security-check</span>.com</code>
                    <p class="label">The real domain is <span class="suspicious-red">security-check.com</span>, not
                        paypal.</p>
                </div>

                <div class="example-box fake">
                    <h3>Hyphenated Trick</h3>
                    <code class="url-display">http://<span class="suspicious-red">paypal-login</span>.com</code>
                    <p class="label">Attackers use hyphens to create plausible-looking names.</p>
                </div>
            </div>
        </section>

        <section id="how-it-works">
            <h2>How Attackers Trick Users</h2>
            <div class="glass-card">
                <div class="trick">
                    <h3>1. The Subdomain Trick</h3>
                    <p>Attackers put the brand name as a subdomain (e.g., <code>brand.attacker.com</code>). Users see
                        the brand name first and assume it's safe, failing to notice the actual domain suffix.</p>
                </div>

                <div id="diagram" class="diagram-container">
                    <h3>Visualizing Domain Hierarchy</h3>
                    <div class="diagram-box">
                        <span class="part-sub">subdomain</span><span class="dot">.</span><span
                            class="part-domain">domain</span><span class="dot">.</span><span class="part-tld">com</span>
                    </div>
                    <div class="diagram-labels">
                        <p>The <span class="part-domain">real domain</span> is the part immediately before the top-level
                            domain (<code>.com</code>, <code>.org</code>, etc.).</p>
                    </div>
                </div>

                <div class="trick">
                    <h3>2. The Spelling Trick (Typosquatting)</h3>
                    <p>Replacing similar-looking characters (<code>l</code> with <code>1</code>, <code>o</code> with
                        <code>0</code>) or adding/omitting a single letter.
                    </p>
                </div>

                <div class="trick">
                    <h3>3. HTTP vs HTTPS</h3>
                    <p>While many phishing sites now use HTTPS to look "secure," a site using only <code>http://</code>
                        is a major red flag as it lacks encryption and identity verification.</p>
                </div>
            </div>
        </section>

        <section id="safety">
            <h2>How to Stay Safe</h2>
            <div class="glass-card">
                <ul class="safety-list">
                    <li><strong>Always check the full domain name:</strong> Look at the part right before the
                        <code>.com</code> or <code>.net</code>.
                    </li>
                    <li><strong>Look for HTTPS:</strong> Ensure there is a padlock icon, but remember it doesn't
                        guarantee the site isn't a spoof.</li>
                    <li><strong>Avoid clicking suspicious links:</strong> Hover over links in emails to see the actual
                        destination URL before clicking.</li>
                    <li><strong>Use Two-Factor Authentication (2FA):</strong> This provides an extra layer of security
                        even if your password is stolen.</li>
                </ul>
            </div>
        </section>

        <section id="interactive">
            <h2>Practice Identifying URLs</h2>
            <div class="glass-card interactive-box">
                <label for="url-checker">Enter a URL to inspect:</label>
                <div class="input-wrapper">
                    <input type="text" id="url-checker" placeholder="e.g., https://secure-login.brand-site.com">
                    <button id="inspect-btn">Inspect URL</button>
                </div>
                <div id="inspection-result" class="result-box hidden">
                    <p>Analysis for: <span id="display-url" class="url-text"></span></p>
                    <div class="analysis-tag">
                        Detected Main Domain: <span id="detected-domain" class="domain-green"></span>
                    </div>
                </div>
                <p class="warning-msg">Always check the main domain name carefully.</p>
            </div>
        </section>
    </main>

    <script>
        const inspectBtn = document.getElementById('inspect-btn');
        const urlInputEl = document.getElementById('url-checker');
        const resultBox = document.getElementById('inspection-result');
        const displayUrl = document.getElementById('display-url');
        const detectedDomain = document.getElementById('detected-domain');

        function performInspection() {
            const urlInput = urlInputEl.value.trim();

            if (!urlInput) {
                alert('Please enter a URL first!');
                return;
            }

            try {
                // Prepend http if protocol is missing for URL parser
                let urlToParse = urlInput;
                if (!/^https?:\/\//i.test(urlToParse)) {
                    urlToParse = 'http://' + urlToParse;
                }

                const url = new URL(urlToParse);
                const hostname = url.hostname;
                const parts = hostname.split('.');

                // Simple logic: real domain is usually the last two parts
                let mainDomain = '';
                if (parts.length >= 2) {
                    const lastPart = parts[parts.length - 1];
                    const secondLast = parts[parts.length - 2];
                    const commonShortTLDs = ['co', 'com', 'org', 'net', 'gov', 'edu'];

                    if (parts.length >= 3 && commonShortTLDs.includes(secondLast) && lastPart.length <= 3) {
                        mainDomain = parts.slice(-3).join('.');
                    } else {
                        mainDomain = parts.slice(-2).join('.');
                    }
                } else {
                    mainDomain = hostname;
                }

                displayUrl.textContent = urlInput;
                detectedDomain.textContent = mainDomain;
                detectedDomain.className = "domain-green";

                // --- SPOOFING DETECTION LOGIC ---
                const statusEl = document.getElementById('security-status') || document.createElement('div');
                if (!document.getElementById('security-status')) {
                    statusEl.id = 'security-status';
                    detectedDomain.parentNode.appendChild(statusEl);
                }

                const brands = ['paypal', 'google', 'facebook', 'microsoft', 'amazon', 'apple', 'netflix', 'instagram'];
                let isSpoofed = false;
                let reason = '';

                // Check if a brand name is used as a subdomain but the main domain is different
                const brandInSubdomain = brands.find(brand => hostname.toLowerCase().includes(brand) && !mainDomain.toLowerCase().includes(brand));

                // Check for common typos (simplified example)
                const isTyposquatted = brands.find(brand => {
                    if (mainDomain.includes(brand)) return false;
                    const simplifiedMain = mainDomain.replace(/1/g, 'l').replace(/0/g, 'o');
                    return simplifiedMain.includes(brand);
                });

                if (brandInSubdomain) {
                    isSpoofed = true;
                    reason = `Warning: This site uses "${brandInSubdomain}" in the address, but the REAL domain is actually "${mainDomain}". This is a common spoofing tactic!`;
                } else if (isTyposquatted) {
                    isSpoofed = true;
                    reason = `Warning: This looks like a spelling trick (typosquatting). The domain "${mainDomain}" is trying to look like a legitimate brand.`;
                } else if (hostname.toLowerCase().includes('-login') || hostname.toLowerCase().includes('secure-')) {
                    isSpoofed = true;
                    reason = `Suspicious: Use of keywords like "login" or "secure" in the domain name is often used to create a false sense of security.`;
                }

                if (isSpoofed) {
                    statusEl.innerHTML = `<p class="suspicious-red" style="margin-top:10px; font-weight:700;">🚨 POTENTIAL SPOOFING DETECTED!</p><p class="label">${reason}</p>`;
                    detectedDomain.className = "suspicious-red";
                } else {
                    statusEl.innerHTML = `<p class="domain-green" style="margin-top:10px; font-weight:700;">✅ Looks Legitimate (for demo only)</p>`;
                    detectedDomain.className = "domain-green";
                }

                resultBox.classList.remove('hidden');

            } catch (e) {
                displayUrl.textContent = urlInput;
                detectedDomain.textContent = "Invalid URL format";
                detectedDomain.className = "suspicious-red";
                resultBox.classList.remove('hidden');
            }
        }

        inspectBtn.addEventListener('click', performInspection);

        urlInputEl.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                performInspection();
            }
        });
    </script>

    <footer>
        <div class="container">
            <p style="margin-bottom: 1rem;"><a href="awareness.php" style="color: #3b82f6; text-decoration: none; font-weight: 600;"><i class="fa-solid fa-arrow-left"></i> Back to Awareness Dashboard</a></p>
            <p>&copy; 2026 Cybersecurity awareness demonstration. Educational purposes only.</p>
        </div>
    </footer>
</body>

</html>
