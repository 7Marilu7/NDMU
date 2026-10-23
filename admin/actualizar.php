<?php
include "../includes/auth.php";
include "../includes/conexion.php";

$id = $_POST["id"];
$titulo = $_POST["titulo"];
$descripcion = $_POST["descripcion"];
$precio = $_POST["precio"];
$categoria = $_POST["categoria"];

if (!empty($_FILES["imagen"]["name"])) {

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
    $imagen = time() . $_FILES["imagen"]["name"];
    move_uploaded_file($_FILES["imagen"]["tmp_name"], "../uploads/servicios/" . $imagen);
    $sql = "UPDATE servicios SET titulo=?, descripcion=?, precio=?, categoria=?, imagen=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssi", $titulo, $descripcion, $precio, $categoria, $imagen, $id);
} else {
    $sql = "UPDATE servicios SET titulo=?, descripcion=?, precio=?, categoria=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssi", $titulo, $descripcion, $precio, $categoria, $id);
}
$stmt->execute();
if (isset($_POST["paquete_nombre"])) {

    foreach ($_POST["paquete_nombre"] as $paqId => $nombre) {

        $descripcionPaq = $_POST["paquete_descripcion"][$paqId];
        $precioPaq = $_POST["paquete_precio"][$paqId];

        $sqlPaq = "UPDATE paquetes 
                   SET nombre=?, descripcion=?, precio=? 
                   WHERE id=?";

        $stmtPaq = $conexion->prepare($sqlPaq);
        $stmtPaq->bind_param(
            "sssi",
            $nombre,
            $descripcionPaq,
            $precioPaq,
            $paqId
        );

        $stmtPaq->execute();
    }
}

if (
    !empty($_POST["nuevo_paquete_nombre"]) &&
    !empty($_POST["nuevo_paquete_descripcion"]) &&
    !empty($_POST["nuevo_paquete_precio"])
) {

    $nuevoNombre = $_POST["nuevo_paquete_nombre"];
    $nuevoDesc = $_POST["nuevo_paquete_descripcion"];
    $nuevoPrecio = $_POST["nuevo_paquete_precio"];

    $sqlNuevo = "INSERT INTO paquetes
                (servicio_id, nombre, descripcion, precio)
                VALUES (?, ?, ?, ?)";

    $stmtNuevo = $conexion->prepare($sqlNuevo);

    $stmtNuevo->bind_param(
        "isss",
        $id,
        $nuevoNombre,
        $nuevoDesc,
        $nuevoPrecio
    );

    $stmtNuevo->execute();
}

header("Location: editar_servicio.php");
exit();