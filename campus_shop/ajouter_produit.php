<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

// Récupérer les données POST
$data = json_decode(file_get_contents('php://input'), true);
$nom = trim($data['nom'] ?? '');
$prix = floatval($data['prix'] ?? 0);
$image = trim($data['image'] ?? '');
$vendeur = trim($data['vendeur'] ?? '');
$categorie = trim($data['categorie'] ?? '');

if (!$nom || !$prix || !$vendeur || !$categorie) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Champs obligatoires manquants']);
    exit;
}

//  Vérifier si la catégorie existe déjà
$stmt = $pdo->prepare('SELECT id FROM categories WHERE LOWER(nom) = LOWER(?) LIMIT 1');
$stmt->execute([$categorie]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $categorie_id = $row['id'];
} else {
    //  Créer la catégorie si elle n’existe pas
    $stmt = $pdo->prepare('INSERT INTO categories (nom) VALUES (?)');
    $stmt->execute([$categorie]);
    $categorie_id = $pdo->lastInsertId();
}

//  Ajouter le produit 
$stmt = $pdo->prepare('INSERT INTO produits (nom, prix, image, vendeur, categorie_id) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$nom, $prix, $image, $vendeur, $categorie_id]);

// 4. Retourner la nouvelle catégorie si elle vient d’être créée
$newCat = null;
if (!$row) {
    $newCat = [
        'id' => $categorie_id,
        'nom' => $categorie
    ];
}

echo json_encode(['success' => true, 'categorie' => $newCat]);
