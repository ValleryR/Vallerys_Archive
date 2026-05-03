<?php
session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] != "admin") {
    header("Location: index.php");
    exit();
}

if (!isset($_POST["id_producto"])) {
    header("Location: admin.php");
    exit();
}

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$id_producto = intval($_POST["id_producto"]);

$sql_check = "SELECT COUNT(*) AS total FROM detalle_compra WHERE id_producto = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id_producto);
$stmt_check->execute();
$resultado_check = $stmt_check->get_result();
$total = $resultado_check->fetch_assoc()["total"];

if ($total > 0) {
    $sql_update = "UPDATE productos SET stock = 0 WHERE id_producto = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $id_producto);
    $stmt_update->execute();

    header("Location: admin.php?msg=producto_con_historial");
    exit();
} else {
    $sql_delete = "DELETE FROM productos WHERE id_producto = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_producto);
    $stmt_delete->execute();

    header("Location: admin.php?msg=producto_eliminado");
    exit();
}
?>