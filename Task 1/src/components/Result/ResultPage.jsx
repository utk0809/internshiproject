import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Certificate from './Certificate';
import './ResultPage.css';
import { supabase } from '../../lib/supabase';

const ResultPage = () => {
    const navigate = useNavigate();
    const [testResult, setTestResult] = useState(null);
    const [showCertificate, setShowCertificate] = useState(false);
    const [userData, setUserData] = useState(null);

    useEffect(() => {
        // PREVENT BACK NAVIGATION
        window.history.pushState(null, '', window.location.href);

        const handlePopState = (e) => {
            e.preventDefault();
            window.history.pushState(null, '', window.location.href);
            alert('⚠️ You cannot go back to the test. Your results have been submitted.');
        };

        const handleBeforeUnload = (e) => {
            e.preventDefault();
            e.returnValue = 'Your test results have been saved.';
            return e.returnValue;
        };

        window.addEventListener('popstate', handlePopState);
        window.addEventListener('beforeunload', handleBeforeUnload);

        // Get both test results from localStorage
        const mcqResult = localStorage.getItem('mcqResult');
        const crosswordResult = localStorage.getItem('crosswordResult');
        const user = localStorage.getItem('userData');

        // If no MCQ result, redirect back (they haven't completed both tests)
        if (!mcqResult || !crosswordResult) {
            navigate('/test/crossword');
            return;
        }

        const mcqData = JSON.parse(mcqResult);
        const crosswordData = JSON.parse(crosswordResult);

        // Combine both test results
        const combinedResult = {
            testType: 'Steganography Complete Assessment',
            score: mcqData.score + crosswordData.score,
            totalScore: 100, // 50 + 50
            correctAnswers: mcqData.correctAnswers + crosswordData.correctAnswers,
            totalQuestions: 20, // 10 + 10
            mcqScore: mcqData.score,
            crosswordScore: crosswordData.score,
            mcqData: mcqData,
            crosswordData: crosswordData,
            completedAt: mcqData.completedAt,
            answers: crosswordData.answers,
            userAnswers: crosswordData.userAnswers
        };
        setTestResult(combinedResult);

        if (user) {
            const parsedUser = JSON.parse(user);
            setUserData(parsedUser);

            // Save complete result for admin panel
            const adminResult = {
                firstName: parsedUser.firstName || '',
                lastName: parsedUser.lastName || '',
                email: parsedUser.email || '',
                mobile: parsedUser.mobile || '',
                score: combinedResult.score,
                totalScore: combinedResult.totalScore,
                mcqScore: combinedResult.mcqScore,
                crosswordScore: combinedResult.crosswordScore,
                completedAt: combinedResult.completedAt || new Date().toISOString()
            };

            // Store with unique key based on email and timestamp (backup)
            const resultKey = `testResult_${parsedUser.email}_${Date.now()}`;
            localStorage.setItem(resultKey, JSON.stringify(adminResult));
            console.log('✅ Test result saved to localStorage:', resultKey);

            // SAVE TO SUPABASE DATABASE
            const saveToDatabase = async () => {
                try {
                    const { data, error } = await supabase
                        .from('test_results')
                        .insert({
                            user_email: parsedUser.email,
                            first_name: parsedUser.firstName,
                            last_name: parsedUser.lastName,
                            mobile: parsedUser.mobile,
                            score: combinedResult.score,
                            total_score: combinedResult.totalScore,
                            mcq_score: combinedResult.mcqScore,
                            crossword_score: combinedResult.crosswordScore,
                            completed_at: combinedResult.completedAt || new Date().toISOString()
                        });

                    if (error) {
                        console.error('❌ Error saving to Supabase:', error);
                    } else {
                        console.log('✅ Test result saved to Supabase database!');
                    }
                } catch (error) {
                    console.error('❌ Database save failed:', error);
                }
            };

            saveToDatabase();
        }

        return () => {
            window.removeEventListener('popstate', handlePopState);
            window.removeEventListener('beforeunload', handleBeforeUnload);
        };
    }, [navigate]);

    if (!testResult) {
        return <div>Loading...</div>;
    }

    const percentage = ((testResult.score / testResult.totalScore) * 100).toFixed(1);

    const getGrade = (percent) => {
        if (percent >= 90) return { grade: 'A+', color: '#00C853' };
        if (percent >= 80) return { grade: 'A', color: '#00E676' };
        if (percent >= 70) return { grade: 'B+', color: '#76FF03' };
        if (percent >= 60) return { grade: 'B', color: '#FFEB3B' };
        if (percent >= 50) return { grade: 'C', color: '#FFC107' };
        return { grade: 'D', color: '#FF5722' };
    };

    const gradeInfo = getGrade(percentage);

    const handleGenerateCertificate = () => {
        setShowCertificate(true);
    };

    const handleTakeAnotherTest = () => {
        localStorage.removeItem('crosswordResult');
        localStorage.removeItem('mcqResult');
        localStorage.removeItem('userData');
        // Redirect back to OAuth signup
        window.location.href = 'http://localhost:3000';
    };

    if (showCertificate) {
        return (
            <Certificate
                userData={userData}
                testResult={testResult}
                onClose={() => setShowCertificate(false)}
            />
        );
    }

    return (
        <div className="result-container">
            <nav className="navbar">
                <div className="logo">
                    <span style={{ color: 'var(--primary-color)' }}>STEGANO</span>
                    <span style={{ color: 'var(--text-color)' }}>GRAPHY</span>
                </div>
            </nav>

            <div className="result-content">
                <div className="result-card">
                    <div className="result-header">
                        <div className="success-icon">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="url(#gradient)" strokeWidth="2" />
                                <path d="M8 12l2 2 4-4" stroke="url(#gradient)" strokeWidth="2" strokeLinecap="round" />
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stopColor="var(--primary-color)" />
                                        <stop offset="100%" stopColor="var(--accent-color)" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>
                        <h1>Test Completed!</h1>
                        <p className="test-type">{testResult.testType}</p>
                    </div>

                    <div className="score-display">
                        <div className="score-circle" style={{ borderColor: gradeInfo.color }}>
                            <div className="score-value">{testResult.score}</div>
                            <div className="score-total">out of {testResult.totalScore}</div>
                        </div>
                        <div className="grade-badge" style={{ background: gradeInfo.color }}>
                            {gradeInfo.grade}
                        </div>
                    </div>

                    <div className="result-stats">
                        <div className="stat-item">
                            <div className="stat-icon">✓</div>
                            <div className="stat-label">Correct Answers</div>
                            <div className="stat-value">{testResult.correctAnswers} / {testResult.totalQuestions}</div>
                        </div>
                        <div className="stat-item">
                            <div className="stat-icon">%</div>
                            <div className="stat-label">Percentage</div>
                            <div className="stat-value">{percentage}%</div>
                        </div>
                        <div className="stat-item">
                            <div className="stat-icon">★</div>
                            <div className="stat-label">Marks per Question</div>
                            <div className="stat-value">5 marks</div>
                        </div>
                    </div>

                    {/* Score Breakdown - Only show if both tests completed */}
                    {testResult.mcqScore !== undefined && (
                        <div className="score-breakdown">
                            <h3>Score Breakdown</h3>
                            <div className="breakdown-items">
                                <div className="breakdown-item">
                                    <div className="breakdown-label">MCQ Test</div>
                                    <div className="breakdown-score">{testResult.mcqScore} / 50</div>
                                </div>
                                <div className="breakdown-item">
                                    <div className="breakdown-label">Crossword Puzzle</div>
                                    <div className="breakdown-score">{testResult.crosswordScore} / 50</div>
                                </div>
                                <div className="breakdown-item breakdown-total">
                                    <div className="breakdown-label">Total Score</div>
                                    <div className="breakdown-score">{testResult.score} / 100</div>
                                </div>
                            </div>
                        </div>
                    )}

                    <div className="result-message">
                        <h3>Congratulations!</h3>
                        <p>
                            You have successfully completed the {testResult.testType}.
                            Your understanding of steganography concepts is {percentage >= 70 ? 'excellent' : percentage >= 50 ? 'good' : 'developing'}.
                        </p>
                    </div>

                    <div className="result-actions">
                        <button onClick={handleGenerateCertificate} className="btn-primary btn-certificate">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10 9 9 9 8 9" />
                            </svg>
                            Generate Certificate
                        </button>
                    </div>

                    <div className="completion-time">
                        <small>Completed on {new Date(testResult.completedAt).toLocaleString()}</small>
                    </div>
                </div>

                {/* Answer Review Section */}
                <div className="answer-review">
                    <h2>Answer Review</h2>
                    <p className="review-description">Review your answers and see the correct solutions</p>

                    {/* MCQ Answers */}
                    {testResult.mcqData && (
                        <>
                            <h3 className="test-section-title">MCQ Test Answers</h3>
                            <div className="answers-grid">
                                {testResult.mcqData.questions.map((question, index) => {
                                    const userAnswer = testResult.mcqData.userAnswers[index];
                                    const isCorrect = userAnswer === question.correctAnswer;

                                    return (
                                        <div key={index} className={`answer-item ${isCorrect ? 'correct' : 'incorrect'}`}>
                                            <div className="answer-header">
                                                <span className="answer-number">Question {index + 1}</span>
                                                <span className={`answer-status ${isCorrect ? 'status-correct' : 'status-incorrect'}`}>
                                                    {isCorrect ? '✓ Correct' : '✗ Incorrect'}
                                                </span>
                                            </div>
                                            <div className="mcq-question-text">{question.question}</div>
                                            <div className="mcq-answer-info">
                                                <div className="mcq-user-answer">
                                                    <strong>Your Answer:</strong> {userAnswer || 'Not answered'}
                                                </div>
                                                {!isCorrect && (
                                                    <div className="mcq-correct-answer">
                                                        <strong>Correct Answer:</strong> {question.correctAnswer}
                                                    </div>
                                                )}
                                            </div>
                                            <div className="answer-marks">{isCorrect ? '5' : '0'} / 5 marks</div>
                                        </div>
                                    );
                                })}
                            </div>
                        </>
                    )}

                    {/* Crossword Answers */}
                    {testResult.crosswordData && (
                        <>
                            <h3 className="test-section-title">Crossword Puzzle Answers</h3>
                            <div className="answers-grid">
                                {Object.entries(testResult.answers).map(([num, data]) => {
                                    const { word, direction } = data;
                                    let isCorrect = true;

                                    // Check if user's answer matches
                                    for (let i = 0; i < word.length; i++) {
                                        const r = direction === 'across' ? data.row : data.row + i;
                                        const c = direction === 'across' ? data.col + i : data.col;
                                        const userAnswer = testResult.userAnswers?.[`${r}-${c}`];

                                        if (userAnswer !== word[i]) {
                                            isCorrect = false;
                                            break;
                                        }
                                    }

                                    return (
                                        <div key={num} className={`answer-item ${isCorrect ? 'correct' : 'incorrect'}`}>
                                            <div className="answer-header">
                                                <span className="answer-number">Question {num}</span>
                                                <span className={`answer-status ${isCorrect ? 'status-correct' : 'status-incorrect'}`}>
                                                    {isCorrect ? '✓ Correct' : '✗ Incorrect'}
                                                </span>
                                            </div>
                                            <div className="answer-details">
                                                <div className="answer-direction">{direction.toUpperCase()}</div>
                                                <div className="answer-word">{word}</div>
                                                <div className="answer-marks">{isCorrect ? '5' : '0'} / 5 marks</div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ResultPage;
