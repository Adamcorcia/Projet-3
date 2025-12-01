<?php
session_start();
require_once 'config.php';

// Vérifier que l'utilisateur est connecté et organizer/admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['user_role'] !== 'organizer' && $_SESSION['user_role'] !== 'admin') {
    die("Accès refusé : tu dois être organisateur ou admin pour créer un événement.");
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $date        = $_POST['date'] ?? '';
    $time        = $_POST['time'] ?? '';
    $capacity    = $_POST['capacity'] ?? '';

    if ($title === '')        $errors[] = "Le titre est obligatoire.";
    if ($description === '')  $errors[] = "La description est obligatoire.";
    if ($city === '')         $errors[] = "La ville est obligatoire.";
    if ($date === '')         $errors[] = "La date est obligatoire.";
    if ($time === '')         $errors[] = "L'heure est obligatoire.";
    if (!ctype_digit((string)$capacity) || (int)$capacity <= 0) {
        $errors[] = "La capacité doit être un nombre positif.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO events (organizer_id, title, description, city, address, date, time, capacity)
             VALUES (:organizer_id, :title, :description, :city, :address, :date, :time, :capacity)"
        );

        $stmt->execute([
            ':organizer_id' => $_SESSION['user_id'],
            ':title'        => $title,
            ':description'  => $description,
            ':city'         => $city,
            ':address'      => $address,
            ':date'         => $date,
            ':time'         => $time,
            ':capacity'     => (int)$capacity,
        ]);

        $success = "Événement créé avec succès ✅";
        $title = $description = $city = $address = $date = $time = $capacity = "";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un événement</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1 class="header-title">Projet Événements</h1>
    <nav class="header-nav">
        <a href="index.php">Accueil</a>
        <a href="events_list.php">Événements</a>
        <a href="my_created_events.php">Mes événements créés</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<main class="main">
    <section class="card">
        <h2>Créer un événement</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="title">Titre :</label>
                <input type="text" name="title" id="title" required
                       value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <textarea name="description" id="description" rows="4" required><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label for="city">Ville :</label>
                <input type="text" name="city" id="city" required
                       value="<?= isset($city) ? htmlspecialchars($city) : '' ?>">
            </div>

            <div class="form-group">
                <label for="address">Adresse (optionnel) :</label>
                <input type="text" name="address" id="address"
                       value="<?= isset($address) ? htmlspecialchars($address) : '' ?>">
            </div>

            <div class="form-group">
                <label for="date">Date :</label>
                <input type="date" name="date" id="date" required
                       value="<?= isset($date) ? htmlspecialchars($date) : '' ?>">
            </div>

            <div class="form-group">
                <label for="time">Heure :</label>
                <input type="time" name="time" id="time" required
                       value="<?= isset($time) ? htmlspecialchars($time) : '' ?>">
            </div>

            <div class="form-group">
                <label for="capacity">Capacité (nombre de places) :</label>
                <input type="number" name="capacity" id="capacity" required
                       value="<?= isset($capacity) ? htmlspecialchars($capacity) : '' ?>">
            </div>

            <button type="submit" class="btn">Créer l'événement</button>
        </form>
    </section>
</main>

</body>
</html>

