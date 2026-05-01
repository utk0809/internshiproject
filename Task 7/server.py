import http.server
import socketserver
import os

PORT = 8081

class CustomHTTPRequestHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        # Default routing to index.html
        if self.path == '/' or self.path == '/index.php':
            self.path = '/index.html'
        elif self.path == '/explanation.php':
            self.path = '/explanation.html'
            
        return super().do_GET()

# Ensure we are in the correct directory if needed, 
# but usually it's run from the project root.

with socketserver.TCPServer(("", PORT), CustomHTTPRequestHandler) as httpd:
    httpd.allow_reuse_address = True
    print(f"Serving at http://localhost:{PORT}")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    httpd.server_close()
