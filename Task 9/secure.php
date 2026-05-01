<?php
$uploadMessage = '';
$messageClass = '';
$uploadedFileName = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $targetDir = "uploads/";
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES["fileToUpload"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        
        // TASK B: SECURE UPLOAD (Restricts file types)
        $allowedTypes = ['jpg', 'png', 'jpeg', 'gif', 'pdf', 'txt'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $uploadMessage = "ACCESS DENIED: File extension [.$fileType] is not whitelisted. Upload aborted.";
            $messageClass = "error";
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFilePath)) {
                $uploadMessage = "FILE VERIFIED AND TRANSMITTED: System security policy enforced.";
                $messageClass = "success";
                $uploadedFileName = htmlspecialchars($fileName);
            } else {
                $uploadMessage = "CRITICAL ERROR: Failed to move the uploaded file. Check server permissions.";
                $messageClass = "error";
            }
        }
    } else {
        $uploadMessage = "INPUT ERROR: Please select a valid file to verify.";
        $messageClass = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHOENIX | Secure File Lab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Specific accent for Secure Mode */
        :root {
            --secure-accent: #10b981;
            --secure-glow: rgba(16, 185, 129, 0.4);
        }
        .nav-tab.active {
            background: linear-gradient(90deg, #10b981, #3b82f6);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
        .header h1 span {
            color: var(--secure-accent);
            -webkit-text-fill-color: var(--secure-accent);
        }
        .header h1 {
            background: linear-gradient(90deg, #10b981, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .educational-panel {
            border-left-color: var(--secure-accent);
        }
        .educational-panel h3 {
            color: var(--secure-accent);
        }
        .educational-panel h3 span {
            border-color: var(--secure-accent);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-tabs">
        <a href="index.php" class="nav-tab">PART A: INSECURE</a>
        <a href="secure.php" class="nav-tab active">PART B: SECURE</a>
    </div>

    <div class="header">
        <h1>SECURE UPLOAD <span>VERIFIED</span></h1>
        <p class="subtitle">Enforcing System Integrity</p>
    </div>

    <div class="card">
        <div class="educational-panel">
            <h3><span style="border: 1px solid var(--secure-accent); border-radius: 4px; padding: 0 4px; margin-right: 4px;">✔</span> MISSION DEFENSE</h3>
            <p>This sequence demonstrates a <strong>hardened</strong> file upload interface. The system enforces strict <strong>extension whitelisting</strong>. Only safe files (JPG, PNG, PDF, etc.) are permitted. Any attempt to upload <code>.php</code> or <code>.exe</code> payloads will be blocked by the security gate.</p>
        </div>

        <form action="secure.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <div class="file-input-wrapper" style="border-style: solid; border-color: rgba(16, 185, 129, 0.2);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 1rem; opacity: 0.8;">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="var(--secure-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 12l2 2 4-4" stroke="var(--secure-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div style="font-family: var(--font-heading); font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase;">
                    Select Safe File for Verification
                </div>
                <input type="file" name="fileToUpload" id="fileToUpload" class="file-input">
                <div id="selected-file-label">
                    <span id="selected-file-name" style="background: rgba(16,185,129,0.1); padding: 4px 12px; border-radius: 4px; color: var(--secure-accent);"></span>
                </div>
            </div>
            
            <button type="submit" name="submit" class="upload-btn" style="background: linear-gradient(90deg, #10b981, #3b82f6); box-shadow: 0 10px 30px rgba(16,185,129,0.2);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                VERIFY & UPLOAD
            </button>
        </form>

        <?php if (!empty($uploadMessage)): ?>
            <div class="message <?php echo $messageClass; ?>">
                <div style="font-family: var(--font-heading); font-size: 0.7rem; margin-bottom: 0.5rem; letter-spacing: 0.05em; text-transform: uppercase;">DEFENSE GATE STATUS:</div>
                <div style="font-size: 0.9rem;"><?php echo $uploadMessage; ?></div>
                <?php if (!empty($uploadedFileName)): ?>
                    <div class="uploaded-filename">
                        <strong>VERIFIED:</strong> <?php echo $uploadedFileName; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('fileToUpload').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : '';
        const label = document.getElementById('selected-file-label');
        const nameSpan = document.getElementById('selected-file-name');
        
        if (fileName) {
            nameSpan.textContent = fileName;
            label.style.display = 'block';
        } else {
            label.style.display = 'none';
        }
    });
</script>

</body>
</html>
