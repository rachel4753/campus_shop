<?php
// supprimer_produit.php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['utilisateur']['id'])) {
    echo json_encode(['success'=>false, 'error'=>'Non connecté']);
    exit;
}
if (!isset($_POST['id_produit'])) {
    echo json_encode(['success'=>false, 'error'=>'ID manquant']);
    exit;
}
$id = intval($_POST['id_produit']);
require_once 'config.php';
try {
    // Vérifier que l'utilisateur est bien le propriétaire
    $stmt = $pdo->prepare('SELECT utilisateur_id FROM produits WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || $row['utilisateur_id'] != $_SESSION['utilisateur']['id']) {
        echo json_encode(['success'=>false, 'error'=>'Non autorisé']);
        exit;
    }
    // Supprimer le produit
    $stmt = $pdo->prepare('DELETE FROM produits WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
