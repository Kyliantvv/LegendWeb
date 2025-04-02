<?php
session_start();
ob_start();
include 'core/logic/config.php';
include "core/logic/navbar.php";
$conn = getDatabaseConnection();

// Assurer que le panier est un tableau valide
if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Ajouter un produit au panier
if (isset($_POST['ajouter_panier']) && !empty($_POST['product_id']) && is_numeric($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    if ($product_id > 0) {
        $found = false;

        // Vérifier si l'article est déjà dans le panier
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity'] += 1; // Augmenter la quantité si l'article existe déjà
                $found = true;
                break;
            }
        }

        // Si l'article n'existe pas encore dans le panier, on l'ajoute
        if (!$found) {
            $_SESSION['panier'][] = ['id' => $product_id, 'quantity' => 1];
        }
    }
    header("Location: panier.php");
    exit();
}



// Modifier la quantité d'un produit
if (isset($_POST['modifier_quantite']) && !empty($_POST['product_id']) && is_numeric($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $new_quantity = max(1, intval($_POST['quantity']));

    foreach ($_SESSION['panier'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] = $new_quantity;
            break;
        }
    }
    header("Location: panier.php");
    exit();
}

// Supprimer un produit
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $product_id = intval($_GET['remove']);

    $_SESSION['panier'] = array_values(array_filter($_SESSION['panier'], function ($item) use ($product_id) {
        return $item['id'] != $product_id;
    }));
    header("Location: panier.php");
    exit();
}

// Vider le panier
if (isset($_GET['clear'])) {
    $_SESSION['panier'] = [];
    header("Location: panier.php");
    exit();
}
ob_end_flush(); 

// Récupérer les infos des produits depuis la BDD
$produits = [];
$total = 0;
$frais_port = 4.95;
if (!empty($_SESSION['panier'])) {
    $ids = array_column($_SESSION['panier'], 'id');
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $conn->prepare("SELECT ProduitID, nom, prix, image, stock FROM produits WHERE ProduitID IN ($placeholders)");
        $stmt->execute($ids);
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Calcul du total
foreach ($produits as $produit) {
    foreach ($_SESSION['panier'] as $item) {
        if ($item['id'] == $produit['ProduitID']) {
            $total += $produit['prix'] * $item['quantity'];
            break;
        }
    }
}
$total_estime = $total + $frais_port;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
    <div class="cart-container">
        <h2>Votre panier</h2>
        <div class="cart-items">
            <?php foreach ($produits as $produit) : ?>
                <div class="cart-item">
                    <img src="assets/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                    <div class="cart-details">
                        <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                        <p><strong><?= number_format($produit['prix'], 2) ?> €</strong></p>
                        <p>En stock: <?= $produit['stock'] ?></p>
                        <form action="panier.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $produit['ProduitID'] ?>">
                            <input type="number" name="quantity" value="<?= $_SESSION['panier'][array_search($produit['ProduitID'], array_column($_SESSION['panier'], 'id'))]['quantity'] ?>" min="1">
                            <button type="submit" name="modifier_quantite">Modifier</button>
                        </form>
                    </div>
                    <a href="panier.php?remove=<?= $produit['ProduitID'] ?>" class="remove-item">❌</a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="cart-summary">
            <h3>Total</h3>
            <p>Total: <?= number_format($total, 2) ?> €</p>
            <p>Frais de port: <?= number_format($frais_port, 2) ?> €</p>
            <p><strong>Total estimé: <?= number_format($total_estime, 2) ?> €</strong></p>
            <a href="confirmation.php" class="checkout-btn">Paiement</a>
        </div>
    </div>
</body>
</html>
