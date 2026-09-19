<?php
// views/client/produits/list.php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/Produit.php';
requireClient();

$produitModel = new Produit();
$produits = $produitModel->getAll();
$pageTitle = 'Notre Boutique — La Belle Pâtisserie';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container py-4">
    <!-- Hero banner -->
    <div class="boutique-hero mb-5">
        <div class="boutique-hero-content">
            <h1 class="boutique-hero-title">🍰 Notre Boutique</h1>
            <p class="boutique-hero-sub">Des créations artisanales faites avec amour</p>
        </div>
    </div>

    <?php if (empty($produits)): ?>
        <div class="empty-state">
            <div class="empty-icon">🍽️</div>
            <h4>Catalogue vide pour le moment</h4>
            <p class="text-muted">Revenez bientôt, de délicieux gâteaux arrivent !</p>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
            <?php foreach ($produits as $p): ?>
            <div class="col">
                <div class="product-card h-100">
                    <a href="/patisserie_MVC/views/client/produits/detail.php?id=<?= $p['id'] ?>" class="product-img-link">
                        <img src="/patisserie_MVC/public/images/<?= htmlspecialchars($p['image']) ?>"
                             alt="<?= htmlspecialchars($p['nom']) ?>"
                             class="product-card-img"
                             onerror="this.src='/patisserie_MVC/public/images/default.jpg'">
                    </a>
                    <div class="product-card-body">
                        <h5 class="product-name"><?= htmlspecialchars($p['nom']) ?></h5>
                        <p class="product-desc"><?= htmlspecialchars(mb_substr($p['description'], 0, 80)) ?>...</p>
                        <div class="product-footer">
                            <span class="product-price"><?= number_format($p['prix'], 2) ?> DT</span>
                            <div class="product-actions">
                                <a href="/patisserie_MVC/views/client/produits/detail.php?id=<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-patisserie">
                                    Détails
                                </a>
                                <button class="btn btn-sm btn-patisserie"
                                        onclick="openOrderModal(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nom'])) ?>', <?= $p['prix'] ?>)">
                                    <i class="bi bi-bag-plus"></i> Commander
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal commande rapide -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">🛒 Commander</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/patisserie_MVC/controllers/commandeController.php?action=passer" method="POST">
                <div class="modal-body py-4">
                    <input type="hidden" name="produit_id" id="modalProduitId">
                    <p class="modal-product-name" id="modalProduitNom"></p>
                    <p class="modal-product-price" id="modalProduitPrix"></p>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Quantité</label>
                        <div class="qty-control">
                            <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                            <input type="number" name="quantite" id="qtyInput" value="1" min="1" max="99"
                                   class="qty-input">
                            <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                        </div>
                    </div>
                    <div class="order-total mt-3">
                        Total : <strong id="totalPrice"></strong>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-patisserie px-4">
                        <i class="bi bi-check-circle"></i> Confirmer la commande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPrice = 0;

function openOrderModal(id, nom, prix) {
    currentPrice = prix;
    document.getElementById('modalProduitId').value  = id;
    document.getElementById('modalProduitNom').textContent  = nom;
    document.getElementById('modalProduitPrix').textContent = prix.toFixed(2) + ' DT / unité';
    document.getElementById('qtyInput').value = 1;
    updateTotal(1);
    new bootstrap.Modal(document.getElementById('orderModal')).show();
}

function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    const newVal = Math.max(1, Math.min(99, parseInt(input.value || 1) + delta));
    input.value = newVal;
    updateTotal(newVal);
}

function updateTotal(qty) {
    document.getElementById('totalPrice').textContent = (currentPrice * qty).toFixed(2) + ' DT';
}

document.getElementById('qtyInput')?.addEventListener('input', function() {
    updateTotal(parseInt(this.value) || 1);
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
