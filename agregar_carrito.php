<?php
session_start();

if (!isset($_POST["id_producto"])) {
    header("Location: bags.php");
    exit();
}

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$id_producto = intval($_POST["id_producto"]);

$sql = "SELECT stock FROM productos WHERE id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: bags.php");
    exit();
}

$producto = $resultado->fetch_assoc();
$stock = intval($producto["stock"]);

if ($stock <= 0) {
    header("Location: carrito.php");
    exit();
}

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

$cantidad_actual = $_SESSION["carrito"][$id_producto] ?? 0;

if ($cantidad_actual < $stock) {
    $_SESSION["carrito"][$id_producto] = $cantidad_actual + 1;
}

header("Location: carrito.php");
exit();
?>