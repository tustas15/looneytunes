<?php
// Conexión a la base de datos
$host = '127.0.0.1';
$db = 'looneytunes';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Obtener las tareas programadas para la fecha y hora actual
$fechaActual = (new DateTime())->format('Y-m-d H:i:s');
$stmt = $pdo->prepare('SELECT * FROM tab_tareas_programadas WHERE FECHA_PROGRAMADA <= ?');
$stmt->execute([$fechaActual]);
$tareas = $stmt->fetchAll();

foreach ($tareas as $tarea) {
    $deportistaId = $tarea['ID_DEPORTISTA'];
    
    // Obtener los detalles del deportista
    $stmt = $pdo->prepare('SELECT NOMBRE_DEPO, APELLIDO_DEPO, NUMERO_CELULAR FROM tab_deportistas WHERE ID_DEPORTISTA = ?');
    $stmt->execute([$deportistaId]);
    $deportista = $stmt->fetch();
    
    if ($deportista) {
        $nombreDeportista = $deportista['NOMBRE_DEPO'] . ' ' . $deportista['APELLIDO_DEPO'];
        $numeroCelular = $deportista['NUMERO_CELULAR'];
        $mensaje = "Hola $nombreDeportista, recordatorio de que ha pasado el tiempo estipulado desde tu último pago. Por favor, revisa tu cuenta o contáctanos si tienes preguntas.";

        // URL de la API de WhatsApp
        $url = 'https://graph.facebook.com/v19.0/345094562024872/messages';

        // Token de autenticación
        $token = 'EAAOq2GY6o5QBOwTinvAAV5pkZBwyy3jknyf2Flh5bbZCBklfFxPZAmCZAYbXTwlXxKBsfJnjdrMPABVtXkqBo9ffpSYE3qDouPtuRQ9JMVZCAM298ZAEAYkSRad850dx9oWmO2LV7qKrGUjFZBHrjTn7rZA4p6ZCMzoTCT9B633nSZBqWD6Sg5iGN1V1zq9lwwQT9gzFvSyofDUVmuT8n3';

        // Configuración del mensaje
        $mensajeJson = json_encode([
            'messaging_product' => 'whatsapp',
            'to' => $numeroCelular,
            'type' => 'text',
            'text' => [
                'body' => $mensaje
            ]
        ]);

        // Cabeceras de la solicitud
        $header = [
            "Authorization: Bearer " . $token,
            "Content-Type: application/json",
        ];

        // Configuración de CURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $mensajeJson);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Ejecución de la solicitud
        $response = curl_exec($curl);

        // Manejo de errores
        if (curl_errno($curl)) {
            echo 'Error:' . curl_error($curl);
        } else {
            $responseDecoded = json_decode($response, true);
            print_r($responseDecoded);

            // Código de respuesta
            $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Verificación de la respuesta
            if ($status_code == 200) {
                echo "Mensaje enviado correctamente a $nombreDeportista.";
            } else {
                echo "Error al enviar el mensaje a $nombreDeportista. Código de respuesta: " . $status_code;
            }
        }

        // Cierre de CURL
        curl_close($curl);
    }
    
    // Eliminar la tarea una vez completada
    $stmt = $pdo->prepare('DELETE FROM tab_tareas_programadas WHERE ID_TAREA = ?');
    $stmt->execute([$tarea['ID_TAREA']]);
}
?>
