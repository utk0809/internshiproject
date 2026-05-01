# XSS Attack Demo Project: Handover & Status Report

## Project Overview
This educational project demonstrates Cross-Site Scripting (XSS) attacks and their prevention. It is built strictly using **HTML, CSS, and PHP** to fulfill the assignment requirements. The project consists of a landing page and two functional demonstrations:
1. **Vulnerable Version (Task 2):** A comment box that executes raw injected scripts.
2. **Secure Version (Task 3):** A protected comment box that sanitizes input, preventing script execution.

## Current Project Status
**Status:** ✅ Ready for Deployment (MilesWeb)

All core requirements outlined in the `xss_attack_demo_project_task.pdf` have been successfully implemented. The project is fully functional locally and is prepared to be uploaded to your MilesWeb subdomain.

---

## Technical Details & Changes Implemented

### 1. Unified Interface and Theme
- **Consistent Styling:** Both the landing page (`index.php`) and the secure application page (`secure.php`) now share a unified, premium light-theme design (`style-light.css`). This provides a seamless transition from the start page to the functional demo.
- **Responsive Layout:** The main grid layout and Attack Arsenal interface have been made deeply responsive with CSS media queries (`max-width: 992px` and `600px`), ensuring the tool is fully usable on mobile screens or half-windows without layout breaking or text overflow.

### 2. Task 3: Secure Version (`secure.php`)
This file was heavily modified to provide an interactive, professional demonstration of security practices while maintaining absolute backend protection.
- **Sanitization Pipeline Visualization:** A visual flow was included to demonstrate the data path to students:
  1. *Raw Input:* `<script>alert('XSS')</script>`
  2. *Processing:* `htmlspecialchars($input, ENT_QUOTES, 'UTF-8')`
  3. *Safe Encoded Output:* `&lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;`
- **Mitigation Explanations (New Feature):** When an attack payload is selected from the Arsenal panel, an automated "Mitigation Report" natively appears in the Attack Info box. It dynamically explains to the user *why* and *how* `htmlspecialchars()` is effectively neutralizing that specific attack vector (e.g., explaining how a DOM Manipulation attack is defeated).
- **Core Security Logic Verification:**
  - Input is collected securely via POST and stored safely in a mutable session.
  - The critical vulnerability patch is deployed precisely at the **output layer**. When rendering the mutable `$_SESSION` data back to the browser, the data is wrapped in `htmlspecialchars()`, neutralizing any injected payloads indiscriminately.

### 3. Frontend Interactivity & Backend Security Balance
- **Frontend JavaScript Restored for UI:** Client-side JavaScript (like `feather-icons`) was carefully re-introduced exclusively for UI state management (auto-filling payloads and showing mitigation info).
- **Zero-Trust Backend:** Despite the interactive frontend scripts, the backend PHP form handler treats all input entirely as untrusted. The security model does not rely on javascript validation; the PHP `htmlspecialchars()` handles 100% of the vulnerability mitigation.
- **Presentation:** Removed the "Task 2" and "Task 3" highlight badges from the `index.php` cards to make the application look like a standalone security tool rather than a course template.

---

## Deployment Instructions for Team Members

When deploying to MilesWeb, please ensure the following:

1. **Files to Upload:**
   - `index.php`
   - `vulnerable.php`
   - `secure.php`
   - `style.css`
   - `style-light.css`
2. **Exclusions:**
   - Do **NOT** upload the `server.py` file. This was a temporary development script used to simulate PHP locally during the building phase. It is not needed on a real Apache/Nginx web server.
   - Do **NOT** upload the original requirements `xss_attack_demo_project_task.pdf` or any `.md` files (like this report) to the public `public_html` directory to keep the server clean.
3. **Environment:** Ensure the MilesWeb subdomain is configured to process PHP 7.x or 8.x (the code is compatible with both).

If you have any questions regarding the sanitization pipeline flow in `secure.php`, refer to the inline PHP comments in the file!
