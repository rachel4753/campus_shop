<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
  header('Location: ../connexion.php');
  exit;
}
$msg = '';
// Suppression utilisateur
if (isset($_POST['supprimer_id'])) {
  $id = intval($_POST['supprimer_id']);
  $stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
  $stmt->execute([$id]);
  $msg = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Utilisateur supprimé !</div>";
}
// Modification rôle
if (isset($_POST['modifier_id'], $_POST['nouveau_role'])) {
  $id = intval($_POST['modifier_id']);
  $role = $_POST['nouveau_role'];
  $stmt = $pdo->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?');
  $stmt->execute([$role, $id]);
  $msg = "<div style='background:#d4edda;color:#155724;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Rôle modifié !</div>";
}
// Liste utilisateurs
$users = $pdo->query('SELECT * FROM utilisateurs ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - Utilisateurs</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body style="background:#f6f7fb;">
<div style="max-width:900px;margin:48px auto;background:#fff;border-radius:18px;box-shadow:0 4px 24px #7f5af022;padding:38px;">
  <h2 style="color:#7f5af0;">Gestion des utilisateurs</h2>
  <?= $msg ?>
  <table style="width:100%;border-collapse:collapse;background:#f3f6fa;border-radius:12px;box-shadow:0 2px 12px #7f5af022;">
    <thead>
      <tr style="background:#e0e7ff;">
        <th style="padding:12px 8px;">Nom</th>
        <th style="padding:12px 8px;">Email</th>
        <th style="padding:12px 8px;">WhatsApp</th>
        <th style="padding:12px 8px;">Rôle</th>
        <th style="padding:12px 8px;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($users as $u): ?>
      <tr>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($u['nom']) ?> </td>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($u['email']) ?> </td>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($u['whatsapp']) ?> </td>
        <td style="padding:10px 8px;">
          <form method="post" style="display:inline;">
            <input type="hidden" name="modifier_id" value="<?= $u['id'] ?>">
            <select name="nouveau_role" style="padding:4px 10px;border-radius:6px;border:1px solid #ccc;">
              <option value="acheteur" <?= $u['role']==='acheteur'?'selected':'' ?>>Acheteur</option>
              <option value="vendeur" <?= $u['role']==='vendeur'?'selected':'' ?>>Vendeur</option>
              <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
            </select>
            <button type="submit" style="background:#43e6ff;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:1rem;font-weight:600;cursor:pointer;">Modifier</button>
          </form>
        </td>
        <td style="padding:10px 8px;">
          <form method="post" style="display:inline;">
            <input type="hidden" name="supprimer_id" value="<?= $u['id'] ?>">
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
