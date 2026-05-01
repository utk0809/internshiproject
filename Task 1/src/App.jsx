import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import CrosswordPuzzle from './components/Test/CrosswordPuzzle';
import MCQTest from './components/Test/MCQTest';
import ResultPage from './components/Result/ResultPage';
import './style.css';

function App() {
    return (
        <Router>
            <Routes>
                {/* Temporary route - will be updated when auth is ready */}
                <Route path="/" element={<Navigate to="/test/mcq" replace />} />

                {/* Test routes - Sequential flow: MCQ → Crossword → Result */}
                <Route path="/test/mcq" element={<MCQTest />} />
                <Route path="/test/crossword" element={<CrosswordPuzzle />} />
                <Route path="/result" element={<ResultPage />} />

                {/* Placeholder routes for other team members */}
                <Route path="/signup" element={<div>Signup Page - To be implemented by Member 2</div>} />
                <Route path="/login" element={<div>Login Page - To be implemented by Member 2</div>} />
            </Routes>
        </Router>
    );
}

export default App;

