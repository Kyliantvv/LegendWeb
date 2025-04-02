<?php
session_start();

include 'core/logic/config.php';
$conn = getDatabaseConnection();
include 'core/logic/navbar.php';

// Nombre d'articles à afficher par défaut
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

try {
    // Requête pour récupérer les produits avec une limite
    $sql = "SELECT * FROM produits LIMIT :limit";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
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
    <title>Liste des Produits</title>
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
    <h1 class="title-pilote">Découvrez nos produits officiel F1</h1>
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
        <p>Aucun produit disponible.</p>
    <?php } ?>
</div>

<!-- Bouton Afficher Plus -->
<div class="load-more-container">
    <button id="load-more" class="btn-load-more">Afficher plus</button>
</div>

<script>
    document.getElementById("load-more").addEventListener("click", function() {
        let currentLimit = <?php echo $limit; ?>;
        let newLimit = currentLimit + 10;
        window.location.href = "?limit=" + newLimit;
    });
</script>
</body>
</html>
