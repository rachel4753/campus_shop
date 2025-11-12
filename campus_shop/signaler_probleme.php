<?php
// signaler_probleme.php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
if ($message === '') {
    echo json_encode(['success' => false, 'error' => 'Message vide']);
    exit;
}
// Crée la table signalements si elle n'existe pas
$pdo->exec("CREATE TABLE IF NOT EXISTS signalements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    date_signalement DATETIME DEFAULT CURRENT_TIMESTAMP
)");
// Insère le signalement
$stmt = $pdo->prepare('INSERT INTO signalements (message) VALUES (?)');
$ok = $stmt->execute([$message]);
if ($ok) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'enregistrement']);
}
