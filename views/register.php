<?php
// views/register.php
require_once __DIR__ . '/../config/auth.php';
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/patisserie_MVC/views/admin/produits/list.php' : '/patisserie_MVC/views/client/produits/list.php'));
    exit;
}
$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — La Belle Pâtisserie</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="/patisserie_MVC/public/css/style.css" rel="stylesheet">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">🎂</div>
            <h1 class="auth-title">Créer un compte</h1>
            <p class="auth-subtitle">Rejoignez notre communauté gourmande</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/patisserie_MVC/controllers/authController.php?action=register" method="POST" class="auth-form">
            <div class="form-group mb-3">
                <label class="form-label">Nom complet</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" name="nom" class="form-control form-control-lg input-with-icon"
                           placeholder="Marie Dupont" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required autofocus>
                </div>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Email</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control form-control-lg input-with-icon"
                           placeholder="votre@email.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Mot de passe</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" class="form-control form-control-lg input-with-icon"
                           placeholder="Minimum 3 caractères" required>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label">Confirmer le mot de passe</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" name="confirm_password" class="form-control form-control-lg input-with-icon"
                           placeholder="Répétez le mot de passe" required>
                </div>
            </div>
            <button type="submit" class="btn btn-patisserie btn-lg w-100">
                <i class="bi bi-person-plus"></i> Créer mon compte
            </button>
        </form>

        <div class="auth-footer">
            Déjà un compte ?
            <a href="/patisserie_MVC/views/login.php">Se connecter</a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
