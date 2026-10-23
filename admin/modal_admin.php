<?php

include "../includes/auth.php";
include "../includes/conexion.php";

$res =$conexion->query( "SELECT * FROM modal_bienvenida LIMIT 1" );
$f =$res->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="panel">
        <h1>Editar bienvenida</h1>
        <form action="guardar_modal.php"
            method="POST"
            enctype="multipart/form-data">
            <label>Título</label>
            <input type="text" name="titulo" value=" <?= $f["titulo"] ?>">
            <label>Descripción</label>
            <textarea name="descripcion"> <?= $f["descripcion"] ?></textarea>
            <label>Imagen</label>
            <input type="file" name="imagen">
            <button>Guardar</button>
        </form>
        <a href="dashboard.php">Volver</a>
    </div>
</body> 
</html>