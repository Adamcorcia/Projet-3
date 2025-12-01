<?php
session_start();
require_once 'config.php';

// Vérifier si connecté + organizer/admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['user_role'] !== 'organizer' && $_SESSION['user_role'] !== 'admin') {
    die("Accès refusé : tu dois être organisateur ou admin.");
}

$user_id = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT e.*
    FROM events e
    WHERE e.organizer_id = ?
    ORDER BY e.date, e.time
");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes événements créés</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1 class="header-title">Projet Événements</h1>
    <nav class="header-nav">
        <a href="index.php">Accueil</a>
        <a href="events_list.php">Événements</a>
        <a href="my_created_events.php">Mes événements créés</a>
        <a href="create_event.php">Créer un événement</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<main class="main">
    <section class="card">
        <h2>Mes événements créés</h2>

        <?php if (empty($events)): ?>
            <p>Tu n'as créé aucun événement pour le moment.</p>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($events as $event): ?>
                    <li class="list-item">
                        <strong><?= htmlspecialchars($event['title']) ?></strong>
                        <div class="meta">
                            <?= htmlspecialchars($event['city']) ?> –
                            le <?= htmlspecialchars($event['date']) ?> à <?= htmlspecialchars(substr($event['time'], 0, 5)) ?>
                        </div>

                        <div style="margin-top:8px;">
                            <a class="btn" href="event_detail.php?id=<?= (int)$event['id'] ?>">Voir</a>
                            <a class="btn btn-secondary" href="edit_event.php?id=<?= (int)$event['id'] ?>">Modifier</a>

                            <form method="post" action="delete_event.php" class="inline"
                                  onsubmit="return confirm('Tu es sûr de vouloir supprimer cet événement ?');">
                                <input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
