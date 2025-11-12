<?php
session_start();
require_once 'config.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $categorie_existante = trim($_POST['categorie_existante'] ?? '');
    $categorie_nouvelle = trim($_POST['categorie_nouvelle'] ?? '');

    // Choix de la catégorie (nouvelle prioritaire)
    $categorie_nom = $categorie_nouvelle ?: $categorie_existante;
    $stmtCat = $pdo->prepare('SELECT id FROM categories WHERE nom = ?');
    $stmtCat->execute([$categorie_nom]);
    $cat = $stmtCat->fetch(PDO::FETCH_ASSOC);
    if (!$cat) {
        $pdo->prepare('INSERT INTO categories (nom) VALUES (?)')->execute([$categorie_nom]);
        $categorie_id = $pdo->lastInsertId();
    } else {
        $categorie_id = $cat['id'];
    }

    // Vérifie les champs obligatoires
    if (empty($nom) || empty($description) || $prix <= 0 || empty($categorie_nom)) {
        die("Veuillez remplir correctement tous les champs obligatoires.");
    }

    // Gestion des images multiples
    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        $dossier = 'assets/img/produits/';
        if (!is_dir($dossier)) {
            mkdir($dossier, 0777, true);
        }
        foreach ($_FILES['images']['name'] as $i => $nomFichier) {
            if (!empty($nomFichier)) {
                $fichierTmp = $_FILES['images']['tmp_name'][$i];
                $nomUnique = uniqid('prod_') . '_' . basename($nomFichier);
                $cheminComplet = $dossier . $nomUnique;
                if (move_uploaded_file($fichierTmp, $cheminComplet)) {
                    $images[] = $cheminComplet;
                }
            }
        }
    }
    $images_json = json_encode($images);

    // Insérer le produit dans la base de données avec l'utilisateur propriétaire
    $utilisateur_id = $_SESSION['utilisateur']['id'];
    $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, stock, categorie_id, images, utilisateur_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $description, $prix, $stock, $categorie_id, $images_json, $utilisateur_id]);

    // Redirection vers l'accueil
    header('Location: index.php');
    exit();
} else {
    echo "Méthode non autorisée.";
}