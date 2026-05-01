import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { mcqQuestions, testConfig } from '../../data/mcqData';
import './MCQTest.css';

const MCQTest = () => {
    const navigate = useNavigate();
    const [currentQuestion, setCurrentQuestion] = useState(0);
    const [userAnswers, setUserAnswers] = useState(Array(testConfig.totalQuestions).fill(null));
    const [showSubmitConfirm, setShowSubmitConfirm] = useState(false);

    // Browser back button prevention - CRITICAL REQUIREMENT
    useEffect(() => {
        // Push a dummy state to history
        window.history.pushState(null, '', window.location.href);

        const handlePopState = (e) => {
            e.preventDefault();
            window.history.pushState(null, '', window.location.href);

            const confirmLeave = window.confirm(
                '⚠️ WARNING: Going back will exit the test and you will lose all progress. Are you sure you want to leave?'
            );

            if (confirmLeave) {
                // Allow navigation
                window.history.back();
            }
        };

        const handleBeforeUnload = (e) => {
            e.preventDefault();
            e.returnValue = 'You have an ongoing test. Are you sure you want to leave?';
            return e.returnValue;
        };

        window.addEventListener('popstate', handlePopState);
        window.addEventListener('beforeunload', handleBeforeUnload);

        return () => {
            window.removeEventListener('popstate', handlePopState);
            window.removeEventListener('beforeunload', handleBeforeUnload);
        };
    }, []);

    const handleOptionSelect = (optionIndex) => {
        const newAnswers = [...userAnswers];
        newAnswers[currentQuestion] = optionIndex;
        setUserAnswers(newAnswers);
    };

    const handleNext = () => {
        if (currentQuestion < testConfig.totalQuestions - 1) {
            setCurrentQuestion(currentQuestion + 1);
            // Scroll to top of question
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    const handlePrevious = () => {
        if (currentQuestion > 0) {
            setCurrentQuestion(currentQuestion - 1);
            // Scroll to top of question
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    const calculateScore = () => {
        let correctAnswers = 0;
        userAnswers.forEach((answer, index) => {
            if (answer === mcqQuestions[index].correctAnswer) {
                correctAnswers++;
            }
        });

        return {
            score: correctAnswers * testConfig.marksPerQuestion,
            totalScore: testConfig.totalMarks,
            correctAnswers,
            totalQuestions: testConfig.totalQuestions
        };
    };

    const handleSubmit = () => {
        // Check if all questions are answered
        const unanswered = userAnswers.filter(answer => answer === null).length;

        if (unanswered > 0) {
            const confirmSubmit = window.confirm(
                `You have ${unanswered} unanswered question(s). Do you want to submit anyway?`
            );
            if (!confirmSubmit) return;
        }

        setShowSubmitConfirm(true);
    };

    const confirmSubmit = () => {
        const result = calculateScore();

        // Store MCQ result in localStorage
        localStorage.setItem('mcqResult', JSON.stringify({
            testType: 'MCQ Test',
            ...result,
            questions: mcqQuestions,
            userAnswers: userAnswers,
            completedAt: new Date().toISOString()
        }));

        // Navigate to result page
        navigate('/result');
    };

    const progress = ((currentQuestion + 1) / testConfig.totalQuestions) * 100;
    const answeredCount = userAnswers.filter(answer => answer !== null).length;

    return (
        <div className="mcq-container">
            {/* Navbar */}
            <nav className="navbar">
                <div className="logo">
                    <span style={{ color: 'var(--primary-color)' }}>STEGANO</span>
                    <span style={{ color: 'var(--text-color)' }}>GRAPHY</span>
                </div>
                <div className="test-info">
                    <span className="test-title">MCQ Test</span>
                    <span className="test-score">10 Questions • 50 Marks</span>
                </div>
            </nav>

            {/* Progress Bar */}
            <div className="progress-container">
                <div className="progress-bar" style={{ width: `${progress}%` }}></div>
            </div>

            {/* Main Content */}
            <div className="mcq-content">
                <div className="mcq-header">
                    <h2>Steganography MCQ Test</h2>
                    <p>Answer all questions carefully. Each question is worth 5 marks.</p>
                    <div className="question-stats">
                        <span>Question {currentQuestion + 1} of {testConfig.totalQuestions}</span>
                        <span>Answered: {answeredCount}/{testConfig.totalQuestions}</span>
                    </div>
                </div>

                {/* Question Card */}
                <div className="question-card">
                    <div className="question-number">Question {currentQuestion + 1}</div>
                    <h3 className="question-text">{mcqQuestions[currentQuestion].question}</h3>

                    {/* Options */}
                    <div className="options-container">
                        {mcqQuestions[currentQuestion].options.map((option, index) => (
                            <div
                                key={index}
                                className={`option-item ${userAnswers[currentQuestion] === index ? 'selected' : ''}`}
                                onClick={() => handleOptionSelect(index)}
                            >
                                <div className="option-radio">
                                    {userAnswers[currentQuestion] === index && (
                                        <div className="option-radio-inner"></div>
                                    )}
                                </div>
                                <div className="option-label">{String.fromCharCode(65 + index)}.</div>
                                <div className="option-text">{option}</div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Navigation Buttons */}
                <div className="mcq-navigation">
                    <button
                        onClick={handlePrevious}
                        disabled={currentQuestion === 0}
                        className="btn-secondary btn-nav"
                    >
                        ← Previous
                    </button>

                    <div className="question-indicators">
                        {mcqQuestions.map((_, index) => (
                            <div
                                key={index}
                                className={`question-indicator ${index === currentQuestion ? 'active' : ''} ${userAnswers[index] !== null ? 'answered' : ''}`}
                                onClick={() => {
                                    setCurrentQuestion(index);
                                    window.scrollTo({ top: 0, behavior: 'smooth' });
                                }}
                                title={`Question ${index + 1}`}
                            >
                                {index + 1}
                            </div>
                        ))}
                    </div>

                    {currentQuestion < testConfig.totalQuestions - 1 ? (
                        <button
                            onClick={handleNext}
                            className="btn-primary btn-nav"
                        >
                            Next →
                        </button>
                    ) : (
                        <button
                            onClick={handleSubmit}
                            className="btn-primary btn-submit"
                        >
                            Submit MCQ Test
                        </button>
                    )}
                </div>
            </div>

            {/* Submit Confirmation Modal */}
            {showSubmitConfirm && (
                <div className="modal-overlay">
                    <div className="modal-content">
                        <h3>Submit MCQ Test?</h3>
                        <p>
                            You have answered {answeredCount} out of {testConfig.totalQuestions} questions.
                        </p>
                        <p className="modal-warning">
                            After submitting, you will proceed to the Crossword Puzzle test. You cannot return to this test.
                        </p>
                        <div className="modal-actions">
                            <button onClick={() => setShowSubmitConfirm(false)} className="btn-secondary">
                                Cancel
                            </button>
                            <button onClick={confirmSubmit} className="btn-primary">
                                Proceed to Crossword
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default MCQTest;
