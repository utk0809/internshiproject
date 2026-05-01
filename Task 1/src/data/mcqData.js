// MCQ Test Data for Steganography
// 10 professional-level multiple choice questions
// Each question worth 5 marks (total 50 marks)

export const mcqQuestions = [
    {
        id: 1,
        question: "What is the primary goal of steganography?",
        options: [
            "To encrypt data to make it unreadable",
            "To hide the existence of secret data within other data",
            "To compress data for efficient storage",
            "To authenticate the sender of a message"
        ],
        correctAnswer: 1,
        explanation: "Steganography focuses on hiding the existence of data within other non-secret data, unlike cryptography which makes data unreadable but visible."
    },
    {
        id: 2,
        question: "In the LSB (Least Significant Bit) technique, which part of the pixel data is modified to hide information?",
        options: [
            "The most significant bit of each color channel",
            "The entire pixel value is replaced",
            "The least significant bit of each color channel",
            "Only the alpha channel transparency value"
        ],
        correctAnswer: 2,
        explanation: "LSB technique modifies the least significant bit of pixel color values, causing minimal visual distortion while hiding data."
    },
    {
        id: 3,
        question: "What is steganalysis?",
        options: [
            "The process of embedding secret messages in files",
            "The art and science of detecting hidden information in files",
            "A type of encryption algorithm",
            "The compression technique used before hiding data"
        ],
        correctAnswer: 1,
        explanation: "Steganalysis is the practice of detecting the presence of hidden information and identifying steganographic techniques used."
    },
    {
        id: 4,
        question: "Which of the following is NOT a common type of steganography carrier medium?",
        options: [
            "Digital images (JPEG, PNG)",
            "Audio files (MP3, WAV)",
            "Plain text documents",
            "Executable binary files"
        ],
        correctAnswer: 3,
        explanation: "While images, audio, and video are common carriers due to their redundant data, executable files are rarely used as they can be easily corrupted."
    },
    {
        id: 5,
        question: "What is a digital watermark in the context of steganography?",
        options: [
            "A visible logo placed on images to prevent copying",
            "An invisible signature embedded in media for copyright protection",
            "A type of encryption key used in secure communication",
            "A compression algorithm for reducing file size"
        ],
        correctAnswer: 1,
        explanation: "Digital watermarks are invisible signatures embedded in media files for copyright protection and ownership verification."
    },
    {
        id: 6,
        question: "What is the main difference between steganography and cryptography?",
        options: [
            "Steganography is faster than cryptography",
            "Cryptography hides the content, steganography hides the existence of the message",
            "Cryptography is more secure than steganography",
            "Steganography can only be used with images"
        ],
        correctAnswer: 1,
        explanation: "Cryptography makes messages unreadable but visible, while steganography hides the very existence of the message within other data."
    },
    {
        id: 7,
        question: "In image steganography, what is the 'carrier file' or 'cover medium'?",
        options: [
            "The secret message to be hidden",
            "The encryption key used to secure the data",
            "The original image used to hide the secret information",
            "The algorithm used for embedding data"
        ],
        correctAnswer: 2,
        explanation: "The carrier file (or cover medium) is the original file (image, audio, video) used to conceal the secret information."
    },
    {
        id: 8,
        question: "What is a covert channel in steganography?",
        options: [
            "A secure VPN connection for data transfer",
            "A communication channel that hides the existence of the message itself",
            "An encrypted messaging application",
            "A type of network firewall"
        ],
        correctAnswer: 1,
        explanation: "A covert channel is a communication method that hides the existence of the message itself, making it undetectable to observers."
    },
    {
        id: 9,
        question: "Why is encryption often combined with steganography?",
        options: [
            "To reduce the file size of the hidden message",
            "To make the carrier file smaller",
            "To add an additional layer of security if the hidden data is discovered",
            "To speed up the embedding process"
        ],
        correctAnswer: 2,
        explanation: "Encrypting data before embedding provides an additional security layer - even if the hidden data is discovered, it remains unreadable without the decryption key."
    },
    {
        id: 10,
        question: "Which factor is most critical when choosing a steganography algorithm?",
        options: [
            "The speed of the embedding process",
            "The file size reduction achieved",
            "The imperceptibility of changes to the carrier file",
            "The color depth of the carrier image"
        ],
        correctAnswer: 2,
        explanation: "The most critical factor is imperceptibility - the hidden data should not cause noticeable distortion to the carrier file, avoiding detection."
    }
];

// Export total marks and question count
export const testConfig = {
    totalQuestions: 10,
    marksPerQuestion: 5,
    totalMarks: 50,
    testType: 'MCQ Test'
};
