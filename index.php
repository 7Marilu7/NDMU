<?php
include "includes/conexion.php";

function resolverEnlaceTikTok($url)
{
    $host = strtolower(parse_url($url, PHP_URL_HOST) ?? "");
    if (!in_array($host, [
        "vm.tiktok.com",
        "vt.tiktok.com"
    ], true)) {
        return $url;
    }

    if (!function_exists("curl_init")) {
        return $url;
    }

    $curl = curl_init($url);

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => "Mozilla/5.0",
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS
    ]);

    curl_exec($curl);

    $urlFinal = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    $codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($codigo >= 200 && $codigo < 400 && !empty($urlFinal)) {
        return $urlFinal;
    }

    return $url;
}
$sqlVideos = "SELECT * FROM videos ORDER BY id DESC";
$videos = $conexion->query($sqlVideos);

$sql = "SELECT * FROM servicios ORDER BY id DESC";
$resultado = $conexion->query($sql);

$sqlModal = "SELECT * FROM modal_bienvenida LIMIT 1";
$modal = $conexion->query($sqlModal);
$m = $modal->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nayeli Díaz MakeUp</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <header class="navbar">
        <div class="logo" id="logo-admin">Nayeli Díaz MakeUp</div>
        <div class="menu-toggle">☰</div>
        <nav>
            <ul class="menu">
                <li><a href="#">Inicio</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#galeria">Galería</a></li>
                <li><a href="#videos">Videos</a></li>
                <?php
$sqlPromo = "SELECT * FROM promociones";
$promoCheck = $conexion->query($sqlPromo);
$hayPromo = $promoCheck->num_rows > 0;
?>

<?php if ($hayPromo) { ?>
<li><a href="#promo">Promociones</a></li>
<?php } ?>
                <li><a href="#contacto">Agendar cita</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-text">
            <h1>Maquillaje profesional para momentos especiales</h1>
            <p>Looks elegantes para bodas, graduaciones, eventos y más.</p>
        </div>
        <div class="hero-image">
            <img src="uploads/img/logoNDMU.jpeg" alt="maquillaje">
        </div>
    </section>

    <section class="section" id="servicios">
        <h2>Servicios destacados</h2>

        <div class="cards servicios-grid">

            <?php while ($fila = $resultado->fetch_assoc()) { ?>

                <div class="card" onclick="abrirModalServicio(<?= $fila['id'] ?>)">
                    <img src="uploads/servicios/<?= $fila["imagen"] ?>">
                    <h3><?= $fila["titulo"] ?></h3>
                    <p class="descripcion-servicio"><?= nl2br($fila["descripcion"]) ?></p>
                    <p><strong><?= $fila["precio"] ?></strong></p>
                    <p><?= $fila["categoria"] ?></p>
                </div>

                <?php
                $idServicio = $fila['id'];
                $paquetes = $conexion->query("SELECT * FROM paquetes WHERE servicio_id = $idServicio");
                ?>

                <div class="modal-servicio" id="modal<?= $fila['id'] ?>">
                    <div class="modal-paquetes-contenido">

                        <span onclick="cerrarModalServicio(<?= $fila['id'] ?>)">✕</span>

                        <h2><?= $fila["titulo"] ?></h2>

                        <?php while ($paq = $paquetes->fetch_assoc()) { ?>
                            <div class="paquete-item">
                                <h3><?= $paq["nombre"] ?></h3>
                                <p class="descripcion-paquete"><?= nl2br($paq["descripcion"]) ?></p>
                                <strong><?= $paq["precio"] ?></strong>
                            </div>
                            <hr>
                        <?php } ?>

                    </div>
                </div>

            <?php } ?>

        </div>
    </section>

    <?php
$sql = "SELECT * FROM promociones ORDER BY id DESC";
$promo = $conexion->query($sql);
$totalPromo = $promo->num_rows;
?>

<?php if ($totalPromo > 0) { ?>

<section class="section" id="promo">
    <h2>Promociones</h2>

    <div class="cards promo-grid">
        <?php while ($p = $promo->fetch_assoc()) { ?>
            <div class="card">
                <img src="uploads/promociones/<?= $p["imagen"] ?>">
                <h3><?= $p["titulo"] ?></h3>
                <p><?= $p["descripcion"] ?></p>
            </div>
        <?php } ?>
    </div>
</section>

<?php } ?>

    <?php
    $sql = "SELECT * FROM galeria ORDER BY id DESC LIMIT 3";
    $galeria = $conexion->query($sql);
    ?>
    <section class="section" id="galeria">
        <h2>Galería</h2>
        <div class="cards galeria-grid-home">
            <?php while ($f = $galeria->fetch_assoc()) { ?>
                <div class="card">
                    <img src="uploads/galeria/<?= $f["imagen"] ?>" loading="lazy">
                    <p><?= $f["descripcion"] ?></p>
                </div>
            <?php } ?>
        </div>
        <a href="galeria.php" class="btn">Ver más</a>
    </section>
    

<section class="section" id="videos">
    <h2>Videos</h2>

    <div class="cards videos-grid">

        <?php while ($v = $videos->fetch_assoc()) {
           
            $urlVideo = $v["url"];
            $urlAnalizar = resolverEnlaceTikTok($urlVideo);
            $hostVideo = strtolower(parse_url($urlAnalizar, PHP_URL_HOST) ?? "");
            $idVideo = "";
            $plataforma = "";
            $reproductor = "";
            $miniatura = "";

            if (preg_match('/(^|\.)youtu\.be$/', $hostVideo) ||
                preg_match('/(^|\.)youtube\.com$/', $hostVideo)) {

                if (preg_match('~youtu\.be/([a-zA-Z0-9_-]{11})~', $urlVideo, $mId)) {
                    $idVideo = $mId[1];
                } elseif (preg_match('~[?&]v=([a-zA-Z0-9_-]{11})~', $urlVideo, $mId)) {
                    $idVideo = $mId[1];
                } elseif (preg_match('~/(?:shorts|embed)/([a-zA-Z0-9_-]{11})~', $urlVideo, $mId)) {
                    $idVideo = $mId[1];
                }

                if ($idVideo !== "") {
                    $plataforma = "youtube";
                    $reproductor = "https://www.youtube-nocookie.com/embed/" . $idVideo;
                    $miniatura = "https://img.youtube.com/vi/" . $idVideo . "/hqdefault.jpg";
                }

            } elseif (preg_match('/(^|\.)tiktok\.com$/', $hostVideo)) {

               if (preg_match('~/video/([0-9]+)~', $urlAnalizar, $mId)) {
                    $idVideo = $mId[1];
                    $plataforma = "tiktok";
                    $reproductor = "https://www.tiktok.com/player/v1/" . $idVideo;
                }
                } elseif (preg_match('/(^|\.)instagram\.com$/', $hostVideo)) {

                    if (preg_match('~/(?:reel|reels|p|tv)/([A-Za-z0-9_-]+)~', $urlAnalizar, $mId)) {
                        $idVideo = $mId[1];
                        $plataforma = "instagram";

                        if (preg_match('~/reels?/~', $urlAnalizar)) {
                            $reproductor = "https://www.instagram.com/reel/" . $idVideo . "/embed/";
                        } elseif (preg_match('~/tv/~', $urlAnalizar)) {
                            $reproductor = "https://www.instagram.com/tv/" . $idVideo . "/embed/";
                        } else {
                            $reproductor = "https://www.instagram.com/p/" . $idVideo . "/embed/";
                        }
                    }
                }

            if (!empty($v["portada"])) {
                $miniatura = "uploads/" . $v["portada"];
            }
        ?>
			<div class="video-item">
            <button
                type="button"
                class="video-portada"
                data-titulo="<?= htmlspecialchars($v["titulo"], ENT_QUOTES, "UTF-8") ?>"
                data-reproductor="<?= htmlspecialchars($reproductor, ENT_QUOTES, "UTF-8") ?>"
                data-plataforma="<?= htmlspecialchars($plataforma, ENT_QUOTES, "UTF-8") ?>"
                data-url-original="<?= htmlspecialchars($urlVideo, ENT_QUOTES, "UTF-8") ?>"
                data-url-oembed="<?= htmlspecialchars($urlAnalizar, ENT_QUOTES, "UTF-8") ?>"
                data-autothumb="<?= $plataforma === "tiktok" && empty($v["portada"]) ? "1" : "0" ?>"
                aria-label="Reproducir <?= htmlspecialchars($v["titulo"], ENT_QUOTES, "UTF-8") ?>">

                <?php if ($miniatura !== "") { ?>
                    <img
                        src="<?= htmlspecialchars($miniatura, ENT_QUOTES, "UTF-8") ?>"
                        alt="Portada de <?= htmlspecialchars($v["titulo"], ENT_QUOTES, "UTF-8") ?>"
                        loading="lazy"
                        onerror="this.style.display='none'; this.parentElement.classList.add('sin-portada');">
                <?php } ?>

                <span class="video-play" aria-hidden="true">▶</span>
                <span class="video-titulo">
                    <?= htmlspecialchars($v["titulo"], ENT_QUOTES, "UTF-8") ?>
                </span>
            </button>
       
            <?php if (!empty($v["descripcion"])) { ?>
                <p class="video-descripcion">
                    <?= nl2br(htmlspecialchars($v["descripcion"], ENT_QUOTES, "UTF-8")) ?>
                </p>
            <?php } ?>
                 </div>
        <?php } ?>

    </div>
        <a href="videos.php" class="btn">Ver más videos</a>
</section>

<div id="modalVideo" class="modal-video" aria-hidden="true">
    <div class="modal-video-contenido" role="dialog" aria-modal="true" aria-label="Reproductor de video">
        <button type="button" id="cerrarModalVideo" class="cerrar-modal-video" aria-label="Cerrar video">✕</button>
        <h3 id="tituloModalVideo"></h3>
        <div class="modal-video-reproductor" id="contenedorReproductor"></div>
    </div>
</div>

    <section class="section" id="ubicacion">

        <h2>Servicio a domicilio</h2>

        <p class="ubicacion-texto"> Realizo servicios de maquillaje a domicilio en <strong>Atizapán de Zaragoza y alrededores</strong> </p>
        <div class="mapa-container">
            <iframe
                src="https://www.google.com/maps?q=Atizapán+de+Zaragoza,+Estado+de+México&output=embed"
                loading="lazy"
                allowfullscreen>
            </iframe>

        </div>

    </section>

    <section class="section aviso-domicilio">
        <div class="burbuja-info">
            <span class="icono-info">📍</span>
            <div>
                <h3>Información importante</h3>
                <p> El servicio a domicilio tiene un <strong>costo adicional</strong> dependiendo de la distancia y ubicación.
                    El monto se confirma al momento de agendar.</p>
            </div>
        </div>

    </section>

    <section class="section" id="contacto">
        <h2>Agenda tu cita</h2>
        <p class="contacto-texto"> Para agendar tu cita o conocer más de mi trabajo, visita mis redes sociales </p>

        <div class="redes-sociales">
            <a href="http://wa.me/5559875027"
                target="_blank">

                <img src="uploads/img/logoWhatsapp.webp"
                    alt="WhatsApp">
            </a>
            <a href="https://www.instagram.com/nayeli_diazmakeup?igsh=MzducXZuZ3RubTIx"
                target="_blank">

                <img src="uploads/img/logoInsta.webp"
                    alt="Instagram">
            </a>
            <a href="https://www.facebook.com/share/1BDmQua9jT/?mibextid=wwXIfr"
                target="_blank">

                <img src="uploads/img/logoFacebook.webp"
                    alt="Facebook">
            </a>
             <a href="https://www.tiktok.com/@nayelidiaz053?_r=1&_t=ZS-96hLpEilfyS"
                target="_blank">

                <img src="uploads/img/logotiktok.avif"
                    alt="Tiktok">
 			</a>
        </div>
    </section>

    <?php
    $sobre = $conexion->query("SELECT * FROM sobre_mi LIMIT 1");
    $s = $sobre->fetch_assoc();
    ?>
    <section class="sobre-mi">
        <div class="sobre-imagen">
            <img src="uploads/img/<?= $s["imagen"] ?>">
        </div>
        <div class="sobre-texto">
            <h2><?= $s["titulo"] ?></h2>
            <p><?= nl2br($s["descripcion"]) ?></p>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-line"></div>
        <h3>Nayeli Díaz MakeUp</h3>
        <p class="footer-copy"> © <?= date("Y") ?> Todos los derechos reservados</p>
    </footer>

   <a href="buzon.php" class="buzon-flotante">💌</a>

    <div id="modalBienvenida">
        <div class="modal-contenido">
            <span id="cerrarModal">✕</span>
            <img src="uploads/img/<?= $m["imagen"] ?>" alt="Nayeli">
            <h2><?= $m["titulo"] ?></h2>
            <p><?= $m["descripcion"] ?></p>
        </div>
    </div>

    <script>
        let toggle = document.querySelector(".menu-toggle");
        let menu = document.querySelector(".menu");
        toggle.onclick = () => {
            menu.classList.toggle("activo");
        }
    </script>

    <script>
        let contadorClicks = 0;
        document.getElementById("logo-admin").addEventListener("click", function() {
            contadorClicks++;
            if (contadorClicks >= 5) {
                window.location.href = "admin/login.php";
            }
            setTimeout(() => {
                contadorClicks = 0;
            }, 2000);
        });
    </script>

    <script>
        let modal = document.getElementById("modalBienvenida");
        let cerrar = document.getElementById("cerrarModal");
        cerrar.onclick = () => {
            modal.style.display = "none";
        };
    </script>
    <script>
function abrirModalServicio(id){
    document.getElementById("modal"+id).style.display="flex";
}

function cerrarModalServicio(id){
    document.getElementById("modal"+id).style.display="none";
}
</script>
<script>

if (!sessionStorage.getItem("modalVisto")) {
    document.getElementById("modalBienvenida").style.display = "flex";
    sessionStorage.setItem("modalVisto", "1");
} else {
    document.getElementById("modalBienvenida").style.display = "none";
}

document.getElementById("cerrarModal").onclick = function () {
    document.getElementById("modalBienvenida").style.display = "none";
};
</script>
    
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modalVideo");
    const titulo = document.getElementById("tituloModalVideo");
    const contenedor = document.getElementById("contenedorReproductor");
    const cerrar = document.getElementById("cerrarModalVideo");

    document.querySelectorAll('[data-autothumb="1"]').forEach(function (tarjeta) {
        const url = tarjeta.dataset.urlOembed || tarjeta.dataset.urlOriginal;

        fetch("https://www.tiktok.com/oembed?url=" + encodeURIComponent(url))
            .then(function (respuesta) {
                if (!respuesta.ok) throw new Error("No se obtuvo la miniatura");
                return respuesta.json();
            })
            .then(function (datos) {
                if (!datos.thumbnail_url) return;

                let imagen = tarjeta.querySelector("img");

                if (!imagen) {
                    imagen = document.createElement("img");
                    imagen.alt = "Portada de " + tarjeta.dataset.titulo;
                    imagen.loading = "lazy";
                    tarjeta.prepend(imagen);
                }

                imagen.src = datos.thumbnail_url;
                imagen.style.display = "block";
                tarjeta.classList.remove("sin-portada");

                imagen.onerror = function () {
                    imagen.style.display = "none";
                    tarjeta.classList.add("sin-portada");
                };
            })
            .catch(function () {
                tarjeta.classList.add("sin-portada");
            });
    });

    document.querySelectorAll(".video-portada").forEach(function (tarjeta) {
        tarjeta.addEventListener("click", function () {
            const plataforma = tarjeta.dataset.plataforma;
            let url = tarjeta.dataset.reproductor;

            titulo.textContent = tarjeta.dataset.titulo;
            contenedor.replaceChildren();

            if (!url) {
                const aviso = document.createElement("p");
                aviso.textContent = "No se pudo preparar el reproductor. Puedes abrir el video directamente.";
                contenedor.appendChild(aviso);

                const enlace = document.createElement("a");
                enlace.href = tarjeta.dataset.urlOriginal;
                enlace.target = "_blank";
                enlace.rel = "noopener noreferrer";
                enlace.textContent = "Abrir video en su plataforma";
                contenedor.appendChild(enlace);
            } else {
                if (plataforma === "youtube" || plataforma === "tiktok") {
                    url += "?autoplay=1";
                }

                const iframe = document.createElement("iframe");
                iframe.src = url;
                iframe.title = tarjeta.dataset.titulo;
                iframe.allow = "autoplay; encrypted-media; fullscreen; picture-in-picture";
                iframe.allowFullscreen = true;
                iframe.loading = "eager";

                if (plataforma === "tiktok" || plataforma === "instagram") {
                    iframe.classList.add("vertical");
                }

                contenedor.appendChild(iframe);
            }

            modal.classList.add("activo");
            modal.setAttribute("aria-hidden", "false");
            document.body.style.overflow = "hidden";
        });
    });

    function cerrarVideo() {
        modal.classList.remove("activo");
        modal.setAttribute("aria-hidden", "true");
        contenedor.replaceChildren();
        document.body.style.overflow = "";
    }

    cerrar.addEventListener("click", cerrarVideo);

    modal.addEventListener("click", function (evento) {
        if (evento.target === modal) cerrarVideo();
    });

    document.addEventListener("keydown", function (evento) {
        if (evento.key === "Escape" && modal.classList.contains("activo")) {
            cerrarVideo();
        }
    });
});
</script>
</body>
</html>