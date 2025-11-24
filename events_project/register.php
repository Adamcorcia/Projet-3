<?php
session_start();
require_once 'config.php';

$errors = [];
$success = "";

// Si le formulaire est soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Récupération des données du formulaire
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 2. Petites vérifications
    if ($name === '') {
        $errors[] = "Le nom est obligatoire.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email est invalide.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // 3. Si pas d'erreur, on regarde si l'email existe déjà
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existing = $stmt->fetch();

        if ($existing) {
            $errors[] = "Cet email est déjà utilisé.";
        } else {
            // 4. On hash le mot de passe
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // 5. On insère en BDD
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
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Projet événements</title>
</head>
<body>
    <h1>Inscription</h1>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="color: green;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div>
            <label for="name">Nom :</label>
            <input type="text" name="name" id="name" required
                   value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
        </div>

        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required
                   value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>

        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit">Créer mon compte</button>
    </form>

    <p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>
