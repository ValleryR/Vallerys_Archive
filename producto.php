<?php
session_start();

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$id_producto = intval($_GET["id"]);

$sql = "SELECT p.*, c.nombre AS categoria 
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.id_producto = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$producto = $resultado->fetch_assoc();

$imagen_principal = $producto["imagen"];
$base_imagen = pathinfo($imagen_principal, PATHINFO_FILENAME);
$base_imagen = preg_replace('/_1$/', '', $base_imagen);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $producto["nombre"]; ?></title>
    <link rel="stylesheet" href="css/estilos.css?v=50">
</head>
<body>

<?php include("header.php"); ?>

<div class="producto-detalle-contenedor">

    <div class="producto-galeria">

        <div class="miniaturas">
            <?php for ($i = 1; $i <= 3; $i++) { 
                $imagen = $base_imagen . "_" . $i . ".jpg";
            ?>
                <img 
                    src="img/productos/<?php echo $imagen; ?>" 
                    alt="<?php echo $producto["nombre"]; ?>"
                    onclick="cambiarImagen('img/productos/<?php echo $imagen; ?>')"
                >
            <?php } ?>
        </div>

        <div class="imagen-principal">
            <img 
                id="imagenPrincipal"
                src="img/productos/<?php echo $producto["imagen"]; ?>" 
                alt="<?php echo $producto["nombre"]; ?>"
            >
        </div>

    </div>

    <div class="producto-info-detalle">

        <p class="producto-marca"><?php echo $producto["marca"]; ?></p>

        <h1><?php echo $producto["nombre"]; ?></h1>

        <p class="detalle-categoria"><?php echo $producto["categoria"]; ?></p>

        <p class="detalle-descripcion"><?php echo $producto["descripcion"]; ?></p>

        <p class="detalle-precio">$<?php echo number_format($producto["precio"], 2); ?></p>

        <p class="detalle-stock">
            Stock disponible: <?php echo $producto["stock"]; ?>
        </p>

        <?php if ($producto["stock"] > 0) { ?>
            <form method="POST" action="agregar_carrito.php">
                <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">
                <button type="submit" class="boton-producto detalle-boton">Add to cart</button>
            </form>
        <?php } else { ?>
            <p class="sin-stock">Out of stock</p>
        <?php } ?>

        <a href="javascript:history.back()" class="volver-link">← Back to shopping</a>

    </div>

</div>
<?php include("footer.php"); ?>
<script>
function cambiarImagen(ruta) {
    document.getElementById("imagenPrincipal").src = ruta;
}

function toggleMenu() {
    document.getElementById("menuLinks").classList.toggle("activo");
}
</script>

</body>
</html>