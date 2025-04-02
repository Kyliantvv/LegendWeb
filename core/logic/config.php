<?php
$servername = "localhost";
$dbname = "LegendWebLocal";
$dbusername = "root";
$dbpassword = "root";

function getDatabaseConnection() {
    global $servername, $dbname, $dbusername, $dbpassword;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}
?>


