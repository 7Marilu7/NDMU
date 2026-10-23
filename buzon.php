<?php
include "includes/conexion.php";

$palabrasProhibidas = [
    "PALABRASPROHIBIDAS"
];

function quitarAcentos($texto) {
    return strtr($texto, [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
        'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u',
        'ñ'=>'n','Ñ'=>'n'
    ]);
}

function normalizarTexto($texto) {
    $texto = strtolower($texto);
    $texto = quitarAcentos($texto);
    $texto = preg_replace('/[^a-z0-9]/', '', $texto);

    return $texto;
}

function contienePalabra($texto, $lista){

    $texto = normalizarTexto($texto);

    foreach($lista as $palabra){

        $palabra = normalizarTexto($palabra);
        $pattern = "/" . implode("[^a-z0-9]*", str_split($palabra)) . "/i";

        if(preg_match($pattern, $texto)){
            return true;
        }
    }

    return false;
}
$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nombre = trim($_POST["nombre"]);
    $comentario = trim($_POST["comentario"]);
    $estrellas = $_POST["estrellas"];

    if(contienePalabra($nombre, $palabrasProhibidas) ||
       contienePalabra($comentario, $palabrasProhibidas)){
        $error = "❌ Tu comentario contiene palabras no permitidas";
    } else {

        $imagen = "";

        if(isset($_FILES["imagen"]) && $_FILES["imagen"]["name"] != ""){
            $imagen = time() . "_" . $_FILES["imagen"]["name"];
            move_uploaded_file($_FILES["imagen"]["tmp_name"], "uploads/reseñas/" . $imagen);
        }

        $stmt = $conexion->prepare("
            INSERT INTO reseñas(nombre, comentario, estrellas, imagen)
            VALUES (?,?,?,?)
        ");
        $stmt->bind_param("ssis", $nombre, $comentario, $estrellas, $imagen);
        $stmt->execute();

        echo "<script>
            alert('💌 Reseña enviada con éxito');
            window.location.href = 'buzon.php';
        </script>";
        exit;
    }
}


$resProm = $conexion->query("SELECT AVG(estrellas) as promedio FROM reseñas");
$promData = $resProm->fetch_assoc();
$promedio = round($promData["promedio"], 1);

$res = $conexion->query("SELECT * FROM reseñas ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<title> Reseñas </title>
<link rel="stylesheet" href="css/style.css">
</head>

<body>

<header class="navbar">
    <div class="logo" id="logo-admin">Nayeli Díaz MakeUp</div>
    <div class="menu-toggle">☰</div>

    <nav>
        <ul class="menu">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="index.php#servicios">Servicios</a></li>
            <li><a href="index.php#galeria">Galería</a></li>
                       <?php
$sqlPromo = "SELECT * FROM promociones";
$promoCheck = $conexion->query($sqlPromo);
$hayPromo = $promoCheck->num_rows > 0;
?>

<?php if ($hayPromo) { ?>
<li><a href="#promo">Promociones</a></li>
<?php } ?>
            <li><a href="index.php#contacto">Agendar cita</a></li>
        </ul>
    </nav>
</header>

<section class="section">

<h3>💌 Comparte tu experiencia  de forma respetuosa</h3>

<?php if($error){ ?>
    <p style="color:red;text-align:center;margin-bottom:15px;">
        <?= $error ?>
    </p>
<?php } ?>

<div class="promedio-estrellas">
    <h3>Calificación general</h3>
    <p style="font-size:22px;">
        <?= str_repeat("⭐", round($promedio)) ?>
    </p>
    <p><?= $promedio ?> / 5</p>
</div>

<form method="POST" enctype="multipart/form-data" class="form-reseña" id="formReseña">

    <input type="text" name="nombre" placeholder="Tu nombre" required>

    <textarea name="comentario" placeholder="Escribe tu experiencia..." required></textarea>

    <label>Calificación:</label>
    <select name="estrellas" required>
        <option value="5">⭐⭐⭐⭐⭐</option>
        <option value="4">⭐⭐⭐⭐</option>
        <option value="3">⭐⭐⭐</option>
        <option value="2">⭐⭐</option>
        <option value="1">⭐</option>
    </select>

    <label>Foto del maquillaje o peinado realizado (opcional):</label>
    <input type="file" name="imagen">

    <button type="submit" class="btn">Enviar reseña</button>

</form>

</section>

<section class="section">

<h2>Reseñas de clientes</h2>

<div class="cartas-grid">

<?php $i = 0; while($r = $res->fetch_assoc()){ ?>

<div class="carta" style="animation-delay: <?= $i * 0.1 ?>s;">

    <div class="carta-header">
        <strong><?= $r["nombre"] ?></strong>
        <span>
            <?= str_repeat("⭐", $r["estrellas"]) ?>
        </span>
    </div>

    <p><?= $r["comentario"] ?></p>

    <?php if($r["imagen"]){ ?>
        <img src="uploads/reseñas/<?= $r["imagen"] ?>">
    <?php } ?>

</div>

<?php $i++; } ?>

</div>

</section>
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