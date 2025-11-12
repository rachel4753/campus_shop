<?php
session_start();
require_once '../config.php';

// Ajout catégorie
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {
  $nom = trim($_POST['nom']);
  if ($nom !== '') {
    $stmt = $pdo->prepare('INSERT INTO categories (nom) VALUES (?)');
    $stmt->execute([$nom]);
    $msg = "<div style='background:#d4edda;color:#155724;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Catégorie ajoutée !</div>";
  }
}
// Suppression catégorie
if (isset($_POST['supprimer_id'])) {
  $id = intval($_POST['supprimer_id']);
  $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
  $stmt->execute([$id]);
  $msg = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Catégorie supprimée !</div>";
}
// Liste catégories
$categories = $pdo->query('SELECT * FROM categories ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - Catégories</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body style="background:#f6f7fb;">
<div style="max-width:700px;margin:48px auto;background:#fff;border-radius:18px;box-shadow:0 4px 24px #7f5af022;padding:38px;">
  <h2 style="color:#7f5af0;">Gestion des catégories</h2>
  <?= $msg ?>
  <form method="post" style="margin-bottom:28px;display:flex;gap:12px;align-items:center;">
    <input type="text" name="nom" placeholder="Nom de la catégorie" required style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;">
    <button type="submit" style="background:#43e6ff;color:#fff;border:none;border-radius:7px;padding:8px 18px;font-size:1rem;font-weight:600;">Ajouter</button>
  </form>
  <table style="width:100%;border-collapse:collapse;background:#f3f6fa;border-radius:12px;box-shadow:0 2px 12px #7f5af022;">
    <thead>
      <tr style="background:#e0e7ff;">
        <th style="padding:12px 8px;text-align:left;">Nom</th>
        <th style="padding:12px 8px;text-align:right;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($categories as $cat): ?>
      <tr>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($cat['nom']) ?> </td>
        <td style="padding:10px 8px;text-align:right;">
          <form method="post" style="display:inline;">
            <input type="hidden" name="supprimer_id" value="<?= $cat['id'] ?>">
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
