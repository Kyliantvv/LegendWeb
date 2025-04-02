<?php
session_start();
include 'core/logic/config.php';
include 'core/logic/functions.php'; // Inclusion des fonctions

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['userID'])) {
    header('Location: index.php'); // Si non connecté, rediriger vers la page de connexion
    exit();
}

$userID = $_SESSION['userID']; // Récupérer le userID depuis la session

// Connexion à la base de données
$conn = getDatabaseConnection();

try {
    // Récupérer les informations de l'utilisateur
    $stmt = $conn->prepare("SELECT * FROM USER WHERE userID = :userID");
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        showAlert("Utilisateur introuvable.", "error");
        exit();
    }

    // Initialiser les variables avec les données de l'utilisateur
    $username = $user['username'];
    $email = $user['email'];
    $password = ''; // Le mot de passe ne sera pas pré-rempli
} catch (PDOException $e) {
    showAlert("Erreur : " . $e->getMessage(), "error");
}

// Mise à jour du profil si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['logout'])) {
        // Déconnexion de l'utilisateur
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }

    $newusername = htmlspecialchars(trim($_POST['username']));
    $newemail = htmlspecialchars(trim($_POST['email']));
    $newpassword = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    try {
        // Préparer la requête pour mettre à jour les informations
        $sql = "UPDATE USER SET username = :username, email = :email" . ($newpassword ? ", password = :password" : "") . " WHERE userID = :userID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $newusername);
        $stmt->bindParam(':email', $newemail);
        if ($newpassword) {
            $stmt->bindParam(':password', $newpassword);
        }
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Mise à jour réussie
            $_SESSION['username'] = $newusername;
            $_SESSION['email'] = $newemail;
            showAlert("Profil mis à jour avec succès.", "success");
            header("Location: home.php");
            exit();
        } else {
            showAlert("Aucune modification n'a été effectuée.", "error");
        }
    } catch (PDOException $e) {
        showAlert("Erreur : " . $e->getMessage(), "error");
    }
}

// Fermer la connexion
$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
<?php include "core/logic/navbar.php"; ?>
<div class="container_center">
    <div class="profil-container">
        <h2>Modifier votre profil</h2>
        <form method="POST" action="">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="Nouveau mot de passe">

            <input type="submit" value="Mettre à jour">
        </form>

        <!-- Formulaire de déconnexion -->
        <form method="POST" action="">
            <button type="submit" name="logout" class="logout-btn">Se déconnecter</button>
        </form>
    </div>
</div>
</body>
</html>
