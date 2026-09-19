<?php
// models/User.php

require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Créer un utilisateur
    public function create(string $nom, string $email, string $password, string $role = 'client'): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (nom, email, password, role) VALUES (:nom, :email, :password, :role)"
        );
        return $stmt->execute([
            ':nom'      => $nom,
            ':email'    => $email,
            ':password' => $hash,
            ':role'     => $role,
        ]);
    }

    // Trouver par email
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    // Trouver par ID
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    // Vérifier si l'email existe déjà
    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // Vérifier les identifiants (login)
    public function verifyCredentials(string $email, string $password): ?array {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    // Tous les utilisateurs
    public function getAll(): array {
        $stmt = $this->db->query("SELECT id, nom, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // Modifier un utilisateur
    public function update(int $id, string $nom, string $email, string $role): bool {
        $stmt = $this->db->prepare(
            "UPDATE users SET nom = :nom, email = :email, role = :role WHERE id = :id"
        );
        return $stmt->execute([':id' => $id, ':nom' => $nom, ':email' => $email, ':role' => $role]);
    }

    // Réinitialiser le mot de passe
    public function resetPassword(int $id, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => $hash, ':id' => $id]);
    }

    // Supprimer un utilisateur
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Stats par rôle
    public function countByRole(): array {
        $stmt = $this->db->query("SELECT role, COUNT(*) as total FROM users GROUP BY role");
        $rows = $stmt->fetchAll();
        $map = ['client' => 0, 'admin' => 0, 'super_admin' => 0];
        foreach ($rows as $r) $map[$r['role']] = (int)$r['total'];
        return $map;
    }

    // Email unique sauf pour l'ID donné
    public function emailExistsExcept(string $email, int $excludeId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
