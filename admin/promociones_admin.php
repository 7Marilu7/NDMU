<?php
include "../includes/auth.php";
include "../includes/conexion.php";
$sql = "SELECT * FROM promociones ORDER BY id DESC";
$res = $conexion->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="panel">
        <h1>Promociones</h1>
        <button onclick="mostrarFormulario()">Agregar promoción</button>
        <div id="formulario" style="display:none;">
            <form action="guardar_promocion.php" method="POST" enctype="multipart/form-data">
                <label>Título</label>
                <input type="text" name="titulo" required>
                <label>Descripción</label>
                <textarea name="descripcion" required></textarea>
                <label>Imagen</label>
                <input type="file" name="imagen">
                <button>Guardar</button>
            </form>
        </div>
        <hr>
        <div class="galeria-grid-admin">
            <?php while ($f = $res->fetch_assoc()) { ?>
                <div class="galeria-admin-item">
                    <img src="../uploads/promociones/<?= $f["imagen"] ?>">
                    <h3><?= $f["titulo"] ?></h3>
                    <p><?= $f["descripcion"] ?></p>
                    <button onclick="window.location.href='editar_promocion.php?id=<?= $f["id"] ?>'">Editar</button>
                    <form action="eliminar_promocion.php" method="POST" onsubmit=" return confirmarEliminacion()">
                        <input type="hidden" name="id" value=" <?= $f["id"] ?> ">
                        <button> Eliminar</button>
                    </form>
                </div>
            <?php } ?>
        </div>
        <a href="dashboard.php">Volver</a>
    </div>
    <script>
        function mostrarFormulario() {
            document.getElementById("formulario").style.display = "block";
        }
    </script>
    <script>
        function confirmarEliminacion() {
            return confirm(
                "¿Estás segura de eliminar esta promoción?\n\nEsta acción no se puede deshacer."
            );

        }
    </script>
</body>

</html>