import os
import subprocess
import webbrowser
import time
import sys

def main():
    print("="*50)
    print("  Starting PHP Development Server for Projects")
    print("="*50)
    
    # Optional: Automatically kill process on port 8000 (usually complex on Windows, so we'll just try to run it)
    port = 8000
    
    common_php_paths = [
        "php", # Check PATH first
        r"C:\xampp\php\php.exe",
        r"C:\wamp\bin\php\php.exe",
        r"C:\php\php.exe"
    ]
    
    php_command = None
    for path in common_php_paths:
        try:
            subprocess.run([path, "-v"], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL, check=True)
            php_command = path
            break
        except (FileNotFoundError, subprocess.CalledProcessError):
            continue

    if php_command:
        print(f"[+] PHP found: {php_command}")
    else:
        print("[-] Error: PHP is not installed or not in the system path.")
        print("    Please install PHP and add it to your PATH to run these projects.")
        input("Press Enter to exit...")
        sys.exit(1)

    print(f"[*] Starting server at http://localhost:{port} ...")
    
    # Start the PHP server
    server_process = subprocess.Popen([php_command, "-S", f"localhost:{port}"])

    # Give the server a moment to spin up
    time.sleep(1.5)

    print("[*] Opening your browser...")
    webbrowser.open(f"http://localhost:{port}/index.html")

    print("\n[+] Server is running! Press Ctrl+C in this terminal to stop.")
    
    try:
        server_process.wait()
    except KeyboardInterrupt:
        print("\n[*] Stopping the server...")
        server_process.terminate()
        print("[+] Server stopped successfully.")

if __name__ == "__main__":
    main()
