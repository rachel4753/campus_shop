<?php session_start(); ?>
<?php
require_once __DIR__ . '/config.php';
// Note: $pdo must être fourni par config.php
$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q !== '') {
  $stmt = $pdo->prepare('SELECT p.*, c.nom as categorie, u.nom as vendeur_nom, u.whatsapp as vendeur_whatsapp FROM produits p JOIN categories c ON p.categorie_id = c.id LEFT JOIN utilisateurs u ON p.utilisateur_id = u.id WHERE p.nom LIKE :q1 OR p.description LIKE :q2 ORDER BY p.id DESC');
  $stmt->execute(['q1' => "%$q%", 'q2' => "%$q%"]);
  $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  $produits = $pdo->query('SELECT p.*, c.nom as categorie, u.nom as vendeur_nom, u.whatsapp as vendeur_whatsapp FROM produits p JOIN categories c ON p.categorie_id = c.id LEFT JOIN utilisateurs u ON p.utilisateur_id = u.id ORDER BY p.id DESC')->fetchAll(PDO::FETCH_ASSOC);
}

// Réponse Campus Bot depuis la base FAQ
if (isset($_POST['campusbot_question'])) {
  $q = strtolower(trim($_POST['campusbot_question']));
  $stmt = $pdo->prepare("SELECT reponse FROM faq WHERE LOWER(question) LIKE ? LIMIT 1");
  $stmt->execute(["%$q%"]);
  $rep = $stmt->fetchColumn();
  echo $rep ? $rep : "Je n'ai pas trouvé de réponse, mais je peux t'aider sur l'achat, la vente, le panier, le profil, la sécurité ou le contact vendeur.";
  exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Campus Shop - Tout ce dont vous avez besoin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <div class="frame" style="width:100vw;max-width:100vw;padding:0;margin:0;">

    <!-- Bouton Ajouter Produit -->


    <!-- Modale Ajouter Produit -->
    <div class="modal-ajout-produit" id="modalAjoutProduit">
      <div class="modal-overlay"></div>
      <div class="modal-content">
        <button class="close-modal" id="closeAjoutProduit" aria-label="Fermer">✖️</button>
        <h2 class="modal-title">Ajouter un produit</h2>
        <form id="formAjoutProduit" method="post" action="ajouter_produit_backend.php" enctype="multipart/form-data" autocomplete="off">
          <div class="form-group floating-label">
            <input type="text" name="nom" id="nomProduit" required />
            <label for="nomProduit">Nom du produit *</label>
          </div>
          <div class="form-group floating-label">
            <textarea name="description" id="descProduit" required></textarea>
            <label for="descProduit">Description *</label>
          </div>
          <div class="form-row">
            <div class="form-group floating-label">
              <input type="number" name="prix" id="prixProduit" min="1" required />
              <label for="prixProduit">Prix (FCFA) *</label>
            </div>
            <div class="form-group floating-label">
              <input type="number" name="stock" id="stockProduit" min="0" value="1" />
              <label for="stockProduit">Stock</label>
            </div>
          </div>
          <div class="form-group floating-label">
            <select name="categorie_existante" id="catExistante" style="width:100%;padding:12px;border-radius:8px;border:1.5px solid #ddd;font-size:1rem;">
              <option value="">-- Choisir une catégorie existante --</option>
              <?php foreach($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['nom']) ?>"><?= htmlspecialchars($cat['nom']) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="categorie_nouvelle" id="catNouvelle" placeholder="Ou ajouter une nouvelle catégorie" style="margin-top:8px;" />
            <label for="catExistante">Catégorie *</label>
          </div>
          <div class="form-group file-group">
            <input type="file" name="images[]" id="imgProduit" accept="image/*" multiple />
            <label for="imgProduit" class="file-label">Images (plusieurs possibles)</label>
          </div>
          <button type="submit" class="btn primary submit-anim">Ajouter</button>
        </form>
      </div>
    </div>

    <style>
      /* UI et animations avancées pour la modale d'ajout produit */
      .modal-ajout-produit {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.35);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        animation: modalBgFadeIn 0.5s cubic-bezier(.4,2,.6,1);
      }
      .modal-ajout-produit.active {
        display: flex !important;
      }
      @keyframes modalBgFadeIn {
        0% { background: rgba(0,0,0,0); }
        100% { background: rgba(0,0,0,0.35); }
      }
      .modal-ajout-produit .modal-content {
        position: relative;
        background: #f8f9fb;
        border-radius: 18px;
        box-shadow: 0 8px 32px #bfc9e633, 0 1.5px 0 #bfc9e6;
        padding: 40px 28px 28px 28px;
        min-width: 340px;
        max-width: 98vw;
        width: 420px;
        animation: modalFadeIn 0.6s cubic-bezier(.4,2,.6,1);
        transform: scale(0.97);
        opacity: 0;
        animation-fill-mode: forwards;
      }
      .modal-ajout-produit.active .modal-content {
        animation: modalFadeIn 0.6s cubic-bezier(.4,2,.6,1) forwards;
      }
      @keyframes modalFadeIn {
        0% { opacity:0; transform:translateY(60px) scale(0.85) rotate(-2deg); }
        60% { opacity:1; transform:translateY(-8px) scale(1.04) rotate(1deg); }
        100% { opacity:1; transform:translateY(0) scale(1) rotate(0deg); }
      }
      .modal-title {
        margin-bottom: 18px;
        font-size: 2rem;
        color: #3a3a4a;
        text-align: center;
        letter-spacing: 0.01em;
        font-weight: 700;
        animation: fadeInTitle 0.7s cubic-bezier(.4,2,.6,1);
      }
      @keyframes fadeInTitle {
        0% { opacity:0; transform:translateY(-20px) scale(0.9); }
        100% { opacity:1; transform:translateY(0) scale(1); }
      }
      .modal-ajout-produit .close-modal {
        position: absolute;
        top: 18px; right: 18px;
        font-size: 1.5rem;
        background: none;
        border: none;
        color: #7f5af0;
        cursor: pointer;
        transition: color 0.2s, transform 0.15s;
      }
      .modal-ajout-produit .close-modal:hover {
        color: #43e6ff;
        transform: rotate(90deg) scale(1.2);
      }
      .modal-ajout-produit .form-group {
        position: relative;
        margin-bottom: 22px;
      }
      .modal-ajout-produit .form-row {
        display: flex;
        gap: 16px;
      }
      .modal-ajout-produit .form-group.floating-label input,
      .modal-ajout-produit .form-group.floating-label textarea {
        width: 100%;
        padding: 16px 14px 12px 14px;
        border-radius: 10px;
        border: 1.5px solid #ddd;
        font-size: 1rem;
        background: #fff;
        transition: border 0.2s, background 0.2s;
        outline: none;
        box-shadow: 0 1px 4px #7f5af011;
      }
      .modal-ajout-produit .form-group.floating-label label {
        position: absolute;
        left: 16px;
        top: 18px;
        color: #6a6a7a;
        font-size: 1rem;
        pointer-events: none;
        background: transparent;
        transition: 0.2s cubic-bezier(.4,2,.6,1);
        opacity: 0.7;
      }
      .modal-ajout-produit .form-group.floating-label input:focus + label,
      .modal-ajout-produit .form-group.floating-label input:not(:placeholder-shown) + label,
      .modal-ajout-produit .form-group.floating-label textarea:focus + label,
      .modal-ajout-produit .form-group.floating-label textarea:not(:placeholder-shown) + label {
        top: -12px;
        left: 10px;
        font-size: 0.92rem;
        color: #4a90e2;
        background: #f8f9fb;
        padding: 0 6px;
        opacity: 1;
      }
      .modal-ajout-produit .form-group.floating-label input:focus,
      .modal-ajout-produit .form-group.floating-label textarea:focus {
        border-color: #4a90e2;
        background: #f3f6fa;
      }
      .modal-ajout-produit .form-group.file-group input[type="file"] {
        padding: 8px 0;
        border: none;
        background: none;
      }
      .modal-ajout-produit .form-group.file-group .file-label {
        color: #7f5af0;
        font-weight: 600;
        margin-left: 8px;
      }
      .modal-ajout-produit .btn.primary {
        background: linear-gradient(90deg,#4a90e2 60%,#bfc9e6 100%);
        color: #fff;
        font-weight: 700;
        border: none;
        border-radius: 10px;
        padding: 14px 0;
        font-size: 1.1rem;
        margin-top: 8px;
        cursor: pointer;
        box-shadow: 0 2px 12px #bfc9e633;
        transition: background 0.2s, color 0.2s, transform 0.15s;
      }
      .modal-ajout-produit .btn.primary:hover {
        background: linear-gradient(90deg,#bfc9e6 60%,#4a90e2 100%);
        color: #222;
        transform: scale(1.03);
      }
      .modal-ajout-produit input[type="file"]::-webkit-file-upload-button {
        background: #7f5af0;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
      }
      .modal-ajout-produit input[type="file"]::-webkit-file-upload-button:hover {
        background: #43e6ff;
        color: #222;
      }
      /* Animation de validation */
      .modal-ajout-produit .form-success {
        animation: formSuccessAnim 0.7s cubic-bezier(.4,2,.6,1);
        color: #43e6ff;
        font-weight: 700;
        text-align: center;
        margin-bottom: 12px;
      }
      @keyframes formSuccessAnim {
        0% { opacity:0; transform:scale(0.8); }
        60% { opacity:1; transform:scale(1.1); }
        100% { opacity:1; transform:scale(1); }
      }
      @keyframes modalFadeIn {
        0% { opacity:0; transform:translateY(40px) scale(0.95); }
        100% { opacity:1; transform:translateY(0) scale(1); }
      }
      .modal-ajout-produit[style*="display: flex"] .modal-content {
        animation: modalFadeIn 0.4s cubic-bezier(.4,2,.6,1);
      }
      .modal-ajout-produit .modal-content:focus-within {
        box-shadow:0 8px 32px #43e6ff33,0 1.5px 0 #7f5af0;
      }
      .modal-ajout-produit input:focus, .modal-ajout-produit textarea:focus {
        outline:2px solid #7f5af0;
        border-color:#7f5af0;
      }
      #btnAjouterProduit:hover {
        background:#43e6ff;
        color:#222;
      }
      .modal-ajout-produit .btn.primary {
        background:#7f5af0;
        color:#fff;
      }
      .modal-ajout-produit .btn.primary:hover {
        background:#43e6ff;
        color:#222;
      }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Modal ajout produit (inchangé)
      var btnAjouter = document.getElementById('btnAjouterProduit');
      var modal = document.getElementById('modalAjoutProduit');
      var closeBtn = document.getElementById('closeAjoutProduit');
      if (btnAjouter && modal && closeBtn) {
        btnAjouter.addEventListener('click', function() {
          modal.classList.add('active');
          setTimeout(function(){modal.querySelector('.modal-content').focus();}, 100);
        });
        closeBtn.addEventListener('click', function() {
          modal.classList.remove('active');
        });
        modal.querySelector('.modal-overlay').addEventListener('click', function() {
          modal.classList.remove('active');
        });
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape' && modal.classList.contains('active')) {
            modal.classList.remove('active');
          }
        });
      }
      // Animation de validation formulaire (inchangé)
      var form = document.getElementById('formAjoutProduit');
      if (form) {
        form.addEventListener('submit', function(e) {
          var btn = form.querySelector('button[type="submit"]');
          btn.disabled = true;
          btn.innerHTML = 'Ajout en cours...';
          setTimeout(function(){
            btn.disabled = false;
            btn.innerHTML = 'Ajouter';
            var msg = document.createElement('div');
            msg.className = 'form-success';
            msg.textContent = 'Produit envoyé !';
            form.prepend(msg);
            setTimeout(function(){ msg.remove(); }, 1800);
          }, 1200);
        });
        var inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(function(input) {
          input.addEventListener('focus', function() {
            input.style.background = '#f5f7ff';
          });
          input.addEventListener('blur', function() {
            input.style.background = '#fff';
          });
        });
      }

      // --- FONCTIONNALITÉS PRODUITS & PANIER ---
      var produits = <?php echo json_encode($produits, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); ?>;
      var categories = <?php echo json_encode($categories, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); ?>;
      const grille = document.getElementById('grilleProduitsNouvelle');
      const catBtns = document.querySelectorAll('.cat-btn');
      const btnFiltrer = document.getElementById('btnFiltrer');
      const btnGrid = document.getElementById('btnGrid');
      const btnListe = document.getElementById('btnListe');
      const btnPromo = document.getElementById('btnPromo');
      const pagination = document.getElementById('paginationModern');
      const panierBtn = document.getElementById('panierBtn');
      const modalPanier = document.getElementById('modalPanier');
      const closePanier = document.getElementById('closePanier');
      const panierContent = document.getElementById('panierContent');
      let currentCat = null;
      let currentTri = 'populaire';
      let currentView = 'grid';
      let page = 1;
      const perPage = 8;

      // Panier (localStorage)
      function getPanier() {
        return JSON.parse(localStorage.getItem('panier')||'[]');
      }
      function setPanier(panier) {
        localStorage.setItem('panier', JSON.stringify(panier));
      }
      function addToPanier(prod) {
        let panier = getPanier();
        let exist = panier.find(p=>p.id==prod.id);
        if(exist) exist.qte++;
        else panier.push({...prod, qte:1});
        setPanier(panier);
        showPanier();
      }
      function showPanier() {
        let panier = getPanier();
        if(panier.length===0) {
          panierContent.innerHTML = 'Aucun article pour le moment.';
        } else {
          panierContent.innerHTML = `
            <button id="viderPanierBtn" style="background:#ff4d4f;color:#fff;padding:8px 18px;border:none;border-radius:8px;font-weight:600;margin-bottom:18px;cursor:pointer;">Vider le panier</button>
            <ul style="list-style:none;padding:0;">`+
            panier.map(p=>{
              let whatsapp = p.vendeur_whatsapp ? p.vendeur_whatsapp.replace(/[^0-9]/g,'') : '';
              let boutonWhatsapp = whatsapp ? `<a href="https://wa.me/${whatsapp}" target="_blank" style="margin-left:12px;background:#43e6ff;color:#fff;padding:6px 14px;border-radius:8px;font-weight:600;text-decoration:none;display:inline-block;">Commander sur WhatsApp</a>` : '';
              return `<li style='margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;'><img src='${p.image?p.image:'https://via.placeholder.com/60x60?text=Image'}' style='width:48px;height:48px;object-fit:cover;border-radius:8px;'> <span style='flex:1;'>${p.nom}</span> <span style='color:#4a90e2;font-weight:600;'>x${p.qte}</span> <span style='font-weight:700;'>${p.prix} FCFA</span> ${boutonWhatsapp}</li>`;
            }).join('')+
            '</ul>';
          setTimeout(()=>{
            const viderBtn = document.getElementById('viderPanierBtn');
            if(viderBtn) viderBtn.onclick = function(){
              localStorage.removeItem('panier');
              showPanier();
            };
          }, 50);
        }
      }
      if(panierBtn && modalPanier && closePanier) {
        panierBtn.onclick = function(){
          showPanier();
          modalPanier.classList.add('active');
        };
        closePanier.onclick = function(){modalPanier.classList.remove('active');};
        modalPanier.querySelector('.modal-overlay').onclick = function(){modalPanier.classList.remove('active');};
      }

      // Affichage dynamique des produits
      function renderProduits() {
        let filtered = produits.slice();
        if (currentCat) filtered = filtered.filter(p => p.categorie === currentCat);
        if (currentTri === 'nouveau') filtered.sort((a,b)=>b.id-a.id);
        else filtered.sort((a,b)=>b.id-a.id);
        const total = filtered.length;
        const start = (page-1)*perPage;
        const end = start+perPage;
        const paged = filtered.slice(start,end);
        grille.innerHTML = '';
        if (paged.length === 0) {
          grille.innerHTML = `<div class='produit-empty'><img src='https://cdn-icons-png.flaticon.com/512/4076/4076549.png' alt='Aucun produit' style='width:90px;opacity:0.4;margin-bottom:18px;'><p class='placeholder'>Aucun produit pour cette catégorie.</p></div>`;
        } else {
          paged.forEach(function(prod) {
            grille.innerHTML += `
              <div class="card produit-card-nouvelle" style="background:#fff;border-radius:22px;box-shadow:0 6px 28px #bfc9e633;padding:0;display:flex;flex-direction:column;overflow:hidden;position:relative;transition:box-shadow 0.2s;">
                <div class="img-zone" style="width:100%;aspect-ratio:4/3;overflow:hidden;position:relative;background:#f3f6fa;">
                  <img src="${prod.image ? prod.image : 'https://via.placeholder.com/400x300?text=Image'}" alt="${prod.nom}" loading="lazy" style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s;cursor:pointer;" onclick="window.open(this.src,'_blank')" onerror="this.src='https://via.placeholder.com/400x300?text=Image';">
                  <span class="badge-nouveau" style="position:absolute;top:14px;left:14px;background:#43e6ff;color:#fff;font-size:0.98rem;padding:3px 12px;border-radius:8px;font-weight:600;box-shadow:0 2px 8px #43e6ff22;">Nouveau</span>
                  <button class="btn-action-fav" title="Favori" style="position:absolute;top:14px;right:14px;background:#fff;border:none;border-radius:50%;width:38px;height:38px;box-shadow:0 2px 8px #bfc9e611;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s;"><svg width='22' height='22' fill='none' viewBox='0 0 24 24'><path d='M12 21s-7-4.35-7-10a5 5 0 019-3.32A5 5 0 0119 11c0 5.65-7 10-7 10z' stroke='#7f5af0' stroke-width='2'/></svg></button>
                </div>
                <div class="infos-produit" style="padding:22px 18px 16px 18px;display:flex;flex-direction:column;gap:8px;flex:1;">
                  <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                    <h4 style="font-size:1.25rem;margin:0;word-break:break-word;flex:1;">${prod.nom}</h4>
                    <span class="cat" style="font-size:0.98rem;color:#7f5af0;font-weight:600;background:#f3f6fa;padding:2px 10px;border-radius:8px;">${prod.categorie}</span>
                  </div>
                  <p style="font-size:1.05rem;margin:0 0 4px 0;text-align:left;min-height:38px;max-height:60px;overflow:hidden;color:#444;">${prod.description}</p>
                  <div style="display:flex;align-items:center;gap:12px;margin-top:8px;">
                    <span style="font-size:1.18rem;font-weight:700;color:#4a90e2;">${prod.prix} FCFA</span>
                    <span style="font-size:0.98rem;color:#43e6ff;font-weight:600;">Stock: ${prod.stock !== undefined ? prod.stock : 'N/A'}</span>
                  </div>
                  <div style="margin-top:8px;font-size:0.98rem;color:#7f5af0;display:flex;align-items:center;gap:8px;">
                    <span style="font-weight:600;">Vendeur :</span> <span>${prod.vendeur_nom ? prod.vendeur_nom : 'N/A'}</span>
                    <a href="https://wa.me/${prod.vendeur_whatsapp ? prod.vendeur_whatsapp.replace(/[^0-9]/g,'') : ''}" target="_blank" style="color:#43e6ff;text-decoration:none;font-weight:600;display:flex;align-items:center;gap:4px;">
                      <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M12 2a10 10 0 00-8.94 14.37l-1.05 3.06a1 1 0 001.26 1.26l3.06-1.05A10 10 0 1012 2z" stroke="#43e6ff" stroke-width="2"/><path d="M16.24 15.32c-.27.76-1.6 1.48-2.22 1.58-.57.09-1.28.13-2.06-.2a8.13 8.13 0 01-3.1-2.5c-.44-.6-1.2-1.7-1.2-3.1 0-1.4.76-2.1 1.03-2.36.27-.27.6-.34.8-.34.2 0 .4.01.57.01.18 0 .43-.07.67.51.24.58.82 2.01.89 2.16.07.15.12.33.02.53-.1.2-.15.32-.3.5-.15.18-.31.4-.44.54-.13.14-.27.29-.12.57.15.28.67 1.1 1.44 1.5.77.4 1.13.44 1.41.37.28-.07.62-.25.79-.5.17-.25.34-.5.48-.67.14-.17.29-.14.48-.08.19.06 1.2.57 1.41.67.2.1.33.15.38.23.05.08.05.46-.22 1.22z" fill="#43e6ff"/></svg>
                      ${prod.vendeur_whatsapp ? prod.vendeur_whatsapp : ''}
                    </a>
                  </div>
                  <div class="actions-ligne" style="display:flex;gap:10px;justify-content:flex-end;margin-top:auto;">
                    <button class="btn-action" title="Partager" onclick="navigator.clipboard.writeText(window.location.href);alert('Lien copié !')" style="background:#f3f6fa;border:none;cursor:pointer;padding:6px 12px;border-radius:8px;transition:background 0.2s;"><svg width='20' height='20' fill='none' viewBox='0 0 24 24'><circle cx='18' cy='5' r='3' stroke='#4a90e2' stroke-width='2'/><circle cx='6' cy='12' r='3' stroke='#4a90e2' stroke-width='2'/><circle cx='18' cy='19' r='3' stroke='#4a90e2' stroke-width='2'/><path d='M8.59 13.51l6.83 3.98M15.41 6.51l-6.82 3.98' stroke='#4a90e2' stroke-width='2'/></svg> Partager</button>
                    <button class="btn-action" title="Ajouter au panier" data-id="${prod.id}" style="background:#7f5af0;color:#fff;border:none;cursor:pointer;padding:6px 16px;border-radius:8px;font-weight:600;transition:background 0.2s;">Ajouter au panier</button>
                    <button class="btn-action" title="Voir détails" data-id="${prod.id}" style="background:#43e6ff;color:#fff;border:none;cursor:pointer;padding:6px 16px;border-radius:8px;font-weight:600;transition:background 0.2s;">Détails</button>
                  </div>
                </div>
              </div>
            `;
// Modale détails produit (HTML ajouté à la fin du body)
if (!document.getElementById('modalProduitDetails')) {
  const modalHtml = `
    <div id="modalProduitDetails" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
      <div style="background:#fff;border-radius:18px;max-width:600px;width:95vw;box-shadow:0 8px 32px #0002;padding:0;position:relative;overflow:hidden;">
  <button id="closeModalProduit" style="position:absolute;top:12px;right:12px;background:#f3f6fa;border:none;border-radius:50%;width:36px;height:36px;cursor:pointer;font-size:1.5rem;z-index:10;">&times;</button>
        <div id="modalGalerie" style="width:100%;min-height:180px;display:flex;flex-wrap:wrap;gap:12px;justify-content:center;align-items:center;background:#f3f6fa;padding:18px 0;">
          <!-- Les images seront injectées ici -->
        </div>
        <div style="padding:22px 18px 16px 18px;display:flex;flex-direction:column;gap:10px;">
          <h2 id="modalNom" style="margin:0;font-size:1.4rem;"></h2>
          <div style="display:flex;align-items:center;gap:10px;">
            <span id="modalCategorie" style="font-size:1rem;color:#7f5af0;font-weight:600;background:#f3f6fa;padding:2px 10px;border-radius:8px;"></span>
            <span id="modalPrix" style="font-size:1.1rem;font-weight:700;color:#4a90e2;"></span>
            <span id="modalStock" style="font-size:0.98rem;color:#43e6ff;font-weight:600;"></span>
          </div>
          <p id="modalDescription" style="font-size:1.05rem;margin:0;color:#444;"></p>
          <div style="margin-top:4px;font-size:0.98rem;color:#7f5af0;display:flex;align-items:center;gap:8px;">
            <span style="font-weight:600;">Vendeur :</span> <span id="modalVendeurNom"></span>
            <a id="modalVendeurWhatsapp" href="#" target="_blank" style="color:#43e6ff;text-decoration:none;font-weight:600;display:flex;align-items:center;gap:4px;">
              <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M12 2a10 10 0 00-8.94 14.37l-1.05 3.06a1 1 0 001.26 1.26l3.06-1.05A10 10 0 1012 2z" stroke="#43e6ff" stroke-width="2"/><path d="M16.24 15.32c-.27.76-1.6 1.48-2.22 1.58-.57.09-1.28.13-2.06-.2a8.13 8.13 0 01-3.1-2.5c-.44-.6-1.2-1.7-1.2-3.1 0-1.4.76-2.1 1.03-2.36.27-.27.6-.34.8-.34.2 0 .4.01.57.01.18 0 .43-.07.67.51.24.58.82 2.01.89 2.16.07.15.12.33.02.53-.1.2-.15.32-.3.5-.15.18-.31.4-.44.54-.13.14-.27.29-.12.57.15.28.67 1.1 1.44 1.5.77.4 1.13.44 1.41.37.28-.07.62-.25.79-.5.17-.25.34-.5.48-.67.14-.17.29-.14.48-.08.19.06 1.2.57 1.41.67.2.1.33.15.38.23.05.08.05.46-.22 1.22z" fill="#43e6ff"/></svg>
              <span id="modalVendeurWhatsappNum"></span>
            </a>
          </div>
          <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
            <span style="font-weight:600;">Note :</span>
            <span id="modalNote"></span>
            <span id="modalNoteStars"></span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
            <label for="modalQuantite" style="font-weight:600;">Quantité :</label>
            <input id="modalQuantite" type="number" min="1" value="1" style="width:60px;padding:4px 8px;border-radius:6px;border:1px solid #ccc;">
            <button id="modalAjouterPanier" style="background:#7f5af0;color:#fff;border:none;cursor:pointer;padding:6px 16px;border-radius:8px;font-weight:600;transition:background 0.2s;">Ajouter au panier</button>
            <button id="modalSupprimerProduit" style="background:#ff4d4f;color:#fff;border:none;cursor:pointer;padding:6px 16px;border-radius:8px;font-weight:600;display:none;">Supprimer</button>
          </div>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// JS pour ouvrir la modale avec les infos du produit
document.addEventListener('click', function(e) {
  if (e.target && e.target.classList.contains('btn-action') && e.target.title === 'Voir détails') {
    const id = e.target.getAttribute('data-id');
    const prod = produits.find(p => p.id == id);
    if (!prod) return;
    // Affichage des images en grille
    let images = [];
    if (prod.images) {
      if (typeof prod.images === 'string') {
        try { images = JSON.parse(prod.images); } catch(e) { images = []; }
      } else if (Array.isArray(prod.images)) {
        images = prod.images;
      }
    }
    if (!images || images.length === 0) {
      images = [prod.image ? prod.image : 'https://via.placeholder.com/400x300?text=Image'];
    }
    const galerie = document.getElementById('modalGalerie');
    galerie.innerHTML = images.map(img => `<img src="${img}" alt="Image produit" style="width:140px;height:110px;object-fit:cover;border-radius:12px;box-shadow:0 2px 8px #0001;cursor:pointer;transition:transform 0.2s;" onclick="window.open('${img}','_blank')">`).join('');
    document.getElementById('modalNom').textContent = prod.nom;
    document.getElementById('modalCategorie').textContent = prod.categorie;
    document.getElementById('modalPrix').textContent = prod.prix + ' FCFA';
    document.getElementById('modalStock').textContent = 'Stock: ' + (prod.stock !== undefined ? prod.stock : 'N/A');
    document.getElementById('modalDescription').textContent = prod.description;
    document.getElementById('modalVendeurNom').textContent = prod.vendeur_nom ? prod.vendeur_nom : 'N/A';
    document.getElementById('modalVendeurWhatsapp').href = 'https://wa.me/' + (prod.vendeur_whatsapp ? prod.vendeur_whatsapp.replace(/[^0-9]/g,'') : '');
    document.getElementById('modalVendeurWhatsappNum').textContent = prod.vendeur_whatsapp ? prod.vendeur_whatsapp : '';
    // Système de notation dynamique
    fetch('get_note_produit.php?id='+prod.id)
      .then(r=>r.json())
      .then(data => {
        let note = data.moyenne ? parseFloat(data.moyenne) : 0;
        let votes = data.votes ? parseInt(data.votes) : 0;
        document.getElementById('modalNote').textContent = note.toFixed(1) + ' ('+votes+' vote'+(votes>1?'s':'')+')';
        // Affichage étoiles cliquables
        let starsHtml = '';
        for(let i=1;i<=5;i++) {
          starsHtml += `<span class='star' data-star='${i}' style='font-size:1.5rem;cursor:pointer;color:${i<=Math.round(note)?'#FFD700':'#ccc'}'>★</span>`;
        }
        document.getElementById('modalNoteStars').innerHTML = starsHtml;
        // Ajout du listener pour noter
        document.querySelectorAll('#modalNoteStars .star').forEach(star => {
          star.onclick = function() {
            let val = parseInt(this.getAttribute('data-star'));
            let commentaire = prompt('Commentaire (optionnel) :');
            fetch('noter_produit.php', {
              method: 'POST',
              headers: {'Content-Type':'application/x-www-form-urlencoded'},
              body: 'produit_id='+prod.id+'&note='+val+'&commentaire='+encodeURIComponent(commentaire||'')
            })
            .then(r=>r.json())
            .then(resp => {
              if(resp.success) {
                alert('Merci pour votre note !');
                document.getElementById('modalProduitDetails').style.display = 'none';
              } else {
                alert(resp.message||'Erreur');
              }
            });
          };
        });
      });
    // Quantité
    document.getElementById('modalQuantite').value = 1;
    // Afficher bouton supprimer si propriétaire (exemple: prod.est_proprietaire)
    if (prod.est_proprietaire) {
      document.getElementById('modalSupprimerProduit').style.display = '';
    } else {
      document.getElementById('modalSupprimerProduit').style.display = 'none';
    }
    document.getElementById('modalProduitDetails').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Galerie (si prod.images est un tableau d'urls)
    if (Array.isArray(prod.images) && prod.images.length > 0) {
      let idx = 0;
      document.getElementById('modalImage').src = prod.images[0];
      document.getElementById('modalGalerie').onclick = function(ev) {
        idx = (idx+1)%prod.images.length;
        document.getElementById('modalImage').src = prod.images[idx];
      };
    } else {
      document.getElementById('modalGalerie').onclick = null;
    }
    // Ajouter au panier depuis la modale
    document.getElementById('modalAjouterPanier').onclick = function() {
      let qte = parseInt(document.getElementById('modalQuantite').value)||1;
      ajouterAuPanier(prod.id, qte);
      alert('Produit ajouté au panier !');
    };
    // Suppression produit (à compléter côté backend)
    document.getElementById('modalSupprimerProduit').onclick = function() {
      if (confirm('Supprimer ce produit ?')) {
        supprimerProduit(prod.id);
      }
    };
  }
});
// Fermer la modale
document.addEventListener('click', function(e) {
  if (e.target && e.target.id === 'closeModalProduit') {
    document.getElementById('modalProduitDetails').style.display = 'none';
    document.body.style.overflow = '';
  }
});

// Fonctions JS à ajouter : ajouterAuPanier et supprimerProduit
function ajouterAuPanier(id, quantite) {
  let panier = JSON.parse(localStorage.getItem('panier')||'[]');
  let idx = panier.findIndex(p=>p.id==id);
  if(idx>=0) panier[idx].quantite += quantite;
  else panier.push({id, quantite});
  localStorage.setItem('panier', JSON.stringify(panier));
}
function supprimerProduit(id) {
  // À compléter : requête AJAX pour supprimer côté serveur puis recharger la page
  fetch('supprimer_produit.php?id='+encodeURIComponent(id), {method:'POST'})
    .then(r=>r.json()).then(data=>{
      if(data.success){
        alert('Produit supprimé');
        location.reload();
      }else{
        alert('Erreur suppression');
      }
    });
}
          });
          // Ajout listeners "Ajouter au panier"
          setTimeout(function(){
            document.querySelectorAll('.btn-action[title="Ajouter au panier"]').forEach(function(btn){
              btn.onclick = function(){
                let id = btn.getAttribute('data-id');
                let prod = produits.find(p=>p.id==id);
                if(prod) addToPanier(prod);
                btn.textContent = 'Ajouté !';
                btn.disabled = true;
                setTimeout(()=>{btn.textContent='Ajouter au panier';btn.disabled=false;}, 1200);
              };
            });
          }, 50);
        }
        let nbPages = Math.ceil(total/perPage);
        let pagHtml = '';
        pagHtml += `<button class='btn' style='background:#f3f6fa;color:#7f5af0;' ${page===1?'disabled':''} onclick='window.goPage(${page-1})'>&lt;</button>`;
        for(let i=1;i<=nbPages;i++) pagHtml += `<button class='btn' style='background:${i===page?'#7f5af0':'#f3f6fa'};color:${i===page?'#fff':'#7f5af0'};font-weight:600;' onclick='window.goPage(${i})'>${i}</button>`;
        pagHtml += `<button class='btn' style='background:#f3f6fa;color:#7f5af0;' ${page===nbPages?'disabled':''} onclick='window.goPage(${page+1})'>&gt;</button>`;
        pagination.innerHTML = pagHtml;
      }

      // Pagination globale
      window.goPage = function(p) {
        page = p; if(page<1) page=1;
        renderProduits();
      }

      // Catégories (sidebar)
      document.querySelectorAll('.cat-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          currentCat = btn.textContent.trim();
          page = 1;
          renderProduits();
          document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));
          btn.classList.add('active');
        });
      });

      // Tri
      document.getElementById('triFlottant')?.querySelectorAll('.tri-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          currentTri = btn.dataset.tri;
          page = 1;
          renderProduits();
          document.getElementById('triFlottant').querySelectorAll('.tri-btn').forEach(b=>b.classList.remove('active-tri'));
          btn.classList.add('active-tri');
        });
      });

      // Vue grille/liste (pour extension future)
      if(btnGrid) btnGrid.onclick = function(){ currentView='grid'; renderProduits(); };
      if(btnListe) btnListe.onclick = function(){ currentView='liste'; renderProduits(); };

      // Promo (filtre fictif : prix < 10000)
      if(btnPromo) btnPromo.onclick = function(){ currentCat=null; currentTri='populaire'; page=1; produits = produits.filter(p=>parseInt(p.prix)<10000); renderProduits(); };

      // Filtrer (ouvre/ferme sidebar sur mobile, à implémenter si besoin)
      if(btnFiltrer) btnFiltrer.onclick = function(){ alert('Fonctionnalité filtre avancé à venir !'); };

      // Initialisation
      renderProduits();
    });
    </script>

    <!-- HEADER -->
    <header class="header-modern">
      <div class="topbar">
        <div class="logo" style="display:flex;align-items:center;gap:12px;">
          <div class="logo-carre" style="width:38px;height:38px;background:linear-gradient(90deg,#7f5af0 60%,#43e6ff 100%);border-radius:12px;box-shadow:0 2px 8px #7f5af033;"></div>
          <div>
            <h1 style="font-size:2rem;font-weight:700;margin:0;color:#7f5af0;letter-spacing:1px;">Campus Shop</h1>
            <span style="font-size:1rem;color:#43e6ff;">Tout ce dont vous avez besoin</span>
          </div>
        </div>
        <div class="actions">
          <?php if (isset($_SESSION['utilisateur'])): ?>
            <button class="btn ajouter-produit-btn" id="btnAjouterProduit" title="Ajouter un produit">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="margin-right:8px;vertical-align:middle;"><rect x="10" y="4" width="4" height="16" fill="#fff"/><rect x="4" y="10" width="16" height="4" fill="#fff"/></svg>
              <span>Ajouter produit</span>
            </button>
          <?php endif; ?>
          <div class="search">
            <form method="get" action="index.php" class="search-form" style="display:flex;gap:6px;">
                <input type="text" name="q" placeholder="Rechercher..." id="rechercheInput" value="<?= htmlspecialchars($q) ?>" required>
                <button type="submit" id="btnRecherche" class="btn">Recherche</button>
            </form>
          </div>
          <?php if (isset($_SESSION['utilisateur'])): ?>
            <button class="btn ajouter-produit-btn" id="btnProfil" onclick="window.location.href='profil.php'" title="Profil">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="margin-right:8px;vertical-align:middle;"><circle cx="12" cy="8" r="4" fill="#fff"/><ellipse cx="12" cy="17" rx="7" ry="5" fill="#fff"/></svg>
              <span>Profil</span>
            </button>
            <button class="btn ajouter-produit-btn" id="panierBtn" title="Panier">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="margin-right:8px;vertical-align:middle;"><path d="M6 6h15l-1.5 9h-13z" fill="#fff"/><circle cx="9" cy="20" r="1.5" fill="#fff"/><circle cx="18" cy="20" r="1.5" fill="#fff"/></svg>
              <span>Panier</span>
            </button>
          <?php else: ?>
            <button class="btn" id="btnInscription" onclick="window.location.href='inscription.php'">S'inscrire</button>
            <button class="btn" id="btnConnexion" onclick="window.location.href='connexion.php'">Se connecter</button>
          <?php endif; ?>
        </div>
      </div>
    </header>
    <!-- Scripts déplacés dans assets/js/app.js -->
  
    <div class="modal-panier" id="modalPanier">
      <div class="modal-overlay"></div>
      <div class="modal-content box-decor panier-center">
        <button class="close-modal" id="closePanier" aria-label="Fermer">✖️</button>
        <h2>Mon Panier 🛒</h2>
        <div id="panierContent">Aucun article pour le moment.</div>
      </div>
    </div>


          

    <!-- HERO -->
    <section class="hero" style="background:url('https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;height:340px;display:flex;justify-content:center;align-items:center;position:relative;margin-bottom:30px;">
      <div class="hero-content" style="position:relative;color:#fff;text-align:center;z-index:2;">
        <h2 style="font-size:3.2rem;margin:0;animation:fadeInDown 1s ease forwards;">Boutique Campus Shop</h2>
        <p style="font-size:1.3rem;margin-top:10px;animation:fadeInUp 1s ease forwards;">Tout ce dont vous avez besoin pour réussir</p>
      </div>
      <div style="position:absolute;inset:0;background:rgba(127,90,240,0.18);"></div>
    </section>

    <!-- MAIN -->
    <main style="width:100vw;max-width:100vw;padding:0;margin:0;">

      <!-- CONTENU PRODUITS -->
      <section class="produits-nouvelle-structure">
  <div class="produits-header" style="display:flex;justify-content:space-between;align-items:center;gap:24px;margin-bottom:32px;">
          <div>
            <h2 style="font-size:2.1rem;font-weight:700;margin:0;">Découvre les articles tendance</h2>
            <p style="color:#7f5af0;font-size:1.1rem;margin:6px 0 0 0;">Des nouveautés chaque semaine, choisis ton style !</p>
          </div>
          <div class="actions-produits" style="display:flex;gap:12px;align-items:center;">
            <button class="btn btn-action" id="btnFiltrer">Filtrer <svg width="18" height="18" style="vertical-align:middle;margin-left:4px;" fill="none" viewBox="0 0 24 24"><path d="M4 6h16M7 12h10M10 18h4" stroke="#7f5af0" stroke-width="2" stroke-linecap="round"/></svg></button>
            <button class="btn btn-action" id="btnGrid">Grille</button>
            <button class="btn btn-action" id="btnListe">Liste</button>
            <button class="btn btn-action" id="btnPromo">Promos</button>
          </div>
        </div>

        <div class="produits-content" style="display:flex;gap:32px;align-items:flex-start;">
          <!-- Filtres latéraux -->
          <aside class="filtres-lateraux" style="min-width:220px;background:#f8f9fb;border-radius:16px;padding:22px 18px;box-shadow:0 2px 12px #bfc9e611;">
            <h4 style="margin-bottom:14px;color:#4a90e2;font-size:1.1rem;">Catégories</h4>
            <ul class="categories-list" style="list-style:none;padding:0;margin:0;">
              <?php foreach ($categories as $cat): ?>
                <li><button class="cat-btn" style="display:block;width:100%;background:#fff;color:#7f5af0;font-weight:600;padding:8px 0;margin-bottom:6px;border:none;border-radius:8px;transition:background 0.18s, color 0.18s;cursor:pointer;box-shadow:0 1px 4px #bfc9e611;"
                  onmouseover="this.style.background='#7f5af0';this.style.color='#fff';"
                  onmouseout="this.style.background='#fff';this.style.color='#7f5af0';"
                ><?= htmlspecialchars($cat['nom'], ENT_QUOTES) ?></button></li>
              <?php endforeach; ?>
            </ul>
            <hr style="margin:18px 0;opacity:0.2;">
           
          </aside>

          <!-- Grille produits nouvelle génération -->
          <div class="grille-produits-nouvelle" id="grilleProduitsNouvelle" style="flex:1;display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:36px 28px;align-items:stretch;">
            <?php if (count($produits) === 0): ?>
              <div class="produit-empty">
                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="Aucun produit" style="width:90px;opacity:0.4;margin-bottom:18px;">
                <?php if ($q !== ''): ?>
                  <p class="placeholder">Aucun résultat trouvé pour "<?= htmlspecialchars($q) ?>".</p>
                <?php else: ?>
                  <p class="placeholder">Aucun produit pour l'instant.</p>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <?php foreach ($produits as $prod): ?>
                <div class="card produit-card-nouvelle" style="background:#fff;border-radius:22px;box-shadow:0 6px 28px #bfc9e633;padding:0;display:flex;flex-direction:column;overflow:hidden;position:relative;transition:box-shadow 0.2s;">
                  <div class="img-zone" style="width:100%;aspect-ratio:4/3;overflow:hidden;position:relative;background:#f3f6fa;">
                    <img src="<?= htmlspecialchars($prod['image'] ?: 'https://via.placeholder.com/400x300?text=Image', ENT_QUOTES) ?>" alt="<?= htmlspecialchars($prod['nom'], ENT_QUOTES) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s;cursor:pointer;" onclick="window.open(this.src,'_blank')" onerror="this.src='https://via.placeholder.com/400x300?text=Image';">
                    <span class="badge-nouveau" style="position:absolute;top:14px;left:14px;background:#43e6ff;color:#fff;font-size:0.98rem;padding:3px 12px;border-radius:8px;font-weight:600;box-shadow:0 2px 8px #43e6ff22;">Nouveau</span>
                    <button class="btn-action-fav" title="Favori" style="position:absolute;top:14px;right:14px;background:#fff;border:none;border-radius:50%;width:38px;height:38px;box-shadow:0 2px 8px #bfc9e611;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s;"><svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M12 21s-7-4.35-7-10a5 5 0 019-3.32A5 5 0 0119 11c0 5.65-7 10-7 10z" stroke="#7f5af0" stroke-width="2"/></svg></button>
                  </div>
                  <div class="infos-produit" style="padding:22px 18px 16px 18px;display:flex;flex-direction:column;gap:8px;flex:1;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                      <h4 style="font-size:1.25rem;margin:0;word-break:break-word;flex:1;"> <?= htmlspecialchars($prod['nom'], ENT_QUOTES) ?> </h4>
                      <span class="cat" style="font-size:0.98rem;color:#7f5af0;font-weight:600;background:#f3f6fa;padding:2px 10px;border-radius:8px;"> <?= htmlspecialchars($prod['categorie'], ENT_QUOTES) ?> </span>
                    </div>
                    <p style="font-size:1.05rem;margin:0 0 4px 0;text-align:left;min-height:38px;max-height:60px;overflow:hidden;color:#444;"> <?= htmlspecialchars($prod['description'], ENT_QUOTES) ?> </p>
                    <div style="display:flex;align-items:center;gap:12px;margin-top:8px;">
                      <span style="font-size:1.18rem;font-weight:700;color:#4a90e2;"> <?= htmlspecialchars($prod['prix'], ENT_QUOTES) ?> FCFA</span>
                      <span style="font-size:0.98rem;color:#43e6ff;font-weight:600;">Stock: <?= isset($prod['stock']) ? (int)$prod['stock'] : 'N/A' ?></span>
                    </div>
                    <div class="actions-ligne" style="display:flex;gap:10px;justify-content:flex-end;margin-top:auto;">
                      <button class="btn-action" title="Partager" style="background:#f3f6fa;border:none;cursor:pointer;padding:6px 12px;border-radius:8px;transition:background 0.2s;"><svg width="20" height="20" fill="none" viewBox="0 0 24 24"><circle cx="18" cy="5" r="3" stroke="#4a90e2" stroke-width="2"/><circle cx="6" cy="12" r="3" stroke="#4a90e2" stroke-width="2"/><circle cx="18" cy="19" r="3" stroke="#4a90e2" stroke-width="2"/><path d="M8.59 13.51l6.83 3.98M15.41 6.51l-6.82 3.98" stroke="#4a90e2" stroke-width="2"/></svg> Partager</button>
                      <button class="btn-action" title="Ajouter au panier" style="background:#7f5af0;color:#fff;border:none;cursor:pointer;padding:6px 16px;border-radius:8px;font-weight:600;transition:background 0.2s;">Ajouter au panier</button>
                      <button class="btn-action" title="Voir détails" style="background:#43e6ff;color:#fff;border:none;cursor:pointer;padding:6px 16px;border-radius:8px;font-weight:600;transition:background 0.2s;">Détails</button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Pagination modernisée -->
        <div class="pagination-modern" id="paginationModern" style="display:flex;justify-content:center;align-items:center;gap:8px;margin:38px 0 0 0;">
          <button class="btn btn-page" id="prevPageBtn">&lt;</button>
          <span class="pagination-dots" style="font-size:1.1rem;font-weight:600;">1</span>
          <button class="btn btn-page" id="nextPageBtn">&gt;</button>
        </div>

        <!-- Bannière promo -->
        <div class="banniere-promo" style="margin:48px 0 0 0;padding:32px 24px;background:linear-gradient(90deg,#43e6ff 60%,#7f5af0 100%);border-radius:18px;box-shadow:0 4px 24px #43e6ff22;display:flex;align-items:center;justify-content:space-between;gap:32px;">
          <div>
            <h3 style="color:#fff;font-size:1.7rem;margin:0 0 8px 0;">Offre spéciale rentrée !</h3>
            <p style="color:#fff;font-size:1.1rem;margin:0;">Profite de -20% sur ta première commande avec le code <b>RENTREE20</b></p>
          </div>
          <button class="btn" style="background:#fff;color:#43e6ff;font-weight:700;font-size:1.1rem;">J'en profite</button>
        </div>
      </section>
    </main>


    <!-- Section Recommandations -->
    <section class="recommendations">
      <h2>Nos Recommandations</h2>
      <div class="recommendations-slider" id="recommendationsSlider">
        <?php 
        $recoProduits = array_slice($produits, 0, 10); // 10 produits recommandés
        foreach ($recoProduits as $prod): ?>
          <div class="reco-slide" style="display:inline-block;width:260px;margin:0 4px;vertical-align:top;background:#fff;border-radius:16px;box-shadow:0 2px 12px #bfc9e622;overflow:hidden;transition:transform 0.7s cubic-bezier(.4,1.6,.6,1), box-shadow 0.3s;">
            <div style="width:100%;aspect-ratio:4/3;overflow:hidden;background:#f3f6fa;">
              <img src="<?= htmlspecialchars($prod['image'] ?: 'https://via.placeholder.com/400x300?text=Image', ENT_QUOTES) ?>" alt="<?= htmlspecialchars($prod['nom'], ENT_QUOTES) ?>" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div style="padding:14px 12px 10px 12px;display:flex;flex-direction:column;gap:6px;align-items:flex-start;">
              <div style="font-weight:700;font-size:1.08rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"> <?= htmlspecialchars($prod['nom'], ENT_QUOTES) ?> </div>
              <div style="color:#7f5af0;font-size:0.98rem;"> <?= htmlspecialchars($prod['categorie'], ENT_QUOTES) ?> </div>
              <div style="color:#4a90e2;font-weight:600;"> <?= htmlspecialchars($prod['prix'], ENT_QUOTES) ?> FCFA</div>
              <button class="btn ajouter-produit-btn carousel-ajout-btn" style="margin-top:6px;font-size:0.98rem;padding:6px 14px;background:linear-gradient(90deg,#7f5af0 60%,#43e6ff 100%);color:#fff;border:none;" onclick="ajouterAuPanierDepuisCarousel('<?= htmlspecialchars($prod['nom'], ENT_QUOTES) ?>','<?= htmlspecialchars($prod['prix'], ENT_QUOTES) ?>','<?= htmlspecialchars($prod['image'] ?: 'https://via.placeholder.com/400x300?text=Image', ENT_QUOTES) ?>')">Ajouter au panier</button>
<style>
.reco-slide {
  opacity: 1;
  transform: scale(1);
}
.reco-slide.active {
  box-shadow: 0 8px 24px #7f5af044;
  transform: scale(1.07) translateY(-8px);
  z-index: 2;
}
.carousel-ajout-btn {
  background: linear-gradient(90deg, #7f5af0 60%, #43e6ff 100%) !important;
  color: #fff !important;
  border: none !important;
  border-radius: 8px !important;
  font-weight: 600;
  transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.carousel-ajout-btn:hover {
  background: linear-gradient(90deg, #43e6ff 0%, #7f5af0 100%) !important;
  color: #fff !important;
  box-shadow: 0 2px 12px #43e6ff44;
}
</style>
            </div>
<script>
function ajouterAuPanierDepuisCarousel(nom, prix, image) {
  // Récupérer le panier depuis le localStorage ou initialiser
  let panier = JSON.parse(localStorage.getItem('panier')) || [];
  let exist = panier.find(p => p.nom === nom);
  if (exist) {
    exist.quantite = (exist.quantite || 1) + 1;
  } else {
    panier.push({ nom, prix, image, quantite: 1 });
  }
  localStorage.setItem('panier', JSON.stringify(panier));
  alert('Produit ajouté au panier !');
}
</script>
          </div>
        <?php endforeach; ?>
      </div>
      <button class="slider-nav prev" id="prevSlideBtn">&lt;</button>
      <button class="slider-nav next" id="nextSlideBtn">&gt;</button>
<style>
.recommendations-slider {
  white-space: nowrap;
  overflow: hidden;
  width: 100%;
  position: relative;
  min-height: 320px;
}
.reco-slide {
  transition: transform 0.5s;
}
.slider-nav {
  background: #fff;
  border: 1.5px solid #7f5af0;
  color: #7f5af0;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  font-size: 1.5rem;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 2;
  cursor: pointer;
  box-shadow: 0 2px 8px #7f5af033;
}
#prevSlideBtn { left: 8px; }
#nextSlideBtn { right: 8px; }
.recommendations { position: relative; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const slider = document.getElementById('recommendationsSlider');
  const slides = slider.querySelectorAll('.reco-slide');
  let index = 0;
  const visible = 3;
  function updateSlider() {
    slides.forEach((slide, i) => {
      slide.classList.remove('active');
      slide.style.transform = `translateX(${(i-index)*280}px)`;
      if (i >= index && i < index+visible) {
        slide.style.opacity = '1';
        slide.style.pointerEvents = 'auto';
        if (i === index+1) slide.classList.add('active');
      } else {
        slide.style.opacity = '1';
        slide.style.pointerEvents = 'none';
      }
    });
  }
  function next() {
    index = (index+1 > slides.length-visible) ? 0 : index+1;
    updateSlider();
  }
  function prev() {
    index = (index-1 < 0) ? slides.length-visible : index-1;
    updateSlider();
  }
  document.getElementById('nextSlideBtn').onclick = next;
  document.getElementById('prevSlideBtn').onclick = prev;
  updateSlider();
  setInterval(next, 3500);
});
</script>
    </section>

    <!-- FOOTER  -->
    <footer class="footer-modern animated-footer" style="width:100vw;min-width:100vw;left:50%;transform:translateX(-50%);position:relative;overflow:hidden;padding:0;margin:0;background:linear-gradient(135deg,#7f5af0 0%,#43e6ff 100%);">
      <div class="footer-content" style="position:relative;z-index:2;display:flex;justify-content:center;align-items:flex-start;gap:60px;padding:40px 0 0 0;width:100%;max-width:1440px;margin:0 auto;">
        <div class="footer-col">
          <h4 style="color:#43e6ff;letter-spacing:1px;font-weight:700;">À propos</h4>
          <ul>
            <li><a href="#" id="showSiteModalLink">Le site</a></li>
            <li><a href="#" id="showTeamModalLink">L'équipe</a></li>
            <li><a href="#" id="showObjectiveModalLink">Objectifs</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4 style="color:#43e6ff;letter-spacing:1px;font-weight:700;">Support</h4>
          <ul>
            <li><a href="#" id="showReportModalLink">Signaler un problème</a></li>
          </ul>
        </div>
        <div class="footer-col" style="display:flex;flex-direction:column;justify-content:flex-start;height:100%;min-height:120px;">
          <h4 style="color:#43e6ff;letter-spacing:1px;font-weight:700;">Contact</h4>
          <div class="social-links" style="align-self:flex-start;margin-top:12px;">
            <a href="https://wa.me/237672108067" target="_blank" class="social-icon whatsapp" title="Discuter sur WhatsApp">
              <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/whatsapp.svg" alt="WhatsApp" style="width:28px;height:28px;filter:brightness(1.2);">
            </a>
            <a href="https://t.me/+237654005403" target="_blank" class="social-icon telegram" title="Rejoindre sur Telegram">
              <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/telegram.svg" alt="Telegram" style="width:28px;height:28px;filter:brightness(1.2);">
            </a>
          </div>
        </div>
      </div>
  <div class="footer-bottom" style="position:relative;z-index:2;display:flex;justify-content:center;align-items:center;height:48px;background:transparent;width:100%;max-width:1440px;margin:0 auto;">
        <span style="color:#fff;font-size:1.1rem;font-weight:600;text-shadow:0 2px 8px #7f5af088;">© <?= date('Y') ?> Campus Shop</span>
      </div>
  <div class="footer-bubbles" style="position:absolute;bottom:0;left:0;width:100vw;height:220px;pointer-events:none;z-index:1;overflow:visible;">
    <style>
      .footer-bubbles .bubble {
        position: absolute;
        bottom: -60px;
        border-radius: 50%;
        opacity: 0.22;
        background: linear-gradient(135deg,#fff,#43e6ff 60%,#7f5af0 100%);
        animation: bubbleUp 10s linear infinite;
      }
      /* Variantes de bulles : tailles, opacités, delays, directions */
      .footer-bubbles .bubble1 { left: 8vw; width: 22px; height: 22px; animation-delay: 0s; animation-duration: 10s; opacity: 0.18; }
      .footer-bubbles .bubble2 { left: 18vw; width: 14px; height: 14px; animation-delay: 1.2s; animation-duration: 8.2s; opacity: 0.25; }
      .footer-bubbles .bubble3 { left: 29vw; width: 32px; height: 32px; animation-delay: 0.7s; animation-duration: 12.5s; opacity: 0.13; }
      .footer-bubbles .bubble4 { left: 41vw; width: 18px; height: 18px; animation-delay: 2.1s; animation-duration: 9.8s; opacity: 0.22; }
      .footer-bubbles .bubble5 { left: 53vw; width: 26px; height: 26px; animation-delay: 0.4s; animation-duration: 11.7s; opacity: 0.19; }
      .footer-bubbles .bubble6 { left: 65vw; width: 16px; height: 16px; animation-delay: 1.7s; animation-duration: 8.5s; opacity: 0.28; }
      .footer-bubbles .bubble7 { left: 77vw; width: 36px; height: 36px; animation-delay: 2.8s; animation-duration: 13.2s; opacity: 0.11; }
      .footer-bubbles .bubble8 { left: 89vw; width: 12px; height: 12px; animation-delay: 0.9s; animation-duration: 7.8s; opacity: 0.32; }
      .footer-bubbles .bubble9 { left: 95vw; width: 20px; height: 20px; animation-delay: 1.5s; animation-duration: 10.9s; opacity: 0.21; }
      .footer-bubbles .bubble10 { left: 15vw; width: 28px; height: 28px; animation-delay: 3.1s; animation-duration: 12.2s; opacity: 0.16; }
      .footer-bubbles .bubble11 { left: 38vw; width: 10px; height: 10px; animation-delay: 2.4s; animation-duration: 7.2s; opacity: 0.29; }
      .footer-bubbles .bubble12 { left: 58vw; width: 24px; height: 24px; animation-delay: 4.2s; animation-duration: 11.1s; opacity: 0.17; }
      .footer-bubbles .bubble13 { left: 82vw; width: 18px; height: 18px; animation-delay: 3.7s; animation-duration: 9.5s; opacity: 0.23; }
      @keyframes bubbleUp {
        0% { transform: translateY(0) scale(1) translateX(0); opacity: 0.22; }
        20% { transform: translateY(-60px) scale(1.1) translateX(10px); }
        40% { transform: translateY(-120px) scale(1.25) translateX(-10px); opacity: 0.32; }
        60% { transform: translateY(-200px) scale(1.4) translateX(20px); }
        80% { transform: translateY(-300px) scale(1.6) translateX(-20px); }
        100% { transform: translateY(-400px) scale(1.8) translateX(0); opacity: 0; }
      }
    </style>
    <div class="bubble bubble1"></div>
    <div class="bubble bubble2"></div>
    <div class="bubble bubble3"></div>
    <div class="bubble bubble4"></div>
    <div class="bubble bubble5"></div>
    <div class="bubble bubble6"></div>
    <div class="bubble bubble7"></div>
    <div class="bubble bubble8"></div>
    <div class="bubble bubble9"></div>
    <div class="bubble bubble10"></div>
    <div class="bubble bubble11"></div>
    <div class="bubble bubble12"></div>
    <div class="bubble bubble13"></div>
  </div>
    </footer>
    <!-- Modale Signalement de problème -->
    <div id="reportModal" class="report-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(34,40,49,0.85);z-index:3000;align-items:center;justify-content:center;animation:fadeInModal 0.5s;">
      <div class="report-modal-content" style="background:linear-gradient(135deg,#ff4d4f 0%,#43e6ff 100%);border-radius:32px;box-shadow:0 8px 48px #ff4d4f88;padding:40px 32px 32px 32px;max-width:400px;width:95%;text-align:left;position:relative;animation:popInModal 0.5s;">
        <button id="closeReportModal" style="position:absolute;top:18px;right:18px;background:none;border:none;font-size:2rem;color:#fff;cursor:pointer;transition:color 0.2s;">&times;</button>
        <h2 style="color:#fff;font-size:2rem;font-weight:800;letter-spacing:1px;margin-bottom:18px;text-shadow:0 2px 12px #ff4d4f88;">Signaler un problème</h2>
        <form id="reportForm" autocomplete="off">
          <label for="reportMessage" style="color:#fff;font-size:1.1rem;font-weight:600;margin-bottom:8px;display:block;">Décris le problème rencontré :</label>
          <textarea id="reportMessage" name="message" rows="5" required style="width:100%;padding:14px;border-radius:12px;border:1.5px solid #ddd;font-size:1rem;margin-bottom:18px;"></textarea>
          <button type="submit" style="background:#43e6ff;color:#fff;padding:12px 28px;border:none;border-radius:18px;font-size:1.1rem;font-weight:700;box-shadow:0 2px 12px #43e6ff44;cursor:pointer;transition:background 0.2s;">Envoyer</button>
        </form>
        <div id="reportSuccess" style="display:none;color:#fff;font-size:1.1rem;font-weight:700;margin-top:18px;text-align:center;"></div>
      </div>
    </div>
    <!-- Modale Objectif Campus Shop -->
    <div id="objectiveModal" class="objective-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(34,40,49,0.85);z-index:3000;align-items:center;justify-content:center;animation:fadeInModal 0.5s;">
      <div class="objective-modal-content" style="background:linear-gradient(135deg,#43e6ff 0%,#7f5af0 100%);border-radius:32px;box-shadow:0 8px 48px #43e6ff88;padding:40px 32px 32px 32px;max-width:480px;width:95%;text-align:left;position:relative;animation:popInModal 0.5s;">
        <button id="closeObjectiveModal" style="position:absolute;top:18px;right:18px;background:none;border:none;font-size:2rem;color:#fff;cursor:pointer;transition:color 0.2s;">&times;</button>
        <h2 style="color:#fff;font-size:2rem;font-weight:800;letter-spacing:1px;margin-bottom:18px;text-shadow:0 2px 12px #43e6ff88;">Objectif de Campus Shop</h2>
        <p style="color:#e0f7fa;font-size:1.15rem;margin-bottom:24px;font-weight:500;">Campus Shop est une plateforme innovante dédiée aux étudiants, conçue pour faciliter l’achat, la vente et l’échange de produits et services au sein de la communauté universitaire.</p>
        <ul style="list-style:none;padding:0;margin:0 0 18px 0;">
          <li style="color:#fff;font-size:1.08rem;margin-bottom:14px;">🔹 <b>Favoriser l’entraide</b> : permettre aux étudiants de s’entraider en proposant des articles utiles à prix abordable.</li>
          <li style="color:#fff;font-size:1.08rem;margin-bottom:14px;">🔹 <b>Soutenir l’entrepreneuriat</b> : offrir un espace sécurisé pour vendre ses créations, services ou produits.</li>
          <li style="color:#fff;font-size:1.08rem;margin-bottom:14px;">🔹 <b>Faciliter la vie étudiante</b> : simplifier la recherche d’objets, de fournitures, ou de bons plans sur le campus.</li>
          <li style="color:#fff;font-size:1.08rem;margin-bottom:14px;">🔹 <b>Créer du lien</b> : renforcer la communauté et les échanges entre étudiants grâce à une plateforme moderne et intuitive.</li>
        </ul>
        <p style="color:#43e6ff;font-size:1.08rem;font-weight:600;">Notre ambition : faire de Campus Shop le réflexe n°1 pour tous les besoins étudiants !</p>
      </div>
    </div>
    <!-- Modale Fonctionnement du site -->
    <div id="siteModal" class="site-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(34,40,49,0.85);z-index:3000;align-items:center;justify-content:center;animation:fadeInModal 0.5s;">
      <div class="site-modal-content" style="background:linear-gradient(135deg,#7f5af0 0%,#43e6ff 100%);border-radius:32px;box-shadow:0 8px 48px #7f5af088;padding:40px 32px 32px 32px;max-width:480px;width:95%;text-align:left;position:relative;animation:popInModal 0.5s;">
        <button id="closeSiteModal" style="position:absolute;top:18px;right:18px;background:none;border:none;font-size:2rem;color:#fff;cursor:pointer;transition:color 0.2s;">&times;</button>
        <h2 style="color:#fff;font-size:2rem;font-weight:800;letter-spacing:1px;margin-bottom:18px;text-shadow:0 2px 12px #43e6ff88;">Fonctionnement du site</h2>
        <p style="color:#e0f7fa;font-size:1.1rem;margin-bottom:24px;font-weight:500;">Voici à quoi servent les principaux boutons :</p>
        <ul style="list-style:none;padding:0;margin:0 0 18px 0;">
          <li style="margin-bottom:18px;"><span style="background:#43e6ff;color:#fff;padding:6px 14px;border-radius:18px;font-weight:700;box-shadow:0 2px 8px #43e6ff44;">Ajouter produit</span> <br><span style="color:#fff;">Permet de mettre en vente un nouvel article sur le site.</span></li>
          <li style="margin-bottom:18px;"><span style="background:#7f5af0;color:#fff;padding:6px 14px;border-radius:18px;font-weight:700;box-shadow:0 2px 8px #7f5af044;">Profil</span> <br><span style="color:#fff;">Accède à ton espace personnel : infos, produits, paramètres.</span></li>
          <li style="margin-bottom:18px;"><span style="background:#43e6ff;color:#fff;padding:6px 14px;border-radius:18px;font-weight:700;box-shadow:0 2px 8px #43e6ff44;">Panier</span> <br><span style="color:#fff;">Consulte et valide tes achats en cours.</span></li>
          <li style="margin-bottom:18px;"><span style="background:#7f5af0;color:#fff;padding:6px 14px;border-radius:18px;font-weight:700;box-shadow:0 2px 8px #7f5af044;">Recherche</span> <br><span style="color:#fff;">Trouve rapidement un produit ou une catégorie.</span></li>
          <li style="margin-bottom:18px;"><span style="background:#43e6ff;color:#fff;padding:6px 14px;border-radius:18px;font-weight:700;box-shadow:0 2px 8px #43e6ff44;">Filtrer / Grille / Liste / Promos</span> <br><span style="color:#fff;">Affiche les produits selon tes préférences ou profite des promotions.</span></li>
          <li style="margin-bottom:18px;"><span style="background:#7f5af0;color:#fff;padding:6px 14px;border-radius:18px;font-weight:700;box-shadow:0 2px 8px #7f5af044;">Campus Bot</span> <br><span style="color:#fff;">Pose toutes tes questions, le bot étudiant te répond instantanément !</span></li>
        </ul>
        <p style="color:#43e6ff;font-size:1rem;font-weight:600;">Découvre, vends, achète et profite de l’expérience Campus Shop !</p>
      </div>
    </div>
    <!-- Modale Équipe -->
    <div id="teamModal" class="team-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(34,40,49,0.85);z-index:3000;align-items:center;justify-content:center;animation:fadeInModal 0.5s;">
      <div class="team-modal-content" style="background:linear-gradient(135deg,#43e6ff 0%,#7f5af0 100%);border-radius:32px;box-shadow:0 8px 48px #43e6ff88;padding:40px 32px 32px 32px;max-width:400px;width:90%;text-align:center;position:relative;animation:popInModal 0.5s;">
        <button id="closeTeamModal" style="position:absolute;top:18px;right:18px;background:none;border:none;font-size:2rem;color:#fff;cursor:pointer;transition:color 0.2s;">&times;</button>
        <h2 style="color:#fff;font-size:2rem;font-weight:800;letter-spacing:1px;margin-bottom:18px;text-shadow:0 2px 12px #43e6ff88;">L'équipe du site</h2>
        <p style="color:#e0f7fa;font-size:1.1rem;margin-bottom:24px;font-weight:500;">Ce site a été imaginé, conçu et réalisé par ces personnes passionnées :</p>
        <ul style="list-style:none;padding:0;margin:0 0 18px 0;">
          <li style="color:#fff;font-size:1.15rem;font-weight:700;margin-bottom:10px;letter-spacing:0.5px;">Feugue Rachel Faith</li>
          <li style="color:#fff;font-size:1.15rem;font-weight:700;margin-bottom:10px;letter-spacing:0.5px;">Mengata Mvondo Audrey Calvin</li>
          <li style="color:#fff;font-size:1.15rem;font-weight:700;margin-bottom:10px;letter-spacing:0.5px;">Elsa</li>
          <li style="color:#fff;font-size:1.15rem;font-weight:700;margin-bottom:10px;letter-spacing:0.5px;">Medjiadeu Djomatchoua Stive</li>
        </ul>
        <p style="color:#43e6ff;font-size:1rem;font-weight:600;">Merci à eux pour leur créativité et leur engagement !</p>
      </div>
    </div>

 <!-- Campus Bot -->
  <div id="campusBotLauncher" style="position:fixed;bottom:32px;right:32px;z-index:9999;cursor:pointer;display:flex;align-items:center;gap:8px;background:linear-gradient(90deg,#7f5af0 60%,#43e6ff 100%);color:#fff;padding:12px 22px;border-radius:32px;box-shadow:0 4px 24px #7f5af044;transition:box-shadow 0.2s;">
    <img src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png" alt="Campus Bot" style="width:32px;height:32px;filter:drop-shadow(0 2px 8px #43e6ff88);">
    <span style="font-weight:700;font-size:1.08rem;letter-spacing:1px;">Campus Bot</span>
  </div>
  <div id="campusBotWindow" style="display:none;position:fixed;bottom:90px;right:32px;z-index:9999;width:340px;max-width:96vw;background:#fff;border-radius:18px;box-shadow:0 8px 32px #7f5af044;overflow:hidden;animation:botIn 0.4s cubic-bezier(.7,-0.4,.3,1.4);">
    <div id="campusBotHeader" style="background:linear-gradient(90deg,#43e6ff 0%,#7f5af0 100%);color:#fff;padding:14px 18px;display:flex;align-items:center;gap:10px;cursor:move;">
      <img src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png" alt="Campus Bot" style="width:28px;height:28px;">
      <span style="font-weight:700;font-size:1.05rem;">Campus Bot – Assistant étudiant</span>
      <button id="closeCampusBot" style="margin-left:auto;background:none;border:none;color:#fff;font-size:22px;cursor:pointer;">&times;</button>
    </div>
    <div id="campusBotBody" style="padding:18px 14px;max-height:260px;overflow-y:auto;background:#f8f8ff;">
      <div class="message" style="margin-bottom:12px;background:#eaf6ff;padding:8px 14px;border-radius:12px;color:#222;font-weight:500;">Bonjour 👋, je suis Campus Bot ! Pose-moi toutes tes questions sur la vente, l’achat, le fonctionnement du site ou la vie étudiante.</div>
    </div>
    <div id="campusBotInput" style="display:flex;gap:8px;padding:12px 14px;background:#fff;border-top:1px solid #eee;">
      <input type="text" id="campusBotUserInput" placeholder="Écris ta question..." style="flex:1;padding:8px 12px;border-radius:8px;border:1px solid #ddd;font-size:1rem;">
      <button id="campusBotSendBtn" style="background:linear-gradient(90deg,#43e6ff 0%,#7f5af0 100%);color:#fff;border:none;border-radius:8px;padding:8px 16px;font-weight:700;cursor:pointer;transition:background 0.2s;">Envoyer</button>
    </div>
  </div>
  <style>
    @keyframes botIn { 0%{transform:scale(0.7) translateY(60px);opacity:0;} 100%{transform:scale(1) translateY(0);opacity:1;} }
    #campusBotWindow::-webkit-scrollbar { width: 6px; background: #eee; }
    #campusBotWindow::-webkit-scrollbar-thumb { background: #43e6ff; border-radius: 6px; }
    #campusBotBody::-webkit-scrollbar { width: 6px; background: #eee; }
    #campusBotBody::-webkit-scrollbar-thumb { background: #7f5af0; border-radius: 6px; }
    #campusBotLauncher:hover { box-shadow:0 8px 32px #43e6ff55, 0 2px 8px #7f5af055; }
    #campusBotWindow { transition:box-shadow 0.2s; }
    #campusBotWindow.active { box-shadow:0 16px 48px #43e6ff55, 0 2px 8px #7f5af055; }
    #campusBotHeader button:hover { background:#fff2; }
    #campusBotInput button:hover { background:linear-gradient(90deg,#7f5af0 0%,#43e6ff 100%); }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Animation ouverture/fermeture
      const botLauncher = document.getElementById('campusBotLauncher');
      const botWindow = document.getElementById('campusBotWindow');
      const botClose = document.getElementById('closeCampusBot');
      botLauncher.onclick = () => { botWindow.style.display = 'block'; botWindow.classList.add('active'); };
      botClose.onclick = () => { botWindow.style.display = 'none'; botWindow.classList.remove('active'); };
      // Drag & drop du bot
      let isDragging = false, dragOffsetX = 0, dragOffsetY = 0;
      const header = document.getElementById('campusBotHeader');
      header.addEventListener('mousedown', function(e) {
        isDragging = true;
        dragOffsetX = e.clientX - botWindow.getBoundingClientRect().left;
        dragOffsetY = e.clientY - botWindow.getBoundingClientRect().top;
        document.body.style.userSelect = 'none';
      });
      document.addEventListener('mousemove', function(e) {
        if (isDragging) {
          botWindow.style.left = (e.clientX - dragOffsetX) + 'px';
          botWindow.style.top = (e.clientY - dragOffsetY) + 'px';
          botWindow.style.bottom = 'auto';
          botWindow.style.right = 'auto';
        }
      });
      document.addEventListener('mouseup', function() {
        isDragging = false;
        document.body.style.userSelect = '';
      });
      // Animation d’envoi et réponses de base
      const botInput = document.getElementById('campusBotUserInput');
      const botSendBtn = document.getElementById('campusBotSendBtn');
      const botBody = document.getElementById('campusBotBody');
      function botReply(msg) {
        if (!msg) return;
        const userMsg = document.createElement('div');
        userMsg.className = 'message';
        userMsg.style = 'margin-bottom:8px;background:#d1e7ff;padding:8px 14px;border-radius:12px;color:#222;text-align:right;font-weight:500;';
        userMsg.textContent = msg;
        botBody.appendChild(userMsg);
        fetch('', {
          method: 'POST',
          headers: {'Content-Type':'application/x-www-form-urlencoded'},
          body: 'campusbot_question=' + encodeURIComponent(msg)
        })
        .then(r=>r.text())
        .then(answer=>{
          setTimeout(()=>{
            const botMsg = document.createElement('div');
            botMsg.className = 'message';
            botMsg.style = 'margin-bottom:12px;background:#eaf6ff;padding:8px 14px;border-radius:12px;color:#222;font-weight:500;';
            botMsg.textContent = answer;
            botBody.appendChild(botMsg);
            botBody.scrollTop = botBody.scrollHeight;
          }, 500);
        });
        botBody.scrollTop = botBody.scrollHeight;
      }
      botSendBtn.onclick = () => { botReply(botInput.value); botInput.value = ''; };
      botInput.addEventListener('keydown', function(e){ if(e.key==='Enter'){ botReply(botInput.value); botInput.value=''; }});
    });
  </script>


  <script src="assets/js/app.js" defer></script>
<script>
  // Modale signalement de problème
  document.addEventListener('DOMContentLoaded', function() {
    const showReportLink = document.getElementById('showReportModalLink');
    const reportModal = document.getElementById('reportModal');
    const closeReportModal = document.getElementById('closeReportModal');
    const reportForm = document.getElementById('reportForm');
    const reportSuccess = document.getElementById('reportSuccess');
    if (showReportLink && reportModal && closeReportModal) {
      showReportLink.addEventListener('click', function(e) {
        e.preventDefault();
        reportModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      });
      closeReportModal.addEventListener('click', function() {
        reportModal.style.display = 'none';
        document.body.style.overflow = '';
      });
      reportModal.addEventListener('click', function(e) {
        if (e.target === reportModal) {
          reportModal.style.display = 'none';
          document.body.style.overflow = '';
        }
      });
    }
    if (reportForm) {
      reportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = document.getElementById('reportMessage').value.trim();
        if (!message) return;
        fetch('signaler_probleme.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'message=' + encodeURIComponent(message)
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            reportForm.style.display = 'none';
            reportSuccess.innerHTML = '<span style="font-size:1.2rem;color:#43e6ff;font-weight:700;">Merci de nous avoir fait part de ce problème !<br>Votre message a bien été transmis à l’équipe Campus Shop.</span>';
            reportSuccess.style.display = 'block';
          } else {
            reportSuccess.textContent = 'Erreur lors de l’envoi. Réessayez plus tard.';
            reportSuccess.style.display = 'block';
          }
        })
        .catch(() => {
          reportSuccess.textContent = 'Erreur lors de l’envoi. Réessayez plus tard.';
          reportSuccess.style.display = 'block';
        });
      });
    }
  });
</script>
<style>
  /* Animation modale signalement de problème */
  @keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes popInModal {
    0% { transform: scale(0.7); opacity: 0; }
    80% { transform: scale(1.05); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
  }
  .report-modal {
    display:none;
    position:fixed;
    top:0;left:0;width:100vw;height:100vh;
    background:rgba(34,40,49,0.85);
    z-index:3000;
    align-items:center;justify-content:center;
    animation:fadeInModal 0.5s;
  }
  .report-modal[style*="display: flex"] {
    display:flex !important;
  }
  .report-modal-content {
    background:linear-gradient(135deg,#ff4d4f 0%,#43e6ff 100%);
    border-radius:32px;
    box-shadow:0 8px 48px #ff4d4f88;
    padding:40px 32px 32px 32px;
    max-width:400px;width:95%;
    text-align:left;position:relative;
    animation:popInModal 0.5s;
  }
  #closeReportModal:hover {
    color:#43e6ff;
    text-shadow:0 2px 8px #43e6ff88;
  }
</style>
<script>
  // Modale objectif Campus Shop
  document.addEventListener('DOMContentLoaded', function() {
    const showObjectiveLink = document.getElementById('showObjectiveModalLink');
    const objectiveModal = document.getElementById('objectiveModal');
    const closeObjectiveModal = document.getElementById('closeObjectiveModal');
    if (showObjectiveLink && objectiveModal && closeObjectiveModal) {
      showObjectiveLink.addEventListener('click', function(e) {
        e.preventDefault();
        objectiveModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      });
      closeObjectiveModal.addEventListener('click', function() {
        objectiveModal.style.display = 'none';
        document.body.style.overflow = '';
      });
      objectiveModal.addEventListener('click', function(e) {
        if (e.target === objectiveModal) {
          objectiveModal.style.display = 'none';
          document.body.style.overflow = '';
        }
      });
    }
  });
</script>
<style>
  /* Animation modale objectif Campus Shop */
  @keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes popInModal {
    0% { transform: scale(0.7); opacity: 0; }
    80% { transform: scale(1.05); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
  }
  .objective-modal {
    display:none;
    position:fixed;
    top:0;left:0;width:100vw;height:100vh;
    background:rgba(34,40,49,0.85);
    z-index:3000;
    align-items:center;justify-content:center;
    animation:fadeInModal 0.5s;
  }
  .objective-modal[style*="display: flex"] {
    display:flex !important;
  }
  .objective-modal-content {
    background:linear-gradient(135deg,#43e6ff 0%,#7f5af0 100%);
    border-radius:32px;
    box-shadow:0 8px 48px #43e6ff88;
    padding:40px 32px 32px 32px;
    max-width:480px;width:95%;
    text-align:left;position:relative;
    animation:popInModal 0.5s;
  }
  #closeObjectiveModal:hover {
    color:#43e6ff;
    text-shadow:0 2px 8px #43e6ff88;
  }
</style>
<script>
  // Modale fonctionnement du site
  document.addEventListener('DOMContentLoaded', function() {
    const showSiteLink = document.getElementById('showSiteModalLink');
    const siteModal = document.getElementById('siteModal');
    const closeSiteModal = document.getElementById('closeSiteModal');
    if (showSiteLink && siteModal && closeSiteModal) {
      showSiteLink.addEventListener('click', function(e) {
        e.preventDefault();
        siteModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      });
      closeSiteModal.addEventListener('click', function() {
        siteModal.style.display = 'none';
        document.body.style.overflow = '';
      });
      siteModal.addEventListener('click', function(e) {
        if (e.target === siteModal) {
          siteModal.style.display = 'none';
          document.body.style.overflow = '';
        }
      });
    }
  });
</script>
<style>
  /* Animation modale fonctionnement du site */
  @keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes popInModal {
    0% { transform: scale(0.7); opacity: 0; }
    80% { transform: scale(1.05); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
  }
  .site-modal {
    display:none;
    position:fixed;
    top:0;left:0;width:100vw;height:100vh;
    background:rgba(34,40,49,0.85);
    z-index:3000;
    align-items:center;justify-content:center;
    animation:fadeInModal 0.5s;
  }
  .site-modal[style*="display: flex"] {
    display:flex !important;
  }
  .site-modal-content {
    background:linear-gradient(135deg,#7f5af0 0%,#43e6ff 100%);
    border-radius:32px;
    box-shadow:0 8px 48px #7f5af088;
    padding:40px 32px 32px 32px;
    max-width:480px;width:95%;
    text-align:left;position:relative;
    animation:popInModal 0.5s;
  }
  #closeSiteModal:hover {
    color:#43e6ff;
    text-shadow:0 2px 8px #43e6ff88;
  }
</style>
<script>
  // Modale équipe
  document.addEventListener('DOMContentLoaded', function() {
    const showTeamLink = document.getElementById('showTeamModalLink');
    const teamModal = document.getElementById('teamModal');
    const closeTeamModal = document.getElementById('closeTeamModal');
    if (showTeamLink && teamModal && closeTeamModal) {
      showTeamLink.addEventListener('click', function(e) {
        e.preventDefault();
        teamModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      });
      closeTeamModal.addEventListener('click', function() {
        teamModal.style.display = 'none';
        document.body.style.overflow = '';
      });
      teamModal.addEventListener('click', function(e) {
        if (e.target === teamModal) {
          teamModal.style.display = 'none';
          document.body.style.overflow = '';
        }
      });
    }
  });
</script>
<style>
  /* Animation modale équipe */
  @keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes popInModal {
    0% { transform: scale(0.7); opacity: 0; }
    80% { transform: scale(1.05); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
  }
  .team-modal {
    display:none;
    position:fixed;
    top:0;left:0;width:100vw;height:100vh;
    background:rgba(34,40,49,0.85);
    z-index:3000;
    align-items:center;justify-content:center;
    animation:fadeInModal 0.5s;
  }
  .team-modal[style*="display: flex"] {
    display:flex !important;
  }
  .team-modal-content {
    background:linear-gradient(135deg,#43e6ff 0%,#7f5af0 100%);
    border-radius:32px;
    box-shadow:0 8px 48px #43e6ff88;
    padding:40px 32px 32px 32px;
    max-width:400px;width:90%;
    text-align:center;position:relative;
    animation:popInModal 0.5s;
  }
  #closeTeamModal:hover {
    color:#43e6ff;
    text-shadow:0 2px 8px #43e6ff88;
  }
</style>
</body>
</html>
