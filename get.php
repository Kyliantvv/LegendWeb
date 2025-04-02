<?php
include 'core/logic/config.php';
$conn = getDatabaseConnection();

if (isset($_GET['table'])) {
    $table = $_GET['table'];
    $stmt = $conn->prepare("DESCRIBE $table");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($columns as $column) {
        if ($column != 'userID' && $column != 'ip_address') { // Exclure l'ID et l'IP
            echo "<label for='$column'>$column :</label>";
            echo "<input type='text' name='$column'><br>";
        }
    }
}
?>
