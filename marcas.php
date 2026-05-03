<?php
session_start();

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$sql = "SELECT DISTINCT marca FROM productos ORDER BY marca ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brands</title>
    <link rel="stylesheet" href="css/estilos.css?v=40">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">

    <h1>BRANDS</h1>
    <p>Explora a nuestros diseñadores</p>

    <div class="grid-marcas">

        <?php while ($row = $resultado->fetch_assoc()) { ?>
            <a href="marca.php?marca=<?php echo urlencode($row["marca"]); ?>" class="boton-marca">
                <?php echo strtoupper($row["marca"]); ?>
            </a>
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