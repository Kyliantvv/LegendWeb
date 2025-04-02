<?php
session_start();
ob_start();
include 'core/logic/config.php'; // Connexion à la base de données

$conn = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['table'])) {
    $table = $_POST['table'];
    unset($_POST['table']); // Supprime 'table' pour ne pas l'insérer

    $columns = [];
    $values = [];
    $placeholders = [];

    foreach ($_POST as $key => $value) {
        if ($key === 'password' && !empty($value)) {
            $value = password_hash($value, PASSWORD_DEFAULT); // Hashage du mot de passe
        }

        if ($key !== 'ip' || !empty($value)) { // Ne pas inclure 'ip' si vide
            $columns[] = $key;
            $values[] = $value;
            $placeholders[] = '?';
        }
    }

    if (!empty($columns)) {
        $columns_str = implode(',', $columns);
        $placeholders_str = implode(',', $placeholders);

        try {
            $stmt = $conn->prepare("INSERT INTO $table ($columns_str) VALUES ($placeholders_str)");
            $stmt->execute($values);

            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Utilisateur ajouté avec succès !'];
        } catch (PDOException $e) {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Erreur : ' . $e->getMessage()];
        }
    } else {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Aucune donnée valide à insérer.'];
    }
}
ob_end_clean();
header("Location: admin_dashbord.php");
exit();
?>
