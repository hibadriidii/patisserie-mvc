# 🍰 La Belle Pâtisserie — Plateforme de vente en ligne
Architecture MVC · PHP · MySQL · Bootstrap 5

---

## 📁 Structure du projet

```
patisserie_MVC/
│
├── config/
│   ├── database.php        ← Connexion PDO (Singleton)
│   ├── database.sql        ← Script SQL (tables + données demo)
│   └── auth.php            ← Gestion sessions & helpers
│
├── models/
│   ├── User.php            ← CRUD utilisateurs
│   ├── Produit.php         ← CRUD produits
│   └── Commande.php        ← CRUD commandes
│
├── controllers/
│   ├── authController.php  ← Login / Register / Logout
│   ├── produitController.php
│   └── commandeController.php
│
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── login.php
│   ├── register.php
│   │
│   ├── admin/
│   │   ├── produits/
│   │   │   ├── list.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   └── commandes/
│   │       └── list.php
│   │
│   └── client/
│       ├── produits/
│       │   ├── list.php
│       │   └── detail.php
│       └── commandes/
│           └── list.php
│
├── public/
│   ├── css/style.css
│   ├── js/main.js
│   └── images/             ← images produits uploadées ici
│
└── logout.php
```

---

## ⚙️ Installation

### 1. Prérequis
- PHP >= 8.0
- MySQL >= 5.7
- Serveur Apache/Nginx (XAMPP, WAMP, Laragon…)

### 2. Base de données
```sql
-- Dans phpMyAdmin ou MySQL Workbench :
SOURCE /chemin/vers/patisserie_MVC/config/database.sql;
```

### 3. Configuration
Modifier `config/database.php` si nécessaire :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'patisserie_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Placer le projet
Copier dans le dossier racine de votre serveur :
```
htdocs/patisserie_MVC/   (XAMPP)
www/patisserie_MVC/      (WAMP)
```

### 5. Accès
```
http://localhost/patisserie_MVC/views/login.php
```

---

## 👤 Comptes de démonstration

| Rôle   | Email               | Mot de passe |
|--------|---------------------|--------------|
| Client | client@gmail.com    | password     |
| Admin  | admin@gmail.com     | password     |

> ⚠️ Les mots de passe dans `database.sql` sont hachés avec `password_hash(PHP_BCRYPT)`.
> Le hash fourni correspond à **"password"**. Changez-les en production !

---

## 🔐 Sécurité implémentée
- Mots de passe hachés avec `password_hash()` + `PASSWORD_BCRYPT`
- Protection CSRF via vérification `REQUEST_METHOD`
- Échappement HTML avec `htmlspecialchars()` partout
- Requêtes préparées PDO (protection SQL injection)
- Contrôle d'accès par rôle sur chaque page (`requireAdmin()`, `requireClient()`)
- Validation des uploads (type MIME + taille max 2 Mo)

---

## ✨ Fonctionnalités

### 👤 Espace Client
- ✅ Inscription / Connexion
- ✅ Catalogue des gâteaux avec photos
- ✅ Page détail produit
- ✅ Commander avec choix de quantité (modal rapide ou page détail)
- ✅ Voir mes commandes avec statut en temps réel
- ✅ Annuler une commande "en préparation"

### 👑 Espace Admin
- ✅ Ajouter / Modifier / Supprimer un produit
- ✅ Upload d'image produit avec prévisualisation
- ✅ Voir toutes les commandes clients
- ✅ Tableau de bord des statuts (stats)
- ✅ Changer le statut : en préparation → prête → livrée

---

## 🛠️ Technologies
| Technologie | Rôle |
|-------------|------|
| PHP 8+      | Logique serveur (MVC) |
| MySQL       | Base de données relationnelle |
| PDO         | Accès BDD sécurisé |
| Bootstrap 5 | Interface responsive |
| Google Fonts| Playfair Display + Lato |
| Bootstrap Icons | Icônes |
