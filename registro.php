<?php
session_start();

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $verificar = "SELECT * FROM usuarios WHERE email = ?";
    $stmt_verificar = $conn->prepare($verificar);
    $stmt_verificar->bind_param("s", $email);
    $stmt_verificar->execute();
    $resultado = $stmt_verificar->get_result();

    if ($resultado->num_rows > 0) {
        $mensaje = "Este correo ya está registrado";
    } else {
        $sql = "INSERT INTO usuarios (nombre, email, password, fecha_registro)
                VALUES (?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $password);

        if ($stmt->execute()) {
            $id_usuario = $conn->insert_id;

            $_SESSION["id_usuario"] = $id_usuario;
            $_SESSION["nombre"] = $nombre;
            $_SESSION["email"] = $email;
            $_SESSION["rol"] = "cliente";

            header("Location: index.php");
            exit();
        } else {
            $mensaje = "Error al crear la cuenta";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="css/estilos.css?v=40">
</head>
<body>

<?php include("header.php"); ?>

<div class="contenedor">

    <h1>CREATE ACCOUNT</h1>

    <form method="POST" class="formulario">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>

        <button type="submit">Create account</button>
    </form>

    <p><?php echo $mensaje; ?></p>

</div>

<?php include("footer.php"); ?>

<script>
function toggleMenu() {
    document.getElementById("menuLinks").classList.toggle("activo");
}
</script>

</body>
</html>