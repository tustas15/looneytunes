<?php
    /*== Almacenando datos ==*/
    $usuario = limpiar_cadena($_POST['login_usuario']);
    $clave = limpiar_cadena($_POST['login_clave']);

    /*== Verificando campos obligatorios ==*/
    if ($usuario == "" || $clave == "") {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    /*== Verificando integridad de los datos ==*/
    if (verificar_datos("[a-zA-Z0-9]{4,20}", $usuario)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                El USUARIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if (verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $clave)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                La CLAVE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    /*== Conectando a la base de datos ==*/
    $check_user = conexion();
    $query = $check_user->prepare("SELECT * FROM tab_usuarios WHERE USUARIO = :usuario");
    $query->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() == 1) {
        $check_user = $query->fetch();

        /*== Verificando si el usuario está bloqueado ==*/
        if ($check_user['bloqueado_hasta'] > date('Y-m-d H:i:s')) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Cuenta bloqueada!</strong><br>
                    Tu cuenta está bloqueada hasta el ' . date('d-m-Y H:i:s', strtotime($check_user['bloqueado_hasta'])) . '
                </div>
            ';
            exit();
        }

        if ($check_user['USUARIO'] == $usuario && password_verify($clave, $check_user['PASS'])) {
            /*== Restableciendo intentos fallidos ==*/
            $update = $check_user->prepare("UPDATE tab_usuarios SET intentos_fallidos = 0 WHERE USUARIO = :usuario");
            $update->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $update->execute();

            /*== Iniciando sesión ==*/
            $_SESSION['id'] = $check_user['ID_USUARIO'];
            $_SESSION['usuario'] = $check_user['USUARIO'];

            if (headers_sent()) {
                echo "<script> window.location.href='index.php?vista=home'; </script>";
            } else {
                header("Location: index.php?vista=home");
            }

        } else {
            /*== Incrementando intentos fallidos ==*/
            $intentos_fallidos = $check_user['intentos_fallidos'] + 1;
            $bloqueado_hasta = null;

            if ($intentos_fallidos >= 3) {
                /*== Bloqueando usuario por 15 minutos ==*/
                $bloqueado_hasta = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $intentos_fallidos = 0;
            }

            $update = $check_user->prepare("UPDATE tab_usuarios SET intentos_fallidos = :intentos_fallidos, bloqueado_hasta = :bloqueado_hasta WHERE USUARIO = :usuario");
            $update->bindParam(':intentos_fallidos', $intentos_fallidos, PDO::PARAM_INT);
            $update->bindParam(':bloqueado_hasta', $bloqueado_hasta, PDO::PARAM_STR);
            $update->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $update->execute();

            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrió un error inesperado!</strong><br>
                    Usuario o clave incorrectos
                </div>
            ';
        }
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                Usuario o clave incorrectos
            </div>
        ';
    }

    $check_user = null;
?>
