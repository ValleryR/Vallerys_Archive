<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Shoes</title>
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<header class="header">

<div class="top-bar">
<div class="logo">
<a href="index.php">Vallery's Archive</a>
</div>

<div class="nav-right">

<?php if (isset($_SESSION["id_usuario"])) { ?>
<a href="cuenta.php" class="icon">
<?php } else { ?>
<a href="login.php" class="icon">
<?php } ?>

<svg width="20" height="20">
<circle cx="12" cy="8" r="4"/>
<path d="M4 20c2-4 6-6 8-6s6 2 8 6"/>
</svg>
</a>

<a href="carrito.php" class="icon">🛒</a>

</div>
</div>

<nav class="menu">
<div class="menu-left">
<a href="index.php">Home</a>
<a href="marcas.php">Brands</a>
<a href="bags.php">Bags</a>
<a href="shoes.php">Shoes</a>
</div>
</nav>

</header>

<div class="contenedor">
<h1>SHOES</h1>
</div>

</body>
</html>