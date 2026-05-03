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

if (!isset($_GET["id"])) {
    header("Location: admin.php");
    exit();
}

$id_producto = intval($_GET["id"]);
$mensaje = "";

$sql = "SELECT * FROM productos WHERE id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: admin.php");
    exit();
}

$producto = $resultado->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $imagen = $_POST["imagen"];
    $marca = $_POST["marca"];
    $id_categoria = $_POST["id_categoria"];

    $sql_update = "UPDATE productos 
                   SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ?, marca = ?, id_categoria = ?
                   WHERE id_producto = ?";

    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param(
        "ssdissii",
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $imagen,
        $marca,
        $id_categoria,
        $id_producto
    );

    if ($stmt_update->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        $mensaje = "Error al actualizar producto";
    }
}

$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto</title>
    <link rel="stylesheet" href="css/estilos.css?v=40">
</head>
<body>

<?php include("header.php"); ?>

<div class="formulario">
    <h1>EDIT PRODUCT</h1>

    <form method="POST">
        <input 
            type="text" 
            name="nombre" 
            placeholder="Nombre del producto" 
            value="<?php echo htmlspecialchars($producto["nombre"]); ?>" 
            required
        >

        <input 
            type="text" 
            name="marca" 
            placeholder="Marca" 
            value="<?php echo htmlspecialchars($producto["marca"]); ?>" 
            required
        >

        <input 
            type="text" 
            name="imagen" 
            placeholder="Imagen: bolsa1_1.jpg" 
            value="<?php echo htmlspecialchars($producto["imagen"]); ?>" 
            required
        >

        <input 
            type="number" 
            step="0.01" 
            name="precio" 
            placeholder="Precio" 
            value="<?php echo $producto["precio"]; ?>" 
            required
        >

        <input 
            type="number" 
            name="stock" 
            placeholder="Stock" 
            value="<?php echo $producto["stock"]; ?>" 
            required
        >

        <select name="id_categoria" required>
            <option value="">Selecciona categoría</option>

            <?php while ($categoria = $categorias->fetch_assoc()) { ?>
                <option 
                    value="<?php echo $categoria["id_categoria"]; ?>"
                    <?php if ($categoria["id_categoria"] == $producto["id_categoria"]) { echo "selected"; } ?>
                >
                    <?php echo $categoria["nombre"]; ?>
                </option>
            <?php } ?>
        </select>

        <textarea name="descripcion" placeholder="Descripción" required><?php echo htmlspecialchars($producto["descripcion"]); ?></textarea>

        <button type="submit">Save changes</button>
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