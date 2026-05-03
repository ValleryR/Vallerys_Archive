<?php
session_start();

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$sql = "SELECT * FROM productos ORDER BY RAND() LIMIT 4";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vallery's Archive</title>
    <link rel="stylesheet" href="css/estilos.css?v=31">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor catalogo-contenedor">

    <h1>NEW ARRIVALS</h1>

    <?php if (isset($_SESSION["nombre"])) { ?>
        <p>Hola <?php echo $_SESSION["nombre"]; ?>, nos gusta verte de nuevo!</p>
    <?php } ?>

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

    <div class="about-section">

        <h2>ABOUT US</h2>

        <p>
            Vallery’s Archive es un espacio donde se mezcla la peronalidad y la moda.
            Creo que el estilo debe de ser divertido, classy y un poco serio.
        </p>

        <p>
            Este proyecto mezcla Inglés y Español porque un buen fashion style has no rules es expresivo, global y unicamente tuyo!
        </p>

        <p>
            Desde iconic bags hasta statement shoes, cada pieza ha sido elegida para elvar tu vibe.
        </p>

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