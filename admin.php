<?php
// Démarrage de la session
session_start();
ob_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    include 'core/logic/config.php';
    $conn = getDatabaseConnection();

    try {
        // Vérifier si l'email existe et obtenir l'ID de rôle
        $stmt = $conn->prepare("SELECT * FROM USER WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vérifier si le mot de passe est correct
            if (password_verify($password, $user['password'])) {
                // Vérifier si le rôle est celui d'un admin (role_id = 2)
                if ($user['_role_id'] == 2) {
                    // Démarrer la session admin et rediriger vers home_admin.php
                    $_SESSION['user_id'] = $user['userID'];  // Vous pouvez stocker d'autres informations dans la session si nécessaire
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role_id'] = $user['_role_id']; // Enregistrer le role_id dans la session
                    ob_end_clean();
                    header('Location: admin_dashbord.php');
                    exit();
                } else {
                    echo "<script>document.getElementById('error-msg').innerHTML = 'Vous n\'êtes pas un administrateur.'; document.getElementById('error-msg').style.color = 'red';</script>";
                }
            } else {
                echo "<script>document.getElementById('error-msg').innerHTML = 'Mot de passe incorrect.'; document.getElementById('error-msg').style.color = 'red';</script>";
            }
        } else {
            echo "<script>document.getElementById('error-msg').innerHTML = 'Aucun utilisateur trouvé avec cet email.'; document.getElementById('error-msg').style.color = 'red';</script>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
<div class="container_center">
    <div class="login-container">
        <h2>Connexion Admin</h2>
        <form action="" method="POST">
            <label for="email">Mail : </label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe : </label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Se connecter">
        </form>
        <p id="error-msg"></p>
        <div class="extra-options">
            <p>T'es pas admin ? <a href="index.php">Se connecter</a></p>
        </div>
    </div>
</div>
</body>
</html>
