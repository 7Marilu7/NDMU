<?php
include "../includes/auth.php";
include "../includes/conexion.php";
$sql = "SELECT * FROM servicios ORDER BY id DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar servicios</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="panel">
        <h1>Editar servicios</h1>
        <?php while ($fila = $resultado->fetch_assoc()) { ?>
            <div class="servicio-item">
                <img src="../uploads/servicios/<?= $fila["imagen"] ?>" alt="Servicio" loading="lazy">
                <div>
                    <h3><?= $fila["titulo"] ?></h3>
                    <p><?= $fila["precio"] ?></p>
                    <button onclick="mostrarEditar(<?= $fila['id'] ?>)">Editar</button>
                    <button onclick="eliminarServicio(<?= $fila['id'] ?>)"style="background:red; color:white;"> Eliminar </button>
                </div>
            </div>
            <div id="editar<?= $fila['id'] ?>" style="display:none;">
                <hr>
                <form action="actualizar.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $fila["id"] ?>">
                    <label>Nombre</label>
                    <input type="text" name="titulo" value="<?= $fila["titulo"] ?>" required>
                    <label>Descripción</label>
                    <textarea name="descripcion" required><?= $fila["descripcion"] ?></textarea>
                    <label>Precio</label>
                    <input type="text" name="precio" value="<?= $fila["precio"] ?>" required>
                    <label>Imagen actual</label>
                    <img src="../uploads/servicios/<?= $fila["imagen"] ?>" style="  width:120px; 
                                                                                    height:120px;
                                                                                    object-fit:cover;
                                                                                    border-radius:12px;
                                                                                    display:block;
                                                                                    margin-bottom:15px; ">

                    <label> Nueva imagen (opcional) </label>
                    <input type="file" name="imagen">
                    <hr>
                    <h3>Paquetes</h3>

                    <?php
                    $servicioId = $fila['id'];
                    $paquetes = $conexion->query("SELECT * FROM paquetes WHERE servicio_id=$servicioId");
                    while ($paq = $paquetes->fetch_assoc()) {
                    ?>
                        <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px;">
                            <input type="text"
                                name="paquete_nombre[<?= $paq['id'] ?>]"
                                value="<?= $paq['nombre'] ?>"
                                placeholder="Nombre paquete">

                            <textarea
                                name="paquete_descripcion[<?= $paq['id'] ?>]"
                                placeholder="Descripción"><?= $paq['descripcion'] ?></textarea>

                            <input type="text"
                                name="paquete_precio[<?= $paq['id'] ?>]"
                                value="<?= $paq['precio'] ?>"
                                placeholder="Precio">
                                 <button
            type="button"
            onclick="eliminarPaquete(<?= $paq['id'] ?>)"
            style="background:red; color:white;">
            Eliminar paquete
        </button>
                        </div>
                    <?php } ?>

                    <h4>Agregar nuevo paquete</h4>

                    <input type="text"
                        name="nuevo_paquete_nombre"
                        placeholder="Nombre paquete">

                    <textarea
                        name="nuevo_paquete_descripcion"
                        placeholder="Descripción"></textarea>

                    <input type="text"
                        name="nuevo_paquete_precio"
                        placeholder="Precio">
                         <button>Guardar cambios</button>
                    <button type="button" onclick="cerrarEditar(<?= $fila['id'] ?>)">Cancelar</button>
                </form>
            </div>
            <hr>
        <?php } ?>
        <a href="dashboard.php">Volver</a>
    </div>
    <script>
        function mostrarEditar(id) {
            document.getElementById("editar" + id).style.display = "block";
        }

        function cerrarEditar(id) {
            document.getElementById("editar" + id).style.display = "none";
        }
        function eliminarServicio(id) {
    if (confirm("¿Seguro que deseas eliminar este servicio?")) {
        window.location.href = "eliminar_servicio.php?id=" + id;
    }
}
    </script>
    <script>
        function eliminarPaquete(id) {
            if (confirm("¿Seguro que deseas eliminar este paquete?")) {
                window.location.href = "eliminar_paquete.php?id=" + id;
            }
        }
    </script>
</body>

</html>