<?php
// Educational demonstration - Awareness Page
// Built strictly for EDUCATIONAL PURPOSES as per project requirements
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Analysis | Phishing Simulation</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="sec-body">

<div class="sec-layout">
    
    <!-- Top Navigation / Branding -->
    <header class="sec-header">
        <div class="sec-brand">
            <div class="sec-logo-icon"><i class="fa-solid fa-shield-virus"></i></div>
            <div class="sec-brand-text">
                <h1>Cyber Security Project</h1>
                <span>Task 02: Awareness Module</span>
            </div>
        </div>
        <div class="sec-badge-ethical">
            <i class="fa-solid fa-graduation-cap"></i> Educational Purposes Only
        </div>
    </header>

    <!-- Main Alert Hero -->
    <section class="sec-hero-alert">
        <div class="sec-hero-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="sec-hero-content">
            <h2>Simulation Complete: You Just Experienced a Phishing Attack</h2>
            <p>The login page you interacted with was a simulated phishing attack designed to demonstrate how cyber criminals operate. <strong>No real passwords or credentials were collected during this exercise.</strong> This project exists solely to fulfill learning objectives around Social Engineering, URL Spoofing, and User Awareness.</p>
        </div>
    </section>

    <!-- Grid Content -->
    <div class="sec-grid">
        
        <!-- Left: Educational Core -->
        <main class="sec-main-content">
            
            <div class="sec-module">
                <div class="sec-module-header">
                    <div class="sec-mod-icon"><i class="fa-solid fa-brain"></i></div>
                    <h3>Understanding Social Engineering</h3>
                </div>
                <div class="sec-module-body">
                    <p>Social engineering is the psychological manipulation of people into performing actions or divulging confidential information. Rather than using technical hacking methods to bypass security, attackers exploit human psychology—trust, fear, urgency, or curiosity.</p>
                    <p>In this simulation, the attacker relied on your trust in a familiar interface (the Instagram login page) to trick you into entering your credentials without second-guessing the environment.</p>
                </div>
            </div>

            <div class="sec-module">
                <div class="sec-module-header">
                    <div class="sec-mod-icon"><i class="fa-solid fa-globe"></i></div>
                    <h3>How Phishing Websites & URL Spoofing Work</h3>
                </div>
                <div class="sec-module-body">
                    <p>A phishing website is a fraudulent clone of a legitimate site. To make the deception convincing, attackers use <strong>URL Spoofing</strong>—registering domains or using subdomains that look nearly identical to the real service.</p>
                    
                    <div class="sec-url-showcase">
                        <div class="sec-url-card valid">
                            <div class="sec-url-status"><i class="fa-solid fa-circle-check"></i> Authentic Destination</div>
                            <div class="sec-url-text"><span>https://</span>www.instagram.com<span>/login</span></div>
                        </div>
                        <div class="sec-url-card invalid">
                            <div class="sec-url-status"><i class="fa-solid fa-circle-xmark"></i> Spoofed Simulation</div>
                            <div class="sec-url-text"><span>http://</span>instagram-demo.milesweb-host.com</div>
                            <div class="sec-url-note">Attackers use similar words or free subdomains to trick the eye. Always verify the root domain!</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sec-module">
                <div class="sec-module-header">
                    <div class="sec-mod-icon"><i class="fa-solid fa-user-shield"></i></div>
                    <h3>The Importance of User Awareness</h3>
                </div>
                <div class="sec-module-body">
                    <p>The strongest firewall in the world cannot stop a user from willingly handing over their password. User awareness is the critical frontline defense against cyber threats.</p>
                    <ul class="sec-defense-list">
                        <li>
                            <i class="fa-solid fa-eye"></i>
                            <div>
                                <strong>Inspect the URL meticulously:</strong> Verify the exact spelling of the domain and ensure it uses HTTPS.
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-mobile-screen-button"></i>
                            <div>
                                <strong>Enable Multi-Factor Authentication (MFA):</strong> Adds a secondary layer of defense even if your password is stolen.
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-stopwatch"></i>
                            <div>
                                <strong>Beware of Urgency:</strong> Attackers create false emergencies (e.g., "Your account will be suspended!") to rush your judgement.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </main>

        <!-- Right: Action & Tasks Sidebar -->
        <aside class="sec-sidebar">
            
            <div class="sec-widget sec-widget-progress">
                <h3><i class="fa-solid fa-list-check"></i> Project Tracking</h3>
                <p>Track your required curriculum tasks.</p>
                
                <div class="sec-task-list">
                    <div class="sec-task completed">
                        <div class="sec-task-icon"><i class="fa-solid fa-check"></i></div>
                        <div class="sec-task-info">
                            <h4>Task 1: Fake Login Page</h4>
                            <span>Demo design implemented</span>
                        </div>
                    </div>
                    <div class="sec-task completed">
                        <div class="sec-task-icon"><i class="fa-solid fa-check"></i></div>
                        <div class="sec-task-info">
                            <h4>Task 2: Awareness Page</h4>
                            <span>Educational content active</span>
                        </div>
                    </div>
                    <div class="sec-task completed">
                        <div class="sec-task-icon"><i class="fa-solid fa-check"></i></div>
                        <div class="sec-task-info">
                            <h4>Task 3: URL Spoofing</h4>
                            <span>URL Spoofing active</span>
                        </div>
                        <a href="url_spoofing.php" class="sec-btn-action">Go to URL Spoofing Task</a>
                    </div>
                </div>
            </div>

            <div class="sec-widget sec-widget-hosting">
                <h3><i class="fa-solid fa-server"></i> Hosting Requirement</h3>
                <p>As per the project guidelines, ensure this final build is hosted on your MilesWeb subdomain (e.g., <code>phishing-demo.yourdomain.com</code>).</p>
            </div>

            <div class="sec-widget sec-widget-return">
                <a href="index.php" class="sec-btn-ghost"><i class="fa-solid fa-arrow-rotate-left"></i> Replay Simulation</a>
            </div>

        </aside>

    </div>
</div>

</body>
</html>