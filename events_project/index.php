<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Projet Événements</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>

<header class="header">
    <h1 class="header-title">Projet Événements</h1>
    <nav class="header-nav">
        <a href="events_list.php">Événements</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="my_events.php">Mes inscriptions</a>

            <?php if ($_SESSION['user_role'] === 'organizer' || $_SESSION['user_role'] === 'admin'): ?>
                <a href="my_created_events.php">Mes événements créés</a>
                <a href="create_event.php">Créer un événement</a>
            <?php endif; ?>

            <?php if ($_SESSION['user_role'] === 'user'): ?>
                <a href="request_organizer.php">Devenir organisateur</a>
            <?php endif; ?>

            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin_dashboard.php">Admin</a>
            <?php endif; ?>

            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Connexion</a>
            <a href="register.php">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<main class="main">

    <section class="card">
        <h2>Bienvenue sur la plateforme d'événements</h2>

        <?php if (isset($_SESSION['user_id'])): ?>
            <p>
                Connecté en tant que 
                <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                <span class="badge badge-blue"><?= htmlspecialchars($_SESSION['user_role']) ?></span>
            </p>

            <p class="meta">
                Utilise le menu en haut pour créer ou gérer tes événements, 
                ou pour voir ceux auxquels tu es inscrit.
            </p>

        <?php else: ?>
            <p>
                Connecte-toi ou crée un compte pour t'inscrire à des événements, 
                ou en organiser.
            </p>
            <div style="margin-top:10px;">
                <a class="btn" href="login.php">Se connecter</a>
                <a class="btn btn-secondary" href="register.php">Créer un compte</a>
            </div>
        <?php endif; ?>
    </section>

    <section class="card">
        <h3>Explorer les événements</h3>
        <p>Consulte la liste complète des événements disponibles.</p>
        <a class="btn" href="events_list.php">Voir les événements</a>
    </section>

</main>

</body>
</html>


