<?php
// Educational demonstration - Phishing simulation
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="https://static.cdninstagram.com/rsrc.php/yv/r/BTPhT6yIYfq.ico" type="image/x-icon">
</head>
<body class="ig-dark-body">
    <div class="ig-page-container">
        <main class="ig-main-desktop">
            <div class="ig-desktop-layout">
                <!-- LEFT SIDE: Marketing Graphic -->
                <div class="ig-marketing-side">
                    <div class="ig-marketing-logo">
                        <i data-visualcompletion="css-img" aria-label="Instagram" class="" role="img" style="background-image: url('https://static.cdninstagram.com/rsrc.php/v3/yM/r/8n91YnfPq0s.png'); background-position: -69px -185px; background-size: 473px 528px; width: 44px; height: 44px; background-repeat: no-repeat; display: inline-block; filter: invert(1);"></i>
                    </div>
                    
                    <div class="ig-marketing-text">
                        <h1>See everyday moments from your<br><span class="gradient-text">close friends</span>.</h1>
                    </div>
                    
                    <div class="ig-marketing-graphic">
                        <div class="ig-phones-container">
                            <img class="ig-screenshot ig-screenshot-1 ig-active-screenshot" src="https://static.cdninstagram.com/images/instagram/xig/homepage/screenshots/screenshot1-2x.png" alt="">
                            <img class="ig-screenshot ig-screenshot-2" src="https://static.cdninstagram.com/images/instagram/xig/homepage/screenshots/screenshot2-2x.png" alt="">
                            <img class="ig-screenshot ig-screenshot-3" src="https://static.cdninstagram.com/images/instagram/xig/homepage/screenshots/screenshot3-2x.png" alt="">
                            <img class="ig-screenshot ig-screenshot-4" src="https://static.cdninstagram.com/images/instagram/xig/homepage/screenshots/screenshot4-2x.png" alt="">
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: Login Panel -->
                <div class="ig-login-side">
                    <div class="ig-login-panel">
                        <div class="ig-login-box">
                            <h2 class="ig-form-title">Log into Instagram</h2>
                            <div class="ig-login-form">
                                <form id="loginForm" method="GET" action="awareness.php">
                                    <div class="ig-input-group">
                                        <div class="ig-input-wrapper">
                                            <label class="ig-input-label">
                                                <span class="ig-label-text">Mobile number, username or email</span>
                                                <input type="text" name="username" class="ig-input" aria-required="true" maxlength="75" required oninput="handleInput(this)">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ig-input-group">
                                        <div class="ig-input-wrapper">
                                            <label class="ig-input-label">
                                                <span class="ig-label-text" id="pwdLabelText">Password</span>
                                                <input type="password" name="password" class="ig-input" id="pwdInput" aria-required="true" required oninput="handleInput(this)">
                                            </label>
                                            <div class="ig-show-pwd" style="display:none;" onclick="togglePwd()">Show</div>
                                        </div>
                                    </div>
                                    <div class="ig-submit-group">
                                        <button class="ig-submit-btn" type="submit" disabled id="loginBtn">
                                            <div class="ig-btn-content">Log in</div>
                                        </button>
                                    </div>
                                    
                                    <a class="ig-forgot-pwd" href="#" tabindex="0">Forgot password?</a>
                                </form>
                            </div>
                            
                            <div class="ig-secondary-actions">
                                <button type="button" class="ig-fb-btn ig-secondary-btn">
                                    <span class="ig-fb-icon"></span>
                                    <span class="ig-fb-text">Log in with Facebook</span>
                                </button>
                                
                                <a href="#" class="ig-secondary-btn ig-create-btn">Create new account</a>
                            </div>
                            
                            <div class="ig-meta-logo">
                                <i data-visualcompletion="css-img" aria-label="Meta" class="" role="img" style="background-image: url('https://static.cdninstagram.com/rsrc.php/v3/yM/r/8n91YnfPq0s.png'); background-position: -464px -235px; background-size: 473px 528px; width: 60px; height: 12px; background-repeat: no-repeat; display: inline-block; filter: invert(1) opacity(0.5);"></i>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="ig-footer ig-dark-footer" role="contentinfo">
            <div class="ig-footer-content">
                <div class="ig-footer-links">
                    <a href="#">Meta</a>
                    <a href="#">About</a>
                    <a href="#">Blog</a>
                    <a href="#">Jobs</a>
                    <a href="#">Help</a>
                    <a href="#">API</a>
                    <a href="#">Privacy</a>
                    <a href="#">Terms</a>
                    <a href="#">Locations</a>
                    <a href="#">Instagram Lite</a>
                    <a href="#">Meta AI</a>
                    <a href="#">Threads</a>
                    <a href="#">Contact Uploading & Non-Users</a>
                    <a href="#">Meta Verified</a>
                </div>
                <div class="ig-footer-copyright">
                    <span class="ig-language">
                        <select aria-label="Switch Display Language">
                            <option value="en">English</option>
                        </select>
                        <i class="fa fa-chevron-down" style="font-size: 10px;"></i>
                    </span>
                    <span>© 2024 Instagram from Meta</span>
                </div>
            </div>
        </footer>
    </div>
    <script>
        function handleInput(elem) {
            const wrapper = elem.closest('.ig-input-group');
            const labelText = wrapper.querySelector('.ig-label-text');
            const pwdToggle = wrapper.querySelector('.ig-show-pwd');
            
            if(elem.value.length > 0) {
                elem.classList.add('has-value');
                labelText.classList.add('active');
                if(elem.type === 'password' || elem.id === 'pwdInput') {
                    if(pwdToggle) pwdToggle.style.display = 'block';
                }
            } else {
                elem.classList.remove('has-value');
                labelText.classList.remove('active');
                if(elem.type === 'password' || elem.id === 'pwdInput') {
                    if(pwdToggle) pwdToggle.style.display = 'none';
                }
            }
            
            checkFormValidity();
        }

        function togglePwd() {
            const input = document.getElementById('pwdInput');
            const toggleBtn = document.querySelector('.ig-show-pwd');
            if (input.type === 'password') {
                input.type = 'text';
                toggleBtn.textContent = 'Hide';
            } else {
                input.type = 'password';
                toggleBtn.textContent = 'Show';
            }
        }

        function checkFormValidity() {
            const inputs = document.querySelectorAll('.ig-input');
            const btn = document.getElementById('loginBtn');
            let valid = true;
            inputs.forEach(input => {
                if(input.value.length < 1) valid = false;
                if((input.type === 'password' || input.id === 'pwdInput') && input.value.length < 6) valid = false;
            });
            
            if(valid) {
                btn.removeAttribute('disabled');
            } else {
                btn.setAttribute('disabled', 'true');
            }
        }

        // Instagram classic phone image rotation
        const screenshots = document.querySelectorAll('.ig-screenshot');
        let currentIdx = 0;
        
        if (screenshots.length > 0) {
            setInterval(() => {
                const prevIdx = currentIdx;
                currentIdx = (currentIdx + 1) % screenshots.length;
                
                screenshots[prevIdx].classList.remove('ig-active-screenshot');
                screenshots[prevIdx].classList.add('ig-prev-screenshot');
                
                screenshots[currentIdx].classList.add('ig-active-screenshot');
                screenshots[currentIdx].classList.remove('ig-prev-screenshot');
                
                setTimeout(() => {
                    screenshots[prevIdx].classList.remove('ig-prev-screenshot');
                }, 1500); // Wait for fade-out
                
            }, 5000); // Change image every 5 seconds
        }
    </script>
</body>
</html>