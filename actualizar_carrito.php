<?php
session_start();

if (!isset($_POST["id_producto"]) || !isset($_POST["cantidad"])) {
    header("Location: carrito.php");
    exit();
}

$id = $_POST["id_producto"];
$cantidad = intval($_POST["cantidad"]);

if ($cantidad <= 0) {
    unset($_SESSION["carrito"][$id]);
} else {
    $_SESSION["carrito"][$id] = $cantidad;
}

header("Location: carrito.php");
exit();
?>