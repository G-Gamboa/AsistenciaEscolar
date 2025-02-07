<?php
$sql = "
SELECT 
    e.nombre,
    e.apellido,
    e.correo,
    CASE 
        WHEN (
            SELECT COUNT(*)
            FROM RegistroAsistencia ra
            WHERE ra.id_empleado = e.id
                AND ra.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-01-01') AND DATE_FORMAT(CURDATE(), '%Y-11-30')
        ) > 0 THEN 15
        ELSE 0
    END AS vacaciones,
    CASE 
        WHEN (
            SELECT COUNT(*)
            FROM RegistroAsistencia ra
            WHERE ra.id_empleado = e.id
                AND ra.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-01-01') AND DATE_FORMAT(CURDATE(), '%Y-11-30')
        ) > 0 THEN 15
        ELSE 0
    END - (
        SELECT SUM(DATEDIFF(p.fecha_fin, p.fecha_inicio) + 1)
        FROM Permiso p
        WHERE p.id_empleado = e.id
    ) AS vacacionesTotales
FROM Empleado e;
";

$result = $conn->query($sql);
$datos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
}
?>