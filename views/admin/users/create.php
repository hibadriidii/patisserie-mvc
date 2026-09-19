<?php
// views/admin/users/create.php
require_once __DIR__ . '/../../../config/auth.php';
requireSuperAdmin();

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

$pageTitle = 'Créer un utilisateur — Super Admin';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container py-4">
    <div class="page-header mb-4">
        <a href="/patisserie_MVC/views/admin/users/list.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Retour aux utilisateurs
        </a>
        <h2 class="page-title mt-3">➕ Créer un utilisateur</h2>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="form-card">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/patisserie_MVC/controllers/userController.php?action=create" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom complet *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" name="nom" class="form-control form-control-lg input-with-icon"
                                   placeholder="ex: Marie Dupont"
                                   value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" class="form-control form-control-lg input-with-icon"
                                   placeholder="email@exemple.com"
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mot de passe *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="password" class="form-control form-control-lg input-with-icon"
                                   placeholder="Minimum 3 caractères" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Rôle *</label>
                        <div class="role-selector">
                            <?php
                            $roles = [
                                'client'      => ['icon' => '👤', 'label' => 'Client',      'desc' => 'Peut commander des gâteaux'],
                                'admin'       => ['icon' => '🛠️', 'label' => 'Admin',       'desc' => 'Gère produits et commandes'],
                                'super_admin' => ['icon' => '👑', 'label' => 'Super Admin', 'desc' => 'Accès total + gestion utilisateurs'],
                            ];
                            $selectedRole = $old['role'] ?? 'client';
                            foreach ($roles as $val => $r): ?>
                            <label class="role-option <?= $selectedRole === $val ? 'selected' : '' ?>">
                                <input type="radio" name="role" value="<?= $val ?>"
                                       <?= $selectedRole === $val ? 'checked' : '' ?>
                                       onchange="document.querySelectorAll('.role-option').forEach(el=>el.classList.remove('selected')); this.closest('.role-option').classList.add('selected')">
                                <span class="role-icon"><?= $r['icon'] ?></span>
                                <span class="role-info">
                                    <strong><?= $r['label'] ?></strong>
                                    <small><?= $r['desc'] ?></small>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-patisserie btn-lg px-5">
                            <i class="bi bi-person-check"></i> Créer le compte
                        </button>
                        <a href="/patisserie_MVC/views/admin/users/list.php" class="btn btn-outline-secondary btn-lg">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
