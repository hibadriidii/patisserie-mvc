<?php
// views/layouts/header.php
// Usage: require_once with $pageTitle defined beforehand
$flash = getFlash();
$isAdminUser = isAdmin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'La Belle Pâtisserie') ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="/patisserie_MVC/public/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-patisserie">
    <div class="container">
        <a class="navbar-brand" href="<?= $isAdminUser ? '/patisserie_MVC/views/admin/produits/list.php' : '/patisserie_MVC/views/client/produits/list.php' ?>">
            <span class="brand-icon">🍰</span>
            <span class="brand-text">La Belle Pâtisserie</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <?php if ($isAdminUser): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'admin/produits') !== false ? 'active' : '' ?>"
                           href="/patisserie_MVC/views/admin/produits/list.php">
                            <i class="bi bi-grid-3x3-gap"></i> Produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'admin/commandes') !== false ? 'active' : '' ?>"
                           href="/patisserie_MVC/views/admin/commandes/list.php">
                            <i class="bi bi-receipt"></i> Commandes
                        </a>
                    </li>
                    <?php if (isSuperAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'admin/users') !== false ? 'active' : '' ?>"
                           href="/patisserie_MVC/views/admin/users/list.php">
                            <i class="bi bi-people"></i> Utilisateurs
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <?php if (isSuperAdmin()): ?>
                            <span class="badge badge-super-admin ms-1">👑 Super Admin</span>
                        <?php else: ?>
                            <span class="badge badge-admin ms-1">Admin</span>
                        <?php endif; ?>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'client/produits') !== false ? 'active' : '' ?>"
                           href="/patisserie_MVC/views/client/produits/list.php">
                            <i class="bi bi-shop"></i> Boutique
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'client/commandes') !== false ? 'active' : '' ?>"
                           href="/patisserie_MVC/views/client/commandes/list.php">
                            <i class="bi bi-bag-heart"></i> Mes commandes
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item dropdown ms-2">
                    <a class="nav-link dropdown-toggle user-menu" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="/patisserie_MVC/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash messages -->
<?php if ($flash): ?>
<div class="container mt-3">
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<main class="main-content">
