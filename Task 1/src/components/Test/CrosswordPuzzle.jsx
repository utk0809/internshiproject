import React, { useState, useEffect, useRef } from 'react';
import { simplifiedCrosswordData } from '../../data/crosswordData';
import { useNavigate } from 'react-router-dom';
import './CrosswordPuzzle.css';

const CrosswordPuzzle = () => {
    const navigate = useNavigate();
    const [userAnswers, setUserAnswers] = useState({});
    const [selectedCell, setSelectedCell] = useState(null);
    const [selectedDirection, setSelectedDirection] = useState('across');
    const [showSubmitConfirm, setShowSubmitConfirm] = useState(false);
    const gridRef = useRef(null);

    // Extract user data from URL parameters and store in localStorage
    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const firstName = urlParams.get('firstName');
        const lastName = urlParams.get('lastName');
        const mobile = urlParams.get('mobile');
        const email = urlParams.get('email');
        const userId = urlParams.get('userId');

        // If URL has user data, store it in localStorage
        if (firstName || lastName || email) {
            const userData = {
                firstName: firstName || '',
                lastName: lastName || '',
                mobile: mobile || '',
                email: email || '',
                userId: userId || ''
            };
            localStorage.setItem('userData', JSON.stringify(userData));
            console.log('✅ User data stored from URL parameters:', userData);
        }
    }, []);

    // Browser back button prevention
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

    // Build grid structure from crossword data
    const buildGrid = () => {
        const grid = Array(13).fill(null).map(() => Array(13).fill({ isBlack: true }));

        Object.entries(simplifiedCrosswordData.answers).forEach(([num, data]) => {
            const { word, direction, row, col } = data;

            for (let i = 0; i < word.length; i++) {
                const r = direction === 'across' ? row : row + i;
                const c = direction === 'across' ? col + i : col;

                if (r < 13 && c < 13) {
                    grid[r][c] = {
                        isBlack: false,
                        letter: word[i],
                        number: i === 0 ? parseInt(num) : (grid[r][c].number || null),
                        row: r,
                        col: c
                    };
                }
            }
        });

        return grid;
    };

    const grid = buildGrid();

    const handleCellClick = (row, col, cell) => {
        if (cell.isBlack) return;

        // Toggle direction if clicking the same cell
        if (selectedCell && selectedCell.row === row && selectedCell.col === col) {
            setSelectedDirection(prev => prev === 'across' ? 'down' : 'across');
        } else {
            setSelectedCell({ row, col });
        }
    };

    const handleKeyPress = (e, row, col) => {
        if (grid[row][col].isBlack) return;

        const key = e.key.toUpperCase();

        if (/^[A-Z]$/.test(key)) {
            setUserAnswers(prev => ({
                ...prev,
                [`${row}-${col}`]: key
            }));

            // Move to next cell
            moveToNextCell(row, col);
        } else if (e.key === 'Backspace') {
            setUserAnswers(prev => {
                const newAnswers = { ...prev };
                delete newAnswers[`${row}-${col}`];
                return newAnswers;
            });
            moveToPreviousCell(row, col);
        } else if (e.key === 'ArrowRight') {
            moveRight(row, col);
        } else if (e.key === 'ArrowLeft') {
            moveLeft(row, col);
        } else if (e.key === 'ArrowDown') {
            moveDown(row, col);
        } else if (e.key === 'ArrowUp') {
            moveUp(row, col);
        }
    };

    const moveToNextCell = (row, col) => {
        if (selectedDirection === 'across') {
            moveRight(row, col);
        } else {
            moveDown(row, col);
        }
    };

    const moveToPreviousCell = (row, col) => {
        if (selectedDirection === 'across') {
            moveLeft(row, col);
        } else {
            moveUp(row, col);
        }
    };

    const moveRight = (row, col) => {
        for (let c = col + 1; c < 13; c++) {
            if (!grid[row][c].isBlack) {
                setSelectedCell({ row, col: c });
                return;
            }
        }
    };

    const moveLeft = (row, col) => {
        for (let c = col - 1; c >= 0; c--) {
            if (!grid[row][c].isBlack) {
                setSelectedCell({ row, col: c });
                return;
            }
        }
    };

    const moveDown = (row, col) => {
        for (let r = row + 1; r < 13; r++) {
            if (!grid[r][col].isBlack) {
                setSelectedCell({ row: r, col });
                return;
            }
        }
    };

    const moveUp = (row, col) => {
        for (let r = row - 1; r >= 0; r--) {
            if (!grid[r][col].isBlack) {
                setSelectedCell({ row: r, col });
                return;
            }
        }
    };

    const calculateScore = () => {
        let correctAnswers = 0;
        const totalQuestions = 10;
        const marksPerQuestion = 5;

        Object.entries(simplifiedCrosswordData.answers).forEach(([num, data]) => {
            const { word, direction, row, col } = data;
            let isCorrect = true;

            for (let i = 0; i < word.length; i++) {
                const r = direction === 'across' ? row : row + i;
                const c = direction === 'across' ? col + i : col;
                const userAnswer = userAnswers[`${r}-${c}`];

                if (userAnswer !== word[i]) {
                    isCorrect = false;
                    break;
                }
            }

            if (isCorrect) correctAnswers++;
        });

        return {
            score: correctAnswers * marksPerQuestion,
            totalScore: totalQuestions * marksPerQuestion,
            correctAnswers,
            totalQuestions
        };
    };

    const handleSubmit = () => {
        setShowSubmitConfirm(true);
    };

    const confirmSubmit = () => {
        const result = calculateScore();

        // Store result and navigate to MCQ test
        localStorage.setItem('crosswordResult', JSON.stringify({
            testType: 'Crossword Puzzle',
            ...result,
            answers: simplifiedCrosswordData.answers,
            userAnswers: userAnswers,
            completedAt: new Date().toISOString()
        }));

        navigate('/test/mcq');
    };

    const handleClueClick = (number, direction) => {
        const answer = simplifiedCrosswordData.answers[number];
        if (answer) {
            setSelectedCell({ row: answer.row, col: answer.col });
            setSelectedDirection(direction);
        }
    };

    return (
        <div className="crossword-container">
            <nav className="navbar">
                <div className="logo">
                    <span style={{ color: 'var(--primary-color)' }}>STEGANO</span>
                    <span style={{ color: 'var(--text-color)' }}>GRAPHY</span>
                </div>
                <div className="test-info">
                    <span className="test-title">Crossword Puzzle Test</span>
                    <span className="test-score">10 Questions • 50 Marks</span>
                </div>
            </nav>

            <div className="crossword-content">
                <div className="crossword-main">
                    <div className="crossword-header">
                        <h2>Steganography Crossword Puzzle</h2>
                        <p>Fill in the crossword based on steganography concepts. Each correct answer is worth 5 marks.</p>
                    </div>

                    <div className="crossword-grid-container">
                        <div className="crossword-grid" ref={gridRef}>
                            {grid.map((row, rowIndex) => (
                                <div key={rowIndex} className="grid-row">
                                    {row.map((cell, colIndex) => (
                                        <div
                                            key={`${rowIndex}-${colIndex}`}
                                            className={`grid-cell ${cell.isBlack ? 'black-cell' : ''} ${selectedCell && selectedCell.row === rowIndex && selectedCell.col === colIndex
                                                ? 'selected'
                                                : ''
                                                }`}
                                            onClick={() => handleCellClick(rowIndex, colIndex, cell)}
                                            tabIndex={cell.isBlack ? -1 : 0}
                                            onKeyDown={(e) => handleKeyPress(e, rowIndex, colIndex)}
                                        >
                                            {!cell.isBlack && (
                                                <>
                                                    {cell.number && <span className="cell-number">{cell.number}</span>}
                                                    <input
                                                        type="text"
                                                        maxLength="1"
                                                        value={userAnswers[`${rowIndex}-${colIndex}`] || ''}
                                                        onChange={(e) => {
                                                            const value = e.target.value.toUpperCase();
                                                            if (/^[A-Z]?$/.test(value)) {
                                                                setUserAnswers(prev => ({
                                                                    ...prev,
                                                                    [`${rowIndex}-${colIndex}`]: value
                                                                }));
                                                            }
                                                        }}
                                                        className="cell-input"
                                                        readOnly
                                                    />
                                                </>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="crossword-actions">
                        <button onClick={handleSubmit} className="btn-primary btn-submit">
                            Submit Test
                        </button>
                    </div>
                </div>

                <div className="crossword-clues">
                    <div className="clues-section">
                        <h3>Across</h3>
                        <div className="clues-list">
                            {simplifiedCrosswordData.clues.across.map((clue) => (
                                <div
                                    key={clue.number}
                                    className="clue-item"
                                    onClick={() => handleClueClick(clue.number, 'across')}
                                >
                                    <span className="clue-number">{clue.number}.</span>
                                    <span className="clue-text">{clue.clue}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="clues-section">
                        <h3>Down</h3>
                        <div className="clues-list">
                            {simplifiedCrosswordData.clues.down.map((clue) => (
                                <div
                                    key={clue.number}
                                    className="clue-item"
                                    onClick={() => handleClueClick(clue.number, 'down')}
                                >
                                    <span className="clue-number">{clue.number}.</span>
                                    <span className="clue-text">{clue.clue}</span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            {showSubmitConfirm && (
                <div className="modal-overlay">
                    <div className="modal-content">
                        <h3>Submit Test?</h3>
                        <p>Are you sure you want to submit your crossword puzzle? You won't be able to change your answers after submission.</p>
                        <div className="modal-actions">
                            <button onClick={() => setShowSubmitConfirm(false)} className="btn-secondary">
                                Cancel
                            </button>
                            <button onClick={confirmSubmit} className="btn-primary">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default CrosswordPuzzle;
