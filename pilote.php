<?php
session_start();

include 'core/logic/config.php'; // Inclure la connexion à la base de données
$conn = getDatabaseConnection();

include 'core/logic/navbar.php';

$pilote = isset($_GET['pilote']) ? $_GET['pilote'] : '';

try {
    // exécuter la requête
    $sql = "SELECT * FROM produits WHERE pilote = :pilote";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pilote', $pilote, PDO::PARAM_STR);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des produits : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits de <?php echo htmlspecialchars($pilote); ?></title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
    <h1 class="title-pilote">Achetez des articles officiels de F1 <?php echo htmlspecialchars($pilote); ?></h1>
    <div class="produits">
    <?php if (count($produits) > 0) { ?>
        <?php foreach ($produits as $row) { ?>
            <div class="produit">
                <img src="assets/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['nom']); ?>">
                <h2><a href="product_details.php?id=<?php echo $row['ProduitID']; ?>" class="product-link"><?php echo htmlspecialchars($row['nom']); ?></a></h2>
                <p><?php echo htmlspecialchars($row['description_courte']); ?></p>
                <p><strong><?php echo number_format($row['prix'], 2, ',', ' '); ?> €</strong></p>

                <!-- Formulaire pour ajouter au panier -->
                <form action="panier.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $row['ProduitID']; ?>">
                    <button type="submit" name="ajouter_panier" class="btn-ajouter-panier">Ajouter au panier</button>
                </form>

            </div>
        <?php } ?>
    <?php } else { ?>
        <p>Aucun produit trouvé pour ce pilote.</p>
    <?php } ?>
</div>

</body>
</html>
