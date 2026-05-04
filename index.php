<?php
session_start();

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$sql = "SELECT * FROM productos 
        WHERE stock > 0 
        ORDER BY RAND() 
        LIMIT 4";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vallery's Archive</title>
    <link rel="stylesheet" href="css/estilos.css?v=50">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor catalogo-contenedor">

    <h1>NEW ARRIVALS</h1>

    <?php if (isset($_SESSION["nombre"])) { ?>
        <p class="mensaje-bienvenida">
            Hola <strong><?php echo $_SESSION["nombre"]; ?></strong>, nos gusta verte de nuevo!
        </p>
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

    <section class="about-section">

        <h2>ABOUT US</h2>

        <p>
            Vallery’s Archive es un espacio donde se mezcla la personalidad con la moda.
            Creo que el estilo debe ser divertido y classy. pero sobre todo auténtico.
        </p>

        <p>
            Este proyecto nace de mi forma de ver la moda: sin reglas estrictas, combinando inglés y español porque a good fashion style has no rules. 
            Es una forma de expresarte, de jugar y de mostrar quién eres sin decir una palabra.
        </p>

        <p>
            Aquí vas a encontrar desde iconic bags hasta statement shoes, piezas pensadas para elevar tu vibe y darle a tu wardrobe ese toque chic que lo hace diferente.
        </p>

        <p>
            Más que solo moda, Vallery’s Archive es una colección de ideas, inspiración y detalles que reflejan una esencia: confiar en tu estilo y hacerlo tuyo.
        </p>

    </section>

</div>

<?php include("footer.php"); ?>

<script>
function toggleMenu() {
    document.getElementById("menuLinks").classList.toggle("activo");
}
</script>

</body>
</html>