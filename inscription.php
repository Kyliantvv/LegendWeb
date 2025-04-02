<?php
session_start();
ob_start(); // ⚠️ Évite les erreurs d'envoi de headers avant la redirection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sécurisation des données
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!$email) {
        echo "<script>alert('Adresse email invalide.');</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Les mots de passe ne correspondent pas.');</script>";
        exit;
    }

    include 'core/logic/config.php'; 
    $conn = getDatabaseConnection(); // Connexion DB

    if (!$conn) {
        echo "<script>alert('Erreur de connexion à la base de données.');</script>";
        exit;
    }

    try {
        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT * FROM USER WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Cet email est déjà utilisé.');</script>";
        } else {
            // Hachage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insérer l'utilisateur avec role_id = 1
            $stmt = $conn->prepare(
                "INSERT INTO USER (username, email, password, _role_id) 
                 VALUES (:username, :email, :password, 1)"
            );
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                ob_end_clean(); // ⚠️ Nettoie le buffer avant la redirection
                header('Location: index.php');
                exit();
            } else {
                echo "<script>alert('Une erreur est survenue lors de l\'inscription.');</script>";
            }
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erreur SQL : " . addslashes($e->getMessage()) . "');</script>";
    }

    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
    <div class="signup-container">
        <div class="signup-left">
            <img src="assets/f1_drivers.webp" alt="Pilotes de F1" class="background-image">
        </div>
        <div class="signup-right">
            <div class="signup-box">
                <h2>Inscription</h2>
                
                <?php if (isset($_GET['success']) && $_GET['success'] == "1") : ?>
                    <p class="success-message" style="color: green;">Inscription réussie ! Vous pouvez maintenant vous connecter.</p>
                <?php endif; ?>

                <form action="" method="POST" onsubmit="return validateForm()">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" required>

                    <label for="email">Mail :</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>

                    <label for="confirm_password">Confirmer le mot de passe :</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <input type="submit" value="S'inscrire" class="signup-btn">
                </form>
                <p id="error-msg" style="color: red;"></p>
                <div id="php-error-msg" style="color: red;"></div>
                
                <div class="extra-options">
                    <p>Vous avez déjà un compte ? <a href="index.php">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirm_password = document.getElementById("confirm_password").value;
            var error_msg = document.getElementById("error-msg");

            if (password !== confirm_password) {
                error_msg.textContent = "Les mots de passe ne correspondent pas.";
                return false;
            }
            return true;
        }
    </script>

</body>
</html>
