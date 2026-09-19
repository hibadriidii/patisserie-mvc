<?php
// controllers/authController.php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Afficher le formulaire de login
    public function showLogin(): void {
        if (isLoggedIn()) {
            $this->redirectByRole();
        }
        require_once __DIR__ . '/../views/login.php';
    }

    // Afficher le formulaire d'inscription
    public function showRegister(): void {
        if (isLoggedIn()) {
            $this->redirectByRole();
        }
        require_once __DIR__ . '/../views/register.php';
    }

    // Traiter le login
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLogin();
            return;
        }
// trim pour effacer les espace
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Veuillez remplir tous les champs.";
            header('Location: /patisserie_MVC/views/login.php');
            exit;
        }
//bech net7a9e9ou li mail w password shah
        $user = $this->userModel->verifyCredentials($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];
            setFlash('success', 'Bienvenue, ' . $user['nom'] . ' !');
            $this->redirectByRole();
        } else {
            $_SESSION['error'] = "Email ou mot de passe incorrect.";
            header('Location: /patisserie_MVC/views/login.php');
            exit;
        }
    }

    // Traiter l'inscription
    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegister();
            return;
        }

        $nom      = trim($_POST['nom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        $errors = [];
        if (empty($nom))      $errors[] = "Le nom est requis.";
        if (empty($email))    $errors[] = "L'email est requis.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
        if (strlen($password) < 3) $errors[] = "Le mot de passe doit contenir au moins 3 caractères.";
        if ($password !== $confirm)  $errors[] = "Les mots de passe ne correspondent pas.";
        if ($this->userModel->emailExists($email)) $errors[] = "Cet email est déjà utilisé.";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = ['nom' => $nom, 'email' => $email];
            header('Location: /patisserie_MVC/views/register.php');
            exit;
        }

        if ($this->userModel->create($nom, $email, $password)) {
            $user = $this->userModel->findByEmail($email);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];
            setFlash('success', 'Compte créé avec succès ! Bienvenue, ' . $nom . ' !');
            header('Location: /patisserie_MVC/views/client/produits/list.php');
        } else {
            $_SESSION['error'] = "Une erreur est survenue. Réessayez.";
            header('Location: /patisserie_MVC/views/register.php');
        }
        exit;
    }

    // Déconnexion
    public function logout(): void {
        session_destroy();
        header('Location: /patisserie_MVC/views/login.php');
        exit;
    }

    // Redirection selon le rôle
    private function redirectByRole(): void {
        if (isAdmin()) {
            header('Location: /patisserie_MVC/views/admin/produits/list.php');
        } else {
            header('Location: /patisserie_MVC/views/client/produits/list.php');
        }
        exit;
    }
}

// Routage simple
$action = $_GET['action'] ?? 'login';
$controller = new AuthController();

match($action) {
    'login'    => $controller->login(),
    'register' => $controller->register(),
    'logout'   => $controller->logout(),
    default    => $controller->showLogin(),
};
