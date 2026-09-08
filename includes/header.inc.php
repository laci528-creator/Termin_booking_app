<?php

$currentPage = basename($_SERVER['PHP_SELF']);

$pageTitles = [
    'index.php' => 'Terminbuchung',
    'formular.php' => 'Termin buchen',
    'einloggen.php' => 'Admin Login',
    'admin.php' => 'Terminverwaltung'
];

$actualPageTitle = $pageTitles[$currentPage] ?? 'Terminbuchung';

?>

<!doctype html>
<html lang="de">
<head>
    <title><?= htmlspecialchars($actualPageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/common.css">
</head>

<body>

<nav class="main-nav">
    <div class="main-nav-content">
    <a href="index.php" class="brand">
        <img src="img/logo.png" alt="Terminbuchung Logo">
        <span>Terminbuchung-App</span>
    </a>

    <div class="nav-links">
        <a
            href="index.php"
            class="<?= $currentPage === 'index.php' ? 'active' : '' ?>"
        >
            Startseite
        </a>

    <?php if (!empty($_SESSION["eingeloggt"])): ?>

        <a
            href="admin.php"
            class="<?= $currentPage === 'admin.php' ? 'active' : '' ?>"
        >
            Admin
        </a>

        <form method="post" action="admin.php" class="logout-form">
            <input type="hidden" name="form_type" value="logout">

            <button type="submit" class="logout-btn">
                Ausloggen
            </button>
        </form>

    <?php else: ?>

        <a
            href="einloggen.php"
            class="<?= $currentPage === 'einloggen.php' ? 'active' : '' ?>"
        >
            Admin Login
        </a>

    <?php endif; ?>
    </div>
</div>

</nav>
<main class="main-area">
<div class="main-container">