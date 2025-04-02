<?php
ob_start(); // Démarre la mise en tampon de sortie
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    include 'core/logic/config.php';
    
    $conn = getDatabaseConnection(); // Obtenir la connexion

    // Fonction pour récupérer l'adresse IP de l'utilisateur
    function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM USER WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
                // Enregistrer le username et userID dans la session
                $_SESSION['username'] = $user['username'];
                $_SESSION['userID'] = $user['userID'];

                // Récupérer l'IP de l'utilisateur
                $ip = getUserIP();

                // Mettre à jour l'IP dans la base de données
                $updateStmt = $conn->prepare("UPDATE USER SET ip_address = :ip WHERE userID = :id");
                $updateStmt->execute(['ip' => $ip, 'id' => $user['userID']]);

                if (!headers_sent()) {
                    header("Location: home.php");
                    exit();
                } else {
                    die("Erreur: Les en-têtes ont déjà été envoyés.");
                }
            } else {
                $errorMsg = "Mot de passe incorrect";
            }
        } else {
            if (!headers_sent()) {
                header("Location: inscription.php");
                exit();
            } else {
                die("Erreur: Les en-têtes ont déjà été envoyés.");
            }
        }
    } catch (PDOException $e) {
        $errorMsg = "Une erreur est survenue. Réessayez plus tard.";
    }
}
ob_end_flush(); // Vide la sortie tamponnée
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <img src="assets/f1_drivers.webp" alt="Pilotes de F1" class="background-image">
        </div>
        <div class="login-right">
            <div class="login-box">
                <h2>Connexion</h2>
                <form action="" method="POST">
                    <label for="email">Mail :</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>

                    <input type="submit" value="Se connecter" class="login-btn">
                </form>
                <div id="php-error-msg">
                    <?php
                    if (isset($errorMsg)) {
                        echo "<p style='color: red;'>$errorMsg</p>";
                    }
                    ?>
                </div>
                <div class="extra-options">
                    <p><a href="inscription.php">Créer un compte</a></p>
                    <p><a href="forgot_password.php">Mot de passe oublié ?</a></p>
                </div>
            </div>
        </div>    
    </div>
</body>
</html>
