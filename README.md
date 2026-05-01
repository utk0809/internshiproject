# Cybersecurity Project Dashboard

A comprehensive suite of attack and defense simulations designed to demonstrate common web vulnerabilities, cryptographic utilities, and security concepts. This project serves as a central hub for various cybersecurity tasks, providing a hands-on learning environment for ethical hacking and security analysis.

##  Overview

The **Cybersecurity Project Dashboard** is a curated collection of 9 distinct tasks, each focusing on a specific area of cybersecurity. From steganography to complex injection attacks, this dashboard provides a unified interface to explore and understand modern security threats.

##  Included Projects

| Project | Title | Description |
| :--- | :--- | :--- |
| **Task 1** | [Steganography Suite](./Task%201/index.html) | Complete Image Steganography Platform. |
| **Task 2** | **Phishing Simulation** | Demonstration of phishing awareness, URL spoofing, and data collection. |
| **Task 3** | **Hash Generator** | Cryptographic utility for MD5, SHA-1, and SHA-256 algorithms. |
| **Task 4** | **XSS Demo** | Cross-Site Scripting attack vectors and mitigation strategies. |
| **Task 5** | **SQL Injection** | Interactive simulation of SQLi attacks and database vulnerabilities. |
| **Task 6** | **Ransomware Sim** | Simulating ransomware infection patterns and locking mechanisms. |
| **Task 7** | **Email Analysis** | Email header analyzer for tracking spoofing and message tracing. |
| **Task 8** | **Dictionary Attack** | Simulating password breaches using dictionary and brute-force techniques. |
| **Task 9** | **File Upload Sim** | Demonstrating file upload vulnerabilities and web shell risks. |

##  Getting Started

### Prerequisites

To run these simulations locally, you need to have **PHP** installed on your system.

- **PHP 7.4 or higher**
- **Python 3.x** (optional, for the automated runner script)

### Running the Project

The easiest way to launch the dashboard is using the provided Python runner:

1. Open your terminal in the project root directory.
2. Run the following command:
   ```bash
   python run.py
   ```
3. The script will automatically:
   - Detect your PHP installation.
   - Start a local PHP development server at `http://localhost:8000`.
   - Open your default web browser to the dashboard.

#### Manual Execution

If you prefer to run the server manually, use the following PHP command:
```bash
php -S localhost:8000
```
Then, navigate to `http://localhost:8000` in your web browser.

##  Project Structure

- `index.html`: The main dashboard interface.
- `style.css`: Premium styling for the dashboard.
- `run.py`: Automation script for starting the server.
- `Task X/`: Individual directories containing the source code for each simulation.
- `Task X.pdf`: Detailed documentation/reports for each task.

##  Disclaimer

These simulations are created for **educational purposes only**. Do not use any of the techniques demonstrated here on systems you do not have explicit permission to test. The goal is to promote security awareness and defensive coding practices.

---
*Created as part of the Cybersecurity Project Portfolio &copy; 2026*
