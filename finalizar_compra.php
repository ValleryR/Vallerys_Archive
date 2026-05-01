<?php
session_start();

if (!isset($_SESSION["id_usuario"]) || empty($_SESSION["carrito"])) {
    header("Location: carrito.php");
    exit();
}

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$id_usuario = $_SESSION["id_usuario"];
$carrito = $_SESSION["carrito"];

$total = 0;
$productos = [];

$ids = implode(",", array_keys($carrito));
$sql = "SELECT * FROM productos WHERE id_producto IN ($ids)";
$resultado = $conn->query($sql);

while ($producto = $resultado->fetch_assoc()) {
    $id = $producto["id_producto"];
    $cantidad = $carrito[$id];

    $subtotal = $producto["precio"] * $cantidad;
    $total += $subtotal;

    $producto["cantidad"] = $cantidad;
    $productos[] = $producto;
}

$conn->begin_transaction();

try {

    // 1. insertar compra
    $stmt = $conn->prepare("INSERT INTO compras (id_usuario, total) VALUES (?, ?)");
    $stmt->bind_param("id", $id_usuario, $total);
    $stmt->execute();

    $id_compra = $conn->insert_id;

    // 2. detalle + actualizar stock
    foreach ($productos as $producto) {

        $stmt = $conn->prepare("INSERT INTO detalle_compra (id_compra, id_producto, cantidad, precio) VALUES (?, ?, ?, ?)");
        $stmt->bind_param(
            "iiid",
            $id_compra,
            $producto["id_producto"],
            $producto["cantidad"],
            $producto["precio"]
        );
        $stmt->execute();

        // actualizar stock
        $stmt = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
        $stmt->bind_param("ii", $producto["cantidad"], $producto["id_producto"]);
        $stmt->execute();
    }

    $conn->commit();

    // vaciar carrito
    $_SESSION["carrito"] = [];

    header("Location: cuenta.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    echo "Error al procesar la compra";
}
?>