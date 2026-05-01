// Crossword puzzle data for Steganography test
// 10 professional-level questions

export const crosswordData = {
    grid: [
        // 13x13 grid where 0 = black cell, numbers = word start positions
        [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 6, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    ],

    // Word positions and answers
    words: {
        across: [
            {
                number: 1,
                answer: 'STEGANOGRAPHY',
                row: 0,
                col: 0,
                clue: 'The practice of concealing messages within other non-secret data to avoid detection',
                length: 13
            },
            {
                number: 3,
                answer: 'COVERT',
                row: 3,
                col: 1,
                clue: 'Type of communication channel that hides the existence of the message itself',
                length: 6
            },
            {
                number: 5,
                answer: 'WATERMARK',
                row: 7,
                col: 0,
                clue: 'Digital signature embedded invisibly in media files for copyright protection and ownership verification',
                length: 9
            },
            {
                number: 6,
                answer: 'PIXEL',
                row: 9,
                col: 2,
                clue: 'The smallest addressable element in a digital image where LSB data can be hidden',
                length: 5
            },
            {
                number: 7,
                answer: 'CARRIER',
                row: 11,
                col: 2,
                clue: 'The file or medium (image, audio, video) used to hide secret information in steganography',
                length: 7
            },
        ],
        down: [
            {
                number: 1,
                answer: 'STEGANALYSIS',
                row: 0,
                col: 0,
                clue: 'The art and science of detecting hidden information in files and identifying steganographic techniques',
                length: 12
            },
            {
                number: 2,
                answer: 'LSB',
                row: 1,
                col: 2,
                clue: 'Least Significant ___ - the most common technique for hiding data in image pixels (abbreviation)',
                length: 3
            },
            {
                number: 3,
                answer: 'EMBEDDING',
                row: 3,
                col: 1,
                clue: 'The process of hiding secret data within a cover medium without noticeable distortion',
                length: 9
            },
            {
                number: 4,
                answer: 'ALGORITHM',
                row: 5,
                col: 0,
                clue: 'A step-by-step procedure used for encoding and decoding hidden messages',
                length: 9
            },
            {
                number: 6,
                answer: 'ENCRYPTION',
                row: 9,
                col: 2,
                clue: 'Converting plaintext into ciphertext before embedding for additional security layer',
                length: 10
            },
        ]
    }
};

// Simplified grid structure for easier implementation
export const simplifiedCrosswordData = {
    // Grid layout: each cell is {letter: 'A', number: 1, isBlack: false}
    gridSize: 13,

    answers: {
        1: { word: 'STEGANOGRAPHY', direction: 'across', row: 0, col: 0 },
        2: { word: 'LSB', direction: 'down', row: 1, col: 2 },
        3: { word: 'COVERT', direction: 'across', row: 3, col: 1 },
        4: { word: 'EMBEDDING', direction: 'down', row: 3, col: 1 },
        5: { word: 'ALGORITHM', direction: 'down', row: 5, col: 0 },
        6: { word: 'WATERMARK', direction: 'across', row: 7, col: 0 },
        7: { word: 'PIXEL', direction: 'across', row: 9, col: 2 },
        8: { word: 'ENCRYPTION', direction: 'down', row: 9, col: 2 },
        9: { word: 'CARRIER', direction: 'across', row: 11, col: 2 },
        10: { word: 'STEGANALYSIS', direction: 'down', row: 0, col: 0 },
    },

    clues: {
        across: [
            { number: 1, clue: 'The practice of concealing messages within other non-secret data to avoid detection', answer: 'STEGANOGRAPHY' },
            { number: 3, clue: 'Type of communication channel that hides the existence of the message itself', answer: 'COVERT' },
            { number: 6, clue: 'Digital signature embedded invisibly in media files for copyright protection', answer: 'WATERMARK' },
            { number: 7, clue: 'The smallest addressable element in a digital image where LSB data can be hidden', answer: 'PIXEL' },
            { number: 9, clue: 'The file or medium used to hide secret information in steganography', answer: 'CARRIER' },
        ],
        down: [
            { number: 2, clue: 'Least Significant ___ - the most common technique for hiding data in pixels (abbreviation)', answer: 'LSB' },
            { number: 4, clue: 'The process of hiding secret data within a cover medium without noticeable distortion', answer: 'EMBEDDING' },
            { number: 5, clue: 'A step-by-step procedure used for encoding and decoding hidden messages', answer: 'ALGORITHM' },
            { number: 8, clue: 'Converting plaintext into ciphertext before embedding for additional security', answer: 'ENCRYPTION' },
            { number: 10, clue: 'The art and science of detecting hidden information and identifying steganographic techniques', answer: 'STEGANALYSIS' },
        ]
    }
};
