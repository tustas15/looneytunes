<?php
include '/xampp/htdocs/looneytunes/admin/includespro/header.php';
session_start();
?>

<main>
	<div class="container mb-6 mt-5">
		<h1 class="title">Categorías</h1>
		<h2 class="subtitle">Actualizar categoría</h2>
	</div>

	<div class="container pb-6 pt-6">
		<?php
		include "../includespro/btn_back.php";
		require_once "./main.php";

		$id = (isset($_GET['category_id_up'])) ? $_GET['category_id_up'] : 0;
		$id = limpiar_cadena($id);

		/*== Verificando categoría ==*/
		$check_categoria = conexion();
		$check_categoria = $check_categoria->query("SELECT * FROM tab_producto_categoria WHERE id_categoria_producto='$id'");

		if ($check_categoria->rowCount() > 0) {
			$datos = $check_categoria->fetch();
		?>

			<div class="form-rest mb-6 mt-6"></div>

			<form action="./php/categoria_actualizar.php" method="POST" class="FormularioAjax" autocomplete="off">

				<input type="hidden" name="id_categoria_producto" value="<?php echo $datos['id_categoria_producto']; ?>" required>

				<div class="row mb-3">
					<div class="col-md-6">
						<div class="form-group">
							<label>Nombre</label>
							<input class="form-control" type="text" name="categoria_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}" maxlength="50" required value="<?php echo $datos['categoria_nombre']; ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Ubicación</label>
							<input class="form-control" type="text" name="categoria_ubicacion" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}" maxlength="150" value="<?php echo $datos['categoria_ubicacion']; ?>">
						</div>
					</div>
				</div>
				<div class="text-center">
					<button type="submit" class="btn btn-success">Actualizar</button>
				</div>
			</form>
		<?php
		} else {
			include "../includespro/error_alert.php";
		}
		$check_categoria = null;
		?>
	</div>
</main>
<?php include '/xampp/htdocs/looneytunes/admin/includespro/footer.php'; ?>