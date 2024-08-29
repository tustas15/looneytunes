<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// ID del usuario actual
$user_id = $_SESSION['user_id'];

// Consulta para obtener la foto del usuario
$sql = "
    SELECT f.FOTO 
    FROM tab_fotos_usuario f
    JOIN tab_usu_tipo ut ON ut.ID_TIPO = f.ID_TIPO
    WHERE ut.ID_USUARIO = :user_id
";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$foto = $stmt->fetchColumn();

// Codificar la foto en base64
$foto_src = $foto ? 'data:image/jpeg;base64,' . base64_encode($foto) : '/looneytunes/Assets/img/illustrations/profiles/profile-1.png';
?>

<html lang="es">


<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="description" content="" />
<meta name="author" content="" />
<title>CASF</title>
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" rel="stylesheet" />
<link href="/looneytunes/Assets/css/styles.css" rel="stylesheet" />
<link rel="stylesheet" href="./css/bulma.min.css">
<link rel="stylesheet" href="./css/estilos.css">
<link rel="icon" type="image/x-icon" href="/looneytunes/AssetsFree/img/logo.png" />
<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>