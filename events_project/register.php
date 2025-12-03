<?php
session_start();
require_once 'config.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $age      = trim($_POST['age'] ?? '');

    // VALIDATION
    if ($name === '')         $errors[] = "Le nom est obligatoire.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if (strlen($password) < 6) $errors[] = "Mot de passe trop court (6 caractères min).";

    if (!ctype_digit($age) || (int)$age < 1 || (int)$age > 120) {
        $errors[] = "L’âge doit être un nombre entre 1 et 120.";
    }

    // Vérifier email déjà utilisé
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé.";
        }
    }

    // INSCRIPTION
    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (email, password_hash, name, age, role)
            VALUES (:email, :password_hash, :name, :age, 'user')
        ");

        $stmt->execute([
            ':email'         => $email,
            ':password_hash' => $passwordHash,
            ':name'          => $name,
            ':age'           => $age
        ]);

        $success = "Compte créé avec succès ! Vous pouvez vous connecter.";
        $name = $email = $age = "";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1 class="header-title">Projet Événements</h1>
    <nav class="header-nav">
        <a href="index.php">Accueil</a>
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

        <form method="post">

            <div class="form-group">
                <label>Nom :</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($name ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Email :</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Âge :</label>
                <input type="number" name="age" required min="1" max="120"
                       value="<?= htmlspecialchars($age ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Mot de passe :</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn">Créer mon compte</button>

        </form>

    </section>
</main>

</body>
</html>


