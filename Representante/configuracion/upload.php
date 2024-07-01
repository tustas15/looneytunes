<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../Admin/configuracion/conexion.php';

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "Error: CSRF token inválido.";
        exit;
    }

    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profilePic']['tmp_name'];
        $fileName = $_FILES['profilePic']['name'];
        $fileSize = $_FILES['profilePic']['size'];
        $fileType = $_FILES['profilePic']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $fileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $conn->prepare("UPDATE TAB_REPRESENTANTES SET FOTO = :foto WHERE ID_REPRESENTANTE = :id");
                $stmt->bindParam(':foto', $fileName);
                $stmt->bindParam(':id', $_SESSION['id_usuario']);
                $stmt->execute();
                
                $_SESSION['message'] = 'La imagen se ha subido exitosamente.';
            } else {
                $_SESSION['message'] = 'Hubo un error moviendo el archivo.';
            }
        } else {
            $_SESSION['message'] = 'Tipo de archivo no permitido.';
        }
    } else {
        $_SESSION['message'] = 'No se subió ningún archivo.';
    }

    header('Location: profile.php');
    exit;
}
?>
