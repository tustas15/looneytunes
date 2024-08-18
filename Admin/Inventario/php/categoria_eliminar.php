<?php
session_start();
/*== Almacenando datos ==*/
$category_id_del = limpiar_cadena($_GET['category_id_del']);

/*== Verificando categoría ==*/
$check_categoria = conexion();
$check_categoria = $check_categoria->query("SELECT id_categoria_producto FROM tab_producto_categoria WHERE id_categoria_producto='$category_id_del'");

if ($check_categoria->rowCount() == 1) {

    $check_productos = conexion();
    $check_productos = $check_productos->query("SELECT id_categoria_producto FROM tab_productos WHERE id_categoria_producto='$category_id_del' LIMIT 1");

    if ($check_productos->rowCount() <= 0) {

        $eliminar_categoria = conexion();
        $eliminar_categoria = $eliminar_categoria->prepare("DELETE FROM tab_producto_categoria WHERE id_categoria_producto=:id");

        $eliminar_categoria->execute([":id" => $category_id_del]);

        if ($eliminar_categoria->rowCount() == 1) {
            // Registrar el evento en tab_logs
            $insert_log = conexion()->prepare("INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (:id_usuario, :evento, :hora_log, :dia_log, :ip, :tipo_evento)");
            $insert_log->execute([
                ':id_usuario' => $_SESSION['user_id'], // Asegúrate de que esta variable esté correctamente definida
                ':evento' => "Categoría eliminada: " . $category_id_del,
                ':hora_log' => date('H:i:s'),
                ':dia_log' => date('Y-m-d'),
                ':ip' => $_SERVER['REMOTE_ADDR'],
                ':tipo_evento' => 'categoria_eliminada'
            ]);

            echo '
                <div class="notification is-info is-light">
                    <strong>¡CATEGORIA ELIMINADA!</strong><br>
                    Los datos de la categoría se eliminaron con éxito
                </div>
            ';
        } else {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo eliminar la categoría, por favor intente nuevamente
                </div>
            ';
        }
        $eliminar_categoria = null;
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No podemos eliminar la categoría ya que tiene productos asociados
            </div>
        ';
    }
    $check_productos = null;
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            La CATEGORÍA que intenta eliminar no existe
        </div>
    ';
}
$check_categoria = null;
?>
