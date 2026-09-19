<?php
// controllers/userController.php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private User $userModel;

    const ROLES = ['client', 'admin', 'super_admin'];

    public function __construct() {
        $this->userModel = new User();
    }

    // Liste tous les utilisateurs
    public function list(): void {
        requireSuperAdmin();
        $users = $this->userModel->getAll();
        $stats = $this->userModel->countByRole();
        require_once __DIR__ . '/../views/admin/users/list.php';
    }

    // Formulaire création
    public function showCreate(): void {
        requireSuperAdmin();
        require_once __DIR__ . '/../views/admin/users/create.php';
    }

    // Créer un utilisateur
    public function create(): void {
        requireSuperAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showCreate();
            return;
        }

        $nom      = trim($_POST['nom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'client';

        $errors = [];
        if (empty($nom))    $errors[] = "Le nom est requis.";
        if (empty($email))  $errors[] = "L'email est requis.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
        if (strlen($password) < 3) $errors[] = "Mot de passe : 3 caractères minimum.";
        if (!in_array($role, self::ROLES)) $errors[] = "Rôle invalide.";
        if ($this->userModel->emailExists($email)) $errors[] = "Cet email est déjà utilisé.";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = ['nom' => $nom, 'email' => $email, 'role' => $role];
            header('Location: /patisserie_MVC/views/admin/users/create.php');
            exit;
        }

        if ($this->userModel->create($nom, $email, $password, $role)) {
            setFlash('success', "Utilisateur « $nom » créé avec succès.");
        } else {
            setFlash('error', "Erreur lors de la création.");
        }
        header('Location: /patisserie_MVC/views/admin/users/list.php');
        exit;
    }

    // Formulaire édition
    public function showEdit(int $id): void {
        requireSuperAdmin();
        $user = $this->userModel->findById($id);
        if (!$user) {
            setFlash('error', 'Utilisateur introuvable.');
            header('Location: /patisserie_MVC/views/admin/users/list.php');
            exit;
        }
        require_once __DIR__ . '/../views/admin/users/edit.php';
    }

    // Modifier un utilisateur
    public function edit(int $id): void {
        requireSuperAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showEdit($id);
            return;
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            setFlash('error', 'Utilisateur introuvable.');
            header('Location: /patisserie_MVC/views/admin/users/list.php');
            exit;
        }

        // Empêcher de modifier son propre rôle
        if ($id === (int)$_SESSION['user_id']) {
            setFlash('error', 'Vous ne pouvez pas modifier votre propre compte ici.');
            header('Location: /patisserie_MVC/views/admin/users/list.php');
            exit;
        }

        $nom   = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role  = $_POST['role'] ?? 'client';

        $errors = [];
        if (empty($nom))   $errors[] = "Le nom est requis.";
        if (empty($email)) $errors[] = "L'email est requis.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
        if (!in_array($role, self::ROLES)) $errors[] = "Rôle invalide.";
        if ($this->userModel->emailExistsExcept($email, $id)) $errors[] = "Cet email est déjà utilisé.";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = ['nom' => $nom, 'email' => $email, 'role' => $role];
            header('Location: /patisserie_MVC/views/admin/users/edit.php?id=' . $id);
            exit;
        }

        if ($this->userModel->update($id, $nom, $email, $role)) {
            setFlash('success', "Utilisateur « $nom » modifié avec succès.");
        } else {
            setFlash('error', "Erreur lors de la modification.");
        }
        header('Location: /patisserie_MVC/views/admin/users/list.php');
        exit;
    }

    // Réinitialiser le mot de passe
    public function resetPassword(int $id): void {
        requireSuperAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /patisserie_MVC/views/admin/users/list.php');
            exit;
        }

        if ($id === (int)$_SESSION['user_id']) {
            setFlash('error', 'Utilisez votre profil pour changer votre propre mot de passe.');
            header('Location: /patisserie_MVC/views/admin/users/list.php');
            exit;
        }

        $newPassword = $_POST['new_password'] ?? '';
        if (strlen($newPassword) < 3) {
            setFlash('error', 'Mot de passe trop court (3 caractères minimum).');
            header('Location: /patisserie_MVC/views/admin/users/edit.php?id=' . $id);
            exit;
        }

        if ($this->userModel->resetPassword($id, $newPassword)) {
            setFlash('success', 'Mot de passe réinitialisé avec succès.');
        } else {
            setFlash('error', 'Erreur lors de la réinitialisation.');
        }
        header('Location: /patisserie_MVC/views/admin/users/list.php');
        exit;
    }

    // Supprimer un utilisateur
    public function delete(int $id): void {
        requireSuperAdmin();

        if ($id === (int)$_SESSION['user_id']) {
            setFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            header('Location: /patisserie_MVC/views/admin/users/list.php');
            exit;
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            setFlash('error', 'Utilisateur introuvable.');
            header('Location: /patisserie_MVC/views/admin/users/list.php');
            exit;
        }

        if ($this->userModel->delete($id)) {
            setFlash('success', "Utilisateur « {$user['nom']} » supprimé.");
        } else {
            setFlash('error', 'Erreur lors de la suppression.');
        }
        header('Location: /patisserie_MVC/views/admin/users/list.php');
        exit;
    }
}

// Routage
requireSuperAdmin();
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ctrl   = new UserController();

match($action) {
    'list'          => $ctrl->list(),
    'create'        => $ctrl->create(),
    'edit'          => $ctrl->edit($id),
    'delete'        => $ctrl->delete($id),
    'resetPassword' => $ctrl->resetPassword($id),
    default         => $ctrl->list(),
};
