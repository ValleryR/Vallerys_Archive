<?php
session_start();

$conn = new mysqli("db", "root", "root", "tienda_moda");

if ($conn->connect_error) {
    die("Error de conexión");
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario["password"])) {
            $_SESSION["id_usuario"] = $usuario["id_usuario"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["email"] = $usuario["email"];

            header("Location: index.php");
            exit();
        } else {
            $mensaje = "Correo o contraseña incorrectos";
        }
    } else {
        $mensaje = "Correo o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<header class="header">
    <div class="top-bar">
        <div class="logo">
            <a href="index.php">Vallery's Archive</a>
        </div>

        <div class="nav-right">
            <a href="login.php" class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="1.5">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 20c2-4 6-6 8-6s6 2 8 6"/>
                </svg>
            </a>

            <a href="carrito.html" class="icon">
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
            <a href="marcas.html">Brands</a>
            <a href="bags.html">Bags</a>
            <a href="shoes.html">Shoes</a>
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

<div class="formulario">
    <h1>LOGIN</h1>

    <form method="POST">
        <input type="email" name="email" placeholder="EMAIL" required>
        <input type="password" name="password" placeholder="PASSWORD" required>
        <button type="submit">LOGIN</button>
    </form>

    <p><?php echo $mensaje; ?></p>

    <p class="registro-link">
        ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
    </p>
</div>

</body>
</html>