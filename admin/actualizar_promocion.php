<?php
include "../includes/conexion.php";
$id = $_POST["id"];
$titulo = $_POST["titulo"];
$descripcion = $_POST["descripcion"];

if ($_FILES["imagen"]["name"] == "") {
    $sql = "UPDATE promociones SET titulo='$titulo', descripcion='$descripcion' WHERE id=$id";
    $conexion->query($sql);
}
 else {
    $sql = "SELECT imagen FROM promociones WHERE id=$id";
    $res = $conexion->query($sql);
    $f = $res->fetch_assoc();
    $ruta = "../uploads/promociones/" . $f["imagen"];
    if (file_exists($ruta)) {
        unlink($ruta);
    }
    $nueva = $_FILES["imagen"]["name"];
    move_uploaded_file($_FILES["imagen"]["tmp_name"], "../uploads/promociones/" . $nueva);
    $sql = "UPDATE promociones SET titulo='$titulo', descripcion='$descripcion', imagen='$nueva' WHERE id=$id";
    $conexion->query($sql);
}
header("Location: promociones_admin.php?ok=1");
exit;