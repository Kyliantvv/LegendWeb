<?php
session_start();
ob_start();

// Vérification de la session admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    ob_end_clean();
    header("Location: index.php");
    exit();
}

include 'core/logic/config.php'; // Inclure la connexion à la base de données

$conn = getDatabaseConnection(); // Connexion à la base de données

$table = $_GET['table'] ?? null;
$id = $_GET['id'] ?? null;

if ($table && $id) {
    try {
        // Détection de la clé primaire de la table
        $stmt = $conn->prepare("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
        $stmt->execute();
        $primaryKey = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($primaryKey) {
            $primaryColumn = $primaryKey['Column_name'];

            // Préparer et exécuter la requête de suppression
            $stmt = $conn->prepare("DELETE FROM $table WHERE $primaryColumn = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Succès : Rediriger avec un message
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => "L'enregistrement a été supprimé avec succès !"
                ];
            } else {
                // Échec : Rediriger avec un message d'erreur
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'message' => "Aucun enregistrement trouvé à supprimer."
                ];
            }
        } else {
            // Pas de clé primaire trouvée
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => "Impossible de trouver une clé primaire pour la table."
            ];
        }
    } catch (PDOException $e) {
        // Erreur SQL : Rediriger avec un message d'erreur
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => "Erreur lors de la suppression : " . $e->getMessage()
        ];
    }
} else {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => "Données invalides pour la suppression."
    ];
}

// Redirection vers la page du tableau de bord
header("Location: admin_dashbord.php");
exit();
?>
