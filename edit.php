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
$conn = getDatabaseConnection();

// Vérifier les paramètres GET
if (!isset($_GET['table'], $_GET['id']) || empty($_GET['table']) || empty($_GET['id'])) {
    die("Paramètres invalides.");
}

$table = htmlspecialchars($_GET['table']);
$id = htmlspecialchars($_GET['id']);

// Récupérer les données existantes
try {
    $stmt = $conn->prepare("SELECT * FROM $table WHERE userID = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Enregistrement non trouvé.");
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Mettre à jour les données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $params = [];

    foreach ($_POST as $key => $value) {
        $updates[] = "$key = :$key";
        $params[$key] = htmlspecialchars($value);
    }

    $params['userID'] = $id; // Ajouter userID pour la condition WHERE

    try {
        $sql = "UPDATE $table SET " . implode(", ", $updates) . " WHERE userID = :userID";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        // Ajouter une alerte de succès dans la session
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => "Les modifications ont été enregistrées avec succès dans la table $table."
        ];

        // Redirection vers le dashboard
        header("Location: admin_dashbord.php");
        exit();
    } catch (PDOException $e) {
        // Ajouter une alerte d'erreur dans la session
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => "Erreur lors de la mise à jour : " . $e->getMessage()
        ];

        // Redirection vers le dashboard
        header("Location: admin_dashbord.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un enregistrement</title>
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
    <div class="edit-container">
        <div class="edit-header">
            Modifier l'enregistrement dans la table <?= htmlspecialchars($table) ?>
        </div>
        <form class="edit-form" method="POST">
            <?php foreach ($row as $column => $value): ?>
                <label for="<?= $column ?>"><?= $column ?></label>
                <input type="text" id="<?= $column ?>" name="<?= $column ?>" value="<?= htmlspecialchars($value) ?>" required>
            <?php endforeach; ?>
            <div class="edit-button">
                <button type="submit">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</body>
</html>
