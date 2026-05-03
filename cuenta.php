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

$sql_compras = "
    SELECT 
        c.id_compra,
        c.fecha,
        c.total,
        GROUP_CONCAT(CONCAT(p.nombre, ' x', dc.cantidad) SEPARATOR '<br>') AS articulos
    FROM compras c
    INNER JOIN detalle_compra dc ON c.id_compra = dc.id_compra
    INNER JOIN productos p ON dc.id_producto = p.id_producto
    WHERE c.id_usuario = ?
    GROUP BY c.id_compra, c.fecha, c.total
    ORDER BY c.fecha DESC
";

$stmt_compras = $conn->prepare($sql_compras);
$stmt_compras->bind_param("i", $id_usuario);
$stmt_compras->execute();
$compras = $stmt_compras->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta</title>
    <link rel="stylesheet" href="css/estilos.css?v=30">
</head>
<body>

<?php include("header.php"); ?>

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
    <?php if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "admin") { ?>
    <a href="admin.php" class="boton-admin-cuenta">Ir al panel admin</a>
    <?php } ?>

    <h2>Purchase history</h2>

    <table class="tabla-historial">
        <tr>
            <th>Pedido</th>
            <th>Fecha</th>
            <th>Artículos</th>
            <th>Total</th>
            <th>Estado</th>
        </tr>

        <?php if ($compras->num_rows > 0) { ?>
            <?php while ($compra = $compras->fetch_assoc()) { ?>
                <tr>
                    <td>#<?php echo $compra["id_compra"]; ?></td>
                    <td><?php echo $compra["fecha"]; ?></td>
                    <td><?php echo $compra["articulos"]; ?></td>
                    <td>$<?php echo number_format($compra["total"], 2); ?></td>
                    <td>Completed</td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5">Aquí aparecerá tu historial de compras cuando realices un pedido.</td>
            </tr>
        <?php } ?>
    </table>

</div>
<?php include("footer.php"); ?>

<script>
function toggleMenu() {
    document.getElementById("menuLinks").classList.toggle("activo");
}
</script>
<script>
function toggleMenu() {
    document.getElementById("menuLinks").classList.toggle("activo");
}
</script>

</body>
</html>