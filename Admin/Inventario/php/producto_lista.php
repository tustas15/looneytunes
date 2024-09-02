<?php
$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

$campos = "tab_productos.id_producto, tab_productos.producto_codigo, tab_productos.producto_nombre, tab_productos.producto_precio, tab_productos.producto_stock, tab_productos.producto_foto, tab_productos.id_categoria_producto, tab_productos.id_usuario, tab_producto_categoria.id_categoria_producto, tab_producto_categoria.categoria_nombre, tab_usuarios.id_usuario, tab_usuarios.usuario";

$conexion = conexion();

if (isset($busqueda) && $busqueda != "") {
    $consulta_datos = "SELECT $campos FROM tab_productos 
                       INNER JOIN tab_producto_categoria ON tab_productos.id_categoria_producto = tab_producto_categoria.id_categoria_producto 
                       INNER JOIN tab_usuarios ON tab_productos.id_usuario = tab_usuarios.id_usuario 
                       WHERE tab_productos.producto_codigo LIKE :busqueda OR tab_productos.producto_nombre LIKE :busqueda 
                       ORDER BY tab_productos.producto_nombre ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(id_producto) FROM tab_productos 
                       WHERE producto_codigo LIKE :busqueda OR producto_nombre LIKE :busqueda";
} elseif ($categoria_id > 0) {
    $consulta_datos = "SELECT $campos FROM tab_productos 
                       INNER JOIN tab_producto_categoria ON tab_productos.id_categoria_producto = tab_producto_categoria.id_categoria_producto 
                       INNER JOIN tab_usuarios ON tab_productos.id_usuario = tab_usuarios.id_usuario 
                       WHERE tab_productos.id_categoria_producto = :categoria_id 
                       ORDER BY tab_productos.producto_nombre ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(id_producto) FROM tab_productos 
                       WHERE id_categoria_producto = :categoria_id";
} else {
    $consulta_datos = "SELECT $campos FROM tab_productos 
                       INNER JOIN tab_producto_categoria ON tab_productos.id_categoria_producto = tab_producto_categoria.id_categoria_producto 
                       INNER JOIN tab_usuarios ON tab_productos.id_usuario = tab_usuarios.id_usuario 
                       ORDER BY tab_productos.producto_nombre ASC 
                       LIMIT $inicio, $registros";

    $consulta_total = "SELECT COUNT(id_producto) FROM tab_productos";
}

$stmt_datos = $conexion->prepare($consulta_datos);
$stmt_total = $conexion->prepare($consulta_total);

if (isset($busqueda) && $busqueda != "") {
    $busqueda = "%$busqueda%";
    $stmt_datos->bindParam(':busqueda', $busqueda);
    $stmt_total->bindParam(':busqueda', $busqueda);
} elseif ($categoria_id > 0) {
    $stmt_datos->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmt_total->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
}

$stmt_datos->execute();
$datos = $stmt_datos->fetchAll();

$stmt_total->execute();
$total = (int)$stmt_total->fetchColumn();

$Npaginas = ceil($total / $registros);

if ($total >= 1 && $pagina <= $Npaginas) {
    $contador = $inicio + 1;
    $pag_inicio = $inicio + 1;
    foreach ($datos as $rows) {
        $tabla .= '
            <article class="media">
                <figure class="media-left">
                    <p class="image is-64x64">';
        if (is_file("./img/producto/" . $rows['producto_foto'])) {
            $tabla .= '<img src="/xampp/htdocs/looneytunes/admin/inventario/img/producto/' . $rows['producto_foto'] . '">';
        } else {
            $tabla .= '<img src="/xampp/htdocs/looneytunes/admin/inventario/img/producto.png">';
        }
        $tabla .= '</p>
                </figure>
                <div class="media-content">
                    <div class="content">
                      <p>
                        <strong>' . $contador . ' - ' . $rows['producto_nombre'] . '</strong><br>
                        <strong>CODIGO:</strong> ' . $rows['producto_codigo'] . ', <strong>PRECIO:</strong> $' . $rows['producto_precio'] . ', <strong>STOCK:</strong> ' . $rows['producto_stock'] . ', <strong>CATEGORIA:</strong> ' . $rows['categoria_nombre'] . ', <strong>REGISTRADO POR:</strong> ' . $rows['usuario'] . ' 
                      </p>
                    </div>
                    <div class="has-text-right">
                        <a href="index.php?vista=product_img&product_id_up=' . $rows['id_producto'] . '" class="button is-link is-rounded is-small">Imagen</a>
                        <a href="index.php?vista=product_update&product_id_up=' . $rows['id_producto'] . '" class="button is-success is-rounded is-small">Actualizar</a>
                        <a href="' . $url . $pagina . '&product_id_del=' . $rows['id_producto'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
                    </div>
                </div>
            </article>
            <hr>';
        $contador++;
    }
    $pag_final = $contador - 1;
} else {
    if ($total >= 1) {
        $tabla .= '
            <p class="has-text-centered">
                <a href="' . $url . '1" class="button is-link is-rounded is-small mt-4 mb-4">
                    Haga clic acá para recargar el listado
                </a>
            </p>';
    } else {
        $tabla .= '
            <p class="has-text-centered">No hay registros en el sistema</p>';
    }
}

if ($total > 0 && $pagina <= $Npaginas) {
    $tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';
}

$conexion = null;
echo $tabla;

if ($total >= 1 && $pagina <= $Npaginas) {
    echo paginador_tablas($pagina, $Npaginas, $url, 7);
}
