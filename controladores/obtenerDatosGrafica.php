<?php
include 'conexion.php';

// Activar visualización de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn->set_charset("utf8mb4");

    $sql = "
        SELECT 
            e.id,
            e.nombre,
            e.apellido,
            COUNT(ra.id) AS asistencias,
            (
                SELECT IFNULL(SUM(DATEDIFF(p.fecha_fin, p.fecha_inicio)), 0) 
                FROM Permiso p 
                WHERE p.id_empleado = e.id AND p.id_estado_permiso = 2
            ) AS permisos
        FROM Empleado e
        LEFT JOIN RegistroAsistencia ra ON e.id = ra.id_empleado
        GROUP BY e.id, e.nombre, e.apellido
    ";

    $result = $conn->query($sql);

    $empleados = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $asistencias = intval($row['asistencias']);
            $permisos = intval($row['permisos']);


            $empleados[] = [
                'nombre' => $row['nombre'] . ' ' . $row['apellido'],
                'asistencias' => $asistencias,
                'permisos' => $permisos
            ];
        }
    }

    // Imprimir el JSON
    header('Content-Type: application/json');
    echo json_encode($empleados, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // En caso de error, devolver un JSON con el mensaje de error
    header('Content-Type: application/json');
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
