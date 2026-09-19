<?php
// views/client/produits/detail.php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/Produit.php';
requireClient();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produitModel = new Produit();
$produit = $produitModel->findById($id);

if (!$produit) {
    setFlash('error', 'Produit introuvable.');
    header('Location: /patisserie_MVC/views/client/produits/list.php');
    exit;
}

$pageTitle = htmlspecialchars($produit['nom']) . ' — La Belle Pâtisserie';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container py-4">
    <a href="/patisserie_MVC/views/client/produits/list.php" class="btn-back mb-4 d-inline-block">
        <i class="bi bi-arrow-left"></i> Retour à la boutique
    </a>

    <div class="detail-card">
        <div class="row g-0">
            <div class="col-md-5">
                <img src="/patisserie_MVC/public/images/<?= htmlspecialchars($produit['image']) ?>"
                     alt="<?= htmlspecialchars($produit['nom']) ?>"
                     class="detail-img"
                     onerror="this.src='/patisserie_MVC/public/images/default.jpg'">
            </div>
            <div class="col-md-7">
                <div class="detail-body">
                    <h1 class="detail-title"><?= htmlspecialchars($produit['nom']) ?></h1>
                    <div class="detail-price"><?= number_format($produit['prix'], 2) ?> DT</div>
                    <p class="detail-description"><?= nl2br(htmlspecialchars($produit['description'])) ?></p>

                    <form action="/patisserie_MVC/controllers/commandeController.php?action=passer" method="POST">
                        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Quantité</label>
                            <div class="qty-control">
                                <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                                <input type="number" name="quantite" id="qtyInput" value="1" min="1" max="99"
                                       class="qty-input">
                                <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                            </div>
                        </div>
                        <div class="order-total mb-4">
                            Total estimé : <strong id="totalPrice"><?= number_format($produit['prix'], 2) ?> DT</strong>
                        </div>
                        <button type="submit" class="btn btn-patisserie btn-lg px-5">
                            <i class="bi bi-bag-heart"></i> Commander maintenant
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const unitPrice = <?= $produit['prix'] ?>;

function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    const newVal = Math.max(1, Math.min(99, parseInt(input.value || 1) + delta));
    input.value = newVal;
    updateTotal(newVal);
}

function updateTotal(qty) {
    document.getElementById('totalPrice').textContent = (unitPrice * qty).toFixed(2) + ' DT';
}

document.getElementById('qtyInput').addEventListener('input', function () {
    updateTotal(parseInt(this.value) || 1);
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
