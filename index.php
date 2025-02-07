<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'controladores/conexion.php';
    
    $nombre_usuario = $_POST['username'];
    $contraseña = $_POST['password'];

    $sql = "SELECT * FROM Usuarios WHERE nombre_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($contraseña === $row['contrasena']) {
            $_SESSION['nombre_usuario'] = $nombre_usuario;

            if($row['id_tipo_usuario']===1){
                header("Location: vistaAdmin.php");
                exit();
            }
            else{
                header("Location: vistaUsuario.php");
                exit();
            }
        } else {
            $error_message = "La contraseña ingresada es incorrecta.";
        }
    } else {
        $error_message = "El nombre de usuario ingresado no existe.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="CSS/indexStyle.css">
    <link rel="stylesheet" href="CSS/form.css">

    <link rel="shortcut icon" href="../img svg (1).ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
    <?php include 'controladores/header.php'; ?>
<div class="infoPrincipal">
    <div>
        <h2 class="titulo">Inicio de Sesión</h2>
    </div>

    <div class="login-form">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Ingresa tu usuario:</label><br>
            <input type="text" id="username" name="username"><br>
            <label for="password">Ingresa tu contraseña:</label><br>
            <input type="password" id="password" name="password"><br>
            <input type="submit" value="Ingresar">
        </form>

        <?php
            if(isset($error_message)) {
                echo '<br><p class="error">' . $error_message . '</p>';
            }
        ?>
    </div>
</div>




    <?php include 'controladores/footer.php'; ?>
</body>

</html>