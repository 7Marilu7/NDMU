<?php
include "../includes/auth.php";
include "../includes/conexion.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $categoria = $_POST["categoria"];
    $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);
    $rutaGuardar = "../uploads/servicios/" . $nombreImagen;
    move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaGuardar);
    $sql = "INSERT INTO servicios(titulo,descripcion,precio,categoria,imagen) VALUES(?,?,?,?,?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $titulo, $descripcion, $precio, $categoria, $nombreImagen);
    $stmt->execute();
    header("Location: dashboard.php?ok=1");
    exit();
}
