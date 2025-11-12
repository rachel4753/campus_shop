-- Table des signalements pour le chatbot et les problèmes signalés
CREATE TABLE IF NOT EXISTS signalements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    date_signalement DATETIME DEFAULT CURRENT_TIMESTAMP
);
