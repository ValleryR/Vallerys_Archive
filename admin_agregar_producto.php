<?php
session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] != "admin") {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $imagen = $_POST["imagen"];
    $marca = $_POST["marca"];
    $id_categoria = $_POST["id_categoria"];

    $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen, marca, id_categoria)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdissi", $nombre, $descripcion, $precio, $stock, $imagen, $marca, $id_categoria);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        $mensaje = "Error al agregar producto";
    }
}

$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar producto</title>
    <link rel="stylesheet" href="css/estilos.css?v=40">
</head>
<body>

<?php include("header.php"); ?>

<div class="formulario">
    <h1>ADD PRODUCT</h1>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre del producto" required>
        <input type="text" name="marca" placeholder="Marca" required>
        <input type="text" name="imagen" placeholder="Imagen: bolsa1_1.jpg" required>
        <input type="number" step="0.01" name="precio" placeholder="Precio" required>
        <input type="number" name="stock" placeholder="Stock" required>

        <select name="id_categoria" required>
            <option value="">Selecciona categoría</option>
            <?php while ($categoria = $categorias->fetch_assoc()) { ?>
                <option value="<?php echo $categoria["id_categoria"]; ?>">
                    <?php echo $categoria["nombre"]; ?>
                </option>
            <?php } ?>
        </select>

        <textarea name="descripcion" placeholder="Descripción" required></textarea>

        <button type="submit">Add product</button>
    </form>

    <p><?php echo $mensaje; ?></p>

    <p class="registro-link">
        <a href="admin.php">Volver al panel</a>
    </p>
</div>

<script>
function toggleMenu() {
    document.getElementById("menuLinks").classList.toggle("activo");
}
</script>

</body>
</html>