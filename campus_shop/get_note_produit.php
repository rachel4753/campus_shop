<?php
require_once 'config.php';
header('Content-Type: application/json');
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['moyenne' => 0, 'votes' => 0]);
    exit;
}
$stmt = $pdo->prepare('SELECT AVG(note) as moyenne, COUNT(*) as votes FROM notes WHERE produit_id = ?');
$stmt->execute([$id]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode([
    'moyenne' => $res['moyenne'] ? round($res['moyenne'],2) : 0,
    'votes' => $res['votes'] ? intval($res['votes']) : 0
]);
