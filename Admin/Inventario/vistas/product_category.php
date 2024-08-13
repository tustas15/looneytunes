<div class="container-fluid mb-6">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle">Lista de productos por categoría</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        require_once "./php/main.php"; // Asegúrate de que esta función se define correctamente
    ?>
    <div class="columns">
        <div class="col-md-4">
            <h2 class="title has-text-centered">Categorías</h2>
            <?php
                $conexion = conexion(); // Asegúrate de que esta función se define correctamente
                $categorias = $conexion->query("SELECT * FROM tab_producto_categoria");
                
                if ($categorias->rowCount() > 0) {
                    $categorias = $categorias->fetchAll();
                    foreach ($categorias as $row) {
                        // Usa htmlspecialchars para escapar los datos en las URLs y en el contenido
                        echo '<a href="index.php?vista=product_category&category_id=' . htmlspecialchars($row['id_categoria_producto']) . '" class="button is-link is-inverted is-fullwidth">' . htmlspecialchars($row['categoria_nombre']) . '</a>';
                    }
                } else {
                    echo '<p class="has-text-centered">No hay categorías registradas</p>';
                }
                $categorias = null;
            ?>
        </div>
        <div class="col-md-8">
            <?php
                // Obtener el ID de la categoría de la URL
                $categoria_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

                // Verificar la categoría
                $check_categoria = conexion(); // Nuevamente, asegúrate de que esta función se define correctamente
                $stmt = $check_categoria->prepare("SELECT * FROM tab_producto_categoria WHERE id_categoria_producto = :categoria_id");
                $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $check_categoria = $stmt->fetch();

                    // Mostrar información de la categoría
                    echo '
                        <h2 class="title text-center">' . htmlspecialchars($check_categoria['categoria_nombre']) . '</h2>
                        <p class="text-center pb-6">' . htmlspecialchars($check_categoria['categoria_ubicacion']) . '</p>
                    ';

                    // Eliminar producto
                    if (isset($_GET['product_id_del'])) {
                        require_once "./php/producto_eliminar.php"; // Verifica que este archivo esté bien definido
                    }

                    // Configuración de la paginación
                    $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $pagina = ($pagina <= 1) ? 1 : $pagina;

                    // Asegúrate de limpiar y validar las entradas
                    $pagina = limpiar_cadena($pagina); // Asegúrate de que esta función esté definida
                    $url = "index.php?vista=product_category&category_id=$categoria_id&page=";
                    $registros = 15;
                    $busqueda = "";

                    // Incluir el archivo de la lista de productos
                    require_once "./php/producto_lista.php";

                } else {
                    echo '<h2 class="title text-center">Seleccione una categoría para empezar</h2>';
                }
                $check_categoria = null;
            ?>
        </div>
    </div>
</div>
