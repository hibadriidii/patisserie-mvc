<?php
// views/admin/produits/edit.php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/Produit.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produitModel = new Produit();
$produit = $produitModel->findById($id);

if (!$produit) {
    setFlash('error', 'Produit introuvable.');
    header('Location: /patisserie_MVC/views/admin/produits/list.php');
    exit;
}

$pageTitle = 'Modifier — ' . $produit['nom'];
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container py-4">
    <div class="page-header mb-4">
        <a href="/patisserie_MVC/views/admin/produits/list.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Retour aux produits
        </a>
        <h2 class="page-title mt-3">✏️ Modifier : <?= htmlspecialchars($produit['nom']) ?></h2>
    </div>

    <div class="form-card">
        <form action="/patisserie_MVC/controllers/produitController.php?action=edit&id=<?= $produit['id'] ?>"
              method="POST" enctype="multipart/form-data">

            <div class="row g-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom du produit *</label>
                        <input type="text" name="nom" class="form-control form-control-lg"
                               value="<?= htmlspecialchars($produit['nom']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($produit['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Prix (DT) *</label>
                        <div class="input-group">
                            <span class="input-group-text">DT</span>
                            <input type="number" name="prix" class="form-control form-control-lg"
                                   value="<?= $produit['prix'] ?>" step="0.01" min="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image actuelle</label>
                        <img src="/patisserie_MVC/public/images/<?= htmlspecialchars($produit['image']) ?>"
                             alt="<?= htmlspecialchars($produit['nom']) ?>"
                             class="w-100 rounded mb-3 current-product-img"
                             onerror="this.src='/patisserie_MVC/public/images/default.jpg'">
                        <label class="form-label fw-semibold">Nouvelle image (optionnel)</label>
                        <div class="upload-zone" id="uploadZone">
                            <div class="upload-preview" id="uploadPreview">
                                <i class="bi bi-arrow-repeat upload-icon"></i>
                                <p class="upload-text">Changer l'image</p>
                                <small class="text-muted">Laissez vide pour conserver</small>
                            </div>
                            <img id="previewImg" src="" alt="" class="d-none w-100 rounded" style="max-height:160px;object-fit:cover;">
                            <input type="file" name="image" id="imageInput" accept="image/*" class="d-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-patisserie btn-lg px-5">
                    <i class="bi bi-check-circle"></i> Enregistrer les modifications
                </button>
                <a href="/patisserie_MVC/views/admin/produits/list.php" class="btn btn-outline-secondary btn-lg">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const zone = document.getElementById('uploadZone');
const input = document.getElementById('imageInput');
const preview = document.getElementById('previewImg');
const previewBox = document.getElementById('uploadPreview');

zone.addEventListener('click', () => input.click());
input.addEventListener('change', showPreview);

function showPreview() {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            previewBox.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
