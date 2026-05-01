import http.server
import socketserver
import urllib.parse
import html
import datetime
import re
import os

PORT = 8000

vulnerable_comments = []
secure_comments = []

class PHPTestHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        if self.path == '/' or self.path == '/index.php':
            self.serve_html_file('index.php')
        elif self.path == '/vulnerable.php' or self.path.startswith('/vulnerable'):
            self.serve_vulnerable()
        elif self.path == '/secure.php' or self.path.startswith('/secure'):
            self.serve_secure()
        else:
            super().do_GET()

    def do_POST(self):
        content_length = int(self.headers['Content-Length'])
        post_data = self.rfile.read(content_length).decode('utf-8')
        params = urllib.parse.parse_qs(post_data)

        if self.path == '/vulnerable.php' or self.path.startswith('/vulnerable'):
            if 'clear' in params:
                vulnerable_comments.clear()
            elif 'name' in params and 'comment' in params:
                vulnerable_comments.append({
                    'name': params['name'][0],
                    'comment': params['comment'][0],
                    'attack_type': params.get('attack_type', ['Custom Payload'])[0],
                    'time': datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                })
            self.send_response(303)
            self.send_header('Location', '/vulnerable.php')
            self.end_headers()
            
        elif self.path == '/secure.php' or self.path.startswith('/secure'):
            if 'clear' in params:
                secure_comments.clear()
            elif 'name' in params and 'comment' in params:
                secure_comments.append({
                    'name': params['name'][0].strip(),
                    'comment': params['comment'][0].strip(),
                    'attack_type': params.get('attack_type', ['Custom Payload'])[0],
                    'time': datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                })
            self.send_response(303)
            self.send_header('Location', '/secure.php')
            self.end_headers()

    def serve_html_file(self, filename):
        if not os.path.exists(filename):
            self.send_error(404)
            return
            
        with open(filename, 'r', encoding='utf-8') as f:
            content = f.read()
        content = re.sub(r'<\?php.*?\?>', '', content, count=1, flags=re.DOTALL)
        
        self.send_response(200)
        self.send_header("Content-type", "text/html")
        self.end_headers()
        self.wfile.write(content.encode('utf-8'))

    def serve_vulnerable(self):
        with open('vulnerable.php', 'r', encoding='utf-8') as f:
            content = f.read()

        # Strip the first PHP block (backend logic — not executed by Python)
        content = re.sub(r'<\?php.*?\?>', '', content, flags=re.DOTALL)

        # Badge colour + icon map
        BADGE = {
            'Cookie Harvester':       ('#ef4444', '🍪'),
            'Keylogger':              ('#f59e0b', '⌨️'),
            'Page Defacement':        ('#dc2626', '💀'),
            'Phishing Overlay':       ('#ec4899', '🎣'),
            'Session Hijacker':       ('#8b5cf6', '🔓'),
            'DOM Manipulation':       ('#f97316', '🔀'),
            'Browser Fingerprinting': ('#06b6d4', '🎯'),
            'Custom Payload':         ('#94a3b8', '✏️'),
        }

        if not vulnerable_comments:
            inject_html = '''
            <div style="text-align:center;padding:3rem 0;color:var(--text-secondary);">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     style="opacity:0.25;margin-bottom:1rem;display:block;margin-left:auto;margin-right:auto">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
                <p style="font-size:0.9rem;">No injections yet.<br>Select an attack and fire a payload.</p>
            </div>'''
        else:
            inject_html = '<div class="comments-list">'
            for c in reversed(vulnerable_comments):
                at    = c.get('attack_type', 'Custom Payload')
                color, icon = BADGE.get(at, ('#94a3b8', '⚡'))
                avatar = (c['name'][0].upper() if c['name'] else '?')
                inject_html += f'''
                <div class="comment-item vuln-border" style="align-items:flex-start;">
                    <div class="comment-avatar"
                         style="background:linear-gradient(135deg,#b91c1c,#7f1d1d);flex-shrink:0;">
                        {avatar}
                    </div>
                    <div class="comment-content" style="flex:1;min-width:0;">
                        <div class="comment-meta" style="flex-wrap:wrap;gap:0.4rem;">
                            <span class="comment-author">{c['name']}</span>
                            <span class="attack-type-badge"
                                  style="background:rgba(0,0,0,0.3);color:{color};
                                         border:1px solid {color}44;border-radius:4px;
                                         padding:0.15rem 0.55rem;font-size:0.68rem;
                                         font-family:monospace;font-weight:700;">
                                {icon} {at}
                            </span>
                            <span class="comment-time" style="margin-left:auto;">{c['time']}</span>
                        </div>
                        <div style="font-family:monospace;font-size:0.8rem;
                                    background:rgba(0,0,0,0.25);
                                    border-left:3px solid {color};
                                    border-radius:0 6px 6px 0;
                                    padding:0.65rem 0.9rem;margin-top:0.4rem;
                                    word-break:break-all;color:#fca5a5;line-height:1.6;">
                            {c['comment']}
                        </div>
                    </div>
                </div>'''
            inject_html += '</div>'

        # Inject into marker — much more reliable than fragile regex
        count_str = str(len(vulnerable_comments))
        content = content.replace('<!-- XSS_INJECT -->', inject_html)
        # Also patch the PHP injection_count variable display
        content = re.sub(
            r'<\?php echo \$injection_count; \?>',
            count_str, content
        )
        # Remove any remaining PHP tags the regex missed
        content = re.sub(r'<\?php.*?\?>', '', content, flags=re.DOTALL)

        self.send_response(200)
        self.send_header('Content-type', 'text/html')
        self.end_headers()
        self.wfile.write(content.encode('utf-8'))

    def serve_secure(self):
        with open('secure.php', 'r', encoding='utf-8') as f:
            content = f.read()
            
        content = re.sub(r'<\?php.*?\?>', '', content, flags=re.DOTALL)
        
        comments_html = ''
        if not secure_comments:
            comments_html = '''
                    <div style="text-align: center; padding: 2rem 0; color: var(--text-secondary);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.2; margin-bottom: 1rem;"><path d="M19.69 14a6.9 6.9 0 0 0 .31-2V5l-8-3-3.16 1.18"></path><path d="M4.73 4.73L4 5v7c0 6 8 10 8 10a20.29 20.29 0 0 0 5.62-4.38"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                        <p style="font-size: 0.95rem;">System clear.<br>No malicious payloads detected.</p>
                    </div>
            '''
        else:
            BADGE = {
                'Cookie Harvester':       ('#10b981', '🍪', 'htmlspecialchars() converted quotes and brackets into HTML entities, preventing the browser from parsing document.cookie access and malicious iframe injection.'),
                'Keylogger':              ('#10b981', '⌨️', 'Event listeners via JavaScript were stripped of their executable context. The &lt;script&gt; tags are rendered inert, protecting keystrokes.'),
                'Page Defacement':        ('#10b981', '💀', 'By encoding &lt; and &gt;, the structural HTML injection is destroyed. The browser treats the defacement code as plain text instead of DOM elements.'),
                'Phishing Overlay':       ('#10b981', '🎣', 'The injected login form cannot render because HTML tags are safely encoded into displayable strings. Credential harvesting fails.'),
                'Session Hijacker':       ('#10b981', '🔓', 'The XMLHttpRequest code is rendered as text. The browser ignores the payload, leaving session credentials secure.'),
                'DOM Manipulation':       ('#10b981', '🔀', 'CSS and element selectors injected via Javascript fail to execute because the script context is escaped safely.'),
                'Browser Fingerprinting': ('#10b981', '🎯', 'System reconnaissance scripts are neutralized. The payload exists safely as text data rather than executing against navigator APIs.'),
                'Custom Payload':         ('#10b981', '✏️', 'Unknown payload. PHP output sanitization neutralized all special HTML characters.'),
            }
            comments_html = '<div class="comments-list">'
            for c in reversed(secure_comments):
                safe_name = html.escape(c['name'])
                safe_time = html.escape(c['time'])
                safe_comment = html.escape(c['comment']).replace('\n', '<br>')
                safe_avatar = html.escape(c['name'][0].upper()) if c['name'] else '?'
                at = c.get('attack_type', 'Custom Payload')
                safe_at = html.escape(at)
                color, icon, expl = BADGE.get(at, ('#10b981', '⚡', 'Output safely encoded.'))
                safe_expl = html.escape(expl)
                
                comments_html += f'''
                        <div class="comment-item sec-border" style="align-items:flex-start;">
                            <div class="comment-avatar" style="flex-shrink:0;">
                                {safe_avatar}
                            </div>
                            <div class="comment-content" style="flex:1;min-width:0;">
                                <div class="comment-meta">
                                    <span class="comment-author">{safe_name}</span>
                                    <span class="attack-type-badge">
                                        {icon} {safe_at}
                                    </span>
                                    <span class="comment-time">{safe_time}</span>
                                </div>
                                <div class="comment-payload-box">
                                    {safe_comment}
                                </div>
                            </div>
                        </div>'''
            comments_html += '</div>'
            
        # Update injection count display
        count_str = str(len(secure_comments))
        content = re.sub(
            r'<\?php echo \$injection_count; \?>',
            count_str, content
        )

        # We'll use a marker-based replacement.
        # But since the Python script strips PHP tags but leaves the inner HTML,
        # we need to remove everything after SECURE_INJECT up to the closing div of the right column.
        # We can just replace the entire block from <!-- SECURE_FEED_START --> to <!-- SECURE_FEED_END -->
        if '<!-- SECURE_FEED_START -->' in content and '<!-- SECURE_FEED_END -->' in content:
            content = re.sub(r'<!-- SECURE_FEED_START -->.*?<!-- SECURE_FEED_END -->', comments_html, content, flags=re.DOTALL)
        elif '<!-- SECURE_INJECT -->' in content:
            # Fallback
            content = re.sub(r'<!-- SECURE_INJECT -->.*?(</div>\s*</div>\s*</div>\s*<footer>)', rf'{comments_html}\n\1', content, flags=re.DOTALL)
        else:
            content = re.sub(r'(<div class="comments-header">.*?</form>\s*</div>).*?(</div>\s*</div>\s*</div>\s*<footer>)', rf'\1\n{comments_html}\n\2', content, flags=re.DOTALL)
        
        self.send_response(200)
        self.send_header("Content-type", "text/html")
        self.end_headers()
        self.wfile.write(content.encode('utf-8'))

socketserver.TCPServer.allow_reuse_address = True

if __name__ == '__main__':
    with socketserver.TCPServer(("", PORT), PHPTestHandler) as httpd:
        print(f"Server started. Python simulating PHP behavior at http://localhost:{PORT}")
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            pass
