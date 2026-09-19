<?php
// views/admin/commandes/list.php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/Commande.php';
requireAdmin();

$commandeModel = new Commande();
$commandes = $commandeModel->getAll();
$stats     = $commandeModel->getStats();

// Organiser les stats
$statsMap = ['en préparation' => 0, 'prête' => 0, 'livrée' => 0];
foreach ($stats as $s) $statsMap[$s['statut']] = $s['total'];

$pageTitle = 'Gestion des Commandes — Admin';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container py-4">
    <div class="page-header mb-4">
        <h2 class="page-title">📋 Gestion des Commandes</h2>
        <p class="text-muted"><?= count($commandes) ?> commande(s) au total</p>
    </div>

    <!-- Stats cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-warning">
                <div class="stat-icon">⏳</div>
                <div class="stat-info">
                    <div class="stat-number"><?= $statsMap['en préparation'] ?></div>
                    <div class="stat-label">En préparation</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-info">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <div class="stat-number"><?= $statsMap['prête'] ?></div>
                    <div class="stat-label">Prêtes à livrer</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-success">
                <div class="stat-icon">🚚</div>
                <div class="stat-info">
                    <div class="stat-number"><?= $statsMap['livrée'] ?></div>
                    <div class="stat-label">Livrées</div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($commandes)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h4>Aucune commande pour le moment</h4>
            <p class="text-muted">Les commandes des clients apparaîtront ici.</p>
        </div>
    <?php else: ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Qté</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th class="text-center">Changer statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $c): ?>
                        <tr>
                            <td><span class="text-muted">#<?= $c['id'] ?></span></td>
                            <td>
                                <strong><?= htmlspecialchars($c['client_nom']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($c['client_email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($c['produit_nom']) ?></td>
                            <td><span class="badge bg-secondary"><?= $c['quantite'] ?></span></td>
                            <td>
                                <strong><?= number_format($c['prix'] * $c['quantite'], 2) ?> DT</strong>
                            </td>
                            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                            <td>
                                <?php
                                $statusClass = match($c['statut']) {
                                    'en préparation' => 'badge-preparation',
                                    'prête'          => 'badge-prete',
                                    'livrée'         => 'badge-livree',
                                    default          => 'bg-secondary',
                                };
                                ?>
                                <span class="badge-statut <?= $statusClass ?>">
                                    <?= htmlspecialchars($c['statut']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="/patisserie_MVC/controllers/commandeController.php?action=updateStatut&id=<?= $c['id'] ?>"
                                      method="POST" class="d-flex gap-1 justify-content-center">
                                    <select name="statut" class="form-select form-select-sm statut-select" style="width:auto">
                                        <?php foreach (Commande::STATUTS as $s): ?>
                                            <option value="<?= $s ?>" <?= $c['statut'] === $s ? 'selected' : '' ?>>
                                                <?= $s ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-patisserie">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
