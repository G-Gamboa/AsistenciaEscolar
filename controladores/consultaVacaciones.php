<?php
$sql = "
SELECT
    e.nombre,
    e.apellido,
    e.correo,
    CASE WHEN COALESCE(asist.total, 0) > 0 THEN 15 ELSE 0 END AS vacaciones,
    CASE WHEN COALESCE(asist.total, 0) > 0 THEN 15 ELSE 0 END
        - COALESCE(perm.dias_usados, 0) AS vacacionesTotales
FROM Empleado e
LEFT JOIN (
    SELECT id_empleado, COUNT(*) AS total
    FROM RegistroAsistencia
    WHERE fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-01-01')
                    AND DATE_FORMAT(CURDATE(), '%Y-11-30')
    GROUP BY id_empleado
) AS asist ON asist.id_empleado = e.id
LEFT JOIN (
    SELECT id_empleado, SUM(DATEDIFF(fecha_fin, fecha_inicio) + 1) AS dias_usados
    FROM Permiso
    GROUP BY id_empleado
) AS perm ON perm.id_empleado = e.id
";

$result = $conn->query($sql);
$datos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
}
?>
