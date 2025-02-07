<?php
$servername = "localhost";
$username = "asistencia";
$password = "g3sti0n@1954_"; 
$database = "AsistenciaPersonal";

$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Error al conectar a la base de datos: " . $conn->connect_error);
}
?>
