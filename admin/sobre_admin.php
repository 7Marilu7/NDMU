<?php
include "../includes/auth.php";
include "../includes/conexion.php";

$res = $conexion->query("SELECT * FROM sobre_mi LIMIT 1");
$f = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="panel">
        <h1>Editar sección: Sobre mí</h1>
        <form action="guardar_sobre.php" method="POST" enctype="multipart/form-data">
            <label>Título</label>
            <input type="text" name="titulo" value="<?= $f["titulo"] ?>">
            <label>Descripción</label>
            <textarea name="descripcion" rows="8"><?= $f["descripcion"] ?></textarea>
            <label>Nueva foto</label>
            <input type="file" name="imagen">
            <?php if ($f["imagen"]) { ?>
                <img src="../uploads/img/<?= $f["imagen"] ?>" width="120">
            <?php } ?>
            <button>Guardar</button>
        </form>
        <a href="dashboard.php">Volver</a>
    </div>
</body>

</html>