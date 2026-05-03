<?php
session_start();

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$marca = $_GET["marca"] ?? "";

$stmt = $conn->prepare("SELECT * FROM productos WHERE marca = ?");
$stmt->bind_param("s", $marca);
$stmt->execute();

$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $marca; ?></title>
    <link rel="stylesheet" href="css/estilos.css?v=40">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor catalogo-contenedor">

    <h1><?php echo strtoupper($marca); ?></h1>

    <div class="grid-productos">

        <?php while ($producto = $resultado->fetch_assoc()) { ?>
            <div class="producto-card">

                <a href="producto.php?id=<?php echo $producto["id_producto"]; ?>">
                    <img 
                        src="img/productos/<?php echo $producto["imagen"]; ?>" 
                        alt="<?php echo $producto["nombre"]; ?>"
                        class="producto-img"
                    >
                </a>

                <p class="producto-marca"><?php echo $producto["marca"]; ?></p>
                <h2 class="producto-nombre"><?php echo $producto["nombre"]; ?></h2>
                <p class="producto-precio">$<?php echo number_format($producto["precio"], 2); ?></p>

                <?php if ($producto["stock"] > 0) { ?>
                    <form method="POST" action="agregar_carrito.php">
                        <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">
                        <button type="submit" class="boton-producto">Add to cart</button>
                    </form>
                <?php } else { ?>
                    <p class="sin-stock">Out of stock</p>
                <?php } ?>

            </div>
        <?php } ?>

    </div>

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