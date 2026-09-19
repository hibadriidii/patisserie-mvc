<?php
// views/admin/users/list.php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/User.php';
requireSuperAdmin();

$userModel = new User();
$users     = $userModel->getAll();
$stats     = $userModel->countByRole();
$pageTitle = 'Gestion des Utilisateurs — Super Admin';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container py-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">👥 Gestion des Utilisateurs</h2>
            <p class="text-muted mb-0"><?= array_sum($stats) ?> compte(s) au total</p>
        </div>
        <a href="/patisserie_MVC/views/admin/users/create.php" class="btn btn-patisserie">
            <i class="bi bi-person-plus"></i> Ajouter un utilisateur
        </a>
    </div>

    <!-- Stat cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-success">
                <div class="stat-icon">👤</div>
                <div class="stat-info">
                    <div class="stat-number"><?= $stats['client'] ?></div>
                    <div class="stat-label">Clients</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-warning">
                <div class="stat-icon">🛠️</div>
                <div class="stat-info">
                    <div class="stat-number"><?= $stats['admin'] ?></div>
                    <div class="stat-label">Administrateurs</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-info">
                <div class="stat-icon">👑</div>
                <div class="stat-info">
                    <div class="stat-number"><?= $stats['super_admin'] ?></div>
                    <div class="stat-label">Super admins</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar mb-3">
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-outline-secondary active" onclick="filterRole('all', this)">Tous</button>
                <button class="btn btn-outline-secondary" onclick="filterRole('client', this)">Clients</button>
                <button class="btn btn-outline-secondary" onclick="filterRole('admin', this)">Admins</button>
                <button class="btn btn-outline-secondary" onclick="filterRole('super_admin', this)">Super admins</button>
            </div>
        </div>
    </div>

    <?php if (empty($users)): ?>
        <div class="empty-state">
            <div class="empty-icon">👥</div>
            <h4>Aucun utilisateur</h4>
        </div>
    <?php else: ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0" id="usersTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Inscrit le</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <?php
                            $isSelf = ($u['id'] == $_SESSION['user_id']);
                            $roleClass = match($u['role']) {
                                'super_admin' => 'role-super',
                                'admin'       => 'role-admin',
                                default       => 'role-client',
                            };
                            $roleLabel = match($u['role']) {
                                'super_admin' => '👑 Super admin',
                                'admin'       => '🛠️ Admin',
                                default       => '👤 Client',
                            };
                        ?>
                        <tr data-role="<?= $u['role'] ?>" data-search="<?= strtolower(htmlspecialchars($u['nom'] . ' ' . $u['email'])) ?>">
                            <td class="text-muted">#<?= $u['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar <?= $roleClass ?>">
                                        <?= mb_strtoupper(mb_substr($u['nom'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($u['nom']) ?></strong>
                                        <?php if ($isSelf): ?>
                                            <span class="badge bg-secondary ms-1" style="font-size:.7rem">Vous</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                            <td><span class="badge-role <?= $roleClass ?>"><?= $roleLabel ?></span></td>
                            <td class="text-muted small"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td class="text-center">
                                <?php if (!$isSelf): ?>
                                    <a href="/patisserie_MVC/views/admin/users/edit.php?id=<?= $u['id'] ?>"
                                       class="btn btn-sm btn-outline-primary me-1" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" title="Supprimer"
                                            onclick="confirmDelete(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['nom'])) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="font-size:3rem">⚠️</div>
                <p class="mt-2">Supprimer le compte de <strong id="deleteUserName"></strong> ?</p>
                <p class="text-muted small">Toutes ses commandes seront aussi supprimées.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a id="deleteConfirmBtn" href="#" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteUserName').textContent = name;
    document.getElementById('deleteConfirmBtn').href =
        '/patisserie_MVC/controllers/userController.php?action=delete&id=' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function filterRole(role, btn) {
    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('#usersTable tbody tr').forEach(row => {
        row.style.display = (role === 'all' || row.dataset.role === role) ? '' : 'none';
    });
}

document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#usersTable tbody tr').forEach(row => {
        row.style.display = row.dataset.search.includes(q) ? '' : 'none';
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
