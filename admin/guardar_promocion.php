<?php
include "../includes/conexion.php";
$titulo = $_POST["titulo"];
$descripcion = $_POST["descripcion"];
$img = $_FILES["imagen"]["name"];
move_uploaded_file($_FILES["imagen"]["tmp_name"], "../uploads/promociones/" . $img);
$sql = "INSERT INTO promociones(titulo, descripcion, imagen) VALUES('$titulo', '$descripcion', '$img')";
$conexion->query($sql);
header("Location: promociones_admin.php");
?>