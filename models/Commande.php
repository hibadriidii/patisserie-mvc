<?php
// models/Commande.php

require_once __DIR__ . '/../config/database.php';

class Commande {
    private PDO $db;

    const STATUTS = ['en préparation', 'prête', 'livrée'];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Créer une commande
    public function create(int $userId, int $produitId, int $quantite): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO commandes (user_id, produit_id, quantite) VALUES (:user_id, :produit_id, :quantite)"
        );
        return $stmt->execute([
            ':user_id'    => $userId,
            ':produit_id' => $produitId,
            ':quantite'   => $quantite,
        ]);
    }

    // Commandes d'un client
    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT c.*, p.nom AS produit_nom, p.prix, p.image
             FROM commandes c
             JOIN produits p ON c.produit_id = p.id
             WHERE c.user_id = :user_id
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    // Toutes les commandes (admin)
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT c.*, u.nom AS client_nom, u.email AS client_email,
                    p.nom AS produit_nom, p.prix
             FROM commandes c
             JOIN users u    ON c.user_id    = u.id
             JOIN produits p ON c.produit_id = p.id
             ORDER BY c.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    // Une commande par ID
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.nom AS client_nom, p.nom AS produit_nom
             FROM commandes c
             JOIN users u    ON c.user_id    = u.id
             JOIN produits p ON c.produit_id = p.id
             WHERE c.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $commande = $stmt->fetch();
        return $commande ?: null;
    }

    // Changer le statut
    public function updateStatut(int $id, string $statut): bool {
        if (!in_array($statut, self::STATUTS)) return false;
        $stmt = $this->db->prepare("UPDATE commandes SET statut = :statut WHERE id = :id");
        return $stmt->execute([':statut' => $statut, ':id' => $id]);
    }

    // Supprimer une commande
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM commandes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Statistiques admin
    public function getStats(): array {
        $stmt = $this->db->query(
            "SELECT statut, COUNT(*) as total FROM commandes GROUP BY statut"
        );
        return $stmt->fetchAll();
    }
}
