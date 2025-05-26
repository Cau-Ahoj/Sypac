<?php
session_start();
if (!isset($_SESSION['verified'])) {
    die("Neoprávněný přístup.");
}
?>

<form action="save-password.php" method="post">
    <h2>Zadej nové heslo</h2>
    <input type="password" name="password" required placeholder="Nové heslo">
    <input type="password" name="confirm" required placeholder="Potvrď heslo">
    <button type="submit">Změnit heslo</button>
</form>
