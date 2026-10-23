<?php
include "../includes/auth.php";
include "../includes/conexion.php";
$id = $_GET["id"];
$sql = "SELECT * FROM promociones WHERE id=$id";
$res = $conexion->query($sql);
$fila = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="panel">
        <h1>Editar promoción</h1>
        <form action="actualizar_promocion.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $fila["id"] ?>">
            <label>Título</label>
            <input type="text" name="titulo" value="<?= $fila["titulo"] ?>" required>
            <label>Descripción</label>
            <textarea name="descripcion" required><?= $fila["descripcion"] ?></textarea>
            <label>Imagen actual</label>
            <img src="../uploads/promociones/<?= $fila["imagen"] ?>" style="width:100%;max-width:250px;border-radius:10px;margin-bottom:20px;">
            <label>Nueva imagen (opcional)</label>
            <input type="file" name="imagen">
            <button>Actualizar</button>
        </form>
        <a href="promociones_admin.php">Volver</a>
    </div>
</body>
</html>