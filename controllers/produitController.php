<?php
// controllers/produitController.php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Produit.php';

class ProduitController {
    private Produit $produitModel;
    private string $uploadDir;

    public function __construct() {
        $this->produitModel = new Produit();
        $this->uploadDir    = __DIR__ . '/../public/images/';
    }

    /* ─── ADMIN ─── */

    public function adminList(): void {
        requireAdmin();
        $produits = $this->produitModel->getAll();
        require_once __DIR__ . '/../views/admin/produits/list.php';
    }

    public function showCreate(): void {
        requireAdmin();
        require_once __DIR__ . '/../views/admin/produits/create.php';
    }

    public function create(): void {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showCreate();
            return;
        }

        $nom         = trim($_POST['nom'] ?? '');
        $prix        = (float)($_POST['prix'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $image       = $this->handleUpload();

        if (empty($nom) || $prix <= 0) {
            setFlash('error', 'Nom et prix sont obligatoires.');
            header('Location: /patisserie_MVC/views/admin/produits/create.php');
            exit;
        }

        if ($this->produitModel->create($nom, $prix, $description, $image)) {
            setFlash('success', 'Produit ajouté avec succès !');
        } else {
            setFlash('error', 'Erreur lors de l\'ajout du produit.');
        }
        header('Location: /patisserie_MVC/views/admin/produits/list.php');
        exit;
    }

    public function showEdit(int $id): void {
        requireAdmin();
        $produit = $this->produitModel->findById($id);
        if (!$produit) {
            setFlash('error', 'Produit introuvable.');
            header('Location: /patisserie_MVC/views/admin/produits/list.php');
            exit;
        }
        require_once __DIR__ . '/../views/admin/produits/edit.php';
    }

    public function edit(int $id): void {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showEdit($id);
            return;
        }

        $produit = $this->produitModel->findById($id);
        if (!$produit) {
            setFlash('error', 'Produit introuvable.');
            header('Location: /patisserie_MVC/views/admin/produits/list.php');
            exit;
        }

        $nom         = trim($_POST['nom'] ?? '');
        $prix        = (float)($_POST['prix'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $image       = !empty($_FILES['image']['name']) ? $this->handleUpload() : $produit['image'];

        if ($this->produitModel->update($id, $nom, $prix, $description, $image)) {
            setFlash('success', 'Produit modifié avec succès !');
        } else {
            setFlash('error', 'Erreur lors de la modification.');
        }
        header('Location: /patisserie_MVC/views/admin/produits/list.php');
        exit;
    }

    public function delete(int $id): void {
        requireAdmin();
        if ($this->produitModel->delete($id)) {
            setFlash('success', 'Produit supprimé.');
        } else {
            setFlash('error', 'Erreur lors de la suppression.');
        }
        header('Location: /patisserie_MVC/views/admin/produits/list.php');
        exit;
    }

    /* ─── CLIENT ─── */

    public function clientList(): void {
        requireLogin();
        $produits = $this->produitModel->getAll();
        require_once __DIR__ . '/../views/client/produits/list.php';
    }

    public function clientDetail(int $id): void {
        requireLogin();
        $produit = $this->produitModel->findById($id);
        if (!$produit) {
            setFlash('error', 'Produit introuvable.');
            header('Location: /patisserie_MVC/views/client/produits/list.php');
            exit;
        }
        require_once __DIR__ . '/../views/client/produits/detail.php';
    }

    /* ─── Upload helper ─── */

    private function handleUpload(): string {
        if (empty($_FILES['image']['name'])) return 'default.jpg';

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $file    = $_FILES['image'];

        if (!in_array($file['type'], $allowed)) return 'default.jpg';
        if ($file['size'] > 2 * 1024 * 1024)   return 'default.jpg';

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('produit_') . '.' . $ext;
        $dest     = $this->uploadDir . $filename;

        if (!is_dir($this->uploadDir)) mkdir($this->uploadDir, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $dest)) return $filename;

        return 'default.jpg';
    }
}

// Routage
requireLogin();
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$controller = new ProduitController();

if (isAdmin()) {
    match($action) {
        'list'   => $controller->adminList(),
        'create' => $controller->create(),
        'edit'   => $controller->edit($id),
        'delete' => $controller->delete($id),
        default  => $controller->adminList(),
    };
} else {
    match($action) {
        'list'   => $controller->clientList(),
        'detail' => $controller->clientDetail($id),
        default  => $controller->clientList(),
    };
}
