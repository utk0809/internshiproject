import http.server
import socketserver
import os

PORT = 8001

class CustomHTTPRequestHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        # Default routing to index.php
        if self.path == '/':
            self.path = '/index.php'
            
        # Ensure we serve .php files as text/html so the browser renders them
        if self.path.endswith('.php'):
            try:
                # Open the file and send its content
                file_path = self.translate_path(self.path)
                with open(file_path, 'rb') as f:
                    content = f.read()
                
                self.send_response(200)
                self.send_header("Content-type", "text/html; charset=utf-8")
                self.send_header("Content-Length", str(len(content)))
                self.end_headers()
                self.wfile.write(content)
                return
            except FileNotFoundError:
                self.send_error(404, "File not found")
                return
                
        # Serve other files (like style.css) normally
        return super().do_GET()

with socketserver.TCPServer(("", PORT), CustomHTTPRequestHandler) as httpd:
    print(f"Serving at http://localhost:{PORT}")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    httpd.server_close()
