import urllib.request
import re

with open('ig_source.html', 'r', encoding='utf-16') as f:
    try:
        content = f.read()
    except UnicodeError:
        with open('ig_source.html', 'rb') as fb:
            content = fb.read().decode('utf-8', errors='ignore')

static_urls = re.findall(r'https://static\.cdninstagram\.com/[a-zA-Z0-9.\-_/?=&]+', content)

print("Found static images:")
for url in list(set(static_urls)):
    if '.jpg' in url or '.png' in url:
        print(url)
