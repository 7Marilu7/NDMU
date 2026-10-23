<?php
include "../includes/conexion.php";
$id = $_POST["id"];
$sql = "SELECT imagen FROM promociones WHERE id=$id";
$res = $conexion->query($sql);
$f = $res->fetch_assoc();
unlink("../uploads/promociones/" . $f["imagen"]);
$conexion->query("DELETE FROM promociones WHERE id=$id");
header("Location: promociones_admin.php");
?>