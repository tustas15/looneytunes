<?php
session_start();
// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No se ha iniciado sesión.']);
    exit();
}

// Lógica para obtener las notificaciones desde la base de datos o donde sea que se almacenen
// Aquí se simula la obtención de datos para las notificaciones
$notifications = [
    ['id' => 1, 'message' => 'Nuevo pago recibido', 'status' => 'unread'],
    ['id' => 2, 'message' => 'Se ha actualizado su información', 'status' => 'unread'],
    ['id' => 3, 'message' => 'Recordatorio: Reunión programada', 'status' => 'read'],
];

// Calcular la cantidad de notificaciones no leídas
$unseen_count = 0;
$notification_html = '';

foreach ($notifications as $notification) {
    if ($notification['status'] === 'unread') {
        $unseen_count++;
        $notification_html .= '<div class="dropdown-divider"></div>';
        $notification_html .= '<p class="dropdown-item">' . htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8') . '</p>';
    }
}

// Preparar la respuesta JSON
$response = [
    'unseen_count' => $unseen_count,
    'notification_html' => $notification_html,
];

// Devolver respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
