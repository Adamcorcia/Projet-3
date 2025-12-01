<?php
session_start();
require_once 'config.php';

// Vérifier admin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin')) {
    die("Accès refusé : réservé à l'administrateur.");
}

// Utilisateurs
$stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Événements
$stmt = $pdo->query("
    SELECT e.*, u.name AS organizer_name
    FROM events e
    JOIN users u ON e.organizer_id = u.id
    ORDER BY e.date, e.time
");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panel administrateur</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1 class="header-title">Panel administrateur</h1>
    <nav class="header-nav">
        <a href="index.php">Accueil</a>
        <a href="events_list.php">Événements</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<main class="main">

    <section class="card">
        <h2>Utilisateurs</h2>

        <?php if (empty($users)): ?>
            <p>Aucun utilisateur trouvé.</p>
        <?php else: ?>
            <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Créé le</th>
                    <th>Changer le rôle</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= (int)$user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td>
                            <form method="post" action="update_user_role.php" class="inline">
                                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                <select name="role">
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                    <option value="organizer" <?= $user['role'] === 'organizer' ? 'selected' : '' ?>>organizer</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                </select>
                                <button type="submit" class="btn btn-secondary">OK</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </section>

    <section class="card">
        <h2>Événements</h2>

        <?php if (empty($events)): ?>
            <p>Aucun événement trouvé.</p>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($events as $event): ?>
                    <li class="list-item">
                        <strong><?= htmlspecialchars($event['title']) ?></strong>
                        <div class="meta">
                            Organisateur : <?= htmlspecialchars($event['organizer_name']) ?><br>
                            <?= htmlspecialchars($event['city']) ?> – le <?= htmlspecialchars($event['date']) ?> à <?= htmlspecialchars(substr($event['time'], 0, 5)) ?><br>
                            Capacité : <?= (int)$event['capacity'] ?>
                        </div>

                        <div style="margin-top:8px;">
                            <a class="btn" href="event_detail.php?id=<?= (int)$event['id'] ?>">Voir</a>
                            <a class="btn btn-secondary" href="edit_event.php?id=<?= (int)$event['id'] ?>">Modifier</a>

                            <form method="post" action="delete_event.php" class="inline"
                                  onsubmit="return confirm('Supprimer cet événement ?');">
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
