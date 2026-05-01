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
<title>Carrito</title>
<link rel="stylesheet" href="css/estilos.css?v=15">
</head>
<body>

<header class="header">

    <div class="top-bar">

        <div class="logo">
            <a href="index.php">Vallery's Archive</a>
        </div>

        <div class="nav-right">

            <?php if (isset($_SESSION["id_usuario"])) { ?>
                <a href="cuenta.php" class="icon">
            <?php } else { ?>
                <a href="login.php" class="icon">
            <?php } ?>

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

<div class="contenedor carrito-contenedor">
    <h1>CART</h1>

    <?php if (empty($productos_carrito)) { ?>

        <p>Your cart is empty.</p>

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
                            <img src="img/productos/<?php echo $producto["imagen"]; ?>" alt="<?php echo $producto["nombre"]; ?>">
                            <div>
                                <strong><?php echo $producto["marca"]; ?></strong><br>
                                <?php echo $producto["nombre"]; ?>
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

            <form method="POST" action="finalizar_compra.php">
                <button type="submit" class="boton-finalizar">Complete purchase</button>
            </form>
        </div>

    <?php } ?>
</div>

</body>
</html>