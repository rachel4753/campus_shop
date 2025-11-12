<?php
session_start();
require_once '../config.php';

$msg = '';
// Suppression signalement
if (isset($_POST['supprimer_id'])) {
  $id = intval($_POST['supprimer_id']);
  $stmt = $pdo->prepare('DELETE FROM signalements WHERE id = ?');
  $stmt->execute([$id]);
  $msg = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Signalement supprimé !</div>";
}
// Liste signalements
$signalements = $pdo->query('SELECT s.*, u.nom AS nom_utilisateur, p.nom AS nom_produit FROM signalements s LEFT JOIN utilisateurs u ON s.utilisateur_id = u.id LEFT JOIN produits p ON s.produit_id = p.id ORDER BY s.id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - Signalements</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body style="background:#f6f7fb;">
<div style="max-width:900px;margin:48px auto;background:#fff;border-radius:18px;box-shadow:0 4px 24px #7f5af022;padding:38px;">
  <h2 style="color:#ff4d4f;">Gestion des signalements</h2>
  <?= $msg ?>
  <table style="width:100%;border-collapse:collapse;background:#f3f6fa;border-radius:12px;box-shadow:0 2px 12px #7f5af022;">
    <thead>
      <tr style="background:#ffe0e7;">
        <th style="padding:12px 8px;">Utilisateur</th>
        <th style="padding:12px 8px;">Produit</th>
        <th style="padding:12px 8px;">Motif</th>
        <th style="padding:12px 8px;">Date</th>
        <th style="padding:12px 8px;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($signalements as $s): ?>
      <tr>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($s['nom_utilisateur']) ?> </td>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($s['nom_produit']) ?> </td>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($s['motif']) ?> </td>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($s['date_signalement']) ?> </td>
        <td style="padding:10px 8px;">
          <form method="post" style="display:inline;">
            <input type="hidden" name="supprimer_id" value="<?= $s['id'] ?>">
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
