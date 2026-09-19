<?php
// models/Produit.php

require_once __DIR__ . '/../config/database.php';

class Produit {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Tous les produits
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM produits ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // Un produit par ID
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM produits WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $produit = $stmt->fetch();
        return $produit ?: null;
    }

    // Créer un produit
    public function create(string $nom, float $prix, string $description, string $image): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO produits (nom, prix, description, image) VALUES (:nom, :prix, :description, :image)"
        );
        return $stmt->execute([
            ':nom'         => $nom,
            ':prix'        => $prix,
            ':description' => $description,
            ':image'       => $image,
        ]);
    }

    // Modifier un produit
    public function update(int $id, string $nom, float $prix, string $description, string $image): bool {
        $stmt = $this->db->prepare(
            "UPDATE produits SET nom = :nom, prix = :prix, description = :description, image = :image WHERE id = :id"
        );
        return $stmt->execute([
            ':id'          => $id,
            ':nom'         => $nom,
            ':prix'        => $prix,
            ':description' => $description,
            ':image'       => $image,
        ]);
    }

    // Supprimer un produit
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM produits WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Dernier ID inséré
    public function getLastInsertId(): string {
        return $this->db->lastInsertId();
    }
}
