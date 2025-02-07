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

$sql_opciones ="SELECT * FROM TiposPermiso";
$stm_opciones = $conn->prepare($sql_opciones);
$stm_opciones->execute();
$result_opciones = $stm_opciones->get_result();
$lista_opciones = array();

while ($row = $result_opciones->fetch_assoc()) {
    $lista_opciones[] = $row;
}

if ($result_empleado->num_rows == 1) {
    $row_empleado = $result_empleado->fetch_assoc();

    $idEmpleado = $row_empleado['id'];
    $correo_empleado = $row_empleado['correo'];
    $nombre_empleado = $row_empleado['nombre'];
    $apellido_empleado = $row_empleado['apellido'];
}
else {
    $MensajeMostrar = "Usuario no encontrado";}
    

    $show_error=false;
    $show_message=false;
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fecha_inicio'], $_POST['fecha_fin'], $_POST['motivo'], $_POST['descripcion'])) {
      $fechaInicio = $_POST['fecha_inicio'];
      $fechaFin = $_POST['fecha_fin'];
      if ($fechaFin < $fechaInicio) {
          $show_error=true;
          $error_message ="La fecha de fin debe ser posterior a la fecha de inicio.";
      } else {
          $motivoSeleccionado = $_POST['motivo'];
          $descripcion = $_POST['descripcion'];
  
          $indice = array_search($motivoSeleccionado, array_column($lista_opciones, 'nombre'));
          if ($indice !== false) {
              $idTipoPermiso = $indice+1;
          }

          $sql_insertar_permiso = "INSERT INTO Permiso (id_empleado, id_tipo_permiso, fecha_inicio, fecha_fin, descripcion, id_estado_permiso) 
                                              VALUES (?, ?, ?, ?, ?, 1)";
                                    $stmt_insertar_permiso = $conn->prepare($sql_insertar_permiso);
                                    $stmt_insertar_permiso->bind_param("iisss", $idEmpleado, $idTipoPermiso, $fechaInicio, $fechaFin, $descripcion);


          if ($stmt_insertar_permiso->execute()) {
            $show_message=true;
            $success_message= "Solicitud de permiso enviada correctamente.";
            header("refresh:2; url=vistaUsuario.php");
          } else {
              $show_error=true;
              $error_message= "Error al enviar la solicitud de permiso.";
          }
          $stmt_insertar_permiso->close();
      }
  }

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Permiso</title>
    <link rel="stylesheet" href="CSS/indexStyle.css">
    <link rel="stylesheet" href="CSS/permiso.css">

    <link rel="shortcut icon" href="../img svg (1).ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
    <?php include 'controladores/header.php'; ?>

    <div class="regresar">
        <form method="post" action="vistaUsuario.php">
            <button type="submit" name="regresar">Regresar</button>
        </form>
    </div>

    <div>
        <h2 class="titulo">Solicitud de permiso</h2>
    </div>
    
    <div class="solicitud">
          <form class="row g-3" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <div class="col-md-6">
              <label for="inputEmail4" class="form-label">Correo Electrónico</label>
              <input type="email" class="form-control" id="inputEmail4" value="<?php echo $correo_empleado; ?>" disabled>
          </div>
          <div class="col-md-6">
              <label for="inputName" class="form-label">Nombre Completo</label>
              <input type="text" class="form-control" id="inputName" value="<?php echo $nombre_empleado.' '.$apellido_empleado; ?>" disabled>
          </div>
          <div class="col-6">
              <label for="inputDate" class="form-label">Fecha Inicio</label>
              <input type="date" class="form-control" id="inputDate" name="fecha_inicio" required>
          </div>
          <div class="col-6">
              <label for="inputDate2" class="form-label">Fecha Fin</label>
              <input type="date" class="form-control" id="inputDate2" name="fecha_fin" required>
          </div>
          <div class="col-md-4">
              <label for="inputState" class="form-label">Motivo</label>
              <select id="inputState" class="form-select" name="motivo" required>
                  <option selected>Elegir...</option>
                  <?php
                  foreach($lista_opciones as $op){
                    echo '<option>'.$op['nombre'].'</option>';
                  }
                  ?>
              </select>
          </div>
          <div class="col-md-8">
              <label for="inputDescription" class="form-label">Descripción:</label>
              <input type="text" class="form-control" id="inputDescription" name="descripcion" required>
          </div>
          <div class="col-12">
              <button type="submit" class="btn btn-primary" name="solicitar_permiso">Solicitar</button>
          </div>

            <!-- Mensaje de error -->
            <?php if ($show_error): ?>
                <div id="error-message" class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Mensaje de éxito -->
            <?php if ($show_message): ?>
                <div id="success-message" class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>

            <?php endif; ?>


            <script>
              setTimeout(function() {
                  var errorMessage = document.getElementById('error-message');
                  if (errorMessage) {
                      errorMessage.style.display = 'none';
                  }
              }, 3000);
          </script>

      </form>

    </div>




    <?php include 'controladores/footer.php'; ?>
</body>

</html>