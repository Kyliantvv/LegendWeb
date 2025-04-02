<?php
session_start();

// Vider le panier après la confirmation de la commande
unset($_SESSION['panier']);

$prenom = isset($_SESSION['prenom_client']) ? htmlspecialchars($_SESSION['prenom_client']) : 'cher client';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande</title>
    <link rel="stylesheet" href="core/style/style.css">
    <script>
        let timeLeft = 5;
        function countdown() {
            if (timeLeft > 0) {
                document.getElementById("countdown").innerHTML = timeLeft;
                timeLeft--;
                setTimeout(countdown, 1000);
            } else {
                window.location.href = "home.php";
            }
        }
        window.onload = countdown;
    </script>
</head>
<body>
    <div class="confirmation-container">
        <h1 class="merci_commande">Merci <?php echo $prenom; ?> pour votre commande !</h1>
        <p class="p_commande">Vous allez être redirigé dans <span id="countdown" class="countdown">5</span> secondes...</p>
    </div>
</body>
</html>
