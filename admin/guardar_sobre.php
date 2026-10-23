<?php
include "../includes/conexion.php";

$titulo = $_POST["titulo"];
$descripcion = $_POST["descripcion"];
$imagen = "";

if (!empty($_FILES["imagen"]["name"])) {
    $imagen = time() . $_FILES["imagen"]["name"];
    move_uploaded_file($_FILES["imagen"]["tmp_name"], "../uploads/img/" . $imagen);
    $sql = "UPDATE sobre_mi SET titulo='$titulo', descripcion='$descripcion', imagen='$imagen' WHERE id=1";
} else {
    $sql = "UPDATE sobre_mi SET titulo='$titulo', descripcion='$descripcion' WHERE id=1";
}

$conexion->query($sql);
header("Location: sobre_admin.php");