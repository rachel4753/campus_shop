<?php
require_once 'config.php';
$erreur = '';
$succes = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nom = trim($_POST['nom'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$motdepasse = $_POST['motdepasse'] ?? '';
	$confirmation = $_POST['confirmation'] ?? '';
	$role = $_POST['role'] ?? '';
	$livreur_nom = ($role === 'livreur') ? trim($_POST['livreur_nom'] ?? '') : null;
	$livreur_transport = ($role === 'livreur') ? trim($_POST['livreur_transport'] ?? '') : null;
	$livreur_residence = ($role === 'livreur') ? trim($_POST['livreur_residence'] ?? '') : null;
	if (!$nom || !$email || !$motdepasse || !$confirmation || !$role || ($role === 'livreur' && (!$livreur_nom || !$livreur_transport || !$livreur_residence))) {
		$erreur = 'Veuillez remplir tous les champs.';
	} elseif ($motdepasse !== $confirmation) {
		$erreur = 'Les mots de passe ne correspondent pas.';
	} elseif (strlen($motdepasse) < 6) {
		$erreur = 'Le mot de passe doit contenir au moins 6 caractères.';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$erreur = 'Adresse email non valide.';
	} else {
		$stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
		$stmt->execute([$email]);
		if ($stmt->fetch()) {
			$erreur = 'Email déjà utilisé.';
		} else {
			$stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, motdepasse, role' . ($role === 'livreur' ? ', livreur_nom, livreur_transport, livreur_residence' : '') . ') VALUES (?, ?, ?, ?' . ($role === 'livreur' ? ', ?, ?, ?' : '') . ')');
			$params = $role === 'livreur' ? [$nom, $email, $hash, $role, $livreur_nom, $livreur_transport, $livreur_residence] : [$nom, $email, $hash, $role];
			$ok = $stmt->execute($params);
			if ($ok) {
				$succes = 'Inscription réussie ! Vous pouvez vous connecter.';
				header('Location: connexion.php');
				exit;
			} else {
				$erreur = "Erreur lors de l'inscription.";
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
	<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-insc">
	<div class="container-insc">
		<div class="card-insc animated bounceInDown">
			<button onclick="window.location.href='index.php'" class="btn-retour-accueil" title="Retour à l'accueil">&larr; Accueil</button>
			<h2 class="titre-insc">Inscription</h2>
			<?php if ($erreur): ?>
				<div style="color:red; margin-bottom:10px;"> <?= htmlspecialchars($erreur) ?> </div>
			<?php endif; ?>
			<?php if ($succes): ?>
				<div style="color:green; margin-bottom:10px;"> <?= htmlspecialchars($succes) ?> </div>
			<?php endif; ?>
			<form method="post" autocomplete="off" class="form-insc">
				<div class="champ-insc">
					<label for="nom">Nom</label>
					<input type="text" name="nom" id="nom" required>
				</div>
				<div class="champ-insc">
					<label for="email">Email</label>
					<input type="email" name="email" id="email" required>
				</div>
				<div class="champ-insc">
					<label for="motdepasse">Mot de passe</label>
					<input type="password" name="motdepasse" id="motdepasse" required>
				</div>
				<div class="champ-insc">
					<label for="confirmation">Répéter le mot de passe</label>
					<input type="password" name="confirmation" id="confirmation" required>
				</div>
				<div class="champ-insc">
					<label for="role">Rôle</label>
					<select name="role" id="role" required>
						<option value="">Choisir un rôle</option>
						<option value="acheteur">Acheteur</option>
						<option value="vendeur">Vendeur</option>
						<option value="livreur">Livreur</option>
					</select>
				</div>
				<div id="livreurFields" style="display:none;">
					<div class="champ-insc">
						<label for="livreur_nom">Nom du livreur</label>
						<input type="text" name="livreur_nom" id="livreur_nom">
					</div>
					<div class="champ-insc">
						<label for="livreur_transport">Moyen de transport</label>
						<input type="text" name="livreur_transport" id="livreur_transport">
					</div>
					<div class="champ-insc">
						<label for="livreur_residence">Résidence</label>
						<input type="text" name="livreur_residence" id="livreur_residence">
					</div>
				</div>
				<button type="submit" class="btn-insc">S'inscrire</button>
			</form>
			<div class="lien-connexion">
				<a href="connexion.php">Déjà un compte ? Se connecter</a>
			</div>
		</div>
	</div>
	<style>
		body.bg-insc {background: linear-gradient(120deg,#7f5af0 0%,#43e6ff 100%);min-height:100vh;overflow-x:hidden;}
		.container-insc {display:flex;align-items:center;justify-content:center;min-height:100vh;}
		.card-insc {background:#fff;border-radius:22px;box-shadow:0 8px 32px #0002;padding:38px 32px 28px 32px;max-width:420px;width:98vw;animation:fadeIn 1s;position:relative;}
		.btn-retour-accueil {position:absolute;top:18px;left:18px;background:#f3f6fa;border:none;border-radius:8px;padding:6px 16px;font-weight:600;color:#7f5af0;cursor:pointer;transition:background 0.2s;box-shadow:0 2px 8px #7f5af022;}
		.btn-retour-accueil:hover {background:#e0e7ff;}
		.titre-insc {text-align:center;color:#7f5af0;margin-bottom:24px;font-size:2rem;letter-spacing:1px;animation:fadeInDown 0.8s;}
		.form-insc {display:flex;flex-direction:column;gap:18px;animation:fadeInUp 1.2s;}
		.champ-insc {display:flex;flex-direction:column;gap:6px;}
		.champ-insc label {font-weight:600;color:#4a90e2;}
		.champ-insc input, .champ-insc select {padding:10px 12px;border-radius:8px;border:1px solid #ccc;font-size:1rem;transition:box-shadow 0.2s;}
		.champ-insc input:focus, .champ-insc select:focus {box-shadow:0 0 0 2px #7f5af055;outline:none;}
		.btn-insc {width:100%;background:#7f5af0;color:#fff;padding:12px 0;border:none;border-radius:8px;font-size:1.1rem;font-weight:700;box-shadow:0 2px 8px #7f5af022;transition:background 0.2s,transform 0.2s;animation:bounceIn 1.2s;}
		.btn-insc:hover {background:#43e6ff;transform:scale(1.04);}
		.lien-connexion {text-align:center;margin-top:18px;animation:fadeIn 1.5s;}
		.lien-connexion a {color:#4a90e2;text-decoration:none;font-weight:600;transition:color 0.2s;}
		.lien-connexion a:hover {color:#7f5af0;}
		@keyframes fadeIn {from{opacity:0;}to{opacity:1;}}
		@keyframes fadeInDown {from{opacity:0;transform:translateY(-40px);}to{opacity:1;transform:translateY(0);}}
		@keyframes fadeInUp {from{opacity:0;transform:translateY(40px);}to{opacity:1;transform:translateY(0);}}
		@keyframes bounceIn {0%{transform:scale(0.8);}60%{transform:scale(1.05);}100%{transform:scale(1);}}
	</style>
	<script>
		// Animation d'apparition
		document.querySelector('.card-insc').classList.add('animated');
		// Affichage dynamique des champs livreur
		document.getElementById('role').addEventListener('change', function() {
			document.getElementById('livreurFields').style.display = this.value==='livreur' ? '' : 'none';
		});
		// Animation input focus
		document.querySelectorAll('.champ-insc input, .champ-insc select').forEach(function(el){
			el.addEventListener('focus',function(){this.parentNode.classList.add('focus');});
			el.addEventListener('blur',function(){this.parentNode.classList.remove('focus');});
		});
	</script>
</body>
</html>
<!-- Modale supprimée. Ce fichier sera utilisé pour afficher le formulaire stylé d'inscription uniquement si besoin. -->