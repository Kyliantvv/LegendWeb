<?php

/**
 * @param string $message Le message à afficher.
 * @param string $type Le type de message ('success' pour vert, 'error' pour rouge).
 */
function showAlert($message, $type = 'success') {
    $color = $type === 'success' ? 'green' : 'red';
    echo "<p style='color: $color; font-weight: bold; text-align: center;'>$message</p>";
}
