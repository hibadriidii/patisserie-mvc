<?php
// views/admin/produits/list.php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/Produit.php';
requireAdmin();

$produitModel = new Produit();
$produits = $produitModel->getAll();
$pageTitle = 'Gestion des Produits — Admin';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container py-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">🛍️ Gestion des Produits</h2>
            <p class="text-muted mb-0"><?= count($produits) ?> produit(s) au catalogue</p>
        </div>
        <a href="/patisserie_MVC/views/admin/produits/create.php" class="btn btn-patisserie">
            <i class="bi bi-plus-circle"></i> Ajouter un produit
        </a>
    </div>

    <?php if (empty($produits)): ?>
        <div class="empty-state">
            <div class="empty-icon">🎂</div>
            <h4>Aucun produit pour le moment</h4>
            <p class="text-muted">Commencez par ajouter des gâteaux à votre catalogue.</p>
            <a href="/patisserie_MVC/views/admin/produits/create.php" class="btn btn-patisserie">
                <i class="bi bi-plus-circle"></i> Ajouter le premier produit
            </a>
        </div>
    <?php else: ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Prix</th>
                            <th>Ajouté le</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $p): ?>
                        <tr>
                            <td>
                                <img src="/patisserie_MVC/public/images/<?= htmlspecialchars($p['image']) ?>"
                                     alt="<?= htmlspecialchars($p['nom']) ?>"
                                     class="admin-product-img"
                                     onerror="this.src='/patisserie_MVC/public/images/default.jpg'">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($p['nom']) ?></strong>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <?= htmlspecialchars(mb_substr($p['description'], 0, 60)) ?>...
                                </span>
                            </td>
                            <td>
                                <span class="price-badge"><?= number_format($p['prix'], 2) ?> DT</span>
                            </td>
                            <td class="text-muted small">
                                <?= date('d/m/Y', strtotime($p['created_at'])) ?>
                            </td>
                            <td class="text-center">
                                <a href="/patisserie_MVC/views/admin/produits/edit.php?id=<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nom'])) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="font-size: 3rem;">🗑️</div>
                <p class="mt-2">Voulez-vous supprimer <strong id="deleteProductName"></strong> ?</p>
                <p class="text-muted small">Cette action est irréversible.</p>
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
    document.getElementById('deleteProductName').textContent = name;
    document.getElementById('deleteConfirmBtn').href =
        '/patisserie_MVC/controllers/produitController.php?action=delete&id=' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
