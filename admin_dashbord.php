<?php
session_start();

// Vérification de la session admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

include 'core/logic/config.php';  // Inclure la connexion à la base de données

$conn = getDatabaseConnection();  // Connexion à la base de données
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur</title>
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>

<?php if (isset($_SESSION['alert'])): ?>
    <div class="alert <?= htmlspecialchars($_SESSION['alert']['type']) ?>">
        <?= htmlspecialchars($_SESSION['alert']['message']) ?>
    </div>
    <?php unset($_SESSION['alert']); // Supprime l'alerte après affichage ?>
<?php endif; ?>


<div class="container_center_dash">
    <div class="dashboard-container">
        <h2>Dashboard Administrateur</h2>

        <?php
        try {
            // Récupérer toutes les tables
            $stmt = $conn->prepare("SHOW TABLES");
            $stmt->execute();
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Affichage des tables sauf 'ROLE'
            foreach ($tables as $table) {
                if ($table != 'ROLE') {  // Exclure la table ROLE
                    echo "<h3>Table : $table</h3>";

                    // Récupérer les données de la table courante
                    $stmt = $conn->prepare("SELECT * FROM $table");
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($rows) > 0) {
                        echo "<table>";
                        echo "<tr>";

                        // Afficher les entêtes de colonne
                        foreach ($rows[0] as $column => $value) {
                            echo "<th>$column</th>";
                        }
                        echo "<th>Actions</th>";
                        echo "</tr>";

                        // Afficher les données des lignes
                        foreach ($rows as $row) {
                            echo "<tr>";


                            foreach ($row as $column => $value) {
                                echo "<td>$value</td>";
                            }

                            // Utiliser userID si la table est USER
                            $row_id = ($table == 'USER') ? $row['userID'] : (isset($row['id']) ? $row['id'] : null);

                            if ($row_id) {
                                echo "<td class='actions'>
                                        <a href='edit.php?table=$table&id=$row_id'>Modifier</a> | 
                                        <a href='delete.php?table=$table&id=$row_id' class='delete'>Supprimer</a>
                                      </td>";
                            } else {
                                echo "<td class='no-data'>ID non disponible</td>";
                            }

                            echo "</tr>";
                        }

                        echo "</table>";
                    } else {
                        echo "<p class='no-data'>Aucune donnée dans la table $table.</p>";
                    }
                }
            }

        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }

        $conn = null;  // plus de bdd
        ?>

        <h3>Ajouter des données</h3>
        <form method="POST" action="add.php">
            <label for="table">Choisir une table :</label>
            <select name="table" id="table" required>
                <?php
                foreach ($tables as $table) {
                    if ($table != 'ROLE') {
                        echo "<option value='$table'>$table</option>";
                    }
                }
                ?>
            </select>
            
            <div id="dynamic-fields"></div>

            <button class="btn-add-data" type="submit">Ajouter</button>
        </form>

        <script>
        document.getElementById("table").addEventListener("change", function() {
            var table = this.value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_columns.php?table=" + table, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("dynamic-fields").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });
        </script>

    </div>
</div>

</body>
</html>
