<?php
// views/admin/users/edit.php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/User.php';
requireSuperAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userModel = new User();
$user      = $userModel->findById($id);

if (!$user) {
    setFlash('error', 'Utilisateur introuvable.');
    header('Location: /patisserie_MVC/views/admin/users/list.php');
    exit;
}

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

$pageTitle = 'Modifier — ' . htmlspecialchars($user['nom']);
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container py-4">
    <div class="page-header mb-4">
        <a href="/patisserie_MVC/views/admin/users/list.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Retour aux utilisateurs
        </a>
        <h2 class="page-title mt-3">✏️ Modifier : <?= htmlspecialchars($user['nom']) ?></h2>
    </div>

    <div class="row g-4 justify-content-center">

        <!-- Informations principales -->
        <div class="col-lg-7">
            <div class="form-card">
                <h5 class="form-section-title">
                    <i class="bi bi-person-gear"></i> Informations du compte
                </h5>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        Vous ne pouvez pas modifier votre propre compte depuis ici.
                    </div>
                <?php else: ?>
                <form action="/patisserie_MVC/controllers/userController.php?action=edit&id=<?= $user['id'] ?>" method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom complet *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" name="nom" class="form-control form-control-lg input-with-icon"
                                   value="<?= htmlspecialchars($old['nom'] ?? $user['nom']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" class="form-control form-control-lg input-with-icon"
                                   value="<?= htmlspecialchars($old['email'] ?? $user['email']) ?>" required>
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
                            $selectedRole = $old['role'] ?? $user['role'];
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
                        <button type="submit" class="btn btn-patisserie btn-lg px-4">
                            <i class="bi bi-check-circle"></i> Enregistrer
                        </button>
                        <a href="/patisserie_MVC/views/admin/users/list.php" class="btn btn-outline-secondary btn-lg">
                            Annuler
                        </a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Réinitialiser mot de passe -->
        <?php if ($user['id'] != $_SESSION['user_id']): ?>
        <div class="col-lg-4">
            <div class="form-card">
                <h5 class="form-section-title text-warning">
                    <i class="bi bi-key"></i> Réinitialiser le mot de passe
                </h5>
                <p class="text-muted small mb-3">Définir un nouveau mot de passe pour cet utilisateur.</p>
                <form action="/patisserie_MVC/controllers/userController.php?action=resetPassword&id=<?= $user['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nouveau mot de passe *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="new_password" id="newPwd"
                                   class="form-control input-with-icon" placeholder="Minimum 3 caractères" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirmer *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" id="confirmPwd"
                                   class="form-control input-with-icon" placeholder="Répéter le mot de passe">
                        </div>
                        <div id="pwdMatch" class="form-text"></div>
                    </div>
                    <button type="submit" id="resetBtn" class="btn btn-warning w-100" disabled>
                        <i class="bi bi-arrow-repeat"></i> Réinitialiser
                    </button>
                </form>
            </div>

            <!-- Carte info utilisateur -->
            <div class="form-card mt-3">
                <h5 class="form-section-title"><i class="bi bi-info-circle"></i> Infos</h5>
                <div class="user-info-row">
                    <span class="text-muted small">Inscrit le</span>
                    <strong><?= date('d/m/Y', strtotime($user['created_at'])) ?></strong>
                </div>
                <div class="user-info-row mt-2">
                    <span class="text-muted small">ID</span>
                    <code>#<?= $user['id'] ?></code>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
const newPwd     = document.getElementById('newPwd');
const confirmPwd = document.getElementById('confirmPwd');
const pwdMatch   = document.getElementById('pwdMatch');
const resetBtn   = document.getElementById('resetBtn');

function checkPwd() {
    if (!confirmPwd.value) { pwdMatch.textContent = ''; resetBtn.disabled = true; return; }
    if (newPwd.value === confirmPwd.value && newPwd.value.length >= 3) {
        pwdMatch.textContent = '✅ Les mots de passe correspondent';
        pwdMatch.className   = 'form-text text-success';
        resetBtn.disabled    = false;
    } else {
        pwdMatch.textContent = '❌ Les mots de passe ne correspondent pas';
        pwdMatch.className   = 'form-text text-danger';
        resetBtn.disabled    = true;
    }
}
newPwd?.addEventListener('input', checkPwd);
confirmPwd?.addEventListener('input', checkPwd);
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
