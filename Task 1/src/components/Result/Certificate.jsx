import React, { useRef, useEffect } from 'react';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import './Certificate.css';

const Certificate = ({ userData, testResult, onClose }) => {
    const certificateRef = useRef(null);

    // Debug: Log userData to console
    useEffect(() => {
        console.log('Certificate userData:', userData);
        console.log('Certificate testResult:', testResult);
    }, [userData, testResult]);

    const downloadAsPDF = async () => {
        const element = certificateRef.current;
        const canvas = await html2canvas(element, {
            scale: 2,
            backgroundColor: '#ffffff',
        });

        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF({
            orientation: 'landscape',
            unit: 'mm',
            format: 'a4',
        });

        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();

        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        const firstName = userData?.firstName || 'Student';
        const lastName = userData?.lastName || '';
        const fileName = `Certificate_${firstName}_${lastName}_Steganography.pdf`.replace(/\s+/g, '_');
        pdf.save(fileName);
    };

    const downloadAsPNG = async () => {
        const element = certificateRef.current;
        const canvas = await html2canvas(element, {
            scale: 3,
            backgroundColor: '#ffffff',
        });

        const link = document.createElement('a');
        const firstName = userData?.firstName || 'Student';
        const lastName = userData?.lastName || '';
        const fileName = `Certificate_${firstName}_${lastName}_Steganography.png`.replace(/\s+/g, '_');
        link.download = fileName;
        link.href = canvas.toDataURL('image/png');
        link.click();
    };

    const currentDate = new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });

    const percentage = ((testResult.score / testResult.totalScore) * 100).toFixed(1);

    // Get full name from userData with fallback
    const firstName = userData?.firstName || '';
    const lastName = userData?.lastName || '';
    const fullName = `${firstName} ${lastName}`.trim() || 'Student Name';

    // Generate unique certificate ID
    const certificateId = `STEG-${new Date().getFullYear()}-${Math.random().toString(36).substr(2, 9).toUpperCase()}`;

    return (
        <div className="certificate-modal">
            <div className="certificate-overlay" onClick={onClose}></div>

            <div className="certificate-container">
                <div className="certificate-actions-top">
                    <button onClick={onClose} className="btn-close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <div className="certificate-wrapper">
                    <div ref={certificateRef} className="certificate">
                        {/* Decorative Border */}
                        <div className="certificate-border">
                            <div className="border-corner top-left"></div>
                            <div className="border-corner top-right"></div>
                            <div className="border-corner bottom-left"></div>
                            <div className="border-corner bottom-right"></div>

                            {/* Decorative side patterns */}
                            <div className="border-pattern left"></div>
                            <div className="border-pattern right"></div>
                        </div>

                        {/* Certificate Content */}
                        <div className="certificate-content">
                            {/* Header */}
                            <div className="certificate-header">
                                <div className="certificate-logo">
                                    <div className="logo-icon">
                                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="url(#cert-gradient)" opacity="0.8" />
                                            <path d="M2 17L12 22L22 17" stroke="url(#cert-gradient)" strokeWidth="2" />
                                            <path d="M2 12L12 17L22 12" stroke="url(#cert-gradient)" strokeWidth="2" />
                                            <defs>
                                                <linearGradient id="cert-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" stopColor="#7000ff" />
                                                    <stop offset="100%" stopColor="#ff0070" />
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                    </div>
                                    <h1 className="certificate-title">CERTIFICATE OF ACHIEVEMENT</h1>
                                    <div className="certificate-subtitle-line"></div>
                                    <p className="certificate-subtitle">Steganography Proficiency Assessment</p>
                                </div>
                            </div>

                            {/* Body */}
                            <div className="certificate-body">
                                <p className="certificate-text-small">This certificate is proudly presented to</p>

                                <div className="certificate-name-section">
                                    <h2 className="certificate-name">{fullName}</h2>
                                    <div className="name-underline"></div>
                                </div>

                                <p className="certificate-text-medium">
                                    for successfully completing the
                                </p>

                                <h3 className="certificate-course">{testResult.testType}</h3>

                                <p className="certificate-text-small certificate-description">
                                    This achievement demonstrates comprehensive knowledge and practical understanding
                                    of steganography concepts, techniques, and applications in information security.
                                </p>

                                {/* Score Section */}
                                <div className="certificate-score-section">
                                    <div className="score-box">
                                        <div className="score-label">FINAL SCORE</div>
                                        <div className="score-display-cert">
                                            <span className="score-number">{testResult.score}</span>
                                            <span className="score-separator">/</span>
                                            <span className="score-total-cert">{testResult.totalScore}</span>
                                        </div>
                                        <div className="score-percentage">{percentage}%</div>
                                    </div>
                                </div>

                                {/* Date and Signature */}
                                <div className="certificate-footer">
                                    <div className="certificate-date">
                                        <div className="footer-value">{currentDate}</div>
                                        <div className="footer-line"></div>
                                        <div className="footer-label">Date of Completion</div>
                                    </div>

                                    <div className="certificate-seal">
                                        <div className="seal-circle">
                                            <div className="seal-inner">
                                                <div className="seal-star">★</div>
                                                <div className="seal-text">CERTIFIED</div>
                                                <div className="seal-year">{new Date().getFullYear()}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="certificate-signature">
                                        <div className="signature-line">
                                            <span className="signature-text">Director</span>
                                        </div>
                                        <div className="footer-line"></div>
                                        <div className="footer-label">Authorized Signature</div>
                                    </div>
                                </div>
                            </div>

                            {/* Certificate ID */}
                            <div className="certificate-id">
                                Certificate ID: {certificateId}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Download Actions */}
                <div className="certificate-download-actions">
                    <button onClick={downloadAsPDF} className="btn-primary btn-download">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Download as PDF
                    </button>
                    <button onClick={downloadAsPNG} className="btn-secondary btn-download">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        Download as PNG
                    </button>
                </div>
            </div>
        </div>
    );
};

export default Certificate;
