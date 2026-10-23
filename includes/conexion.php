<?php

$conexion =
    new mysqli( "SERVIDOR", "USUARIO",  "CONTRASEÑA",  "BASEDEDATOS"
    );

if ($conexion->connect_error) {
    die("Error conexión");
}
$conexion->set_charset("utf8mb4");
?>