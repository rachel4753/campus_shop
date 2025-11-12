
<?php
require_once 'config.php';
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';
    if (!$email || !$motdepasse) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare('SELECT id, email, motdepasse, nom, role, livreur_nom, livreur_transport, livreur_residence FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($motdepasse, $user['motdepasse'])) {
            session_start();
            $_SESSION['utilisateur'] = $user;
            header('Location: index.php');
            exit;
        } else {
            $erreur = 'Identifiants invalides.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Campus Shop</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-conn">
    <div class="container-conn">
        <div class="card-conn animated bounceInDown">
            <button onclick="window.location.href='index.php'" class="btn-retour-accueil" title="Retour à l'accueil">&larr; Accueil</button>
            <h2 class="titre-conn">Connexion</h2>
            <?php if ($erreur): ?>
                <div style="color:red; margin-bottom:10px;"> <?= htmlspecialchars($erreur) ?> </div>
            <?php endif; ?>
            <form method="post" autocomplete="off" class="form-conn">
                <div class="champ-conn">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="champ-conn">
                    <label for="motdepasse">Mot de passe</label>
                    <input type="password" name="motdepasse" id="motdepasse" required>
                </div>
                <button type="submit" class="btn-conn">Se connecter</button>
            </form>
            <div class="lien-inscription">
                <a href="inscription.php">Pas encore de compte ? S'inscrire</a>
            </div>
        </div>
    </div>
    <style>
        body.bg-conn {background: linear-gradient(120deg,#43e6ff 0%,#7f5af0 100%);min-height:100vh;overflow-x:hidden;}
        .container-conn {display:flex;align-items:center;justify-content:center;min-height:100vh;}
        .card-conn {background:#fff;border-radius:22px;box-shadow:0 8px 32px #0002;padding:38px 32px 28px 32px;max-width:420px;width:98vw;animation:fadeIn 1s;position:relative;}
        .btn-retour-accueil {position:absolute;top:18px;left:18px;background:#f3f6fa;border:none;border-radius:8px;padding:6px 16px;font-weight:600;color:#43e6ff;cursor:pointer;transition:background 0.2s;box-shadow:0 2px 8px #43e6ff22;}
        .btn-retour-accueil:hover {background:#e0e7ff;}
        .titre-conn {text-align:center;color:#43e6ff;margin-bottom:24px;font-size:2rem;letter-spacing:1px;animation:fadeInDown 0.8s;}
        .form-conn {display:flex;flex-direction:column;gap:18px;animation:fadeInUp 1.2s;}
        .champ-conn {display:flex;flex-direction:column;gap:6px;}
        .champ-conn label {font-weight:600;color:#7f5af0;}
        .champ-conn input {padding:10px 12px;border-radius:8px;border:1px solid #ccc;font-size:1rem;transition:box-shadow 0.2s;}
        .champ-conn input:focus {box-shadow:0 0 0 2px #43e6ff55;outline:none;}
        .btn-conn {width:100%;background:#43e6ff;color:#fff;padding:12px 0;border:none;border-radius:8px;font-size:1.1rem;font-weight:700;box-shadow:0 2px 8px #43e6ff22;transition:background 0.2s,transform 0.2s;animation:bounceIn 1.2s;}
        .btn-conn:hover {background:#7f5af0;transform:scale(1.04);}
        .lien-inscription {text-align:center;margin-top:18px;animation:fadeIn 1.5s;}
        .lien-inscription a {color:#7f5af0;text-decoration:none;font-weight:600;transition:color 0.2s;}
        .lien-inscription a:hover {color:#43e6ff;}
        @keyframes fadeIn {from{opacity:0;}to{opacity:1;}}
        @keyframes fadeInDown {from{opacity:0;transform:translateY(-40px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeInUp {from{opacity:0;transform:translateY(40px);}to{opacity:1;transform:translateY(0);}}
        @keyframes bounceIn {0%{transform:scale(0.8);}60%{transform:scale(1.05);}100%{transform:scale(1);}}
    </style>
    <script>
        // Animation d'apparition
        document.querySelector('.card-conn').classList.add('animated');
        // Animation input focus
        document.querySelectorAll('.champ-conn input').forEach(function(el){
            el.addEventListener('focus',function(){this.parentNode.classList.add('focus');});
            el.addEventListener('blur',function(){this.parentNode.classList.remove('focus');});
        });
    </script>
</body>
</html>
