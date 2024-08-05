<?php
// Iniciar el búfer de salida para evitar errores de encabezado
ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$modulo_buscador = limpiar_cadena($_POST['modulo_buscador']);
$modulos = ["usuario", "categoria", "producto"];

if (in_array($modulo_buscador, $modulos)) {
    $modulos_url = [
        "usuario" => "user_search.php",
        "categoria" => "category_search.php",
        "producto" => "product_search.php"
    ];

    $modulos_url = $modulos_url[$modulo_buscador];
    $modulo_buscador = "busqueda_" . $modulo_buscador;

    // Iniciar búsqueda
    if (isset($_POST['txt_buscador'])) {
        $txt = limpiar_cadena($_POST['txt_buscador']);

        if ($txt == "") {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrió un error inesperado!</strong><br>
                    Introduce el término de búsqueda
                </div>
            ';
        } else {
            if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $txt)) {
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrió un error inesperado!</strong><br>
                        El término de búsqueda no coincide con el formato solicitado
                    </div>
                ';
            } else {
                $_SESSION[$modulo_buscador] = $txt;
                header("Location: " . $modulos_url, true, 303);
                ob_end_flush(); // Asegúrate de enviar el contenido del búfer y desactivar el búfer
                exit();
            }
        }
    }

    // Eliminar búsqueda
    if (isset($_POST['eliminar_buscador'])) {
        unset($_SESSION[$modulo_buscador]);
        header("Location: " . $modulos_url, true, 303);
        ob_end_flush(); // Asegúrate de enviar el contenido del búfer y desactivar el búfer
        exit();
    }
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrió un error inesperado!</strong><br>
            No podemos procesar la petición
        </div>
    ';
}

ob_end_flush(); // Asegúrate de enviar el contenido del búfer y desactivar el búfer
?>
