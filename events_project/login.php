<?php
session_start();
require_once 'config.php';

$errors = [];

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }

    if ($password === '') {
        $errors[] = "Mot de passe obligatoire.";
    }

    if (empty($errors)) {
        // On cherche l'utilisateur en BDD
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = "Aucun compte avec cet email.";
        } else {
            // On vérifie le mot de passe
            if (!password_verify($password, $user['password_hash'])) {
                $errors[] = "Mot de passe incorrect.";
            } else {
                // Connexion OK → on stocke les infos en session
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirection vers l'accueil (pour l'instant)
                header('Location: index.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Projet événements</title>
</head>
<body>
    <h1>Connexion</h1>

    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required
                   value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>

        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit">Se connecter</button>
    </form>

    <p>
        <a href="register.php">Créer un compte</a> |
        <a href="index.php">Retour à l'accueil</a>
    </p>
</body>
</html>
