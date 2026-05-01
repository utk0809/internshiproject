<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Attacks Interactive Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;800&family=Orbitron:wght@400;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="dashboard">
        <header class="app-header">
            <h1>Different Types of Cyber Attack </h1>
            <p>Select an attack vector below to explore its mechanics.</p>
        </header>

        <!-- Main Tabs Control -->
        <input type="radio" name="main_tab" id="tab_malware" class="tab-radio" checked>
        <input type="radio" name="main_tab" id="tab_ransomware" class="tab-radio">
        <input type="radio" name="main_tab" id="tab_mitm" class="tab-radio">
        <input type="radio" name="main_tab" id="tab_ddos" class="tab-radio">

        <div class="main-content">
            <div class="sidebar">
                <div class="attack-nav">
                    <label for="tab_malware" class="nav-button" data-id="malware">Malware</label>
                    <label for="tab_ransomware" class="nav-button" data-id="ransomware">Ransomware</label>
                    <label for="tab_mitm" class="nav-button" data-id="mitm">Man in the Middle</label>
                    <label for="tab_ddos" class="nav-button" data-id="ddos">DDoS Attack</label>
                </div>
                <div class="instructions">
                    Select an attack to view detailed animations and information.
                </div>
            </div>

            <div class="viewer-container">
                <!-- Malware View -->
                <div class="attack-view" id="view_malware">
                    <div class="attack-header">
                        <h2>Malware</h2>
                        <span class="subtitle">Malicious Software</span>
                    </div>
                    <div class="attack-body full-width">
                        <p class="intro-text">Malware is a catch-all term for any type of malicious software designed to harm or exploit programmable devices or networks. Select a specific malware type below to see how it operates:</p>
                        
                        <div class="malware-sub-tabs">
                            <input type="radio" name="malware_type" id="sub_virus" checked class="sub-radio">
                            <input type="radio" name="malware_type" id="sub_worm" class="sub-radio">
                            <input type="radio" name="malware_type" id="sub_trojan" class="sub-radio">
                            
                            <div class="sub-nav">
                                <label for="sub_virus" class="sub-btn">Virus</label>
                                <label for="sub_worm" class="sub-btn">Worm</label>
                                <label for="sub_trojan" class="sub-btn">Trojan</label>
                            </div>
                            
                            <div class="sub-content">
                                <!-- VIRUS -->
                                <div class="sub-pane" id="pane_virus">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-virus">Computer Virus</h3>
                                        <p>A virus attaches itself to clean files and infects other clean files. They require a user to execute the infected program to spread, much like a biological virus requires a host.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Modifies, corrupts, or deletes files.</li>
                                            <li><strong>Spread:</strong> Requires human interaction (e.g., opening an infected email attachment).</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-virus">
                                                <div class="file-icon">📄</div>
                                                <div class="tiny-virus">🦠</div>
                                                <div class="file-status">Clean File</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- WORM -->
                                <div class="sub-pane" id="pane_worm">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-worm">Network Worm</h3>
                                        <p>Worms are standalone malicious programs that replicate themselves to spread to other computers. Unlike viruses, worms do NOT need to attach to a host program or require human interaction.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Consumes network bandwidth and system resources.</li>
                                            <li><strong>Spread:</strong> Crawls automatically across networks by exploiting vulnerabilities.</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-worm">
                                                <div class="network-pc pc1">🖥️</div>
                                                <div class="network-pc pc2">🖥️</div>
                                                <div class="network-pc pc3">🖥️</div>
                                                <div class="worm-entity">🐛</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- TROJAN -->
                                <div class="sub-pane" id="pane_trojan">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-trojan">Trojan Horse</h3>
                                        <p>A Trojan disguises itself as legitimate, useful software. Users are tricked into downloading and executing it. Once inside, it creates a "backdoor" for attackers.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Steals data, installs more malware, or gives remote control to hackers.</li>
                                            <li><strong>Spread:</strong> Deceptive social engineering (e.g., fake software downloads).</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-trojan">
                                                <div class="trojan-box">🎁</div>
                                                <div class="trojan-hacker">🥷</div>
                                                <div class="user-cursor">🖱️</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ransomware View -->
                <div class="attack-view" id="view_ransomware">
                    <div class="attack-header">
                        <h2>Ransomware</h2>
                        <span class="subtitle">Encryption &amp; Extortion</span>
                    </div>
                    <div class="attack-body full-width">
                        <p class="intro-text">Ransomware is a type of malicious software that encrypts a victim's files or locks their system, then demands a ransom payment to restore access. It is one of the most financially damaging forms of cyberattack. Select a ransomware variant below:</p>
                        
                        <div class="malware-sub-tabs">
                            <input type="radio" name="ransomware_type" id="sub_crypto" checked class="sub-radio">
                            <input type="radio" name="ransomware_type" id="sub_locker" class="sub-radio">
                            <input type="radio" name="ransomware_type" id="sub_raas" class="sub-radio">
                            
                            <div class="sub-nav">
                                <label for="sub_crypto" class="sub-btn">Crypto Ransomware</label>
                                <label for="sub_locker" class="sub-btn">Locker Ransomware</label>
                                <label for="sub_raas" class="sub-btn">RaaS</label>
                            </div>
                            
                            <div class="sub-content">
                                <!-- CRYPTO RANSOMWARE -->
                                <div class="sub-pane" id="pane_crypto">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-crypto">Crypto Ransomware</h3>
                                        <p>The most prevalent form of ransomware. It searches for and encrypts valuable files (documents, images, databases) using strong cryptography, making them completely inaccessible without the decryption key held by the attacker.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Encrypts files using AES/RSA algorithms, appends a new extension (e.g., .locked).</li>
                                            <li><strong>Demand:</strong> Drops a ransom note (HTML/TXT) demanding cryptocurrency payment for the decryption key.</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-crypto">
                                                <div class="crypto-file">📄</div>
                                                <div class="crypto-lock">🔒</div>
                                                <div class="crypto-label">FILE ENCRYPTED</div>
                                                <div class="crypto-key">🔑</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- LOCKER RANSOMWARE -->
                                <div class="sub-pane" id="pane_locker">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-locker">Locker Ransomware</h3>
                                        <p>Instead of encrypting files, Locker Ransomware locks the victim out of their entire operating system or device. The screen is replaced with a ransom demand, preventing access to any applications or files.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Disables the mouse, keyboard, and desktop, rendering the device completely unusable.</li>
                                            <li><strong>Deception:</strong> Often impersonates law enforcement ("police ransomware"), claiming the user committed a crime.</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-locker">
                                                <div class="locker-screen">🖥️</div>
                                                <div class="locker-overlay">⛔</div>
                                                <div class="locker-badge">🚔</div>
                                                <div class="locker-warning">ACCESS DENIED</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- RaaS -->
                                <div class="sub-pane" id="pane_raas">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-raas">Ransomware-as-a-Service</h3>
                                        <p>A cybercrime business model where ransomware developers lease their malware to affiliate attackers. The developer handles infrastructure and decryption, while affiliates conduct the actual attacks and share a cut of the ransom proceeds.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Model:</strong> Developers provide a full kit (malware, C2 server, payment portal) to affiliates for 20–30% of profits.</li>
                                            <li><strong>Scale:</strong> Enables low-skill criminals to launch sophisticated attacks; responsible for groups like LockBit &amp; REvil.</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-raas">
                                                <div class="raas-dev">👨‍💻</div>
                                                <div class="raas-affiliate-1">🥷</div>
                                                <div class="raas-affiliate-2">🥷</div>
                                                <div class="raas-victim">🏢</div>
                                                <div class="raas-bitcoin">₿</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Man in the Middle View -->
                <div class="attack-view" id="view_mitm">
                    <div class="attack-header">
                        <h2>Man in the Middle</h2>
                        <span class="subtitle">Eavesdropping & Interception</span>
                    </div>
                    <div class="attack-body full-width">
                        <p class="intro-text">A Man in the Middle (MitM) attack occurs when an attacker secretly relays and possibly alters the communications between two parties who believe they are directly communicating with each other. Select a MitM technique:</p>
                        
                        <div class="malware-sub-tabs">
                            <input type="radio" name="mitm_type" id="sub_eviltwin" checked class="sub-radio">
                            <input type="radio" name="mitm_type" id="sub_arp" class="sub-radio">
                            <input type="radio" name="mitm_type" id="sub_hijack" class="sub-radio">
                            
                            <div class="sub-nav">
                                <label for="sub_eviltwin" class="sub-btn">Evil Twin</label>
                                <label for="sub_arp" class="sub-btn">ARP Spoofing</label>
                                <label for="sub_hijack" class="sub-btn">Session Hijacking</label>
                            </div>
                            
                            <div class="sub-content">
                                <!-- EVIL TWIN -->
                                <div class="sub-pane" id="pane_eviltwin">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-eviltwin">Evil Twin Wi-Fi</h3>
                                        <p>An attacker sets up a fraudulent Wi-Fi access point with a name (SSID) identical to a legitimate one. Victims connect to this rogue point, forwarding all data to the attacker.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Intercepts login credentials and data from unwary users.</li>
                                            <li><strong>Spread:</strong> Physical proximity in public places like cafes or airports.</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-eviltwin">
                                                <div class="user-device">📱</div>
                                                <div class="rogue-ap">🗼</div>
                                                <div class="real-ap">🏢</div>
                                                <div class="data-packet">🔑</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- ARP SPOOFING -->
                                <div class="sub-pane" id="pane_arp">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-arp">ARP Spoofing</h3>
                                        <p>The attacker sends falsified ARP messages over a local area network to link their MAC address with the IP address of a legitimate computer or server on the network.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Receives data intended for the IP address they are spoofing.</li>
                                            <li><strong>Spread:</strong> Exploits trust within a Local Area Network (LAN).</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-arp">
                                                <div class="router">🌐</div>
                                                <div class="attacker-pc">💻</div>
                                                <div class="target-pc">🖥️</div>
                                                <div class="arp-message">✉️</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- SESSION HIJACKING -->
                                <div class="sub-pane" id="pane_hijack">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-hijack">Session Hijacking</h3>
                                        <p>The attacker takes over a user's web session by obtaining the session ID and masquerading as the authorized user to access sensitive information or services.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Bypasses authentication by stealing the session token (cookie).</li>
                                            <li><strong>Spread:</strong> Often via Cross-Site Scripting (XSS) or network sniffing.</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-hijack">
                                                <div class="legit-user">🧑‍💻</div>
                                                <div class="session-cookie">🍪</div>
                                                <div class="hijacker">🥷</div>
                                                <div class="auth-server">🗄️</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DDoS View -->
                <div class="attack-view" id="view_ddos">
                    <div class="attack-header">
                        <h2>DDoS Attack</h2>
                        <span class="subtitle">Distributed Denial of Service</span>
                    </div>
                    <div class="attack-body full-width">
                        <p class="intro-text">A Distributed Denial of Service (DDoS) attack involves multiple compromised systems attacking a single target, causing a denial of service for users of the targeted resource. Select a DDoS technique:</p>
                        
                        <div class="malware-sub-tabs">
                            <input type="radio" name="ddos_type" id="sub_httpflood" checked class="sub-radio">
                            <input type="radio" name="ddos_type" id="sub_udpflood" class="sub-radio">
                            <input type="radio" name="ddos_type" id="sub_synflood" class="sub-radio">
                            
                            <div class="sub-nav">
                                <label for="sub_httpflood" class="sub-btn">HTTP Flood</label>
                                <label for="sub_udpflood" class="sub-btn">UDP Flood</label>
                                <label for="sub_synflood" class="sub-btn">SYN Flood</label>
                            </div>
                            
                            <div class="sub-content">
                                <!-- HTTP FLOOD -->
                                <div class="sub-pane" id="pane_httpflood">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-httpflood">HTTP Flood</h3>
                                        <p>This attack mimics legitimate HTTP requests from multiple sources to overwhelm a web server. It forces the server to use all available resources to handle the flood of requests.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Exhausts server CPU and memory by requesting resource-heavy pages.</li>
                                            <li><strong>Complexity:</strong> Hard to detect as requests appear to be from regular users.</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-httpflood">
                                                <div class="target-server">🖥️</div>
                                                <div class="request req1">📩</div>
                                                <div class="request req2">📩</div>
                                                <div class="request req3">📩</div>
                                                <div class="request req4">📩</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- UDP FLOOD -->
                                <div class="sub-pane" id="pane_udpflood">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-udpflood">UDP Flood</h3>
                                        <p>The attacker sends a large number of User Datagram Protocol (UDP) packets to random ports on a remote host. The host repeatedly checks for applications at those ports and sends back ICMP "Destination Unreachable" packets.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Saturates network bandwidth and overwhelms system resources.</li>
                                            <li><strong>Impact:</strong> Causes the system to become unresponsive to legitimate traffic.</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-udpflood">
                                                <div class="target-host">🏠</div>
                                                <div class="udp-packet p1">📦</div>
                                                <div class="udp-packet p2">📦</div>
                                                <div class="udp-packet p3">📦</div>
                                                <div class="udp-packet p4">📦</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- SYN FLOOD -->
                                <div class="sub-pane" id="pane_synflood">
                                    <div class="sub-info glass">
                                        <h3 class="gradient-text gradient-synflood">SYN Flood</h3>
                                        <p>Exploits the TCP three-way handshake by sending a continuous stream of SYN (synchronize) requests but never completing the handshake, leaving the server with many "half-open" connections.</p>
                                        <ul class="clean-list mt-4">
                                            <li><strong>Action:</strong> Consumes the connection queue of the server.</li>
                                            <li><strong>Vulnerability:</strong> Exploits the standard TCP protocol design.</li>
                                        </ul>
                                    </div>
                                    <div class="sub-visual glass">
                                        <div class="diagram-container">
                                            <div class="anim-specific anim-synflood">
                                                <div class="syn-server">⚙️</div>
                                                <div class="syn-half syn1">🔗</div>
                                                <div class="syn-half syn2">🔗</div>
                                                <div class="syn-half syn3">🔗</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>