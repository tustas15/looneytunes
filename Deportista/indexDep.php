<?php
session_start();
// Verifica si el usuario está autenticado, de lo contrario redirige al formulario de inicio de sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.html");
    exit();
}

// Verifica si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link rel="stylesheet" type="text/css" href="indexDep.css">
		<link rel="icon" type="image/png" href="../img/logo.png">
	</head>
	<body>
		<nav>
			<div>
				<h1>Deportista <?=htmlspecialchars($_SESSION['usuario'], ENT_QUOTES)?></h1>
				<div>
					<a href="profile.php"><i></i>Profile</a>
					<a href="logout.php"><i></i>Logout</a>
				</div>
			</div>
		</nav>
		<div class="container">
			<h2>Home Page</h2>
			<p>Welcome back, <?=htmlspecialchars($_SESSION['usuario'], ENT_QUOTES)?>!</p>
		</div>
	</body>
</html>
