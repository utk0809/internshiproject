import http.server
import socketserver
import urllib.parse
import hashlib
import datetime
import html
import re

# We use 8085 to avoid any previous port issues
PORT = 8086

class Handler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        if self.path == '/' or self.path.endswith('/index.php'):
            self.serve_mock_php()
        else:
            return http.server.SimpleHTTPRequestHandler.do_GET(self)

    def do_POST(self):
        content_length = int(self.headers.get('Content-Length', 0))
        post_data = self.rfile.read(content_length).decode('utf-8')
        params = urllib.parse.parse_qs(post_data)
        
        # Determine which action was taken
        message = params.get('message', [''])[0]
        hash1 = params.get('hash1', [''])[0]
        hash2 = params.get('hash2', [''])[0]
        is_comparing = 'compare' in params
        
        self.serve_mock_php(message, hash1, hash2, is_comparing)

    def serve_mock_php(self, message="", hash1="", hash2="", is_comparing=False):
        try:
            with open('index.php', 'r', encoding='utf-8') as f:
                raw_content = f.read()

            # 1. Strip the PHP logic block at the top
            content = re.sub(r'<\?php.*?(\?>)', '', raw_content, flags=re.DOTALL, count=1)

            # 2. Variable Replacements
            content = content.replace('<?php echo htmlspecialchars($input_text); ?>', html.escape(message))
            content = content.replace('<?php echo htmlspecialchars($hash1); ?>', html.escape(hash1))
            content = content.replace('<?php echo htmlspecialchars($hash2); ?>', html.escape(hash2))
            content = content.replace('<?php echo date("Y"); ?>', str(datetime.datetime.now().year))

            # 3. Process Generation Logic (Task 1-3)
            if message:
                gen_hash = hashlib.sha256(message.encode('utf-8')).hexdigest()
                # Find the if...endif block for generation
                # We use a non-greedy match to find the first block
                gen_pattern = r'<\?php if \(!empty\(\$generated_hash\)\): \?>(.*?)<\?php endif; \?>'
                def replace_gen(m):
                    inner = m.group(1)
                    return inner.replace('<?php echo $generated_hash; ?>', gen_hash)
                content = re.sub(gen_pattern, replace_gen, content, flags=re.DOTALL)
            else:
                # Remove it
                content = re.sub(r'<\?php if \(!empty\(\$generated_hash\)\): \?>.*?<\?php endif; \?>', '', content, flags=re.DOTALL)

            # 4. Process Comparison Logic (Task 4-5)
            if is_comparing:
                h1 = hash1.strip()
                h2 = hash2.strip()
                is_match = (h1 == h2) and h1 != ""
                
                # Find the comparison block
                comp_pattern = r'<\?php if \(\$comparison_result !== ""\): \?>(.*?)<\?php endif; \?>'
                
                def replace_comp(m):
                    inner = m.group(1)
                    
                    # Apply styles
                    match_style = 'color: var(--accent-success); border-color: var(--accent-success);'
                    not_match_style = 'color: #ff4b2b; border-color: #ff4b2b;'
                    style = match_style if is_match else not_match_style
                    
                    # replace the ternary style
                    inner = re.sub(r'style="<\?php echo \$comparison_result === \'Match\' \? \'[^\']*\' : \'[^\']*\'; \?>"', f'style="{style}"', inner)
                    
                    # handle Match/Not Match icons/labels
                    if is_match:
                        # Keep only the Match part of the internal if
                        inner = re.sub(r'<\?php if \(\$comparison_result === \'Match\'\): \?>(.*?)<\?php else: \?>.*?<\?php endif; \?>', r'\1', inner, flags=re.DOTALL)
                        inner = inner.replace("<?php echo $comparison_result === 'Match' ? 'The provided hashes are identical, confirming data integrity.' : 'The hashes are different, indicating that the data has been altered or is not the same.'; ?>", "The provided hashes are identical, confirming data integrity.")
                    else:
                        # Keep only the else part
                        inner = re.sub(r'<\?php if \(\$comparison_result === \'Match\'\): \?>.*?<\?php else: \?>(.*?)<\?php endif; \?>', r'\1', inner, flags=re.DOTALL)
                        inner = inner.replace("<?php echo $comparison_result === 'Match' ? 'The provided hashes are identical, confirming data integrity.' : 'The hashes are different, indicating that the data has been altered or is not the same.'; ?>", "The hashes are different, indicating that the data has been altered or is not the same.")
                    
                    # Add an anchor and scroll script
                    return f'<div id="demo-anchor"></div>{inner}<script>document.getElementById("demo-anchor").scrollIntoView({{behavior: "smooth", block: "center"}});</script>'

                content = re.sub(comp_pattern, replace_comp, content, flags=re.DOTALL)
            else:
                content = re.sub(r'<\?php if \(\$comparison_result !== ""\): \?>.*?<\?php endif; \?>', '', content, flags=re.DOTALL)

            self.send_response(200)
            self.send_header('Content-type', 'text/html; charset=utf-8')
            self.end_headers()
            self.wfile.write(content.encode('utf-8'))
            
        except Exception as e:
            self.send_response(500)
            self.end_headers()
            self.wfile.write(f"Simulation Error: {e}".encode('utf-8'))

with socketserver.TCPServer(("", PORT), Handler) as httpd:
    print(f"Server started: http://localhost:{PORT}")
    httpd.serve_forever()
