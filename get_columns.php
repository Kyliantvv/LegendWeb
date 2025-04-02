<?php
include 'core/logic/config.php';
$conn = getDatabaseConnection();

if (isset($_GET['table'])) {
    $table = $_GET['table'];
    $stmt = $conn->prepare("DESCRIBE $table");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($columns as $column) {
        if ($column != 'id') { // Ne pas afficher l'ID auto-incrémenté
            echo "<label for='$column'>$column :</label>";
            echo "<input type='text' name='$column' required><br>";
        }
    }
}
?>
