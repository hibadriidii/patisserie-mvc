<?php
// config/auth.php

session_start();

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'super_admin']);
}

function isSuperAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin';
}

function isClient(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'client';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /patisserie_MVC/views/login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /patisserie_MVC/views/client/produits/list.php');
        exit;
    }
}

function requireSuperAdmin(): void {
    requireLogin();
    if (!isSuperAdmin()) {
        setFlash('error', 'Accès réservé au super administrateur.');
        header('Location: /patisserie_MVC/views/admin/produits/list.php');
        exit;
    }
}

function requireClient(): void {
    requireLogin();
    if (!isClient()) {
        header('Location: /patisserie_MVC/views/admin/produits/list.php');
        exit;
    }
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
