<?php
// views/client/commandes/list.php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/Commande.php';
requireClient();

$commandeModel = new Commande();
$commandes = $commandeModel->getByUser($_SESSION['user_id']);
$pageTitle = 'Mes Commandes — La Belle Pâtisserie';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container py-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">🛍️ Mes Commandes</h2>
            <p class="text-muted mb-0"><?= count($commandes) ?> commande(s)</p>
        </div>
        <a href="/patisserie_MVC/views/client/produits/list.php" class="btn btn-patisserie">
            <i class="bi bi-shop"></i> Continuer mes achats
        </a>
    </div>

    <?php if (empty($commandes)): ?>
        <div class="empty-state">
            <div class="empty-icon">🛒</div>
            <h4>Aucune commande pour le moment</h4>
            <p class="text-muted">Découvrez nos délicieux gâteaux et passez votre première commande !</p>
            <a href="/patisserie_MVC/views/client/produits/list.php" class="btn btn-patisserie">
                <i class="bi bi-shop"></i> Voir la boutique
            </a>
        </div>
    <?php else: ?>
        <div class="commandes-list">
            <?php foreach ($commandes as $c): ?>
            <?php
                $statusClass = match($c['statut']) {
                    'en préparation' => 'badge-preparation',
                    'prête'          => 'badge-prete',
                    'livrée'         => 'badge-livree',
                    default          => 'bg-secondary',
                };
                $statusIcon = match($c['statut']) {
                    'en préparation' => '⏳',
                    'prête'          => '✅',
                    'livrée'         => '🚚',
                    default          => '📦',
                };
            ?>
            <div class="commande-card">
                <div class="commande-img-wrap">
                    <img src="/patisserie_MVC/public/images/<?= htmlspecialchars($c['image']) ?>"
                         alt="<?= htmlspecialchars($c['produit_nom']) ?>"
                         class="commande-img"
                         onerror="this.src='/patisserie_MVC/public/images/default.jpg'">
                </div>
                <div class="commande-info">
                    <div class="commande-header">
                        <div>
                            <h5 class="commande-product"><?= htmlspecialchars($c['produit_nom']) ?></h5>
                            <small class="text-muted">Commande #<?= $c['id'] ?> — <?= date('d/m/Y à H:i', strtotime($c['created_at'])) ?></small>
                        </div>
                        <span class="badge-statut <?= $statusClass ?>">
                            <?= $statusIcon ?> <?= htmlspecialchars($c['statut']) ?>
                        </span>
                    </div>
                    <div class="commande-details">
                        <span>Quantité : <strong><?= $c['quantite'] ?></strong></span>
                        <span>Prix unitaire : <strong><?= number_format($c['prix'], 2) ?> DT</strong></span>
                        <span class="commande-total">Total : <strong><?= number_format($c['prix'] * $c['quantite'], 2) ?> DT</strong></span>
                    </div>
                </div>
                <?php if ($c['statut'] === 'en préparation'): ?>
                <div class="commande-actions">
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="confirmAnnuler(<?= $c['id'] ?>)">
                        <i class="bi bi-x-circle"></i> Annuler
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal annulation -->
<div class="modal fade" id="annulerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Annuler la commande ?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="font-size:3rem">⚠️</div>
                <p class="mt-2">Cette commande sera définitivement annulée.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Garder</button>
                <a id="annulerBtn" href="#" class="btn btn-danger">Oui, annuler</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmAnnuler(id) {
    document.getElementById('annulerBtn').href =
        '/patisserie_MVC/controllers/commandeController.php?action=annuler&id=' + id;
    new bootstrap.Modal(document.getElementById('annulerModal')).show();
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
