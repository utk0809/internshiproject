<?php
$uploadMessage = '';
$messageClass = '';
$uploadedFileName = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $targetDir = "uploads/";
    
    // Ensure the uploads directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Check if a file was selected
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES["fileToUpload"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        
        // TASK A: INSECURE UPLOAD (Allows ANY file type)
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFilePath)) {
            $uploadMessage = "FILE TRANSMITTED SUCCESSFULLY! ALERT: System is currently running in VULNERABLE mode.";
            $messageClass = "success";
            $uploadedFileName = htmlspecialchars($fileName);
        } else {
            $uploadMessage = "CRITICAL ERROR: Failed to move the uploaded file. Check server permissions.";
            $messageClass = "error";
        }
    } else {
        $uploadMessage = "INPUT ERROR: Please select a valid file to intercept.";
        $messageClass = "error";
        
        if(isset($_FILES['fileToUpload'])) {
            switch ($_FILES['fileToUpload']['error']) {
                case UPLOAD_ERR_INI_SIZE: $uploadMessage = "PAYLOAD OVERSIZE: File exceeds server limits."; break;
                case UPLOAD_ERR_NO_FILE: $uploadMessage = "EMPTY HANDSHAKE: No file detected in the request."; break;
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
    <title>PHOENIX | Insecure File Lab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="nav-tabs">
        <a href="index.php" class="nav-tab active">PART A: INSECURE</a>
        <a href="secure.php" class="nav-tab">PART B: SECURE</a>
    </div>

    <div class="header">
        <h1>FILE UPLOAD <span>LAB</span></h1>
        <p class="subtitle">System Vulnerability Simulation</p>
    </div>

    <div class="card">
        <div class="educational-panel">
            <h3><span style="border: 1px solid var(--accent-purple); border-radius: 4px; padding: 0 4px; margin-right: 4px;">!</span> MISSION BRIEFING</h3>
            <p>This sequence demonstrates an <strong>unprotected</strong> file upload interface. The system accepts any file extension, including <code>.php</code>, <code>.html</code>, or <code>.exe</code>. In a real-world breach, an attacker would upload a web shell here to gain total control of the host.</p>
        </div>

        <form action="index.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <div class="file-input-wrapper">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 1rem; opacity: 0.5;">
                    <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" stroke="var(--accent-purple)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div style="font-family: var(--font-heading); font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase;">
                    Drop Payload or Click to Browse
                </div>
                <input type="file" name="fileToUpload" id="fileToUpload" class="file-input">
                <div id="selected-file-label">
                    <span id="selected-file-name" style="background: rgba(236,72,153,0.1); padding: 4px 12px; border-radius: 4px; color: var(--accent-pink);"></span>
                </div>
            </div>
            
            <button type="submit" name="submit" class="upload-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13 2v7h7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                INJECT PAYLOAD
            </button>
        </form>

        <?php if (!empty($uploadMessage)): ?>
            <div class="message <?php echo $messageClass; ?>">
                <div style="font-family: var(--font-heading); font-size: 0.7rem; margin-bottom: 0.5rem; letter-spacing: 0.05em; text-transform: uppercase;">LAB STATUS:</div>
                <div style="font-size: 0.9rem;"><?php echo $uploadMessage; ?></div>
                <?php if (!empty($uploadedFileName)): ?>
                    <div class="uploaded-filename">
                        <strong>LOCATED:</strong> <?php echo $uploadedFileName; ?>
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
