<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pit Stop Store</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link rel="stylesheet" href="../style/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js" crossorigin="anonymous"></script>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">

</head>
<body>
    <div class="menu-bar">
        <a href="home.php"><img src="assets/logo.png" alt="Logo"></a>
        <button class="menu-toggle" id="menu-toggle">&#9776;</button>
        <ul>
            <li><a href="#">Écuries</a>
                <div class="dropdown-menu">
                    <ul>
                        <li><a href="ecurie.php?ecurie=Scuderia Ferrari">Scuderia Ferrari</a></li>
                        <li><a href="ecurie.php?ecurie=Mercedes">Mercedes-AMG F1 Team</a></li>
                        <li><a href="ecurie.php?ecurie=Red Bull">Red Bull Racing</a></li>
                        <li><a href="ecurie.php?ecurie=McLaren">McLaren F1 Team</a></li>
                        <li><a href="ecurie.php?ecurie=Aston Martin">Aston Martin F1 Team</a></li>
                        <li><a href="ecurie.php?ecurie=Williams">Williams Racing</a></li>
                        <li><a href="ecurie.php?ecurie=Racing Bulls">Racing Bulls RB</a></li>
                        <li><a href="ecurie.php?ecurie=Alpine">Alpine F1 Team</a></li>
                        <li><a href="ecurie.php?ecurie=Haas">Haas F1 Team</a></li>
                        <li><a href="ecurie.php?ecurie=Kick">Kick Sauber</a></li>
                    </ul>
                </div>
            </li>
            <li><a href="#">Pilotes</a>
                <div class="dropdown-menu">
                    <ul>
                        <li><a href="pilote.php?pilote=Charles Leclerc">Charles Leclerc</a></li>
                        <li><a href="pilote.php?pilote=Lewis Hamilton">Lewis Hamilton</a></li>
                        <li><a href="pilote.php?pilote=George Russell">George Russell</a></li>
                        <li><a href="pilote.php?pilote=Lando Norris">Lando Norris</a></li>
                        <li><a href="pilote.php?pilote=Oscar Piastri">Oscar Piastri</a></li>
                        <li><a href="pilote.php?pilote=Max Verstappen">Max Verstappen</a></li>
                        <li><a href="pilote.php?pilote=Fernando Alonso">Fernando Alonso</a></li>
                    </ul></li>
            <li><a href="collections.php">Collections</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="icons">
            <a href="compte.php"><i class="fa-solid fa-user"></i></a>
            <a href="panier.php" class="cart-link">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-count"><?= array_sum(array_column($_SESSION['panier'] ?? [], 'quantity')) ?: 0 ?></span>
            </a>

        </div>
    </div>

    <nav class="mobile-nav" id="mobile-nav">
        <a href="#">Écuries</a>
        <a href="#">Pilotes</a>
        <a href="#">Collections</a>
        <a href="#">Contact</a>
    </nav>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileNav = document.getElementById('mobile-nav');

        menuToggle.addEventListener('click', () => {
            mobileNav.classList.toggle('active');
        });

        $(document).ready(function() {
            function updateCartCount() {
                $.ajax({
                    url: "cart_count.php",
                    type: "GET",
                    success: function(response) {
                        $(".cart-count").text(response);
                    }
                });
            }

            updateCartCount(); // Mise à jour au chargement de la page

            $(document).on("click", ".quantity-btn", function() {
                setTimeout(updateCartCount, 500); // Met à jour après modification du panier
            });
        });


    </script>
</body>
</html>

