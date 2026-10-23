<?php
include "../includes/conexion.php";
$descripcion = $_POST["descripcion"];
$imagen = time() . "_" . basename($_FILES["imagen"]["name"]);
$ruta = "../uploads/galeria/" . $imagen;
move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
$sql = "INSERT INTO galeria(imagen,descripcion) VALUES(?,?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $imagen, $descripcion);
$stmt->execute();
header("Location: galeria_admin.php");
exit();