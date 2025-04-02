<?php
session_start();
include 'core/logic/config.php'; // Connexion à la base de données
$conn = getDatabaseConnection();

// Vérifier l'ID passé en paramètre GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Produit introuvable.";
    exit;
}

// Récupérer les infos du produit
$stmt = $conn->prepare("SELECT * FROM produits WHERE ProduitID = :id LIMIT 1");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Produit introuvable.";
    exit;
}

// Récupérer les images associées
$stmtImages = $conn->prepare("SELECT image_url FROM images WHERE produit_id = :id");
$stmtImages->bindParam(':id', $id, PDO::PARAM_INT);
$stmtImages->execute();
$images = $stmtImages->fetchAll(PDO::FETCH_ASSOC);

// Déterminer l'image principale (la première ou une image par défaut)
$mainImage = count($images) > 0 ? $images[0]['image_url'] : 'placeholder.jpg';

// Calcul du stock
$stock = (int)$product['stock'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nom']); ?></title>
    <!-- Ton CSS personnalisé -->
    <link rel="stylesheet" href="core/style/style.css">
    <link rel="icon" type="image/png" href="/e5/Assets/faviCO.png">
</head>
<body>

    <?php include "core/logic/navbar.php"; ?>

    <div class="CenterProduitDetails">
        <div class="product-details-wrapper">
            <!-- Galerie produit -->
            <div class="pd-gallery">
                <div class="pd-thumbnail-column">
                    <?php foreach ($images as $index => $img) : ?>
                        <img 
                            src="assets/<?php echo htmlspecialchars($img['image_url']); ?>" 
                            class="pd-thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                            onclick="changeImage(this)">
                    <?php endforeach; ?>
                </div>
                <div class="pd-main-image">
                    <img 
                        src="assets/<?php echo htmlspecialchars($mainImage); ?>" 
                        id="mainImage" 
                        alt="<?php echo htmlspecialchars($product['nom']); ?>">
                </div>
            </div>

            <!-- Infos produit -->
            <div class="pd-product-info">
                <h1 class="product-title">
                    <?php echo htmlspecialchars($product['nom']); ?>
                </h1>

                <p class="product-price">
                    <?php 
                        // Formatage du prix : 2 décimales, virgule, espace
                        echo number_format($product['prix'], 2, ',', ' ') . ' €'; 
                    ?>
                </p>

                <!-- Affichage du stock selon différentes conditions -->
                <?php if ($stock < 1): ?>
                    <p class="product-status" style="color:red;">Rupture de stock</p>
                <?php elseif ($stock < 15): ?>
                    <p class="product-status" style="color:red;">Stock : <?php echo $stock; ?></p>
                <?php else: ?>
                    <p class="product-status">Stock : <?php echo $stock; ?></p>
                <?php endif; ?>

                <!-- Sélection de la quantité (bloquée par le stock) -->
                <div class="pd-qty-controls">
                    <button type="button" class="pd-qty-btn" onclick="decrementQty()">-</button>
                    <input 
                        type="number" 
                        id="quantity" 
                        class="pd-qty-input"
                        value="1" 
                        min="1" 
                        max="<?php echo $stock; ?>" 
                        <?php if ($stock < 1) echo 'disabled'; ?> 
                    >
                    <button type="button" class="pd-qty-btn" onclick="incrementQty()">+</button>
                </div>

                <!-- Bouton Ajouter au panier (désactivé si stock épuisé) -->

                <form action="panier.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                    <button type="submit" name="ajouter_panier" class="btn-ajouter-panier">Ajouter au panier</button>
                </form>

                

                <!-- Description courte -->
                <div class="pd-product-description">
                    <h3>Description</h3>
                    <p>
                        <?php 
                            echo nl2br(htmlspecialchars($product['description'])); 
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Changer l'image principale lors d'un clic sur une miniature
    function changeImage(element) {
        document.getElementById('mainImage').src = element.src;
        document.querySelectorAll('.pd-thumbnail').forEach(img => {
            img.classList.remove('active');
        });
        element.classList.add('active');
    }

    // Limiter la quantité max au stock
    var maxStock = <?php echo $stock; ?>;

    function decrementQty() {
        let qtyField = document.getElementById('quantity');
        let currentVal = parseInt(qtyField.value);
        if (currentVal > 1) {
            qtyField.value = currentVal - 1;
        }
    }

    function incrementQty() {
        let qtyField = document.getElementById('quantity');
        let currentVal = parseInt(qtyField.value);
        if (currentVal < maxStock) {
            qtyField.value = currentVal + 1;
        }
    }

    // Ajouter au panier + Rediriger vers panier
    function ajouterAuPanier(productID) {
        let quantity = parseInt(document.getElementById('quantity').value);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'ajouter_panier.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Dès que c'est ajouté, on redirige vers le panier
                window.location.href = 'panier.php';
            }
        };
        
        xhr.send('id=' + productID + '&quantity=' + quantity);
    }
    </script>
</body>
</html>
