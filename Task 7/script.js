document.addEventListener('DOMContentLoaded', () => {
    const analyzeBtn = document.getElementById('analyze-btn');
    const headerInput = document.getElementById('header-input');
    const resultsContainer = document.getElementById('results-container');

    // Educational Explanations for Students
    const explanations = {
        'from': 'Specifies the sender as shown in the email client. WARNING: This can be easily spoofed!',
        'to': 'The primary recipient address of the email.',
        'subject': 'The topic or title of the email message.',
        'date': 'The timestamp when the sender\'s system dispatched the email.',
        'received': 'Crucial for tracing! Each server that handles the email adds one of these. The bottom-most is the real origin.',
        'return-path': 'Where bounce messages go. If this doesn\'t match the "From" domain, it often indicates spoofing.',
        'reply-to': 'Indicates where replies should be sent, which can be different from the sender address.',
        'x-mailer': 'The software used to compose or send the email (e.g., Outlook, Gmail, or custom scripts).',
        'message-id': 'A unique identifier for the message, assigned by the originating mail system.',
        'authentication-results': 'Records of security checks like SPF, DKIM, and DMARC performed by the receiving server.'
    };

    if (analyzeBtn && headerInput) {
        analyzeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const rawHeader = headerInput.value.trim();
            if (!rawHeader) {
                alert("Please paste an email header to analyze.");
                return;
            }
            const parsedData = parseEmailHeaders(rawHeader);
            displayResults(parsedData);
            
            // Auto-scroll to results
            resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    function parseEmailHeaders(rawHeader) {
        const unfolded = rawHeader.replace(/\r?\n[ \t]+/g, ' ');
        const lines = unfolded.split('\n');
        
        const headers = {};
        lines.forEach(line => {
            const match = line.match(/^([a-zA-Z0-9-]+):(.*)$/);
            if (match) {
                const key = match[1].toLowerCase();
                const val = match[2].trim();
                if (!headers[key]) headers[key] = [];
                headers[key].push(val);
            }
        });

        const data = {
            info: {},
            security: { spf: 'unknown', dkim: 'unknown', dmarc: 'unknown' },
            hops: [],
            spoof_risk: null
        };

        const infoFields = ['from', 'to', 'subject', 'date', 'return-path', 'reply-to', 'x-mailer', 'message-id'];
        infoFields.forEach(field => {
            if (headers[field]) {
                data.info[field] = headers[field][0];
            }
        });

        if (headers['authentication-results']) {
            const authStr = headers['authentication-results'].join(' ');
            const spfMatch = authStr.match(/spf=(pass|fail|softfail|neutral|none)/i);
            const dkimMatch = authStr.match(/dkim=(pass|fail|none)/i);
            const dmarcMatch = authStr.match(/dmarc=(pass|fail|none)/i);
            
            if (spfMatch) data.security.spf = spfMatch[1].toLowerCase();
            if (dkimMatch) data.security.dkim = dkimMatch[1].toLowerCase();
            if (dmarcMatch) data.security.dmarc = dmarcMatch[1].toLowerCase();
        }

        if (headers['received']) {
            data.hops = headers['received'].map(hopDetails => {
                const hop = {
                    full: hopDetails,
                    from: 'Unknown',
                    by: 'Unknown',
                    ip: 'Not Found',
                    date: ''
                };
                
                // IP Extraction (v4 and v6)
                const ipv4Regex = /\b(?:\d{1,3}\.){3}\d{1,3}\b/;
                const ipv6Regex = /(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/;
                
                const v4 = hopDetails.match(ipv4Regex);
                const v6 = hopDetails.match(ipv6Regex);
                if (v4) hop.ip = v4[0];
                else if (v6) hop.ip = v6[0];

                const fromMatch = hopDetails.match(/from\s+([^\s;]+)/i);
                const byMatch = hopDetails.match(/by\s+([^\s;]+)/i);
                if (fromMatch) hop.from = fromMatch[1];
                if (byMatch) hop.by = byMatch[1];
                
                const parts = hopDetails.split(';');
                if (parts.length > 1) {
                    hop.date = parts[parts.length - 1].trim();
                }
                return hop;
            }).reverse();
        }

        const spoofReasons = [];
        const from = data.info['from'] || '';
        const returnPath = data.info['return-path'] || '';
        const fromDomainMatch = from.match(/@([^\s>]+)/);
        const returnDomainMatch = returnPath.match(/@([^\s>]+)/);
        const fromDomain = fromDomainMatch ? fromDomainMatch[1].toLowerCase() : '';
        const returnDomain = returnDomainMatch ? returnDomainMatch[1].toLowerCase() : '';
        
        if (fromDomain && returnDomain && fromDomain !== returnDomain) {
            spoofReasons.push(`Domain Mismatch: From domain (${fromDomain}) differs from Return-Path (${returnDomain}).`);
        }
        
        if (data.security.spf === 'fail') {
            spoofReasons.push("SPF Check Failed: Sender is not authorized by the domain's policy.");
        }

        if (spoofReasons.length > 0) {
            data.spoof_risk = spoofReasons;
        }

        return data;
    }

    function displayResults(data) {
        resultsContainer.innerHTML = '';
        resultsContainer.style.display = 'block';

        const securityCard = document.createElement('div');
        securityCard.className = 'glass routing-card';
        
        let securityHTML = `<h2><span style="color: var(--accent-purple)">&#9670;</span> Security Verification</h2>`;
        
        if (data.spoof_risk) {
            securityHTML += `
                <div class="security-tip" style="border-left-color: var(--accent-red); background: rgba(255, 0, 85, 0.05); margin-bottom: 2rem; padding: 1.5rem; border-left-width: 4px; border-radius: 12px;">
                    <span class="tip-label" style="color: var(--accent-red); display: block; font-family: var(--font-heading); font-size: 0.8rem; text-transform: uppercase; margin-bottom: 0.5rem;">SPOOFING ALERT</span>
                    <ul style="color: var(--text-secondary); padding-left: 1.2rem; font-size: 0.95rem;">
                        ${data.spoof_risk.map(reason => `<li>${reason}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        securityHTML += `<div class="security-check" style="margin-bottom: 2rem;">`;
        ['spf', 'dkim', 'dmarc'].forEach(key => {
            const status = data.security[key];
            let badgeClass = 'badge-yellow';
            if (status === 'pass') badgeClass = 'badge-green';
            if (status === 'fail' || status === 'softfail') badgeClass = 'badge-red';
            securityHTML += `<div class="badge ${badgeClass}">${key.toUpperCase()}: ${status.toUpperCase()}</div>`;
        });
        securityHTML += `</div>`;

        securityHTML += `<div class="info-grid">`;
        for (const [label, value] of Object.entries(data.info)) {
            const explanation = explanations[label] || "Email header field providing metadata about the transmission.";
            securityHTML += `
                <div class="info-item">
                    <span class="info-label">
                        ${label.replace(/-/g, ' ')}
                        <span class="edu-info">?</span>
                    </span>
                    <span class="tooltip">${explanation}</span>
                    <span class="info-value">${escapeHTML(value)}</span>
                </div>
            `;
        }
        securityHTML += `</div>`;
        
        securityCard.innerHTML = securityHTML;
        resultsContainer.appendChild(securityCard);

        if (data.hops.length > 0) {
            const traceCard = document.createElement('div');
            traceCard.className = 'glass routing-card';
            
            let traceHTML = `<h2><span style="color: var(--accent-blue)">&#9670;</span> Routing Trace (${data.hops.length} Hops)</h2>`;
            traceHTML += `<div class="routing-timeline" style="margin-top: 2rem;">`;
            
            data.hops.forEach((hop, index) => {
                const labelColor = index === 0 ? 'var(--accent-green)' : (index === data.hops.length - 1 ? 'var(--accent-pink)' : 'var(--accent-blue)');
                const markerTitle = index === 0 ? 'ORIGIN' : (index === data.hops.length - 1 ? 'DESTINATION' : `HOP ${index + 1}`);
                
                traceHTML += `
                    <div class="hop-item">
                        <div class="hop-icon" style="background: ${labelColor}">${index + 1}</div>
                        <div class="hop-content">
                            <span style="font-family: var(--font-heading); font-size: 0.7rem; color: ${labelColor}; margin-bottom: 0.5rem; display: block;">${markerTitle}</span>
                            <div class="hop-header">
                                <div style="flex: 1">
                                    <span class="hop-label">Source Network:</span> <span class="hop-value">${escapeHTML(hop.from)}</span>
                                </div>
                                <div style="flex: 1">
                                    <span class="hop-label">Resolved IP:</span> <span class="hop-value" style="color: var(--accent-yellow)">${escapeHTML(hop.ip)}</span>
                                </div>
                            </div>
                            <div class="hop-header">
                                <div style="flex: 1">
                                    <span class="hop-label">Handled By:</span> <span class="hop-value">${escapeHTML(hop.by)}</span>
                                </div>
                            </div>
                            ${hop.date ? `<div class="hop-date">Received: ${escapeHTML(hop.date)}</div>` : ''}
                            <div class="hop-raw">${escapeHTML(hop.full)}</div>
                        </div>
                    </div>
                `;
            });
            
            traceHTML += `</div>`;
            traceCard.innerHTML = traceHTML;
            resultsContainer.appendChild(traceCard);
        }
    }

    function escapeHTML(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
