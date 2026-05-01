<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bags</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<header class="header">

    <div class="top-bar">

        <div class="logo">
            <a href="index.php">Vallery's Archive</a>
        </div>

        <div class="nav-right">

            <?php if (isset($_SESSION["nombre"])) { ?>
                <a href="logout.php" class="icon">Logout</a>
            <?php } else { ?>
                <a href="login.php" class="icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="1.5">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 20c2-4 6-6 8-6s6 2 8 6"/>
                    </svg>
                </a>
            <?php } ?>

            <a href="carrito.php" class="icon">
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
            <a href="marcas.php">Brands</a>
            <a href="bags.php">Bags</a>
            <a href="shoes.php">Shoes</a>
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

<div class="contenedor">
    <h1>BAGS</h1>

    <?php if (isset($_SESSION["nombre"])) { ?>
        <p>Bienvenida, <?php echo $_SESSION["nombre"]; ?> 🤍</p>
    <?php } ?>
</div>

</body>
</html>