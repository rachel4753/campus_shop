<?php
session_start();
require_once '../config.php';

// Ajout produit
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'], $_POST['prix'], $_POST['categorie_id'])) {
  $nom = trim($_POST['nom']);
  $prix = floatval($_POST['prix']);
  $cat = intval($_POST['categorie_id']);
  $desc = trim($_POST['description'] ?? '');
  $image = trim($_POST['image'] ?? '');
  if ($nom !== '' && $prix > 0 && $cat > 0) {
    $stmt = $pdo->prepare('INSERT INTO produits (nom, prix, categorie_id, description, image) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$nom, $prix, $cat, $desc, $image]);
    $msg = "<div style='background:#d4edda;color:#155724;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Produit ajouté !</div>";
  }
}
// Suppression produit
if (isset($_POST['supprimer_id'])) {
  $id = intval($_POST['supprimer_id']);
  $stmt = $pdo->prepare('DELETE FROM produits WHERE id = ?');
  $stmt->execute([$id]);
  $msg = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Produit supprimé !</div>";
}
// Liste produits
$produits = $pdo->query('SELECT p.*, c.nom as categorie FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.id DESC')->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query('SELECT * FROM categories ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - Produits</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body style="background:#f6f7fb;">
<div style="max-width:900px;margin:48px auto;background:#fff;border-radius:18px;box-shadow:0 4px 24px #7f5af022;padding:38px;">
  <h2 style="color:#7f5af0;">Gestion des produits</h2>
  <?= $msg ?>
  <form method="post" style="margin-bottom:28px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;align-items:center;">
    <input type="text" name="nom" placeholder="Nom du produit" required style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;">
    <input type="number" name="prix" placeholder="Prix" min="1" required style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;">
    <select name="categorie_id" required style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;">
      <option value="">-- Catégorie --</option>
      <?php foreach($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>"> <?= htmlspecialchars($cat['nom']) ?> </option>
      <?php endforeach; ?>
    </select>
    <input type="text" name="description" placeholder="Description" style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;grid-column:span 3;">
    <input type="text" name="image" placeholder="URL image (optionnel)" style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;grid-column:span 3;">
    <button type="submit" style="background:#43e6ff;color:#fff;border:none;border-radius:7px;padding:8px 18px;font-size:1rem;font-weight:600;grid-column:span 3;">Ajouter</button>
  </form>
  <table style="width:100%;border-collapse:collapse;background:#f3f6fa;border-radius:12px;box-shadow:0 2px 12px #7f5af022;">
    <thead>
      <tr style="background:#e0e7ff;">
        <th style="padding:12px 8px;">Nom</th>
        <th style="padding:12px 8px;">Prix</th>
        <th style="padding:12px 8px;">Catégorie</th>
        <th style="padding:12px 8px;">Image</th>
        <th style="padding:12px 8px;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($produits as $prod): ?>
      <tr>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($prod['nom']) ?> </td>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($prod['prix']) ?> FCFA </td>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($prod['categorie']) ?> </td>
        <td style="padding:10px 8px;">
          <?php if(!empty($prod['image'])): ?>
            <img src="<?= htmlspecialchars($prod['image']) ?>" alt="img" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
          <?php endif; ?>
        </td>
        <td style="padding:10px 8px;">
          <form method="post" style="display:inline;">
            <input type="hidden" name="supprimer_id" value="<?= $prod['id'] ?>">
            <button type="submit" style="background:#ff4d4f;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:1rem;font-weight:600;cursor:pointer;">Supprimer</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a href="index.php" style="display:inline-block;margin-top:32px;color:#7f5af0;font-weight:600;text-decoration:underline;">Retour au dashboard admin</a>
</div>
</body>
</html>
