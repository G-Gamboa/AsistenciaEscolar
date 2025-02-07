<?php
$servername = "localhost";
$username = "asistencia";
$password = "g3sti0n@1954_"; 
$database = "AsistenciaPersonal";

$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");

header('Content-Type: application/json'); // Asegúrate de que el contenido siempre sea JSON

$response = array();

try {
    // Verificar si se proporcionó el parámetro estado_permiso en la solicitud
    if (isset($_GET['estado_permiso'])) {
        $estado_permiso = $_GET['estado_permiso'];
        
        // Preparar y ejecutar la consulta SQL según el estado de permiso seleccionado
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
        
        // Crear un array para almacenar los resultados de la consulta
        $solicitudes = array();
        while ($row = $result_permisos->fetch_assoc()) {
            // Agregar cada fila de resultado al array de solicitudes
            $solicitudes[] = $row;
        }
        
        // Devolver los datos como respuesta JSON
        $response['status'] = 'success';
        $response['data'] = $solicitudes;
    } else {
        // Si no se proporcionó el parámetro estado_permiso, devolver un error
        throw new Exception('El parámetro estado_permiso no se proporcionó en la solicitud.');
    }
} catch (Exception $e) {
    // Manejar cualquier excepción y devolver un error JSON
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response, JSON_PRETTY_PRINT);
?>