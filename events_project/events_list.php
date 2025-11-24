<?php
session_start();
require_once 'config.php';

// On récupère tous les événements triés par date/heure
$stmt = $pdo->query("SELECT * FROM events ORDER BY date, time");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des événements</title>
</head>
<body>
    <h1>Liste des événements</h1>

    <p><a href="index.php">← Retour à l'accueil</a></p>

    <?php if (empty($events)): ?>
        <p>Aucun événement pour le moment.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($events as $event): ?>
                <li>
                    <strong><?= htmlspecialchars($event['title']) ?></strong><br>
                    <?= htmlspecialchars($event['city']) ?> –
                    le <?= htmlspecialchars($event['date']) ?> à <?= htmlspecialchars(substr($event['time'], 0, 5)) ?><br>
                    <a href="event_detail.php?id=<?= (int)$event['id'] ?>">Voir les détails</a>
                </li>
                <hr>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
