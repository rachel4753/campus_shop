<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['utilisateur'])) {
  header('Location: connexion.php');
  exit;
}
$user = $_SESSION['utilisateur'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['motif'], $_POST['description'])) {
  $motif = trim($_POST['motif']);
  $description = trim($_POST['description']);
  $produit_id = isset($_POST['produit_id']) ? intval($_POST['produit_id']) : null;
  $stmt = $pdo->prepare('INSERT INTO signalements (utilisateur_id, produit_id, motif, description, date_signalement) VALUES (?, ?, ?, ?, NOW())');
  $stmt->execute([$user['id'], $produit_id, $motif, $description]);
  header('Location: profil.php#signalements');
  exit;
}
header('Location: profil.php#signalements');
exit;
