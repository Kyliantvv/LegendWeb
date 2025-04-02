<?php
session_start();
include 'core/logic/config.php';
include "core/logic/navbar.php";
$conn = getDatabaseConnection();

$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = htmlspecialchars($_POST['name']);
    $email_utilisateur = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $to = "contact@legendweb.fr";
    $subject = "Nouveau message du formulaire de contact";

    $headers = "From: contact@legendweb.fr\r\n";
    $headers .= "Reply-To: $email_utilisateur\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $body = "Nom: $nom\n";
    $body .= "Email: $email_utilisateur\n";
    $body .= "Message:\n$message\n";

    if (mail($to, $subject, $body, $headers)) {
        $success = true;
    } else {
        $error = "Erreur lors de l'envoi. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="core/style/style.css">
</head>
<body>
    <div class="contact-container">
        <h2>Contactez-nous</h2>

        <?php if ($success): ?>
            <div class="success-container">
                <p class="success-message">Message envoyé avec succès !</p>
                <p class="redirect-message">Vous serez redirigé vers la page d'accueil dans <span id="countdown">5</span> secondes...</p>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    let count = 5;
                    const countdown = setInterval(function () {
                        count--;
                        document.getElementById("countdown").textContent = count;
                        if (count <= 0) {
                            clearInterval(countdown);
                            document.body.classList.add("fade-out");
                            setTimeout(function () {
                                window.location.href = "home.php";
                            }, 1000);
                        }
                    }, 1000);
                });
            </script>
        <?php else: ?>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form action="contact.php" method="POST">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>

                <button type="submit" class="checkout-btn">Envoyer</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
