<?php
include "../includes/conexion.php";
$titulo = $_POST["titulo"];
$descripcion = $_POST["descripcion"];
$sqlImagen = "";
if (!empty($_FILES["imagen"]["name"])) {
    $nombre = time() . $_FILES["imagen"]["name"];
    move_uploaded_file($_FILES["imagen"]["tmp_name"], "../uploads/img/" . $nombre);
    $sqlImagen = ", imagen='$nombre'";
}
$sql = "UPDATE modal_bienvenida SET titulo='$titulo', descripcion='$descripcion' $sqlImagen WHERE id=1";
$conexion->query($sql);
header("Location: modal_admin.php");
?>