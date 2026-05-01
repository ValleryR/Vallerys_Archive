<?php
session_start();

if (isset($_POST["id_producto"])) {
    $id = $_POST["id_producto"];
    unset($_SESSION["carrito"][$id]);
}

header("Location: carrito.php");
exit();
?>