<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION["carrito"])) {
    header("Location: carrito.php");
    exit();
}

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$carrito = $_SESSION["carrito"];
$productos_checkout = [];
$total = 0;
$checkout_valido = true;

$ids = implode(",", array_keys($carrito));
$sql = "SELECT * FROM productos WHERE id_producto IN ($ids)";
$resultado = $conn->query($sql);

while ($producto = $resultado->fetch_assoc()) {
    $id = $producto["id_producto"];
    $cantidad = $carrito[$id];

    if ($producto["stock"] <= 0 || $cantidad > $producto["stock"]) {
        $checkout_valido = false;
    }

    $subtotal = $producto["precio"] * $cantidad;

    $producto["cantidad"] = $cantidad;
    $producto["subtotal"] = $subtotal;

    $productos_checkout[] = $producto;
    $total += $subtotal;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/estilos.css?v=60">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor checkout-contenedor">

    <h1>CHECKOUT</h1>
    <p>Review your order before completing your purchase.</p>

    <?php if (!$checkout_valido) { ?>
        <p class="mensaje-carrito-error">
            Some items are no longer available or exceed current stock. Please return to your cart.
        </p>

        <a href="carrito.php" class="boton-seguir">Back to cart</a>

    <?php } else { ?>

        <div class="checkout-grid">

            <div class="checkout-form-box">
                <h2>Shipping details</h2>

                <form method="POST" action="finalizar_compra.php" class="checkout-form">
                    <input type="text" name="nombre_envio" placeholder="Nombre completo" required>
                    <input type="text" name="direccion" placeholder="Dirección de envío" required>
                    <input type="text" name="ciudad" placeholder="Ciudad" required>
                    <input type="text" name="codigo_postal" placeholder="Código postal" required>

                    <select name="metodo_pago" required>
                        <option value="">Método de pago</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                    </select>

                    <button type="submit" class="boton-finalizar">
                        Confirm order
                    </button>
                </form>
            </div>

            <div class="checkout-resumen">
                <h2>Order summary</h2>

                <?php foreach ($productos_checkout as $producto) { ?>
                    <div class="checkout-producto">
                        <img src="img/productos/<?php echo $producto["imagen"]; ?>" alt="<?php echo $producto["nombre"]; ?>">

                        <div>
                            <strong><?php echo $producto["marca"]; ?></strong>
                            <p><?php echo $producto["nombre"]; ?></p>
                            <p>Qty: <?php echo $producto["cantidad"]; ?></p>
                            <p>$<?php echo number_format($producto["subtotal"], 2); ?></p>
                        </div>
                    </div>
                <?php } ?>

                <h3>Total: $<?php echo number_format($total, 2); ?></h3>
            </div>

        </div>

    <?php } ?>

</div>

<script>
function toggleMenu() {
    document.getElementById("menuLinks").classList.toggle("activo");
}
</script>

</body>
</html>