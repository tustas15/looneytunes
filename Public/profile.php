<?php
session_start();
require_once('../Admin/configuracion/conexion.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID del usuario logueado
$id_usuario = $_SESSION['user_id'];

try {
    // Conexión a la base de datos
    $conn = new PDO("mysql:host=$server;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar los datos del administrador
    $stmt = $conn->prepare("SELECT * FROM tab_administradores WHERE ID_USUARIO = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $name = htmlspecialchars($admin['NOMBRE_ADMIN'] . ' ' . $admin['APELLIDO_ADMIN']);
        $phone = htmlspecialchars($admin['CELULAR_ADMIN']);
    } else {
        $name = '';
        $phone = '';
    }

    // Manejar la actualización del perfil
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_email = htmlspecialchars($_POST['email']);
        $new_phone = htmlspecialchars($_POST['phone']);

        // Actualizar la base de datos
        $update_stmt = $conn->prepare("UPDATE tab_administradores SET CELULAR_ADMIN = :phone WHERE ID_USUARIO = :id_usuario");
        $update_stmt->bindParam(':phone', $new_phone, PDO::PARAM_STR);
        $update_stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $update_stmt->execute();

        // Actualizar la sesión
        $_SESSION['user_email'] = $new_email;

        // Redirigir para evitar reenvío de formulario
        header("Location: profile.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

// Verificar si el correo electrónico está definido en la sesión
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'Correo no proporcionado';

?>
<?php include('header.php'); ?>
        <div id="layoutSidenav_content">
            <main>
                <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
                    <div class="container-xl px-4">
                        <div class="page-header-content pt-4">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-auto mt-4">
                                    <h1 class="page-header-title">
                                        <div class="page-header-icon"><i data-feather="user"></i></div>
                                        Account Settings - Profile
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="container-xl px-4 mt-n10">
                    <div class="row">
                        <div class="col-xl-4">
                            <!-- Profile picture card-->
                            <div class="card mb-4 mb-xl-0">
                                <div class="card-header">Profile Picture</div>
                                <div class="card-body text-center">
                                    <!-- Profile picture image-->
                                    <img class="img-account-profile rounded-circle mb-2" src="../Assets/img/illustrations/profiles/profile-1.png" alt="" />
                                    <!-- Profile picture help block-->
                                    <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                                    <!-- Profile picture upload button-->
                                    <button class="btn btn-primary" type="button">Upload new image</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8">
                            <!-- Account details card-->
                            <div class="card mb-4">
                                <div class="card-header">Account Details</div>
                                <div class="card-body">
                                    <form method="POST" action="profile.php">
                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputUsername">Username (how your name will appear to other users on the site)</label>
                                            <input class="form-control" id="inputUsername" type="text" placeholder="Enter your username" value="<?php echo $name; ?>" readonly>
                                        </div>
                                        <div class="row gx-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputPhone">Phone</label>
                                                <input class="form-control" id="inputPhone" type="text" name="phone" placeholder="Enter your phone number" value="<?php echo $phone; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputEmailAddress">Email address</label>
                                                <input class="form-control" id="inputEmailAddress" type="email" name="email" placeholder="Enter your email address" value="<?php echo htmlspecialchars($user_email); ?>">
                                            </div>
                                        </div>
                                        <div class="row gx-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputOrgName">Organization name</label>
                                                <input class="form-control" id="inputOrgName" type="text" placeholder="Enter your organization name" value="Start Bootstrap" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputLocation">Location</label>
                                                <input class="form-control" id="inputLocation" type="text" placeholder="Enter your location" value="San Francisco, CA" readonly>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary" type="submit">Save changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php include('footer.php'); ?>