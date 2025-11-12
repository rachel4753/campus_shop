<?php
session_start();
require_once '../config.php';
$msgCat = $msgProd = $msgUser = $msgSign = $msgFaq = '';
// --- Catégories ---
if (isset($_POST['add_categorie'])) {
  $nom = trim($_POST['nom_categorie']);
  if ($nom !== '') {
    $stmt = $pdo->prepare('INSERT INTO categories (nom) VALUES (?)');
    $stmt->execute([$nom]);
    $msgCat = "<div style='background:#d4edda;color:#155724;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Catégorie ajoutée !</div>";
  }
}
if (isset($_POST['del_categorie'])) {
  $id = intval($_POST['del_categorie']);
  $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
  $stmt->execute([$id]);
  $msgCat = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Catégorie supprimée !</div>";
}
$categories = $pdo->query('SELECT * FROM categories ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
// --- Produits ---
if (isset($_POST['add_produit'])) {
  $nom = trim($_POST['nom_produit']);
  $prix = floatval($_POST['prix_produit']);
  $cat = intval($_POST['categorie_id']);
  $desc = trim($_POST['description_produit'] ?? '');
  $image = trim($_POST['image_produit'] ?? '');
  if ($nom !== '' && $prix > 0 && $cat > 0) {
    $stmt = $pdo->prepare('INSERT INTO produits (nom, prix, categorie_id, description, image) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$nom, $prix, $cat, $desc, $image]);
    $msgProd = "<div style='background:#d4edda;color:#155724;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Produit ajouté !</div>";
  }
}
if (isset($_POST['del_produit'])) {
  $id = intval($_POST['del_produit']);
  $stmt = $pdo->prepare('DELETE FROM produits WHERE id = ?');
  $stmt->execute([$id]);
  $msgProd = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Produit supprimé !</div>";
}
$produits = $pdo->query('SELECT p.*, c.nom as categorie FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.id DESC')->fetchAll(PDO::FETCH_ASSOC);
// --- Utilisateurs ---
if (isset($_POST['del_user'])) {
  $id = intval($_POST['del_user']);
  $stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
  $stmt->execute([$id]);
  $msgUser = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Utilisateur supprimé !</div>";
}
if (isset($_POST['modif_role'])) {
  $id = intval($_POST['modif_user_id']);
  $role = $_POST['modif_role_val'];
  $stmt = $pdo->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?');
  $stmt->execute([$role, $id]);
  $msgUser = "<div style='background:#d4edda;color:#155724;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Rôle modifié !</div>";
}
$users = $pdo->query('SELECT * FROM utilisateurs ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
// --- Signalements ---
if (isset($_POST['del_signalement'])) {
  $id = intval($_POST['del_signalement']);
  $stmt = $pdo->prepare('DELETE FROM signalements WHERE id = ?');
  $stmt->execute([$id]);
  $msgSign = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>Signalement supprimé !</div>";
}
$signalements = $pdo->query('SELECT id, description, date_signalement FROM signalements ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
// --- FAQ ---
if (isset($_POST['add_faq'])) {
  $question = trim($_POST['faq_question']);
  $reponse = trim($_POST['faq_reponse']);
  $stmt = $pdo->prepare('INSERT INTO faq (question, reponse) VALUES (?, ?)');
  $stmt->execute([$question, $reponse]);
  $msgFaq = "<div style='background:#d4edda;color:#155724;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>FAQ ajoutée !</div>";
}
if (isset($_POST['del_faq'])) {
  $id = intval($_POST['del_faq']);
  $stmt = $pdo->prepare('DELETE FROM faq WHERE id = ?');
  $stmt->execute([$id]);
  $msgFaq = "<div style='background:#f8d7da;color:#721c24;padding:10px 18px;border-radius:7px;margin-bottom:18px;'>FAQ supprimée !</div>";
}
$faqs = $pdo->query('SELECT * FROM faq ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Administrateur - Campus Shop</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-container">
  <aside class="admin-sidebar">
    <h2>Admin Campus Shop</h2>
    <nav>
  <a href="#categories" class="active">Catégories</a>
  <a href="#produits">Produits</a>
  <a href="#utilisateurs">Utilisateurs</a>
  <a href="#signalements">Signalements</a>
  <a href="#faq">FAQ</a>
    </nav>
    <form method="post" action="../logout.php" style="margin-top:32px;">
      <button type="submit" style="background:#e74c3c;color:#fff;border:none;border-radius:8px;padding:13px 0;font-size:17px;font-weight:600;cursor:pointer;box-shadow:0 2px 8px #e74c3c33;">Déconnexion</button>
    </form>
  </aside>
  <main class="admin-main">
    <section class="admin-section" id="categories">
      <h2>Gestion des catégories</h2>
      <?= $msgCat ?>
      <form method="post" style="margin-bottom:28px;display:flex;gap:12px;align-items:center;">
        <input type="text" name="nom_categorie" placeholder="Nom de la catégorie" required style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;">
        <button type="submit" name="add_categorie" style="background:#43e6ff;color:#fff;border:none;border-radius:7px;padding:8px 18px;font-size:1rem;font-weight:600;">Ajouter</button>
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
                <input type="hidden" name="del_categorie" value="<?= $cat['id'] ?>">
                <button type="submit" style="background:#ff4d4f;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:1rem;font-weight:600;cursor:pointer;">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
    <section class="admin-section" id="produits">
      <h2>Gestion des produits</h2>
      <?= $msgProd ?>
      <form method="post" style="margin-bottom:28px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;align-items:center;">
        <input type="text" name="nom_produit" placeholder="Nom du produit" required style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;">
        <input type="number" name="prix_produit" placeholder="Prix" min="1" required style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;">
        <select name="categorie_id" required style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;">
          <option value="">-- Catégorie --</option>
          <?php foreach($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"> <?= htmlspecialchars($cat['nom']) ?> </option>
          <?php endforeach; ?>
        </select>
        <input type="text" name="description_produit" placeholder="Description" style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;grid-column:span 3;">
        <input type="text" name="image_produit" placeholder="URL image (optionnel)" style="padding:8px 14px;border-radius:7px;border:1px solid #ccc;font-size:1rem;grid-column:span 3;">
        <button type="submit" name="add_produit" style="background:#43e6ff;color:#fff;border:none;border-radius:7px;padding:8px 18px;font-size:1rem;font-weight:600;grid-column:span 3;">Ajouter</button>
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
                <input type="hidden" name="del_produit" value="<?= $prod['id'] ?>">
                <button type="submit" style="background:#ff4d4f;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:1rem;font-weight:600;cursor:pointer;">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
    <section class="admin-section" id="utilisateurs">
      <h2>Gestion des utilisateurs</h2>
      <?= $msgUser ?>
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
                <input type="hidden" name="modif_user_id" value="<?= $u['id'] ?>">
                <select name="modif_role_val" style="padding:4px 10px;border-radius:6px;border:1px solid #ccc;">
                  <option value="acheteur" <?= $u['role']==='acheteur'?'selected':'' ?>>Acheteur</option>
                  <option value="vendeur" <?= $u['role']==='vendeur'?'selected':'' ?>>Vendeur</option>
                  <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
                </select>
                <button type="submit" name="modif_role" style="background:#43e6ff;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:1rem;font-weight:600;cursor:pointer;">Modifier</button>
              </form>
            </td>
            <td style="padding:10px 8px;">
              <form method="post" style="display:inline;">
                <input type="hidden" name="del_user" value="<?= $u['id'] ?>">
                <button type="submit" style="background:#ff4d4f;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:1rem;font-weight:600;cursor:pointer;">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
    <section class="admin-section" id="signalements">
      <h2>Signalements</h2>
      <?= $msgSign ?>
      <table style="width:100%;border-collapse:collapse;background:#f3f6fa;border-radius:12px;box-shadow:0 2px 12px #7f5af022;">
        <thead>
          <tr style="background:#ffe0e7;">
            <th style="padding:12px 8px;">Message</th>
            <th style="padding:12px 8px;">Date</th>
            <th style="padding:12px 8px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($signalements as $s): ?>
          <tr>
            <td style="padding:10px 8px;max-width:340px;word-break:break-word;"> <?= htmlspecialchars($s['message']) ?> </td>
            <td style="padding:10px 8px;"> <?= htmlspecialchars($s['date_signalement']) ?> </td>
            <td style="padding:10px 8px;">
              <form method="post" style="display:inline;">
                <input type="hidden" name="del_signalement" value="<?= $s['id'] ?>">
                <button type="submit" style="background:#ff4d4f;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:1rem;font-weight:600;cursor:pointer;">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
    <section class="admin-section" id="faq">
      <h2>FAQ Campus Bot</h2>
      <?= $msgFaq ?>
      <form method="post" style="margin-bottom:32px;background:#f3f6fa;padding:18px 24px;border-radius:12px;box-shadow:0 2px 12px #7f5af022;max-width:520px;">
        <label for="question" style="font-weight:600;">Question :</label><br>
        <input type="text" name="faq_question" id="question" required style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;">
        <label for="reponse" style="font-weight:600;">Réponse :</label><br>
        <textarea name="faq_reponse" id="reponse" rows="3" required style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;"></textarea>
        <button type="submit" name="add_faq" style="background:#7f5af0;color:#fff;border:none;border-radius:7px;padding:10px 22px;font-size:1rem;font-weight:600;">Ajouter à la FAQ</button>
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
                <input type="hidden" name="del_faq" value="<?= $f['id'] ?>">
                <button type="submit" style="background:#ff4d4f;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:1rem;font-weight:600;cursor:pointer;">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>
<script>
document.querySelectorAll('.admin-sidebar nav a').forEach(link => {
  link.addEventListener('click', function() {
    document.querySelectorAll('.admin-sidebar nav a').forEach(l => l.classList.remove('active'));
    this.classList.add('active');
    const section = document.querySelector(this.getAttribute('href'));
    if(section) section.scrollIntoView({behavior:'smooth'});
  });
});
</script>
</body>
</html>
