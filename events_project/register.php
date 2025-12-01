<?php
session_start();
require_once 'config.php';

$errors = [];
$success = "";

// Si le formulaire est soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') {
        $errors[] = "Le nom est obligatoire.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email est invalide.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existing = $stmt->fetch();

        if ($existing) {
            $errors[] = "Cet email est déjà utilisé.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (email, password_hash, name, role)
                 VALUES (:email, :password_hash, :name, :role)"
            );

            $stmt->execute([
                ':email'         => $email,
                ':password_hash' => $passwordHash,
                ':name'          => $name,
                ':role'          => 'user'
            ]);

            $success = "Compte créé avec succès ! Tu peux maintenant te connecter.";
            $name = $email = "";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Projet Événements</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1 class="header-title">Projet Événements</h1>
    <nav class="header-nav">
        <a href="index.php">Accueil</a>
        <a href="events_list.php">Événements</a>
        <a href="login.php">Connexion</a>
    </nav>
</header>

<main class="main">
    <section class="card">
        <h2>Créer un compte</h2>

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
                <label for="name">Nom :</label>
                <input type="text" name="name" id="name" required
                       value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" required
                       value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" class="btn">Créer mon compte</button>
        </form>

        <p class="meta" style="margin-top:10px;">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>
    </section>
</main>

</body>
</html>

