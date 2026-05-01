import pypdf

reader = pypdf.PdfReader(r'd:\CS T02\phishing simulation project task.pdf')
with open(r'd:\CS T02\pdf_text.txt', 'w', encoding='utf-8') as f:
    for i, page in enumerate(reader.pages):
        f.write(f"--- PAGE {i+1} ---\n")
        f.write(page.extract_text() + "\n")
