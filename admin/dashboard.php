<?php
include "../includes/auth.php";
include "../includes/conexion.php";
$totalServicios =
    $conexion
        ->query("SELECT COUNT(*) total FROM servicios")->fetch_assoc()["total"];
$totalGaleria =
    $conexion
        ->query("SELECT COUNT(*) total FROM galeria")->fetch_assoc()["total"];
$totalPromos =
    $conexion
        ->query("SELECT COUNT(*) total FROM promociones")->fetch_assoc()["total"];
$totalVideos =
    $conexion
        ->query("SELECT COUNT(*) total FROM videos")->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="panel">
        <div class="admin-header">
            <div class="avatar">🎀 </div>
            <div>
                <h1> Hola, Nayeli </h1>
                <p> Gestiona tus servicios, galería, promociones y videos</p>
            </div>
        </div>
        <!-- STATS -->
        <div class="stats">
            <div class="stat-card">
                <h2>
                    <?= $totalServicios ?>
                </h2>
                <p> Servicios </p>
            </div>
            <div class="stat-card">
                <h2>
                    <?= $totalGaleria ?>
                </h2>
                <p>Galería</p>
            </div>
            <div class="stat-card">
                <h2>
                    <?= $totalPromos ?>
                </h2>
                <p>Promociones</p>
            </div>
            <div class="stat-card">
       			 <h2>
           			 <?= $totalVideos ?>
        		</h2>
        		<p>Videos</p>
    		</div>
        </div>
        <?php
        if (isset($_GET["ok"])) {
            echo "<p style='color:green;text-align:center;'>Servicio guardado correctamente</p>";
        }
        ?>
        <div class="acciones">
            <button class="accion-card" onclick="mostrarFormulario()">
                <div>➕</div>
                <h3>Agregar servicio</h3>
                <p>Crea nuevos servicios (Ejemplo: Social, Boda, etc.)</p>
            </button>
            <button class="accion-card" onclick="window.location.href='promociones_admin.php'">
                <div>🎁</div>
                <h3>Promociones</h3>
                <p>Agrega nuevas ofertas o gestiona las existentes</p>
            </button>
            <button class="accion-card" onclick="window.location.href= 'galeria_admin.php'">
                <div>🖼️</div>
                <h3>Galería</h3>
                <p>Administra las imágenes</p>
            </button>
            <button class="accion-card" onclick="window.location.href='videos_admin.php'">
    			<div>🎬</div>
   				<h3>Videos</h3>
    			<p>Administra tus videos y sus portadas</p>
			</button>
            <button class="accion-card" onclick=" window.location.href='editar_servicio.php'">
                <div>✏️</div>
                <h3>Editar servicios </h3>
                <p>Modifica los servicios existentes </p>
            </button>
            <button class="accion-card" onclick=" window.location.href='modal_admin.php'">
                <div>💌</div>
                <h3>Modal bienvenida</h3>
                <p>Edita foto y mensaje inicial</p>
            </button>
            <button class="accion-card" onclick="window.location.href='sobre_admin.php'">
                <div>👩🏻‍🎨</div>
                <h3>Sobre mí</h3>
                <p>Edita foto y descripción personal</p>
            </button>
        </div>
        <div id="formularioServicio" style=" display:none; margin-top:20px;">
            <hr>
            <h2>Nuevo servicio</h2>
            <form action="guardar_servicio.php" method="POST" enctype="multipart/form-data" onsubmit="ocultarFormulario()">
                <label>Nombre del servicio</label>
                <input type="text" name="titulo" placeholder="Ej: Maquillaje bodas" required>
                <label>Descripción</label>
                <textarea name="descripcion" required></textarea>
                <label>Precio</label>
                <input type="text" name="precio" placeholder="Ej: 500" required>
                <label>Imagen</label>
                <input type="file" name="imagen" required>
                <button type="submit">Guardar servicio</button>
                <button type="button" onclick="cerrarFormulario()">Cancelar</button>
            </form>
        </div>
        <a href="logout.php">Cerrar sesión</a>
    </div>
    <script>
        function mostrarFormulario() {
            document.getElementById("formularioServicio").style.display = "block";
        }

        function cerrarFormulario() {
            document.getElementById("formularioServicio").style.display = "none";
        }

        function ocultarFormulario() {
            document.getElementById("formularioServicio").style.display = "none";
        }
    </script>
</body>
</html>