<?php
// Conexión a la base de datos
$servername = "localhost"; // Cambia esto si tu servidor es diferente
$username = "root"; // Cambia esto según tu configuración
$password = ""; // Cambia esto según tu configuración
$dbname = "looneytunes";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del representante desde el formulario
$id_representante = $_POST['representante'] ?? 0;
$monto = $_POST['monto'] ?? '0.00';

// Consultar el nombre y número de teléfono del representante
$sql = "SELECT NOMBRE_REPRE, APELLIDO_REPRE, CELULAR_REPRE FROM tab_representantes WHERE ID_REPRESENTANTE = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_representante);
$stmt->execute();
$stmt->bind_result($nombre_repre,$apellido_repre, $celular_repre);
$stmt->fetch();
$stmt->close();
$conn->close();

// Verificar si se obtuvo el nombre y número de teléfono
if (!$nombre_repre || $apellido_repre || !$celular_repre) {
    // Responder con error en JSON
    echo json_encode([
        'status' => 'error',
        'message' => 'No se encontró el representante.'
    ]);
    exit();
}

// TOKEN QUE NOS DA FACEBOOK
$token = 'EAAOq2GY6o5QBOyHci6ey2K05twoGQpYfizbfzBnydPjp8ypsKrCalJqIC8Ryy01o0ax24TcpHdWfPZBZB02LXkA5UgSPLLiB3ZA4Km99qL8ZApPxiE5SEWhGFTNkHaaYbxFEA4y8s7zhOeWjAB55XUc2YuU7mQarloyIYwoUFsQXWG3pKesLzyS3fCzxZB3NZBthJblZCjhFRiOZA5ZBr';

// URL A DONDE SE MANDARA EL MENSAJE
$url = 'https://graph.facebook.com/v19.0/345094562024872/messages';

// CONFIGURACION DEL MENSAJE
$mensaje = json_encode([
    'messaging_product' => 'whatsapp',
    'to' => $celular_repre,
    'type' => 'template',
    'template' => [
        'name' => 'pagoitsi',
        'language' => ['code' => 'es'],
        'components' => [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $nombre_repre],
                    ['type' => 'text', 'text' => $apellido_repre],

                    ['type' => 'text', 'text' => $monto]
                ]
            ]
        ]
    ]
]);

// DECLARAMOS LAS CABECERAS
$header = [
    "Authorization: Bearer " . $token,
    "Content-Type: application/json"
];

// INICIAMOS EL CURL
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POSTFIELDS, $mensaje);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// OBTENEMOS LA RESPUESTA DEL ENVIO DE INFORMACION
$response = json_decode(curl_exec($curl), true);

// OBTENEMOS EL CODIGO DE LA RESPUESTA
$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

// CERRAMOS EL CURL
curl_close($curl);

// Verificar si el mensaje fue enviado correctamente
if ($status_code == 200 && isset($response['messages'])) {
    $response_message = 'El mensaje se envió correctamente.';
} else {
    $response_message = 'Hubo un problema al enviar el mensaje: ' . ($response['error']['message'] ?? 'Error desconocido');
}

// Responder con el estado y mensaje de éxito/error
$response = [
    'status' => $status_code == 200 ? 'success' : 'error',
    'message' => $response_message
];

echo json_encode($response);
?>
