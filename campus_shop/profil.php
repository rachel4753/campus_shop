
<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}
// Récupération de l'utilisateur depuis la session
$user = $_SESSION['utilisateur'];

// Traitement de la soumission du numéro WhatsApp
$whatsapp_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['whatsapp'])) {
    $numero_whatsapp = trim($_POST['whatsapp']);
    if (!empty($numero_whatsapp)) {
        // Mettre à jour le numéro WhatsApp dans la base de données
        require_once 'config.php';
        $stmt = $pdo->prepare('UPDATE utilisateurs SET whatsapp = ? WHERE id = ?');
        if ($stmt->execute([$numero_whatsapp, $user['id']])) {
            // Mettre à jour la session
            $_SESSION['utilisateur']['whatsapp'] = $numero_whatsapp;
            $user['whatsapp'] = $numero_whatsapp;
            $whatsapp_message = '<div style="background:#d4edda;color:#155724;padding:12px 18px;border-radius:7px;margin-bottom:18px;">Numéro WhatsApp enregistré avec succès !</div>';
        } else {
            $whatsapp_message = '<div style="background:#f8d7da;color:#721c24;padding:12px 18px;border-radius:7px;margin-bottom:18px;">Erreur lors de l\'enregistrement du numéro WhatsApp.</div>';
        }
    } else {
        $whatsapp_message = '<div style="background:#f8d7da;color:#721c24;padding:12px 18px;border-radius:7px;margin-bottom:18px;">Veuillez saisir un numéro WhatsApp valide.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
        <meta charset="UTF-8">
        <title>Dashboard utilisateur - Campus Shop</title>
        <link rel="stylesheet" href="assets/css/styles.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <!-- Aucun JS avatar -->
        <style>
            body { background: #f6f7fb; }
            .sidebar-dashboard {
                position: fixed;
                top: 0; left: 0;
                height: 100vh;
                width: 340px;
                background: linear-gradient(135deg,#7f5af0 0%,#43e6ff 100%);
                box-shadow: 0 0 32px rgba(127,90,240,0.12);
                display: flex;
                flex-direction: column;
                z-index: 3000;
                animation: slideIn 0.6s cubic-bezier(.77,.2,.05,1.0);
            }
            .sidebar-dashboard .avatar {
                width: 90px; height: 90px;
                border-radius: 50%;
                margin: 32px auto 12px auto;
                box-shadow: 0 4px 18px #43e6ff44;
                object-fit: cover;
                background: #fff;

            }
                            body {
                                background: linear-gradient(120deg, #f6f7fb 60%, #e0e7ff 100%);
                                font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
                                margin: 0;
                                min-height: 100vh;
                                overflow-x: hidden;
                            }
                            .dashboard-container {
                                display: flex;
                                min-height: 100vh;
                            }
                            .sidebar {
                                width: 320px;
                                background: linear-gradient(135deg, #7f5af0 0%, #43e6ff 100%);
                                color: #fff;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                padding: 32px 0 0 0;
                                box-shadow: 0 0 32px #7f5af022;
                                position: relative;
                                z-index: 10;
                                animation: slideInLeft 0.7s cubic-bezier(.77,.2,.05,1.0);
                            }
                            .sidebar .avatar {
                                width: 100px;
                                height: 100px;
                                border-radius: 50%;
                                box-shadow: 0 4px 18px #43e6ff44;
                                object-fit: cover;
                                background: #fff;
                                margin-bottom: 18px;
                                border: 4px solid #fff;
                                transition: transform 0.3s;
                            }
                            .sidebar .avatar:hover {
                                transform: scale(1.08) rotate(-3deg);
                            }
                            .sidebar .user-info {
                                text-align: center;
                                margin-bottom: 24px;
                            }
                            .sidebar .user-info .name {
                                font-size: 22px;
                                font-weight: 700;
                                letter-spacing: 0.5px;
                            }
                            .sidebar .user-info .email {
                                font-size: 15px;
                                opacity: 0.85;
                            }
                            .sidebar .user-info .role {
                                display: inline-block;
                                background: #25D366;
                                color: #fff;
                                font-size: 13px;
                                font-weight: 600;
                                border-radius: 12px;
                                padding: 3px 12px;
                                margin-top: 8px;
                                box-shadow: 0 2px 8px #25d36633;
                            }
                            .sidebar nav {
                                width: 100%;
                                display: flex;
                                flex-direction: column;
                                gap: 8px;
                                margin-bottom: 32px;
                            }
                            .sidebar nav a {
                                display: flex;
                                align-items: center;
                                gap: 16px;
                                padding: 14px 38px;
                                color: #fff;
                                font-size: 18px;
                                font-weight: 500;
                                border-radius: 0 24px 24px 0;
                                text-decoration: none;
                                transition: background 0.2s, color 0.2s, transform 0.2s;
                                position: relative;
                            }
                            .sidebar nav a.active, .sidebar nav a:hover {
                                background: rgba(255,255,255,0.18);
                                color: #111;
                                transform: translateX(8px) scale(1.04);
                            }
                            .sidebar nav a .fa {
                                font-size: 22px;
                                min-width: 26px;
                            }
                            .sidebar .logout-btn {
                                margin: 0 38px 32px 38px;
                                background: #e74c3c;
                                color: #fff;
                                border: none;
                                border-radius: 8px;
                                padding: 13px 0;
                                font-size: 17px;
                                font-weight: 600;
                                cursor: pointer;
                                box-shadow: 0 2px 8px #e74c3c33;
                                transition: background 0.2s;
                            }
                            .sidebar .logout-btn:hover {
                                background: #c0392b;
                            }
                            .main-content {
                                flex: 1;
                                min-width: 0;
                                padding: 48px 3vw 48px 3vw;
                                background: linear-gradient(120deg, #f6f7fb 60%, #e0e7ff 100%);
                                animation: fadeInUp 0.8s;
                                box-sizing: border-box;
                                display: flex;
                                flex-direction: column;
                                align-items: stretch;
                            }
                            .dashboard-welcome {
                                font-size: 2.2rem;
                                font-weight: 700;
                                margin-bottom: 12px;
                                color: #7f5af0;
                                letter-spacing: 0.5px;
                                animation: fadeIn 1.2s;
                            }
                            .dashboard-cards {
                                display: grid;
                                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                                gap: 32px;
                                margin-bottom: 48px;
                            }
                            .dashboard-card {
                                background: #fff;
                                border-radius: 18px;
                                box-shadow: 0 4px 24px #7f5af022;
                                padding: 32px 24px 24px 24px;
                                display: flex;
                                flex-direction: column;
                                align-items: flex-start;
                                position: relative;
                                overflow: hidden;
                                transition: transform 0.2s, box-shadow 0.2s;
                                cursor: pointer;
                                min-height: 180px;
                                animation: cardPop 0.7s;
                            }
                            .dashboard-card:hover {
                                transform: translateY(-8px) scale(1.03);
                                box-shadow: 0 8px 32px #7f5af044;
                            }
                            .dashboard-card .card-icon {
                                font-size: 2.5rem;
                                color: #7f5af0;
                                margin-bottom: 18px;
                                animation: iconBounce 1.2s infinite alternate;
                            }
                            .dashboard-card .card-title {
                                font-size: 1.2rem;
                                font-weight: 600;
                                margin-bottom: 8px;
                            }
                            .dashboard-card .card-desc {
                                font-size: 1rem;
                                color: #555;
                                margin-bottom: 12px;
                            }
                            .dashboard-card .card-link {
                                color: #7f5af0;
                                font-weight: 600;
                                text-decoration: underline;
                                font-size: 1rem;
                                margin-top: auto;
                                transition: color 0.2s;
                            }
                            .dashboard-card .card-link:hover {
                                color: #43e6ff;
                            }
                            .dashboard-section {
                                margin-bottom: 48px;
                                animation: fadeIn 1.2s;
                            }
                            .dashboard-section h2 {
                                color: #7f5af0;
                                font-size: 1.5rem;
                                margin-bottom: 18px;
                            }
                            .dashboard-table {
                                width: 100%;
                                border-collapse: collapse;
                                background: #fff;
                                border-radius: 12px;
                                box-shadow: 0 2px 12px #7f5af022;
                                overflow: hidden;
                            }
                            /* Animations */
                            @keyframes slideInLeft {
                                from { transform: translateX(-100px); opacity: 0; }
                                to { transform: translateX(0); opacity: 1; }
                            }
                            @keyframes fadeInUp {
                                from { transform: translateY(40px); opacity: 0; }
                                to { transform: translateY(0); opacity: 1; }
                            }
                            @keyframes fadeIn {
                                from { opacity: 0; }
                                to { opacity: 1; }
                            }
                            @keyframes cardPop {
                                from { transform: scale(0.95); opacity: 0; }
                                to { transform: scale(1); opacity: 1; }
                            }
                            @keyframes iconBounce {
                                0% { transform: translateY(0); }
                                100% { transform: translateY(-8px); }
                            }
                            @media (max-width: 900px) {
                                .dashboard-container { flex-direction: column; }
                                .sidebar { width: 100vw; flex-direction: row; justify-content: flex-start; padding: 18px 0; height: auto; }
                                .sidebar nav { flex-direction: row; gap: 0; }
                                .sidebar nav a { border-radius: 0 0 18px 18px; padding: 10px 18px; font-size: 16px; }
                                .main-content { padding: 32px 2vw; align-items: stretch; }
                            }
                        </style>
                    </head>
                    <body>
                    <div class="dashboard-container">
                        <aside class="sidebar">
                            <img src="assets/img/avatar_default.png" alt="Avatar" class="avatar">
                            <div class="user-info">
                                <div class="name"><?= htmlspecialchars($user['nom'] ?? '') ?></div>
                                <div class="email"> <?= htmlspecialchars($user['email'] ?? '') ?> </div>
                                <span class="role"> <?= htmlspecialchars($user['role'] ?? 'acheteur') ?> </span>
                            </div>
                            <nav>
                                <a href="#dashboard" class="active"><i class="fa fa-home"></i> Dashboard</a>
                                <a href="#commandes"><i class="fa fa-box"></i> Commandes</a>
                                <a href="#panier"><i class="fa fa-shopping-cart"></i> Panier</a>
                                <a href="#signalements"><i class="fa fa-exclamation-triangle"></i> Signalements</a>
                                <a href="#chat"><i class="fa fa-comments"></i> Chatbot</a>
                                <a href="#parametres"><i class="fa fa-cog"></i> Paramètres</a>
                            </nav>
                            <form method="post" action="logout.php">
                                <button type="submit" class="logout-btn"><i class="fa fa-sign-out-alt"></i> Déconnexion</button>
                            </form>
                        </aside>
                        <main class="main-content">
                            <div class="dashboard-welcome">Bienvenue, <?= htmlspecialchars($user['nom'] ?? '') ?> !</div>

                            <?php
                            // Afficher le message d'alerte et le formulaire si WhatsApp n'est pas renseigné
                            if (empty($user['whatsapp'])) {
                                echo $whatsapp_message;
                            ?>
                            <div style="background:#fff3cd;color:#856404;padding:18px 24px;border-radius:9px;margin-bottom:24px;box-shadow:0 2px 12px #ffeeba55;max-width:520px;">
                                <strong>⚠️ Pour recevoir vos commandes, veuillez renseigner votre numéro WhatsApp.</strong><br>
                                <form method="post" style="margin-top:12px;display:flex;gap:12px;align-items:center;">
                                    <input type="text" name="whatsapp" placeholder="Numéro WhatsApp" style="padding:8px 14px;border-radius:6px;border:1px solid #ccc;font-size:1rem;" required>
                                    <button type="submit" style="background:#25D366;color:#fff;border:none;border-radius:6px;padding:8px 18px;font-size:1rem;font-weight:600;">Enregistrer</button>
                                </form>
                            </div>
                            <?php } elseif (!empty($whatsapp_message)) { echo $whatsapp_message; } ?>
                            <div class="dashboard-cards">
                                <div class="dashboard-card" onclick="window.location='#commandes'">
                                    <div class="card-icon"><i class="fa fa-box"></i></div>
                                    <div class="card-title">Mes commandes</div>
                                    <div class="card-desc">Consultez l’historique de vos commandes et leur statut en temps réel.</div>
                                    <div class="card-link">Voir mes commandes</div>
                                </div>
                                <div class="dashboard-card" onclick="window.location='#panier'">
                                    <div class="card-icon"><i class="fa fa-shopping-cart"></i></div>
                                    <div class="card-title">Mon panier</div>
                                    <div class="card-desc">Gérez vos articles, passez commande ou modifiez votre panier facilement.</div>
                                    <div class="card-link">Accéder au panier</div>
                                </div>
                                <div class="dashboard-card" onclick="window.location='#signalements'">
                                    <div class="card-icon"><i class="fa fa-exclamation-triangle"></i></div>
                                    <div class="card-title">Signalements</div>
                                    <div class="card-desc">Signalez un problème ou consultez vos signalements en cours.</div>
                                    <div class="card-link">Faire un signalement</div>
                                </div>
                                <div class="dashboard-card" onclick="window.location='#chat'">
                                    <div class="card-icon"><i class="fa fa-comments"></i></div>
                                    <div class="card-title">Chatbot & Support</div>
                                    <div class="card-desc">Discutez avec notre assistant ou contactez le support Campus Shop.</div>
                                    <div class="card-link">Ouvrir le chat</div>
                                </div>
                            </div>
                            <section class="dashboard-section" id="commandes">
                                                                                                <h2>Notifications de commande reçues</h2>
                                                                                                <table class="dashboard-table" style="width:100%;text-align:left;margin-bottom:38px;">
                                                                                                    <thead>
                                                                                                        <tr style="background:#f3f6fa;">
                                                                                                            <th style="padding:12px 8px;">Nom acheteur</th>
                                                                                                            <th style="padding:12px 8px;">WhatsApp</th>
                                                                                                            <th style="padding:12px 8px;">Produits</th>
                                                                                                            <th style="padding:12px 8px;">Quantité</th>
                                                                                                            <th style="padding:12px 8px;">Date</th>
                                                                                                            <th style="padding:12px 8px;">Statut</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        <!-- Exemple statique, à remplacer par une boucle PHP sur les commandes reçues -->
                                                                                                        <tr>
                                                                                                            <td style="padding:10px 8px;">Jean Dupont</td>
                                                                                                            <td style="padding:10px 8px;"><a href="https://wa.me/22501020304" target="_blank" style="color:#25D366;font-weight:600;text-decoration:none;">22501020304</a></td>
                                                                                                            <td style="padding:10px 8px;">Casque Bluetooth</td>
                                                                                                            <td style="padding:10px 8px;">2</td>
                                                                                                            <td style="padding:10px 8px;">29/10/2025</td>
                                                                                                            <td style="padding:10px 8px;"><span style="color:#43e6ff;font-weight:600;">En attente</span></td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td style="padding:10px 8px;">Aminata Kone</td>
                                                                                                            <td style="padding:10px 8px;"><a href="https://wa.me/22505060708" target="_blank" style="color:#25D366;font-weight:600;text-decoration:none;">22505060708</a></td>
                                                                                                            <td style="padding:10px 8px;">Livre de maths</td>
                                                                                                            <td style="padding:10px 8px;">1</td>
                                                                                                            <td style="padding:10px 8px;">28/10/2025</td>
                                                                                                            <td style="padding:10px 8px;"><span style="color:#7f5af0;font-weight:600;">Validée</span></td>
                                                                                                        </tr>
                                                                                                    </tbody>
                                                                                                </table>

                                                                                                <h2>Mes commandes</h2>
                                                                                                <table class="dashboard-table" style="width:100%;text-align:left;">
                                                                                                    <thead>
                                                                                                        <tr style="background:#f3f6fa;">
                                                                                                            <th style="padding:12px 8px;">Produits</th>
                                                                                                            <th style="padding:12px 8px;">Quantité</th>
                                                                                                            <th style="padding:12px 8px;">Date</th>
                                                                                                            <th style="padding:12px 8px;">Statut</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        <!-- Exemple statique, à remplacer par une boucle PHP sur les commandes de l’utilisateur -->
                                                                                                        <tr>
                                                                                                            <td style="padding:10px 8px;">Casque Bluetooth</td>
                                                                                                            <td style="padding:10px 8px;">2</td>
                                                                                                            <td style="padding:10px 8px;">29/10/2025</td>
                                                                                                            <td style="padding:10px 8px;"><span style="color:#43e6ff;font-weight:600;">En attente</span></td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td style="padding:10px 8px;">Livre de maths</td>
                                                                                                            <td style="padding:10px 8px;">1</td>
                                                                                                            <td style="padding:10px 8px;">28/10/2025</td>
                                                                                                            <td style="padding:10px 8px;"><span style="color:#7f5af0;font-weight:600;">Validée</span></td>
                                                                                                        </tr>
                                                                                                    </tbody>
                                                                                                </table>
                            </section>
                            <section class="dashboard-section" id="panier">
                                                                <h2>Mon panier</h2>
                                                                <div id="dashboardPanierContent" class="dashboard-table" style="padding:24px 0;"></div>
                                                                <script>
                                                                function getPanier() {
                                                                    return JSON.parse(localStorage.getItem('panier')||'[]');
                                                                }
                                                                function showDashboardPanier() {
                                                                    let panier = getPanier();
                                                                    const cont = document.getElementById('dashboardPanierContent');
                                                                    if(!cont) return;
                                                                    if(panier.length===0) {
                                                                        cont.innerHTML = '<div style="text-align:center;color:#888;font-size:1.1rem;">Votre panier est vide.</div>';
                                                                        return;
                                                                    }
                                                                    cont.innerHTML = `
                                                                        <button id=\"dashboardViderPanierBtn\" style=\"background:#ff4d4f;color:#fff;padding:8px 18px;border:none;border-radius:8px;font-weight:600;margin-bottom:18px;cursor:pointer;\">Vider le panier</button>
                                                                        <ul style=\"list-style:none;padding:0;\">`+
                                                                        panier.map(p=>{
                                                                            let whatsapp = p.vendeur_whatsapp ? p.vendeur_whatsapp.replace(/[^0-9]/g,'') : '';
                                                                            let boutonWhatsapp = whatsapp ? `<a href=\"https://wa.me/${whatsapp}\" target=\"_blank\" style=\"margin-left:12px;background:#43e6ff;color:#fff;padding:6px 14px;border-radius:8px;font-weight:600;text-decoration:none;display:inline-block;\">Commander sur WhatsApp</a>` : '';
                                                                            return `<li style='margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;'><img src='${p.image?p.image:'https://via.placeholder.com/60x60?text=Image'}' style='width:48px;height:48px;object-fit:cover;border-radius:8px;'> <span style='flex:1;'>${p.nom}</span> <span style='color:#4a90e2;font-weight:600;'>x${p.qte}</span> <span style='font-weight:700;'>${p.prix} FCFA</span> ${boutonWhatsapp}</li>`;
                                                                        }).join('')+
                                                                        '</ul>';
                                                                    setTimeout(()=>{
                                                                        const viderBtn = document.getElementById('dashboardViderPanierBtn');
                                                                        if(viderBtn) viderBtn.onclick = function(){
                                                                            localStorage.removeItem('panier');
                                                                            showDashboardPanier();
                                                                        };
                                                                    }, 50);
                                                                }
                                                                document.addEventListener('DOMContentLoaded', showDashboardPanier);
                                                                </script>
                            </section>
                            <section class="dashboard-section" id="signalements">
                                                                <h2>Mes signalements</h2>
                                                                <?php if (isset($_GET['signalement']) && $_GET['signalement']==='ok') {
                                                                  echo "<div style='background:#d4edda;color:#155724;padding:12px 18px;border-radius:7px;margin-bottom:18px;'>Signalement envoyé avec succès !</div>";
                                                                } ?>
                                                                <form method="post" action="signalement.php" style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px #7f5af022;max-width:520px;margin-bottom:24px;">
                                                                    <label for="motif" style="font-weight:600;">Motif du signalement :</label><br>
                                                                    <select name="motif" id="motif" required style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;">
                                                                        <option value="">-- Choisir un motif --</option>
                                                                        <option value="produit frauduleux">Produit frauduleux</option>
                                                                        <option value="arnaque">Arnaque</option>
                                                                        <option value="comportement inapproprié">Comportement inapproprié</option>
                                                                        <option value="autre">Autre</option>
                                                                    </select>
                                                                    <label for="description" style="font-weight:600;">Description :</label><br>
                                                                    <textarea name="description" id="description" rows="4" required style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;"></textarea>
                                                                    <button type="submit" style="background:#e74c3c;color:#fff;border:none;border-radius:7px;padding:10px 22px;font-size:1rem;font-weight:600;">Envoyer le signalement</button>
                                                                </form>
                                                                <div class="dashboard-table" style="text-align:center;padding:32px 0;">Aucun signalement en cours.</div>
                            </section>
                            <section class="dashboard-section" id="chat">
                                                                <h2>Chatbot & Support</h2>
                                                                <div id="campusBotContainer" style="position:relative;min-height:320px;">
                                                                    <div id="campusBotLauncher" style="position:absolute;bottom:24px;right:24px;z-index:1000;">
                                                                        <button id="openCampusBot" style="background:#7f5af0;color:#fff;border:none;border-radius:50%;width:60px;height:60px;box-shadow:0 4px 18px #7f5af044;font-size:2rem;cursor:pointer;animation:bounceIn 1s;">🤖</button>
                                                                    </div>
                                                                    <div id="campusBotWindow" style="display:none;position:absolute;bottom:90px;right:24px;width:340px;max-width:95vw;background:#fff;border-radius:18px;box-shadow:0 8px 32px #7f5af022;overflow:hidden;z-index:1001;animation:fadeInUp 0.7s;">
                                                                        <div style="background:linear-gradient(90deg,#7f5af0 60%,#43e6ff 100%);padding:18px 22px;color:#fff;font-weight:700;font-size:1.2rem;display:flex;align-items:center;justify-content:space-between;">
                                                                            <span>Campus Bot</span>
                                                                            <button id="closeCampusBot" style="background:none;border:none;color:#fff;font-size:1.3rem;cursor:pointer;">&times;</button>
                                                                        </div>
                                                                        <div id="campusBotBody" style="padding:18px 18px 12px 18px;max-height:260px;overflow-y:auto;font-size:1rem;"></div>
                                                                        <div style="display:flex;gap:8px;padding:12px 18px 18px 18px;background:#f3f6fa;">
                                                                            <input id="campusBotInput" type="text" placeholder="Pose ta question..." style="flex:1;padding:8px 12px;border-radius:8px;border:1px solid #ddd;font-size:1rem;">
                                                                            <button id="campusBotSend" style="background:#43e6ff;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-weight:600;cursor:pointer;">Envoyer</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <script>
                                                                document.addEventListener('DOMContentLoaded', function(){
                                                                    const launcher = document.getElementById('openCampusBot');
                                                                    const windowBot = document.getElementById('campusBotWindow');
                                                                    const closeBtn = document.getElementById('closeCampusBot');
                                                                    const botBody = document.getElementById('campusBotBody');
                                                                    const botInput = document.getElementById('campusBotInput');
                                                                    const botSendBtn = document.getElementById('campusBotSend');
                                                                    if(launcher && windowBot && closeBtn){
                                                                        launcher.onclick = ()=>{ windowBot.style.display = 'block'; botInput.focus(); };
                                                                        closeBtn.onclick = ()=>{ windowBot.style.display = 'none'; };
                                                                    }
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
                            </section>
                            <section class="dashboard-section" id="parametres">
                                                                <h2>Paramètres</h2>
                                                                <form method="post" action="modifier_infos.php" style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px #7f5af022;max-width:520px;margin-bottom:24px;">
                                                                    <label for="nom" style="font-weight:600;">Nom :</label><br>
                                                                    <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;">
                                                                    <label for="email" style="font-weight:600;">Email :</label><br>
                                                                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;">
                                                                    <label for="whatsapp" style="font-weight:600;">Numéro WhatsApp :</label><br>
                                                                    <input type="text" name="whatsapp" id="whatsapp" value="<?= htmlspecialchars($user['whatsapp'] ?? '') ?>" required style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;">
                                                                    <label for="password" style="font-weight:600;">Nouveau mot de passe :</label><br>
                                                                    <input type="password" name="password" id="password" style="width:100%;padding:8px 12px;border-radius:7px;border:1px solid #ccc;margin-bottom:14px;">
                                                                    <button type="submit" style="background:#43e6ff;color:#fff;border:none;border-radius:7px;padding:10px 22px;font-size:1rem;font-weight:600;">Enregistrer les modifications</button>
                                                                </form>
                                                                <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 12px #ff4d4f22;max-width:520px;">
                                                                                                        <p style="color:#ff4d4f;font-weight:600;">Vos produits publiés :</p>
                                                                                                                                            <ul style="list-style:none;padding:0;margin:0;">
                                                                                                                                                                                <?php
                                                                                                                                                                                // Message de succès JS
                                                                                                                                                                                echo '<div id="msgSuppression" style="display:none;background:#d4edda;color:#155724;padding:12px 18px;border-radius:7px;margin-bottom:18px;position:relative;box-shadow:0 2px 12px #43e6ff22;max-width:420px;">
                                                                                                                                                                                    <span id="msgSuppressionTxt">Produit supprimé avec succès !</span>
                                                                                                                                                                                    <button id="closeMsgSuppression" style="position:absolute;top:8px;right:12px;background:none;border:none;color:#155724;font-size:1.2rem;cursor:pointer;">&times;</button>
                                                                                                                                                                                </div>';
                                                                                                                                                                                // Récupérer les produits publiés par l'utilisateur
                                                                                                                                                                                $stmt = $pdo->prepare('SELECT id, nom, image FROM produits WHERE utilisateur_id = ? ORDER BY id DESC');
                                                                                                                                                                                $stmt->execute([$user['id']]);
                                                                                                                                                                                $mesProduits = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                                                                if (empty($mesProduits)) {
                                                                                                                                                                                    echo '<li style="color:#888;font-size:1.05rem;padding:12px;">Aucun produit publié.</li>';
                                                                                                                                                                                } else {
                                                                                                                                                                                    foreach ($mesProduits as $prod) {
                                                                                                                                                                                        $img = htmlspecialchars($prod['image'] ?: 'https://via.placeholder.com/60x60?text=Image');
                                                                                                                                                                                        $nom = htmlspecialchars($prod['nom']);
                                                                                                                                                                                        $id = (int)$prod['id'];
                                                                                                                                                                                        echo "<li style='display:flex;align-items:center;gap:12px;margin-bottom:14px;background:#f3f6fa;padding:8px 12px;border-radius:8px;' data-id='$id'>";
                                                                                                                                                                                        echo "<img src='$img' style='width:48px;height:48px;object-fit:cover;border-radius:8px;'>";
                                                                                                                                                                                        echo "<span style='flex:1;font-weight:600;'>$nom</span>";
                                                                                                                                                                                        echo "<button class='btnSuppProduit' data-id='$id' style='background:none;border:none;color:#ff4d4f;font-size:1.5rem;cursor:pointer;' title='Supprimer ce produit'>&times;</button>";
                                                                                                                                                                                        echo "</li>";
                                                                                                                                                                                    }
                                                                                                                                                                                }
                                                                                                                                                                                ?>
                                                                                                                                                                                <script>
                                                                                                                                                                                document.addEventListener('DOMContentLoaded', function(){
                                                                                                                                                                                    document.querySelectorAll('.btnSuppProduit').forEach(btn => {
                                                                                                                                                                                        btn.onclick = function(e){
                                                                                                                                                                                            e.preventDefault();
                                                                                                                                                                                            var id = this.getAttribute('data-id');
                                                                                                                                                                                            var li = this.closest('li');
                                                                                                                                                                                            fetch('supprimer_produit.php', {
                                                                                                                                                                                                method: 'POST',
                                                                                                                                                                                                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                                                                                                                                                                                                body: 'id_produit=' + encodeURIComponent(id)
                                                                                                                                                                                            })
                                                                                                                                                                                            .then(r=>r.json())
                                                                                                                                                                                            .then(res=>{
                                                                                                                                                                                                if(res.success){
                                                                                                                                                                                                    li.remove();
                                                                                                                                                                                                    var msg = document.getElementById('msgSuppression');
                                                                                                                                                                                                    if(msg){ msg.style.display = 'block'; }
                                                                                                                                                                                                }else{
                                                                                                                                                                                                    var msg = document.getElementById('msgSuppressionTxt');
                                                                                                                                                                                                    if(msg){ msg.textContent = 'Erreur : ' + (res.error||'Suppression impossible'); document.getElementById('msgSuppression').style.display = 'block'; }
                                                                                                                                                                                                }
                                                                                                                                                                                            });
                                                                                                                                                                                        };
                                                                                                                                                                                    });
                                                                                                                                                                                    var closeBtn = document.getElementById('closeMsgSuppression');
                                                                                                                                                                                    if(closeBtn){ closeBtn.onclick = function(){ document.getElementById('msgSuppression').style.display = 'none'; }; }
                                                                                                                                                                                });
                                                                                                                                                                                </script>
                                                                                                                                            </ul>
                                                                </div>
                            </section>
                        </main>
                    </div>
                    <script>
                    // Animation navigation active
                    document.querySelectorAll('.sidebar nav a').forEach(link => {
                        link.addEventListener('click', function() {
                            document.querySelectorAll('.sidebar nav a').forEach(l => l.classList.remove('active'));
                            this.classList.add('active');
                        });
                    });
                    </script>
                    </body>
                    </html>
                
