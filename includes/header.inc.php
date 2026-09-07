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

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css"
    >
    <link rel="stylesheet" href="css/common.css">
</head>

<body>

<nav class="main-nav">

    <a href="index.php" class="brand">
        <img src="img/logo.svg" alt="Terminbuchung Logo">
        <span>Terminbuchung-App</span>
    </a>

    <div class="nav-links">
        <a
            href="index.php"
            class="<?= $currentPage === 'index.php' ? 'active' : '' ?>"
        >
            Startseite
        </a>

        <a
            href="einloggen.php"
            class="<?= $currentPage === 'einloggen.php' ? 'active' : '' ?>"
        >
            Admin Login
        </a>
    </div>

</nav>