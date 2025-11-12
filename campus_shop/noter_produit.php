<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = intval($_POST['produit_id'] ?? 0);
    $note = floatval($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');
    $utilisateur_id = $_SESSION['utilisateur']['id'];
    if ($produit_id <= 0 || $note < 1 || $note > 5) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit;
    }
    // Vérifier si l'utilisateur est propriétaire du produit
    $stmt = $pdo->prepare('SELECT utilisateur_id FROM produits WHERE id = ?');
    $stmt->execute([$produit_id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($prod && $prod['utilisateur_id'] == $utilisateur_id) {
        echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas noter votre propre produit']);
        exit;
    }
    // Vérifier si l'utilisateur a déjà noté ce produit
    $stmt = $pdo->prepare('SELECT id FROM notes WHERE produit_id = ? AND utilisateur_id = ?');
    $stmt->execute([$produit_id, $utilisateur_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Vous avez déjà noté ce produit']);
        exit;
    }
    // Enregistrer la note
    $stmt = $pdo->prepare('INSERT INTO notes (produit_id, utilisateur_id, note, commentaire, date) VALUES (?, ?, ?, ?, NOW())');
    $ok = $stmt->execute([$produit_id, $utilisateur_id, $note, $commentaire]);
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
