<?php
include "includes/conexion.php";
$sql = "SELECT * FROM galeria ORDER BY id DESC";
$imagenes = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería</title>
    <link rel="stylesheet" href="css/style.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
<header class="navbar">
    <div class="logo">Nayeli Díaz MakeUp</div>
    <div class="menu-toggle">☰</div>
    <nav>
        <ul class="menu">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="index.php#servicios">Servicios</a></li>

            <?php
            $sqlPromo = "SELECT * FROM promociones";
            $promoCheck = $conexion->query($sqlPromo);
            $hayPromo = $promoCheck && $promoCheck->num_rows > 0;
            ?>

            <?php if ($hayPromo) { ?>
                <li><a href="index.php#promo">Promociones</a></li>
            <?php } ?>

            <li><a href="videos.php">Videos</a></li>

            <li><a href="index.php#contacto">Agendar cita</a></li>
        </ul>
    </nav>
</header>
    <section class="section">
       <h2>Galería de trabajos</h2>
        <p class="texto-galeria">
        Aquí podrás ver algunos de los looks que he creado para mis clientas.<br>
    Haz clic en cualquier imagen para verla más grande y apreciar cada detalle ✨
</p>
        <div class="galeria-grid">
            <?php while ($img = $imagenes->fetch_assoc()) { ?>
                <div class="galeria-item">
                    <img src="uploads/galeria/<?= $img["imagen"] ?>" loading="lazy" onclick="abrirModalGaleria(<?= $img['id'] ?>)">
                    <p><?= $img["descripcion"] ?></p>
                </div>
            <?php } ?>
        </div>
    </section>
<div id="modalGaleria" class="modal-galeria">

    <span class="cerrar-galeria" onclick="cerrarModalGaleria()">✕</span>

    <span class="flecha izquierda" onclick="imagenAnterior()">❮</span>

    <img id="imagenGrande">

    <span class="flecha derecha" onclick="imagenSiguiente()">❯</span>

</div>

<script>
let imagenes = [
<?php
$imagenes2 = $conexion->query("SELECT * FROM galeria ORDER BY id DESC");
while($img2 = $imagenes2->fetch_assoc()){
?>
{
    id: <?= $img2['id'] ?>,
    archivo: "<?= $img2['imagen'] ?>"
},
<?php } ?>
];

let indiceActual = 0;

function abrirModalGaleria(id){

    indiceActual = imagenes.findIndex(img => img.id == id);

    document.getElementById("imagenGrande").src =
        "uploads/galeria/" + imagenes[indiceActual].archivo;

    document.getElementById("modalGaleria").style.display = "flex";
}

function cerrarModalGaleria(){
    document.getElementById("modalGaleria").style.display = "none";
}

function imagenSiguiente(){
    indiceActual++;

    if(indiceActual >= imagenes.length){
        indiceActual = 0;
    }

    document.getElementById("imagenGrande").src =
        "uploads/galeria/" + imagenes[indiceActual].archivo;
}

function imagenAnterior(){
    indiceActual--;

    if(indiceActual < 0){
        indiceActual = imagenes.length - 1;
    }

    document.getElementById("imagenGrande").src =
        "uploads/galeria/" + imagenes[indiceActual].archivo;
}
</script>
<footer class="footer">
    <div class="footer-line"></div>
    <h3>Nayeli Díaz MakeUp</h3>
    <p class="footer-copy"> © <?= date("Y") ?> Todos los derechos reservados</p>
</footer>
<script>
let toggle = document.querySelector(".menu-toggle");
let menu = document.querySelector(".menu");

toggle.onclick = () => {
    menu.classList.toggle("activo");
}
</script>
</body>

</html>