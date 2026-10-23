
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

function obtenerRutaPortada($portada)
{
    $portada = trim($portada);

    if ($portada === "") {
        return "";
    }

    $portada = str_replace("\\", "/", $portada);

    if (
        preg_match('~^(?:https?:)?//~i', $portada) ||
        preg_match('~(^|/)\.\.(/|$)~', $portada)
    ) {
        return "";
    }

    $portada = ltrim($portada, "/");

    $candidatas = [];
    if (strpos($portada, "uploads/") === 0) {
        $candidatas[] = $portada;
    } else {
    
        $candidatas[] = "uploads/" . $portada;
        $candidatas[] = "uploads/videos/" . basename($portada);
    }

    foreach ($candidatas as $ruta) {
        if (is_file(__DIR__ . "/" . $ruta)) {
            return $ruta;
        }
    }
    return $candidatas[0];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de videos</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"
    >
</head>

<body class="pagina-videos">
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

            <li><a href="galeria.php">Galería</a></li>

            <li><a href="index.php#contacto">Agendar cita</a></li>
        </ul>
    </nav>
</header>
    <main>

        <section class="section" id="todos-los-videos">

            <h2 style="text-align:center;">
                Galería de videos
            </h2>

            <p class="texto-galeria">
               Aquí podrás ver algunos de los looks que he creado para mis clientas.
			Haz clic en cualquier vídeo para reproducirlo. ✨
            </p>

            <?php if ($videos && $videos->num_rows > 0) { ?>

                <div class="videos-grid">

                    <?php while ($v = $videos->fetch_assoc()) {
                        $urlVideo = trim($v["url"] ?? "");

                        $urlAnalizar = resolverEnlaceTikTok($urlVideo);

                        $hostVideo = strtolower(
                            parse_url($urlAnalizar, PHP_URL_HOST) ?? ""
                        );

                        $idVideo = "";
                        $plataforma = "";
                        $reproductor = "";
                        $miniatura = "";
                        if (
                            preg_match('/(^|\.)youtu\.be$/', $hostVideo) ||
                            preg_match('/(^|\.)youtube\.com$/', $hostVideo)
                        ) {

                            if (
                                preg_match(
                                    '~youtu\.be/([a-zA-Z0-9_-]{11})~',
                                    $urlVideo,
                                    $mId
                                )
                            ) {
                                $idVideo = $mId[1];

                            } elseif (
                                preg_match(
                                    '~[?&]v=([a-zA-Z0-9_-]{11})~',
                                    $urlVideo,
                                    $mId
                                )
                            ) {
                                $idVideo = $mId[1];

                            } elseif (
                                preg_match(
                                    '~/(?:shorts|embed)/([a-zA-Z0-9_-]{11})~',
                                    $urlVideo,
                                    $mId
                                )
                            ) {
                                $idVideo = $mId[1];
                            }

                            if ($idVideo !== "") {
                                $plataforma = "youtube";

                                $reproductor =
                                    "https://www.youtube-nocookie.com/embed/" .
                                    $idVideo;

                                $miniatura =
                                    "https://img.youtube.com/vi/" .
                                    $idVideo .
                                    "/hqdefault.jpg";
                            }

                        } elseif (
                            preg_match('/(^|\.)tiktok\.com$/', $hostVideo)
                        ) {

                            if (
                                preg_match(
                                    '~/video/([0-9]+)~',
                                    $urlAnalizar,
                                    $mId
                                )
                            ) {
                                $idVideo = $mId[1];

                                $plataforma = "tiktok";

                                $reproductor =
                                    "https://www.tiktok.com/player/v1/" .
                                    $idVideo;
                            }

                        } elseif (
                            preg_match('/(^|\.)instagram\.com$/', $hostVideo)
                        ) {

                            if (
                                preg_match(
                                    '~/(?:reel|reels|p|tv)/([A-Za-z0-9_-]+)~',
                                    $urlAnalizar,
                                    $mId
                                )
                            ) {
                                $idVideo = $mId[1];

                                $plataforma = "instagram";

                                if (preg_match('~/reels?/~', $urlAnalizar)) {

                                    $reproductor =
                                        "https://www.instagram.com/reel/" .
                                        $idVideo .
                                        "/embed/";

                                } elseif (preg_match('~/tv/~', $urlAnalizar)) {

                                    $reproductor =
                                        "https://www.instagram.com/tv/" .
                                        $idVideo .
                                        "/embed/";

                                } else {

                                    $reproductor =
                                        "https://www.instagram.com/p/" .
                                        $idVideo .
                                        "/embed/";
                                }
                            }
                        }

                        if (!empty($v["portada"])) {
                            $miniatura = obtenerRutaPortada($v["portada"]);
                        }

                        $tituloOriginal = $v["titulo"] ?? "";
                        $descripcionOriginal = $v["descripcion"] ?? "";

                        $tituloSeguro = htmlspecialchars(
                            $tituloOriginal,
                            ENT_QUOTES,
                            "UTF-8"
                        );

                        $descripcionSegura = htmlspecialchars(
                            $descripcionOriginal,
                            ENT_QUOTES,
                            "UTF-8"
                        );

                        $reproductorSeguro = htmlspecialchars(
                            $reproductor,
                            ENT_QUOTES,
                            "UTF-8"
                        );

                        $urlVideoSegura = htmlspecialchars(
                            $urlVideo,
                            ENT_QUOTES,
                            "UTF-8"
                        );

                        $urlAnalizarSegura = htmlspecialchars(
                            $urlAnalizar,
                            ENT_QUOTES,
                            "UTF-8"
                        );

                        $plataformaSegura = htmlspecialchars(
                            $plataforma,
                            ENT_QUOTES,
                            "UTF-8"
                        );

                        $miniaturaSegura = htmlspecialchars(
                            $miniatura,
                            ENT_QUOTES,
                            "UTF-8"
                        );
                    ?>

                        <div class="video-item">

                            <button
                                type="button"
                                class="video-portada <?php echo $miniatura !== '' ? '' : 'sin-portada'; ?>"
                                data-titulo="<?php echo $tituloSeguro; ?>"
                                data-reproductor="<?php echo $reproductorSeguro; ?>"
                                data-plataforma="<?php echo $plataformaSegura; ?>"
                                data-url-original="<?php echo $urlVideoSegura; ?>"
                                data-url-oembed="<?php echo $urlAnalizarSegura; ?>"
                                data-autothumb="<?php echo $plataforma === 'tiktok' && empty($v['portada']) ? '1' : '0'; ?>"
                                aria-label="Reproducir <?php echo $tituloSeguro; ?>"
                            >

                                <?php if ($miniatura !== "") { ?>

                                    <img
                                        src="<?php echo $miniaturaSegura; ?>"
                                        alt="Portada de <?php echo $tituloSeguro; ?>"
                                        loading="lazy"
                                        onerror="this.style.display='none'; this.parentElement.classList.add('sin-portada');"
                                    >

                                <?php } ?>

                                <span class="video-play" aria-hidden="true">
                                    ▶
                                </span>

                                <span class="video-titulo">
                                    <?php echo $tituloSeguro; ?>
                                </span>

                            </button>

                            <?php if (trim($descripcionOriginal) !== "") { ?>

                                <p class="video-descripcion">
                                    <?php echo nl2br($descripcionSegura); ?>
                                </p>

                            <?php } ?>

                        </div>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <p style="text-align:center;">
                    Todavía no hay videos publicados.
                </p>

            <?php } ?>

            <div style="text-align:center; margin-top:35px;">

                <a href="index.php" class="btn">
                    Volver al inicio
                </a>

            </div>

        </section>

    </main>

    <div
        id="modalVideo"
        class="modal-video"
        aria-hidden="true"
    >

        <div
            class="modal-video-contenido"
            role="dialog"
            aria-modal="true"
            aria-label="Reproductor de video"
        >

            <button
                type="button"
                id="cerrarModalVideo"
                class="cerrar-modal-video"
                aria-label="Cerrar video"
            >
                ✕
            </button>

            <h3 id="tituloModalVideo"></h3>

            <div
                class="modal-video-reproductor"
                id="contenedorReproductor"
            ></div>

        </div>

    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {

        const modal = document.getElementById("modalVideo");
        const titulo = document.getElementById("tituloModalVideo");
        const contenedor = document.getElementById("contenedorReproductor");
        const cerrar = document.getElementById("cerrarModalVideo");

        document.querySelectorAll('[data-autothumb="1"]').forEach(function (tarjeta) {

            const url =
                tarjeta.dataset.urlOembed ||
                tarjeta.dataset.urlOriginal;

            fetch(
                "https://www.tiktok.com/oembed?url=" +
                encodeURIComponent(url)
            )
                .then(function (respuesta) {

                    if (!respuesta.ok) {
                        throw new Error("No se obtuvo la miniatura");
                    }

                    return respuesta.json();

                })
                .then(function (datos) {

                    if (!datos.thumbnail_url) {
                        return;
                    }

                    let imagen = tarjeta.querySelector("img");

                    if (!imagen) {

                        imagen = document.createElement("img");

                        imagen.alt =
                            "Portada de " + tarjeta.dataset.titulo;

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
                titulo.textContent = tarjeta.dataset.titulo || "";
                contenedor.replaceChildren();

                if (!url) {
                    const aviso = document.createElement("p");
                    aviso.textContent = "No se pudo preparar el reproductor. Puedes abrir el video directamente.";
                    contenedor.appendChild(aviso);
                    const enlace = document.createElement("a");
                    enlace.href = tarjeta.dataset.urlOriginal;
                    enlace.target = "_blank";
                    enlace.rel = "noopener noreferrer";
                    enlace.textContent =
                        "Abrir video en su plataforma";

                    contenedor.appendChild(enlace);

                } else {

                    if (
                        plataforma === "youtube" ||
                        plataforma === "tiktok"
                    ) {
                        url += "?autoplay=1";
                    }

                    const iframe = document.createElement("iframe");

                    iframe.src = url;

                    iframe.title =
                        tarjeta.dataset.titulo || "Reproductor de video";

                    iframe.allow =
                        "autoplay; encrypted-media; fullscreen; picture-in-picture";

                    iframe.allowFullscreen = true;
                    iframe.loading = "eager";

                    if (
                        plataforma === "tiktok" ||
                        plataforma === "instagram"
                    ) {
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

            if (evento.target === modal) {
                cerrarVideo();
            }

        });

        document.addEventListener("keydown", function (evento) {

            if (
                evento.key === "Escape" &&
                modal.classList.contains("activo")
            ) {
                cerrarVideo();
            }

        });

    });
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