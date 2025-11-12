<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
  header('Location: ../connexion.php');
  exit;
}
$msg = '';
// Ajout FAQ
if (isset($_POST['question'], $_POST['reponse'])) {
  $question = trim($_POST['question']);
  $reponse = trim($_POST['reponse']);
  $stmt = $pdo->prepare('INSERT INTO faq (question, reponse) VALUES (?, ?)');
  $stmt->execute([$question, $reponse]);
  $msg = "<div style='background:#d4edda;color:#155724;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>FAQ ajoutée !</div>";
}
// Suppression FAQ
if (isset($_POST['supprimer_id'])) {
  $id = intval($_POST['supprimer_id']);
  $stmt = $pdo->prepare('DELETE FROM faq WHERE id = ?');
  $stmt->execute([$id]);
  $msg = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>FAQ supprimée !</div>";
}
// Liste FAQ
$faqs = $pdo->query('SELECT * FROM faq ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - FAQ Campus Bot</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body style="background:#f6f7fb;">
<div style="max-width:900px;margin:48px auto;background:#fff;border-radius:18px;box-shadow:0 4px 24px #7f5af022;padding:38px;">
  <h2 style="color:#7f5af0;">Gestion de la FAQ du bot</h2>
  <?= $msg ?>
  <form method="post" style="margin-bottom:32px;background:#f3f6fa;padding:18px 24px;border-radius:12px;box-shadow:0 2px 12px #7f5af022;max-width:520px;">
    <label for="question" style="font-weight:600;">Question :</label><br>
    <input type="text" name="question" id="question" required style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;">
    <label for="reponse" style="font-weight:600;">Réponse :</label><br>
    <textarea name="reponse" id="reponse" rows="3" required style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;"></textarea>
    <button type="submit" style="background:#7f5af0;color:#fff;border:none;border-radius:7px;padding:10px 22px;font-size:1rem;font-weight:600;">Ajouter à la FAQ</button>
  </form>
  <table style="width:100%;border-collapse:collapse;background:#f3f6fa;border-radius:12px;box-shadow:0 2px 12px #7f5af022;">
    <thead>
      <tr style="background:#e0e7ff;">
        <th style="padding:12px 8px;">Question</th>
        <th style="padding:12px 8px;">Réponse</th>
        <th style="padding:12px 8px;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($faqs as $f): ?>
      <tr>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($f['question']) ?> </td>
        <td style="padding:10px 8px;"> <?= htmlspecialchars($f['reponse']) ?> </td>
        <td style="padding:10px 8px;">
          <form method="post" style="display:inline;">
            <input type="hidden" name="supprimer_id" value="<?= $f['id'] ?>">
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
