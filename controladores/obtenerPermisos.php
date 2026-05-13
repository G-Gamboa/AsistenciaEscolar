<?php
include 'conexion.php';

header('Content-Type: application/json');

$response = array();

try {
    if (!isset($_GET['estado_permiso'])) {
        throw new Exception('El parámetro estado_permiso no se proporcionó en la solicitud.');
    }

    $estado_permiso = (int) $_GET['estado_permiso'];

    $sql_permisos = "SELECT p.id, e.nombre, e.apellido, p.fecha_inicio, p.fecha_fin, p.descripcion
                     FROM Permiso p
                     INNER JOIN Empleado e ON p.id_empleado = e.id
                     WHERE p.id_estado_permiso = ?";

    $stm_permisos = $conn->prepare($sql_permisos);
    if ($stm_permisos === false) {
        throw new Exception('Error al preparar la consulta');
    }

    $stm_permisos->bind_param("i", $estado_permiso);
    if ($stm_permisos->execute() === false) {
        throw new Exception('Error al ejecutar la consulta');
    }

    $result_permisos = $stm_permisos->get_result();
    if ($result_permisos === false) {
        throw new Exception('Error al obtener los resultados de la consulta');
    }

    $solicitudes = [];
    while ($row = $result_permisos->fetch_assoc()) {
        $solicitudes[] = $row;
    }

    $response['status'] = 'success';
    $response['data'] = $solicitudes;
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response, JSON_PRETTY_PRINT);
?>