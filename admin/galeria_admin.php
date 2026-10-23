<?php
include "../includes/auth.php";
include "../includes/conexion.php";
$sql = "SELECT * FROM galeria ORDER BY id DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="panel">
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'ok') { ?>
        <div id="mensaje-exito">
            ✅ Eliminación exitosa
        </div>
    <?php } ?>
        <h1>Galería</h1>
        <button onclick="mostrarFormulario()">Agregar imagen</button>
        <div id="formulario" style="display:none;">
            <form action="guardar_galeria.php" method="POST" enctype="multipart/form-data">
                <label>Imagen</label>
                <input type="file" name="imagen" required>
                <label>Descripción</label>
                <textarea name="descripcion" maxlength="100"></textarea>
                <button>Guardar</button>
            </form>
        </div>
        <hr>
        <form action="./eliminar_galeria.php" method="POST" onsubmit="return confirmarEliminacion()">

            <div class="galeria-grid-admin">

                <?php while ($fila = $resultado->fetch_assoc()) { ?>
                    <div class="galeria-admin-item">

                        <input type="checkbox" name="eliminar[]" value="<?= $fila["id"] ?>">

                        <img src="../uploads/galeria/<?= $fila["imagen"] ?>">
                        <p><?= $fila["descripcion"] ?></p>

                    </div>
                <?php } ?>

            </div>

            <button type="submit">Eliminar seleccionadas</button>

        </form>
        <a href="dashboard.php">Volver</a>
    </div>
    <script>
        function mostrarFormulario() {
            document.getElementById("formulario").style.display = "block";
        }
    </script>
    <script>
        function mostrarFormulario() {
            document.getElementById("formulario").style.display = "block";
        }

        function confirmarEliminacion() {
            let checkboxes = document.querySelectorAll('input[name="eliminar[]"]:checked');

            if (checkboxes.length === 0) {
                alert("Selecciona al menos una imagen");
                return false;
            }

            return confirm("¿Estás segura de que quieres eliminar " + checkboxes.length + " imagen(es)?");
        }
    </script>
    <script>
        setTimeout(() => {
            const mensaje =
                document.getElementById(
                    "mensaje-exito"
                );
            if (mensaje) {
                mensaje.remove();
            }
        }, 3000);
    </script>
</body>
</html>