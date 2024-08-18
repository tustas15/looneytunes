<?php
require_once "main.php";
session_start();
/*== Almacenando id ==*/
$id = limpiar_cadena($_POST['id_categoria_producto']);

/*== Verificando categoria ==*/
$check_categoria = conexion();
$check_categoria = $check_categoria->query("SELECT * FROM tab_producto_categoria WHERE id_categoria_producto='$id'");

if ($check_categoria->rowCount() <= 0) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            La categoría no existe en el sistema
        </div>
    ';
    exit();
} else {
    $datos = $check_categoria->fetch();
}
$check_categoria = null;

/*== Almacenando datos ==*/
$nombre = limpiar_cadena($_POST['categoria_nombre']);
$ubicacion = limpiar_cadena($_POST['categoria_ubicacion']);

/*== Verificando campos obligatorios ==*/
if ($nombre == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    exit();
}

/*== Verificando integridad de los datos ==*/
if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}", $nombre)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El NOMBRE no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if ($ubicacion != "") {
    if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}", $ubicacion)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La UBICACION no coincide con el formato solicitado
            </div>
        ';
        exit();
    }
}

/*== Verificando nombre ==*/
if ($nombre != $datos['categoria_nombre']) {
    $check_nombre = conexion();
    $check_nombre = $check_nombre->query("SELECT categoria_nombre FROM tab_producto_categoria WHERE categoria_nombre='$nombre'");
    if ($check_nombre->rowCount() > 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE ingresado ya se encuentra registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_nombre = null;
}

/*== Actualizar datos ==*/
$actualizar_categoria = conexion();
$actualizar_categoria = $actualizar_categoria->prepare("UPDATE tab_producto_categoria SET categoria_nombre=:nombre, categoria_ubicacion=:ubicacion WHERE id_categoria_producto=:id");

$marcadores = [
    ":nombre" => $nombre,
    ":ubicacion" => $ubicacion,
    ":id" => $id
];

if ($actualizar_categoria->execute($marcadores)) {
    // Registrar el evento en tab_logs
    $insert_log = conexion()->prepare("INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (:id_usuario, :evento, :hora_log, :dia_log, :ip, :tipo_evento)");
    $insert_log->execute([
        ':id_usuario' => $_SESSION['user_id'], // Asegúrate de que esta variable esté correctamente definida
        ':evento' => "Categoría actualizada: " . $nombre,
        ':hora_log' => date('H:i:s'),
        ':dia_log' => date('Y-m-d'),
        ':ip' => $_SERVER['REMOTE_ADDR'],
        ':tipo_evento' => 'categoria_actualizada'
    ]);

    echo '
        <div class="notification is-info is-light">
            <strong>¡CATEGORIA ACTUALIZADA!</strong><br>
            La categoría se actualizó con éxito
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo actualizar la categoría, por favor intente nuevamente
        </div>
    ';
}
$actualizar_categoria = null;
?>
