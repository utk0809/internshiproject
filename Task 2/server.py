import http.server
import socketserver

PORT = 8000
Handler = http.server.SimpleHTTPRequestHandler

# Add a mapping so that PHP files are served as HTML, allowing them to render in the browser
Handler.extensions_map['.php'] = 'text/html'

with socketserver.TCPServer(("", PORT), Handler) as httpd:
    print("Serving at port", PORT)
    print("Go to http://localhost:8000/index.php in your browser.")
    httpd.serve_forever()
