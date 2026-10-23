<?php
include "../includes/auth.php";
include "../includes/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id > 0) {
    $sql = "DELETE FROM paquetes WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: editar_servicio.php");
exit();
?>