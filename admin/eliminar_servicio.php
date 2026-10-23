<?php
include "../includes/auth.php";
include "../includes/conexion.php";

$id = $_GET["id"];

$sql = "SELECT imagen FROM servicios WHERE id=?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$res = $stmt->get_result();
$fila = $res->fetch_assoc();

$ruta = "../uploads/servicios/" . $fila["imagen"];

if (file_exists($ruta)) {
    unlink($ruta);
}

$sqlPaquetes = "DELETE FROM paquetes WHERE servicio_id=?";
$stmtPaquetes = $conexion->prepare($sqlPaquetes);
$stmtPaquetes->bind_param("i", $id);
$stmtPaquetes->execute();

$sqlEliminar = "DELETE FROM servicios WHERE id=?";
$stmtEliminar = $conexion->prepare($sqlEliminar);
$stmtEliminar->bind_param("i", $id);
$stmtEliminar->execute();

header("Location: editar_servicio.php");
exit();