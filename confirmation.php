<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['prenom'])) {
        $_SESSION['prenom_client'] = htmlspecialchars($_POST['prenom']);
    } else {
        echo "Erreur : Prénom non reçu.";
        exit();
    }

    // Vérifier que la session a bien stocké le prénom
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    // Rediriger vers la page de confirmation après 3 sec pour voir les valeurs stockées
    header("refresh:3;url=confirmation_commande.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande</title>
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Adresse de livraison :</h2>
            <form action="confirmation_commande.php" method="POST">
                <div class="nom-prenom">
                    <input type="text" name="prenom" placeholder="Prénom *" required>
                    <input type="text" name="nom" placeholder="Nom *" required>
                </div>
                
                <label>Saisissez votre adresse</label>
                <input type="text" name="adresse1" placeholder="Adresse (ligne 1) *" required>
                <input type="text" name="adresse2" placeholder="Adresse (ligne 2)">
                
                <div class="zip-city">
                    <input type="text" name="code_postal" placeholder="Code postal *" required>
                    <input type="text" name="ville" placeholder="Ville *" required>
                </div>
                
                <select name="pays" required>
                    <option value="France" selected>France</option>
                    <option value="Belgique">Belgique</option>
                    <option value="Suisse">Suisse</option>
                    <option value="Luxembourg">Luxembourg</option>
                    <option value="Canada">Canada</option>
                </select>

                <h3>Informations de contact</h3>
                <input type="email" name="email" placeholder="Email *" required>
                <input type="tel" name="telephone" placeholder="Numéro de téléphone *" required>
                
                <button type="submit" class="checkout-btn">Valider la commande</button>
            </form>
        </div>

        <div class="checkout-summary">
            <h2>Votre résumé de commande</h2>
            <div class="summary-items">
                <?php
                include 'core/logic/config.php';
                $conn = getDatabaseConnection();
                
                if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
                    echo "<p>Votre panier est vide.</p>";
                } else {
                    $ids = array_column($_SESSION['panier'], 'id');
                    if (!empty($ids)) {
                        $placeholders = implode(',', array_fill(0, count($ids), '?'));
                        $stmt = $conn->prepare("SELECT ProduitID, nom, prix, image FROM produits WHERE ProduitID IN ($placeholders)");
                        $stmt->execute($ids);
                        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                        foreach ($produits as $produit) {
                            $quantite = 1;
                            foreach ($_SESSION['panier'] as $item) {
                                if ($item['id'] == $produit['ProduitID']) {
                                    $quantite = $item['quantity'];
                                    break;
                                }
                            }
                            echo "<div class='summary-item'>
                                    <img src='assets/" . htmlspecialchars($produit['image']) . "' alt='" . htmlspecialchars($produit['nom']) . "'>
                                    <div>
                                        <p><strong>" . htmlspecialchars($produit['nom']) . "</strong></p>
                                        <p><span class='price'>" . number_format($produit['prix'], 2) . " €</span></p>
                                        <p>Quantité: " . $quantite . "</p>
                                    </div>
                                  </div>";
                        }
                    }
                }
                ?>
            </div>
            <div class="total-summary">
                <p><strong>Total : </strong><span class="price">
                <?php
                $total = 0;
                foreach ($_SESSION['panier'] as $item) {
                    foreach ($produits as $produit) {
                        if ($item['id'] == $produit['ProduitID']) {
                            $total += $produit['prix'] * $item['quantity'];
                            break;
                        }
                    }
                }
                echo number_format($total, 2) . " €";
                ?>
                </span></p>
                <p>Frais de livraison: 4,95 €</p>
                <p><strong>Total: <span class="price"><?php echo number_format($total + 4.95, 2); ?> €</span></strong></p>
            </div>
        </div>
    </div>
</body>
</html>
