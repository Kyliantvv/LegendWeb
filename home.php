<?php
session_start();

include 'core/logic/config.php'; // Inclure la connexion à la base de données
$conn = getDatabaseConnection();

include "core/logic/navbar.php";

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
    <div class="img_container_home">
        <img src="assets/back_img_2.png" alt="imagef1defond">
    </div>

    <div class="home_title">
        <h2>PUMA Scuderia Ferrari</h2>
        <p>La collection officielle 2025</p>
        <a href="ecurie.php?ecurie=Scuderia Ferrari">
            <button>Acheter maintenant</button>
        </a>
    </div>

    <div class="teams-container">
        <?php
        $teams = [
            ["Ferrari", "ferrari.png", "ecurie.php?ecurie=Scuderia Ferrari"],
            ["Mercedes", "mercedes.png", "ecurie.php?ecurie=Mercedes"],
            ["McLaren", "mclaren.png", "ecurie.php?ecurie=McLaren"],
            ["Red Bull", "redbull.png", "ecurie.php?ecurie=Red Bull"],
            ["Aston Martin F1", "aston.png", "ecurie.php?ecurie=Aston Martin"],
            ["Williams", "williams.png", "ecurie.php?ecurie=Williams"],
            ["Alpine F1", "alpine.png", "ecurie.php?ecurie=Alpine"],
            ["Haas F1", "haas.png", "ecurie.php?ecurie=Haas"]
        ];
        foreach ($teams as $team) {
            echo "<div class='team'>
                    <a href='{$team[2]}'>
                        <img src='assets/{$team[1]}' alt='{$team[0]}'>
                        <p>{$team[0]}</p>
                    </a>
                  </div>";
        }
        ?>
    </div>

    <h1 class="title-product">SOUTENEZ VOTRE ÉQUIPE. CHOISISSEZ VOS COULEURS</h1>
    <div class="products-container">
        <?php
        // Récupérer les produits sélectionnés depuis la base de données
        $query = "SELECT p.ProduitID, p.nom, p.prix, p.image FROM produits p
                  JOIN produits_home ps ON p.ProduitID = ps.produit_id";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($produits as $produit) {
            $imagePath = (strpos($produit['image'], 'http') === 0) ? $produit['image'] : "assets/" . $produit['image'];
            $produitID = htmlspecialchars($produit['ProduitID']);
            echo "<div class='produit-home'>
                    <a href='product_details.php?id=" . $produitID . "'>
                        <img src='" . htmlspecialchars($imagePath) . "' alt='" . htmlspecialchars($produit['nom']) . "'>
                        <h3>" . htmlspecialchars($produit['nom']) . "</h3>
                    </a>
                    <p>" . number_format($produit['prix'], 2, ',', ' ') . " €</p>
                  </div>";
        }
        
        ?>
    </div>

<?php include 'core/logic/footer.php'; ?>
</body>
</html>
