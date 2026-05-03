<?php
session_start();

if (!isset($_POST["id_producto"]) || !isset($_POST["cantidad"])) {
    header("Location: carrito.php");
    exit();
}

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$id_producto = intval($_POST["id_producto"]);
$cantidad = intval($_POST["cantidad"]);

if ($cantidad <= 0) {
    unset($_SESSION["carrito"][$id_producto]);
    header("Location: carrito.php");
    exit();
}

$sql = "SELECT stock FROM productos WHERE id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $producto = $resultado->fetch_assoc();
    $stock = intval($producto["stock"]);

    if ($cantidad > $stock) {
        $cantidad = $stock;
    }

    if ($cantidad > 0) {
        $_SESSION["carrito"][$id_producto] = $cantidad;
    } else {
        unset($_SESSION["carrito"][$id_producto]);
    }
}

header("Location: carrito.php");
exit();
?>