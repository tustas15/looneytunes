<?php
require_once "main.php";
session_start();
/*== Almacenando id ==*/
$id = limpiar_cadena($_POST['id_producto']);

/*== Verificando producto ==*/
$check_producto = conexion();
$check_producto = $check_producto->query("SELECT * FROM tab_productos WHERE id_producto='$id'");

if ($check_producto->rowCount() <= 0) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El producto no existe en el sistema
        </div>
    ';
    exit();
} else {
    $datos = $check_producto->fetch();
}
$check_producto = null;

/*== Almacenando datos ==*/
$codigo = limpiar_cadena($_POST['producto_codigo']);
$nombre = limpiar_cadena($_POST['producto_nombre']);
$precio = limpiar_cadena($_POST['producto_precio']);
$stock = limpiar_cadena($_POST['producto_stock']);
$categoria = limpiar_cadena($_POST['producto_categoria']);

/*== Verificando campos obligatorios ==*/
if ($codigo == "" || $nombre == "" || $precio == "" || $stock == "" || $categoria == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    exit();
}

/*== Verificando integridad de los datos ==*/
if (verificar_datos("[a-zA-Z0-9- ]{1,70}", $codigo)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El CODIGO de BARRAS no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}", $nombre)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El NOMBRE no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[0-9.]{1,25}", $precio)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El PRECIO no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[0-9]{1,25}", $stock)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El STOCK no coincide con el formato solicitado
        </div>
    ';
    exit();
}

/*== Verificando codigo ==*/
if ($codigo != $datos['producto_codigo']) {
    $check_codigo = conexion()->prepare("SELECT producto_codigo FROM tab_productos WHERE producto_codigo = :codigo");
    $check_codigo->execute([':codigo' => $codigo]);
    if ($check_codigo->rowCount() > 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El CODIGO de BARRAS ingresado ya se encuentra registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_codigo = null;
}

/*== Verificando nombre ==*/
if ($nombre != $datos['producto_nombre']) {
    $check_nombre = conexion()->prepare("SELECT producto_nombre FROM tab_productos WHERE producto_nombre = :nombre");
    $check_nombre->execute([':nombre' => $nombre]);
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

/*== Verificando categoria ==*/
if ($categoria != $datos['id_categoria_producto']) {
    $check_categoria = conexion()->prepare("SELECT id_categoria_producto FROM tab_producto_categoria WHERE id_categoria_producto = :categoria");
    $check_categoria->execute([':categoria' => $categoria]);
    if ($check_categoria->rowCount() <= 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La categoría seleccionada no existe
            </div>
        ';
        exit();
    }
    $check_categoria = null;
}

/*== Actualizando datos ==*/
try {
    $actualizar_producto = conexion()->prepare("UPDATE tab_productos SET producto_codigo = :codigo, producto_nombre = :nombre, producto_precio = :precio, producto_stock = :stock, id_categoria_producto = :categoria WHERE id_producto = :id");

    $marcadores = [
        ":codigo" => $codigo,
        ":nombre" => $nombre,
        ":precio" => $precio,
        ":stock" => $stock,
        ":categoria" => $categoria,
        ":id" => $id
    ];

    if ($actualizar_producto->execute($marcadores)) {
        // Insertar en tab_logs
        $insert_log = conexion()->prepare("INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (:id_usuario, :evento, :hora_log, :dia_log, :ip, :tipo_evento)");
        $insert_log->execute([
            ':id_usuario' => $_SESSION['user_id'], // Asegúrate de tener el ID del usuario en la sesión
            ':evento' => "Producto actualizado: $nombre",
            ':hora_log' => date('H:i:s'),
            ':dia_log' => date('Y-m-d'),
            ':ip' => $_SERVER['REMOTE_ADDR'],
            ':tipo_evento' => 'nuevo_producto_creado'
        ]);

        echo '
            <div class="notification is-info is-light">
                <strong>¡PRODUCTO ACTUALIZADO!</strong><br>
                El producto se actualizó con éxito
            </div>
        ';
    } else {
        throw new Exception("No se pudo actualizar el producto.");
    }
} catch (Exception $e) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            ' . $e->getMessage() . '
        </div>
    ';
}
