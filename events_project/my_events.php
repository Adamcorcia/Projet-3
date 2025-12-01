<?php
session_start();
require_once 'config.php';

// Vérifier si connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT e.*, u.name AS organizer_name
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    JOIN users u ON e.organizer_id = u.id
    WHERE r.user_id = ?
    ORDER BY e.date, e.time
");
$stmt->execute([$user_id]);
$myEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes événements inscrits</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1 class="header-title">Projet Événements</h1>
    <nav class="header-nav">
        <a href="index.php">Accueil</a>
        <a href="events_list.php">Événements</a>
        <a href="my_events.php">Mes inscriptions</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<main class="main">
    <section class="card">
        <h2>Mes événements (inscriptions)</h2>

        <?php if (empty($myEvents)): ?>
            <p>Tu n'es inscrit à aucun événement pour le moment.</p>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($myEvents as $event): ?>
                    <li class="list-item">
                        <strong><?= htmlspecialchars($event['title']) ?></strong>
                        <div class="meta">
                            <?= htmlspecialchars($event['city']) ?> –
                            le <?= htmlspecialchars($event['date']) ?> à <?= htmlspecialchars(substr($event['time'], 0, 5)) ?><br>
                            Organisé par : <?= htmlspecialchars($event['organizer_name']) ?>
                        </div>

                        <div style="margin-top:8px;">
                            <a class="btn" href="event_detail.php?id=<?= (int)$event['id'] ?>">Voir</a>

                            <form method="post" action="unregister_event.php" class="inline" style="margin-left:8px;">
                                <input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">
                                <button type="submit" class="btn btn-secondary">Se désinscrire</button>
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

