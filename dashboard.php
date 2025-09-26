<?php
session_start();

// Proteger la página: si no hay un admin logueado, redirigir al login.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/app/core/View.php';
require_once __DIR__ . '/app/dao/ContactoDAO.php';

// Obtener todas las solicitudes de contacto
$contactoDAO = new ContactoDAO();
$solicitudes = $contactoDAO->findAll();

// Construir las filas de la tabla dinámicamente
$tableRows = '';
if (empty($solicitudes)) {
    $tableRows = '<tr><td colspan="6" class="text-center">No hay solicitudes de asesoría por el momento.</td></tr>';
} else {
    foreach ($solicitudes as $solicitud) {
        $tableRows .= '<tr>';
        $tableRows .= '<th>' . htmlspecialchars($solicitud->getId()) . '</th>';
        $tableRows .= '<td>' . htmlspecialchars($solicitud->getNombre()) . '</td>';
        $tableRows .= '<td>' . htmlspecialchars($solicitud->getCorreo()) . '</td>';
        $tableRows .= '<td>' . htmlspecialchars($solicitud->getTelefono() ?: 'No provisto') . '</td>';
        $tableRows .= '<td><span class="badge bg-primary">' . htmlspecialchars(ucfirst($solicitud->getEstado())) . '</span></td>';
        $tableRows .= '<td>' . htmlspecialchars((new DateTime($solicitud->getCreatedAt()))->format('Y-m-d H:i')) . '</td>';
        $tableRows .= '</tr>';
    }
}

// Renderizar la vista del dashboard con los datos
View::render('dashboard', [
    'page_title' => 'Panel de Administración',
    'admin_name' => htmlspecialchars($_SESSION['admin_name']),
    'solicitudes_table_rows' => $tableRows
]);