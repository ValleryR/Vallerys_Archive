<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$id_usuario = $_SESSION["id_usuario"];

$sql = "SELECT nombre, email, fecha_registro FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$fecha_registro = new DateTime($usuario["fecha_registro"]);
$hoy = new DateTime();
$diferencia = $fecha_registro->diff($hoy);

if ($diferencia->y > 0) {
    $tiempo_usuario = $diferencia->y . " año(s)";
} elseif ($diferencia->m > 0) {
    $tiempo_usuario = $diferencia->m . " mes(es)";
} elseif ($diferencia->d > 0) {
    $tiempo_usuario = $diferencia->d . " día(s)";
} else {
    $tiempo_usuario = "hoy";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi cuenta</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<header class="header">

    <div class="top-bar">

        <div class="logo">
            <a href="index.php">Vallery's Archive</a>
        </div>

        <div class="nav-right">

            <a href="cuenta.php" class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="1.5">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 20c2-4 6-6 8-6s6 2 8 6"/>
                </svg>
            </a>

            <a href="carrito.php" class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="1.5">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.5 12h11l2-8H6"/>
                </svg>
            </a>

        </div>

    </div>

    <nav class="menu">

        <div class="menu-left">
            <a href="index.php">Home</a>
            <a href="marcas.php">Brands</a>
            <a href="bags.php">Bags</a>
            <a href="shoes.php">Shoes</a>
        </div>

        <div class="menu-right">
            <div class="search-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="1.5">
                    <circle cx="11" cy="11" r="7"/>
                    <line x1="16.65" y1="16.65" x2="21" y2="21"/>
                </svg>

                <input type="text" placeholder="Search">
            </div>
        </div>

    </nav>

</header>

<div class="cuenta-contenedor">

    <h1 class="titulo-cuenta">MY ACCOUNT</h1>

    <h2>Account details</h2>

    <table class="tabla-cuenta">
        <tr>
            <th>Nombre</th>
            <td><?php echo $usuario["nombre"]; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $usuario["email"]; ?></td>
        </tr>
        <tr>
            <th>Usuario desde</th>
            <td><?php echo $tiempo_usuario; ?></td>
        </tr>
    </table>

    <a href="logout.php" class="boton-cerrar-sesion">Cerrar sesión</a>

    <h2>Purchase history</h2>

    <table class="tabla-historial">
        <tr>
            <th>Pedido</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
        </tr>
        <tr>
            <td colspan="4">Aquí aparecerá tu historial de compras cuando realices un pedido.</td>
        </tr>
    </table>

</div>

</body>
</html>