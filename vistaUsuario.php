<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
}
include 'controladores/conexion.php';
$nombre_usuario = $_SESSION['nombre_usuario'];

$sql_empleado = "SELECT e.id, e.nombre, e.apellido, e.genero, e.correo FROM Empleado e
                INNER JOIN Usuarios u ON e.id = u.id_empleado
                WHERE u.nombre_usuario = ?";
$stmt_empleado = $conn->prepare($sql_empleado);
$stmt_empleado->bind_param("s", $nombre_usuario);
$stmt_empleado->execute();
$result_empleado = $stmt_empleado->get_result();

if ($result_empleado->num_rows == 1) {
    $row_empleado = $result_empleado->fetch_assoc();
    $idEmpleado = $row_empleado['id'];
    $correo_empleado = $row_empleado['correo'];

    if ($row_empleado['genero'] == 'Masculino') {
        $MensajeMostrar = "Bienvenido ";
    } else {
        $MensajeMostrar = "Bienvenida ";
    }

    $nombre_empleado = $row_empleado['nombre'];
    $apellido_empleado = $row_empleado['apellido'];
    $MensajeMostrar.=$nombre_empleado . ' ' . $apellido_empleado.'<br>';

    $sql_asistencia = "SELECT id FROM RegistroAsistencia
                        WHERE id_empleado = ? AND fecha = CURDATE() AND hora_salida IS NULL";
    $stmt_asistencia = $conn->prepare($sql_asistencia);
    $stmt_asistencia->bind_param("i", $idEmpleado);
    $stmt_asistencia->execute();
    $result_asistencia = $stmt_asistencia->get_result();

    if ($result_asistencia->num_rows == 1) {
        $MensajeMostrar .= "Ya has marcado tu asistencia.";
        $check_in_habilitado = false;
        $check_out_habilitado = true;
    } else {
        $MensajeMostrar .= "Por favor, marca tu asistencia.";
        $check_in_habilitado = true;
        $check_out_habilitado = false;
    }
} else {
    $MensajeMostrar = "Usuario no encontrado";
    $check_in_habilitado = false;
    $check_out_habilitado = false;
}

$stmt_empleado->close();
$stmt_asistencia->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['check_in'])) {
        $sql_insertar_asistencia = "INSERT INTO RegistroAsistencia (id_empleado, fecha, hora_entrada) VALUES (?, CURDATE(), CURTIME())";
        $stmt_insertar_asistencia = $conn->prepare($sql_insertar_asistencia);
        $stmt_insertar_asistencia->bind_param("i", $idEmpleado);
        $stmt_insertar_asistencia->execute();
        $stmt_insertar_asistencia->close();

        header("Location: vistaUsuario.php");
        exit();

    } elseif (isset($_POST['check_out'])) {
        $sql_actualizar_asistencia = "UPDATE RegistroAsistencia SET hora_salida = CURTIME() WHERE id_empleado = ? AND fecha = CURDATE() ORDER BY id DESC LIMIT 1";
        $stmt_actualizar_asistencia = $conn->prepare($sql_actualizar_asistencia);
        $stmt_actualizar_asistencia->bind_param("i", $idEmpleado);
        $stmt_actualizar_asistencia->execute();
        $stmt_actualizar_asistencia->close();
        
        header("Location: vistaUsuario.php");
        exit();
    } elseif (isset($_POST['cerrar_sesion'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}    


$conn->close();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Asistencia</title>
    <link rel="stylesheet" href="CSS/indexStyle.css">
    <link rel="stylesheet" href="CSS/accionesUsuarios.css">

    <link rel="shortcut icon" href="../img svg (1).ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
    <?php include 'controladores/header.php'; ?>

    <div class="cierreSesion">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <button type="submit" name="cerrar_sesion">Cerrar Sesión</button>
        </form>
    </div>

    <div class="contenidoPrincipal">
        <div>
            <h2 class="titulo"><?php echo $MensajeMostrar; ?></h2>
        </div>

        <div class="menu container">
            <div class="row">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <button class="col-md-6 btn" type="submit" name="check_in" <?php if (!$check_in_habilitado) {echo "disabled";} ?>>CHECK-IN</button>
                </form>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <button class="col-md-6 btn" type="submit" name="check_out" <?php if (!$check_out_habilitado){echo "disabled";} ?>>CHECK-OUT</button>
                </form>
            </div>
            <div class="row permis">
                <form method="post" action="solicitudPermiso.php">
                    <button class="col-md-12 btn" type="submit" name="solicitar_permiso">SOLICITAR PERMISO</button>
                </form>
            </div>
        </div>

    </div>

    <?php include 'controladores/footer.php'; ?>
</body>

</html>