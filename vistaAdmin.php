<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: index.php");
    exit();
}
include 'controladores/conexion.php';

$nombre_usuario = $_SESSION['nombre_usuario'];

$sql_empleado = "SELECT e.id, e.nombre, e.apellido, e.genero FROM Empleado e
                INNER JOIN Usuarios u ON e.id = u.id_empleado
                WHERE u.nombre_usuario = ?";
$stmt_empleado = $conn->prepare($sql_empleado);
$stmt_empleado->bind_param("s", $nombre_usuario);
$stmt_empleado->execute();
$result_empleado = $stmt_empleado->get_result();


if ($result_empleado->num_rows == 1) {
    $row_empleado = $result_empleado->fetch_assoc();
    $idEmpleado = $row_empleado['id'];

    if ($row_empleado['genero'] == 'Masculino') {
        $MensajeMostrar = "Bienvenido ";
    } else {
        $MensajeMostrar = "Bienvenida ";
    }
    $nombre_empleado = $row_empleado['nombre'];
    $apellido_empleado = $row_empleado['apellido'];
    $MensajeMostrar.=$nombre_empleado . ' ' . $apellido_empleado.'<br>';
}    

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if(isset($_POST['action'])){
    $id_permiso = $_POST['id_permiso'];
    $action = $_POST['action'];

    if($action === 'pendiente') {
      $nuevo_estado = 1;
    } else if ($action === 'aprobar') {
        $nuevo_estado = 2;
    } elseif ($action === 'rechazar') {
        $nuevo_estado = 3;
    }

    $stmt_update = $conn->prepare("UPDATE Permiso SET id_estado_permiso = ? WHERE id = ?");
    $stmt_update->bind_param("ii", $nuevo_estado, $id_permiso);
    $stmt_update->execute();
    $stmt_update->close();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
  }elseif (isset($_POST['cerrar_sesion'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

}


?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrar Asistencias</title>
  <link rel="stylesheet" href="CSS/indexStyle.css">
  <link rel="stylesheet" href="CSS/accionesAdmin.css">

  <link rel="shortcut icon" href="../img svg (1).ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <script src="JS/opcionesAdmin.js"></script>
</head>

<body>
  <?php include 'controladores/header.php'; ?>

  <div>
    <h2 class="titulo">
      <?php echo $MensajeMostrar; ?>
    </h2>
  </div>

  <div class="dropdown menu_opciones">
    <button class="btn btn-secondary dropdown-toggle adminOP" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
      aria-expanded="false">
      Opciones
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
      <li><a class="dropdown-item">Solicitudes Permisos</a></li>
      <li><a class="dropdown-item">Reporte Asistencias</a></li>
      <li><a class="dropdown-item" id="generarVacacionesBtn">Generar Vacaciones</a></li>
      <li>  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <button type="submit" name="cerrar_sesion" class="dropdown-item">Cerrar Sesión</button>
        </form></li>
    </ul>
  </div>

  <div class="contenedor">
    <div class="solicitudes">
      <table class="table">
        <thead>
          <tr class="cabeza">
            <th colspan="5">
              <select id="estado_permiso" onchange="cargarSolicitudes(this.value)">
                <option value="1">Solicitud Por Resolver</option>
                <option value="2">Solicitudes Aprobadas</option>
                <option value="3">Solicitudes Rechazadas</option>
              </select>
            </th>

          </tr>
          <tr>
            <th scope="col" class="encabezados">Nombre</th>
            <th scope="col" class="encabezados">Inicio</th>
            <th scope="col" class="encabezados">Fin</th>
            <th scope="col" class="encabezados">Descripción</th>
            <th scope="col" class="encabezados">Estado Permiso</th>
          </tr>
        </thead>
        <tbody id="tabla_solicitudes">

        </tbody>
      </table>
    </div>
    <div id="chartdiv" class="reporteAsistencia">
    </div>
    <div class="generarVacaciones vacas">
    <div class="vacas">
    <table border="1" id="vistaPreviaTabla" class="table">
            <thead>
                <tr>
                    <th scope="col" class="encabezados">Nombre</th>
                    <th scope="col" class="encabezados">Apellido</th>
                    <th scope="col" class="encabezados">Correo</th>
                    <th scope="col" class="encabezados">Vacaciones</th>
                    <th scope="col" class="encabezados">Vacaciones Totales</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="acciones">
       <a href="archivos/Vacaciones.xlsx" download>Descargar Excel</a>
        <button id="confirmarBtn" style="display:none;">Confirmar y Enviar Correos</button>
    </div>

    </div>
  </div>


  <?php include 'controladores/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



  <script src="JS/cargarGraficoAsistencia.js"></script>

  <script src="JS/generarExcel.js"></script>
  <script src="JS/cargarTabla.js"></script>




</body>

<?php 
    $conn->close(); 
?>

</html>