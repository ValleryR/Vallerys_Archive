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

$total_productos = $conn->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc()["total"];
$total_compras = $conn->query("SELECT COUNT(*) AS total FROM compras")->fetch_assoc()["total"];
$total_usuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()["total"];

$busqueda = $_GET["buscar"] ?? "";
$categoria = $_GET["categoria"] ?? "";
$orden = $_GET["orden"] ?? "recientes";

$where = "WHERE 1=1";

if ($busqueda != "") {
    $busqueda_segura = $conn->real_escape_string($busqueda);
    $where .= " AND (p.nombre LIKE '%$busqueda_segura%' OR p.marca LIKE '%$busqueda_segura%')";
}

if ($categoria != "") {
    $categoria_segura = intval($categoria);
    $where .= " AND p.id_categoria = $categoria_segura";
}

$order_sql = "ORDER BY p.id_producto DESC";

if ($orden == "stock_asc") {
    $order_sql = "ORDER BY p.stock ASC";
} elseif ($orden == "stock_desc") {
    $order_sql = "ORDER BY p.stock DESC";
} elseif ($orden == "precio_asc") {
    $order_sql = "ORDER BY p.precio ASC";
} elseif ($orden == "precio_desc") {
    $order_sql = "ORDER BY p.precio DESC";
} elseif ($orden == "marca_asc") {
    $order_sql = "ORDER BY p.marca ASC";
}

$sql_productos = "SELECT p.*, c.nombre AS categoria 
                  FROM productos p
                  LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                  $where
                  $order_sql";

$productos = $conn->query($sql_productos);

$compras = $conn->query("SELECT c.id_compra, c.fecha, c.total, u.nombre, u.email
                         FROM compras c
                         INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                         ORDER BY c.fecha DESC
                         LIMIT 10");

$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Vallery's Archive</title>
    <link rel="stylesheet" href="css/estilos.css?v=41">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor admin-contenedor">

    <h1>ADMIN PANEL</h1>
    <p>Productos, inventario e historial de compras.</p>

    <div class="admin-resumen">
        <div class="admin-card">
            <h2><?php echo $total_productos; ?></h2>
            <p>Products</p>
        </div>

        <div class="admin-card">
            <h2><?php echo $total_compras; ?></h2>
            <p>Purchases</p>
        </div>

        <div class="admin-card">
            <h2><?php echo $total_usuarios; ?></h2>
            <p>Users</p>
        </div>
    </div>

    <div class="admin-acciones">
        <a href="admin_agregar_producto.php" class="boton-admin">Add product</a>
    </div>

    <h2>Products</h2>

    <form method="GET" class="admin-filtros">
        <input 
            type="text" 
            name="buscar" 
            placeholder="Search product or brand"
            value="<?php echo htmlspecialchars($busqueda); ?>"
        >

        <select name="categoria">
            <option value="">All categories</option>
            <?php while ($cat = $categorias->fetch_assoc()) { ?>
                <option 
                    value="<?php echo $cat["id_categoria"]; ?>"
                    <?php if ($categoria == $cat["id_categoria"]) { echo "selected"; } ?>
                >
                    <?php echo $cat["nombre"]; ?>
                </option>
            <?php } ?>
        </select>

        <select name="orden">
            <option value="recientes" <?php if ($orden == "recientes") echo "selected"; ?>>Newest</option>
            <option value="stock_asc" <?php if ($orden == "stock_asc") echo "selected"; ?>>Stock low-high</option>
            <option value="stock_desc" <?php if ($orden == "stock_desc") echo "selected"; ?>>Stock high-low</option>
            <option value="precio_asc" <?php if ($orden == "precio_asc") echo "selected"; ?>>Price low-high</option>
            <option value="precio_desc" <?php if ($orden == "precio_desc") echo "selected"; ?>>Price high-low</option>
            <option value="marca_asc" <?php if ($orden == "marca_asc") echo "selected"; ?>>Brand A-Z</option>
        </select>

        <button type="submit">Filter</button>
        <a href="admin.php" class="link-admin">Clear</a>
    </form>

    <table class="tabla-admin">
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Product</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>

        <?php if ($productos && $productos->num_rows > 0) { ?>
            <?php while ($producto = $productos->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $producto["id_producto"]; ?></td>
                    <td>
                        <img src="img/productos/<?php echo $producto["imagen"]; ?>" class="admin-img">
                    </td>
                    <td><?php echo $producto["nombre"]; ?></td>
                    <td><?php echo $producto["marca"]; ?></td>
                    <td><?php echo $producto["categoria"]; ?></td>
                    <td>$<?php echo number_format($producto["precio"], 2); ?></td>
                    <td><?php echo $producto["stock"]; ?></td>
                    <td>
                        <a href="admin_editar_producto.php?id=<?php echo $producto["id_producto"]; ?>" class="link-admin">Edit</a>

                        <form method="POST" action="admin_eliminar_producto.php" class="form-eliminar-admin" onsubmit="return confirm('¿Seguro que quieres eliminar este producto?');">
                            <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">
                            <button type="submit" class="boton-delete-admin">Eliminar producto</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="8">No products found.</td>
            </tr>
        <?php } ?>
    </table>

    <h2>Compras recientes</h2>

    <table class="tabla-admin">
        <tr>
            <th>Order</th>
            <th>User</th>
            <th>Email</th>
            <th>Date</th>
            <th>Total</th>
        </tr>

        <?php while ($compra = $compras->fetch_assoc()) { ?>
            <tr>
                <td>#<?php echo $compra["id_compra"]; ?></td>
                <td><?php echo $compra["nombre"]; ?></td>
                <td><?php echo $compra["email"]; ?></td>
                <td><?php echo $compra["fecha"]; ?></td>
                <td>$<?php echo number_format($compra["total"], 2); ?></td>
            </tr>
        <?php } ?>
    </table>

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