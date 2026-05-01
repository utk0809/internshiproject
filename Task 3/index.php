<?php
/**
 * SHA-256 Hash Generator – Cybersecurity Demonstration
 * This script demonstrates the use of hashing in a cybersecurity context.
 * It's designed for educational purposes only.
 */

$input_text = "";
$generated_hash = "";
$hash1 = "";
$hash2 = "";
$comparison_result = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Task 1, 2, 3: Generation logic
    if (isset($_POST['message'])) {
        $input_text = trim((string)$_POST['message']);
        if ($input_text !== "") {
            $generated_hash = hash('sha256', $input_text);
        }
    }
    
    // Task 4, 5: Comparison logic
    if (isset($_POST['compare'])) {
        $hash1 = trim((string)$_POST['hash1']);
        $hash2 = trim((string)$_POST['hash2']);
        
        if ($hash1 !== "" && $hash2 !== "") {
            if ($hash1 === $hash2) {
                $comparison_result = "Match";
            } else {
                $comparison_result = "Not Match";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHA-256 Hash Generator – Cybersecurity Demonstration</title>
    <!-- Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <!-- Meta tags for SEO as per instructions -->
    <meta name="description" content="An educational demonstration of SHA-256 hashing for cybersecurity, explaining the difference between hashing and encryption.">
</head>
<body>

    <header>
        <h1>SHA-256 Hash Generator</h1>
        <p>Cybersecurity Demonstration & Integrity Lab</p>
    </header>

    <div class="intro-section">
        <section id="hashing-basics">
            <h2>1. Introduction to Hashing</h2>
            <p><strong>Hashing</strong> is the process of transforming a given set of data into a fixed-length string of characters, which is usually a hexadecimal number. Even a tiny change in the input data will result in a completely different hash value.</p>
            
            <p><strong>Hashing vs. Encryption</strong>:</p>
            <ul>
                <li><strong>Hashing</strong> is a <strong>one-way function</strong>. Once data is hashed, it cannot be reversed back to its original form. It is primarily used for verifying data integrity.</li>
                <li><strong>Encryption</strong> is a <strong>two-way function</strong>. Data is transformed to be secret, but it can be decrypted back to plain text using a cryptographic key.</li>
            </ul>

            <p><strong>Maintain Data Integrity and Secure Password Storage</strong>:</p>
            <ul>
                <li><strong>Data Integrity</strong>: Since a unique input always produces the same hash, we can compare hashes to ensure a file or message hasn't been altered during transmission.</li>
                <li><strong>Password Security</strong>: Instead of storing actual passwords, websites store the <strong>hash</strong> of the password. When a user logs in, the entered password is hashed and compared to the stored hash. If the database is compromised, attackers only get hashes, not actual passwords.</li>
            </ul>
        </section>
    </div>

    <main>
        <div class="hash-card">
            <form action="index.php" method="POST" id="hashForm">
                <div class="form-group">
                    <label for="message">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent-primary); vertical-align: middle; margin-right: 8px;"><path d="m12 22-8-4.5v-7L12 2l8 4.5v7Z"/><path d="m9 12 2 2 4-4"/></svg>
                        Input Message
                    </label>
                    <input type="text" id="message" name="message" placeholder="Enter text to hash..." value="<?php echo htmlspecialchars($input_text); ?>" required autofocus>
                </div>
                
                <button type="submit" id="generateBtn">
                    Generate Hash 
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px; vertical-align: middle;"><line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/></svg>
                </button>
            </form>

            <?php if (!empty($generated_hash)): ?>
            <div class="output-section" id="hashOutput">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><rect width="16" height="16" x="4" y="4" rx="2" ry="2"/><rect width="6" height="6" x="9" y="9" rx="1" ry="1"/><path d="M15 2v2"/><path d="M15 20v2"/><path d="M2 15h2"/><path d="M2 9h2"/><path d="M20 15h2"/><path d="M20 9h2"/><path d="M9 2v2"/><path d="M9 20v2"/></svg>
                    Generated Fingerprint
                </h3>
                <div class="hash-box" id="resultHash">
                    <?php echo $generated_hash; ?>
                </div>
                <div style="margin-top: 1.5rem; text-align: center;">
                    <p style="color: var(--accent-success); font-size: 0.95rem; font-weight: 500;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: middle;"><polyline points="20 6 9 17 4 12"/></svg>
                        Hash generated successfully
                    </p>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-dim); margin-top: 1.5rem; line-height: 1.5;">
                    SHA-256 (Secure Hash Algorithm) is a cryptographic hash function that produces a unique 256-bit (64-character) signature.
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- ============================================== -->
        <!-- TASKS 4 & 5 PLACEHOLDER: HASH COMPARISON TOOL  -->
        <!-- ============================================== -->
        <div class="hash-card" style="margin-top: 2rem;">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent-secondary); vertical-align: middle; margin-right: 12px;"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8.3a4 4 0 0 0-1.172-2.872L3 3"/><path d="m15 9 6-6"/></svg>
                Hash Comparison Tool
            </h2>
            
            <form action="index.php" method="POST" id="compareForm">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    <!-- Hash 1 Input (Task 4) -->
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <label for="hash1">Hash Value 1</label>
                        <input type="text" id="hash1" name="hash1" placeholder="Enter first SHA-256 hash..." value="<?php echo htmlspecialchars($hash1); ?>" required>
                    </div>
                    
                    <!-- Hash 2 Input (Task 4) -->
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <label for="hash2">Hash Value 2</label>
                        <input type="text" id="hash2" name="hash2" placeholder="Enter second SHA-256 hash..." value="<?php echo htmlspecialchars($hash2); ?>" required>
                    </div>
                </div>
                
                <!-- Compare Button (Task 5) -->
                <button type="submit" name="compare" style="margin-top: 2rem; background: linear-gradient(135deg, var(--accent-secondary), #9333ea);">
                    Compare Hashes
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px; vertical-align: middle;"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
            </form>

            <!-- Hash Comparison Output (Task 5) -->
            <?php if ($comparison_result !== ""): ?>
            <div class="output-section" style="margin-top: 2.5rem; animation: fadeIn 0.5s ease-out;">
                <h3>Comparison Result</h3>
                <div class="hash-box" style="<?php echo $comparison_result === 'Match' ? 'color: var(--accent-success); border-color: var(--accent-success);' : 'color: #ff4b2b; border-color: #ff4b2b;'; ?>">
                    <div style="display: flex; align-items: center; gap: 12px; font-weight: 800; font-size: 1.5rem;">
                        <?php if ($comparison_result === 'Match'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            MATCH
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                            NOT MATCH
                        <?php endif; ?>
                    </div>
                    <p style="font-size: 0.9rem; margin-top: 10px; color: var(--text-dim); font-weight: 400; font-family: var(--font-body);">
                        <?php echo $comparison_result === 'Match' ? 'The provided hashes are identical, confirming data integrity.' : 'The hashes are different, indicating that the data has been altered or is not the same.'; ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <footer style="margin-top: 3rem; font-size: 0.85rem; color: var(--text-secondary); text-align: center;">
        <p>&copy; <?php echo date("Y"); ?> Cybersecurity Educational Laboratory | Powered by PHP SHA-256</p>
    </footer>

</body>
</html>
