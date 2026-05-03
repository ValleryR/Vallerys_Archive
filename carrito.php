<?php
session_start();

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$carrito = $_SESSION["carrito"] ?? [];
$productos_carrito = [];
$total = 0;

if (!empty($carrito)) {
    $ids = implode(",", array_keys($carrito));
    $sql = "SELECT * FROM productos WHERE id_producto IN ($ids)";
    $resultado = $conn->query($sql);

    while ($producto = $resultado->fetch_assoc()) {
        $id = $producto["id_producto"];
        $cantidad = $carrito[$id];
        $subtotal = $producto["precio"] * $cantidad;

        $producto["cantidad"] = $cantidad;
        $producto["subtotal"] = $subtotal;

        $productos_carrito[] = $producto;
        $total += $subtotal;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
    <link rel="stylesheet" href="css/estilos.css?v=30">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor carrito-contenedor">
    <h1>CART</h1>

    <?php if (empty($productos_carrito)) { ?>

    <div class="carrito-vacio">
        <p>Tu carrito esta vacío, let's fix that!</p>

        <a href="index.php" class="boton-seguir">
            Continue shopping
        </a>
    </div>

    <?php } else { ?>

        <table class="tabla-carrito">
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>

            <?php foreach ($productos_carrito as $producto) { ?>
                <tr>
                    <td>
                        <div class="carrito-producto">

                        <a href="producto.php?id=<?php echo $producto["id_producto"]; ?>">
                            <img 
                                src="img/productos/<?php echo $producto["imagen"]; ?>" 
                                alt="<?php echo $producto["nombre"]; ?>"
                            >
                        </a>

                        <div>
                            <strong><?php echo $producto["marca"]; ?></strong><br>

                            <a href="producto.php?id=<?php echo $producto["id_producto"]; ?>" class="link-producto-carrito">
                                <?php echo $producto["nombre"]; ?>
                            </a>
                        </div>

                        </div>
                    </td>

                    <td>$<?php echo number_format($producto["precio"], 2); ?></td>

                    <td>
                        <form method="POST" action="actualizar_carrito.php" class="form-cantidad">
                            <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">
                            <input type="number" name="cantidad" value="<?php echo $producto["cantidad"]; ?>" min="1" max="<?php echo $producto["stock"]; ?>">
                            <button type="submit">Update</button>
                        </form>
                    </td>

                    <td>$<?php echo number_format($producto["subtotal"], 2); ?></td>

                    <td>
                        <form method="POST" action="eliminar_carrito.php">
                            <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">
                            <button type="submit" class="boton-eliminar">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>

        </table>

        <div class="carrito-total">
            <h2>Total: $<?php echo number_format($total, 2); ?></h2>

            <div class="acciones-carrito">
                <form method="POST" action="finalizar_compra.php">
                <button type="submit" class="boton-finalizar">Complete purchase</button>
            </form>

            <form method="POST" action="vaciar_carrito.php">
                <button type="submit" class="boton-vaciar">Vaciar carrito</button>
            </form>
            </div>
        </div>

    <?php } ?>
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