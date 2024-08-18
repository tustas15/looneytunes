<?php
session_start();
/*== Almacenando datos ==*/
$product_id_del = limpiar_cadena($_GET['product_id_del']);

/*== Verificando producto ==*/
$check_producto = conexion();
$check_producto = $check_producto->query("SELECT * FROM tab_productos WHERE id_producto='$product_id_del'");

if ($check_producto->rowCount() == 1) {
    $datos = $check_producto->fetch();

    $eliminar_producto = conexion();
    $eliminar_producto = $eliminar_producto->prepare("DELETE FROM tab_productos WHERE id_producto = :id");
    $eliminar_producto->execute([":id" => $product_id_del]);

    if ($eliminar_producto->rowCount() == 1) {
        // Registrar el evento en tab_logs
        $insert_log = conexion()->prepare("INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (:id_usuario, :evento, :hora_log, :dia_log, :ip, :tipo_evento)");
        $insert_log->execute([
            ':id_usuario' => $_SESSION['user_id'], // Asegúrate de tener el ID del usuario en la sesión
            ':evento' => "Producto eliminado: " . $datos['producto_nombre'],
            ':hora_log' => date('H:i:s'),
            ':dia_log' => date('Y-m-d'),
            ':ip' => $_SERVER['REMOTE_ADDR'],
            ':tipo_evento' => 'producto_eliminado'
        ]);

        // Eliminar la foto del producto si existe
        if (is_file("./img/producto/" . $datos['producto_foto'])) {
            chmod("./img/producto/" . $datos['producto_foto'], 0777);
            unlink("./img/producto/" . $datos['producto_foto']);
        }

        echo '
            <div class="notification is-info is-light">
                <strong>¡PRODUCTO ELIMINADO!</strong><br>
                Los datos del producto se eliminaron con éxito
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo eliminar el producto, por favor intente nuevamente
            </div>
        ';
    }
    $eliminar_producto = null;
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El PRODUCTO que intenta eliminar no existe
        </div>
    ';
}
$check_producto = null;
?>
