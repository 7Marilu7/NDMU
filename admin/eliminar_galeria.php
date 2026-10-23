<?php
include "../includes/conexion.php";

if (!empty($_POST["eliminar"])) {

    $ids = $_POST["eliminar"];

    foreach ($ids as $id) {
        $sql = "SELECT imagen FROM galeria WHERE id=$id";
        $res = $conexion->query($sql);
        $fila = $res->fetch_assoc();

        if ($fila) {

            $ruta = "../uploads/galeria/" . $fila["imagen"];
            if (file_exists($ruta)) {
                unlink($ruta);
            }
            $conexion->query("DELETE FROM galeria WHERE id=$id");
        }
    }
}
header("Location: galeria_admin.php?msg=ok");
exit;