<?php
// controllers/commandeController.php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Commande.php';
require_once __DIR__ . '/../models/Produit.php';

class CommandeController {
    private Commande $commandeModel;
    private Produit  $produitModel;

    public function __construct() {
        $this->commandeModel = new Commande();
        $this->produitModel  = new Produit();
    }

    /* ─── CLIENT ─── */

    // Passer une commande
    public function passer(): void {
        requireClient();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /patisserie_MVC/views/client/produits/list.php');
            exit;
        }

        $produitId = (int)($_POST['produit_id'] ?? 0);
        $quantite  = max(1, (int)($_POST['quantite'] ?? 1));
        $produit   = $this->produitModel->findById($produitId);

        if (!$produit) {
            setFlash('error', 'Produit introuvable.');
            header('Location: /patisserie_MVC/views/client/produits/list.php');
            exit;
        }

        if ($this->commandeModel->create($_SESSION['user_id'], $produitId, $quantite)) {
            setFlash('success', '✅ Commande passée avec succès !');
            header('Location: /patisserie_MVC/views/client/commandes/list.php');
        } else {
            setFlash('error', 'Erreur lors de la commande.');
            header('Location: /patisserie_MVC/views/client/produits/list.php');
        }
        exit;
    }

    // Mes commandes (client)
    public function mesCommandes(): void {
        requireClient();
        $commandes = $this->commandeModel->getByUser($_SESSION['user_id']);
        require_once __DIR__ . '/../views/client/commandes/list.php';
    }

    // Annuler une commande
    public function annuler(int $id): void {
        requireClient();
        $commande = $this->commandeModel->findById($id);
        if ($commande && $commande['user_id'] == $_SESSION['user_id'] && $commande['statut'] === 'en préparation') {
            $this->commandeModel->delete($id);
            setFlash('success', 'Commande annulée.');
        } else {
            setFlash('error', 'Impossible d\'annuler cette commande.');
        }
        header('Location: /patisserie_MVC/views/client/commandes/list.php');
        exit;
    }

    /* ─── ADMIN ─── */

    // Toutes les commandes
    public function adminList(): void {
        requireAdmin();
        $commandes = $this->commandeModel->getAll();
        $stats     = $this->commandeModel->getStats();
        require_once __DIR__ . '/../views/admin/commandes/list.php';
    }

    // Changer le statut
    public function updateStatut(int $id): void {
        requireAdmin();
        $statut = $_POST['statut'] ?? '';
        if ($this->commandeModel->updateStatut($id, $statut)) {
            setFlash('success', 'Statut mis à jour.');
        } else {
            setFlash('error', 'Statut invalide.');
        }
        header('Location: /patisserie_MVC/views/admin/commandes/list.php');
        exit;
    }
}

// Routage
requireLogin();
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$controller = new CommandeController();

if (isAdmin()) {
    match($action) {
        'list'          => $controller->adminList(),
        'updateStatut'  => $controller->updateStatut($id),
        default         => $controller->adminList(),
    };
} else {
    match($action) {
        'list'    => $controller->mesCommandes(),
        'passer'  => $controller->passer(),
        'annuler' => $controller->annuler($id),
        default   => $controller->mesCommandes(),
    };
}
