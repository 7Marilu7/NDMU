<?php
include "../includes/auth.php";
include "../includes/conexion.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION["csrf_videos"])) {
    $_SESSION["csrf_videos"] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION["csrf_videos"];

function redirigirVideos($mensaje)
{
    header("Location: videos_admin.php?msg=" . urlencode($mensaje));
    exit();
}

function validarUrlVideo($url)
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $partesUrl = parse_url($url);

    if (
        empty($partesUrl["scheme"]) ||
        strtolower($partesUrl["scheme"]) !== "https" &&
        strtolower($partesUrl["scheme"]) !== "http"
    ) {
        return false;
    }

    $host = strtolower($partesUrl["host"] ?? "");
    $host = preg_replace('/^www\./', '', $host);

    $dominiosPermitidos = [
        "instagram.com",
        "m.instagram.com",
        "tiktok.com",
        "m.tiktok.com",
        "vm.tiktok.com",
        "vt.tiktok.com",
        "youtube.com",
        "youtu.be"
    ];

    return in_array($host, $dominiosPermitidos, true);
}

function procesarPortada()
{
    if (
        !isset($_FILES["portada"]) ||
        $_FILES["portada"]["error"] === UPLOAD_ERR_NO_FILE
    ) {
        return [
            "ruta" => null,
            "archivo" => null,
            "error" => null
        ];
    }

    if ($_FILES["portada"]["error"] !== UPLOAD_ERR_OK) {
        return ["ruta" => null, "archivo" => null, "error" => "subida"];
    }

    if ($_FILES["portada"]["size"] > 5 * 1024 * 1024) {
        return ["ruta" => null, "archivo" => null, "error" => "tamano"];
    }

    $infoImagen = @getimagesize($_FILES["portada"]["tmp_name"]);

    if ($infoImagen === false) {
        return ["ruta" => null, "archivo" => null, "error" => "imagen"];
    }

    $tiposPermitidos = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp",
        "image/gif"  => "gif"
    ];

    $mime = $infoImagen["mime"];

    if (!isset($tiposPermitidos[$mime])) {
        return ["ruta" => null, "archivo" => null, "error" => "imagen"];
    }

    $directorio = __DIR__ . "/../uploads/videos/";

    if (!is_dir($directorio) || !is_writable($directorio)) {
        return ["ruta" => null, "archivo" => null, "error" => "subida"];
    }

    $nombreArchivo = bin2hex(random_bytes(16))
        . "." . $tiposPermitidos[$mime];

    $destino = $directorio . $nombreArchivo;

    if (!move_uploaded_file($_FILES["portada"]["tmp_name"], $destino)) {
        return ["ruta" => null, "archivo" => null, "error" => "subida"];
    }

    return [
        "ruta" => "videos/" . $nombreArchivo,
        "archivo" => $destino,
        "error" => null
    ];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tokenRecibido = $_POST["csrf_token"] ?? "";

    if (
        !is_string($tokenRecibido) ||
        !hash_equals($_SESSION["csrf_videos"], $tokenRecibido)
    ) {
        redirigirVideos("csrf");
    }

    $accion = $_POST["accion"] ?? "";

    if ($accion === "eliminar") {
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            redirigirVideos("error");
        }

        $stmt = $conexion->prepare("DELETE FROM videos WHERE id = ?");
        if (!$stmt) {
            redirigirVideos("error");
        }
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $eliminado = $stmt->affected_rows;
            $stmt->close();

            redirigirVideos($eliminado > 0 ? "eliminado" : "noexiste");
        }
        $stmt->close();
        redirigirVideos("error");
    }

    if ($accion === "crear" || $accion === "editar") {

        $titulo = trim($_POST["titulo"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");
        $url = trim($_POST["url"] ?? "");

        if (
            $titulo === "" ||
            $url === "" ||
            mb_strlen($titulo) > 150 ||
            mb_strlen($descripcion) > 255 ||
            strlen($url) > 500
        ) {
            redirigirVideos("campos");
        }

        if (!validarUrlVideo($url)) {
            redirigirVideos("plataforma");
        }

        $id = null;
        $portadaActual = null;

        if ($accion === "editar") {
            $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                redirigirVideos("error");
            }

            $stmt = $conexion->prepare(
                "SELECT portada FROM videos WHERE id = ?"
            );

            if (!$stmt) {
                redirigirVideos("error");
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($portadaActual);

            if (!$stmt->fetch()) {
                $stmt->close();
                redirigirVideos("noexiste");
            }

            $stmt->close();
        }

        $subida = procesarPortada();

        if ($subida["error"] !== null) {
            redirigirVideos($subida["error"]);
        }
        $portada = $subida["ruta"] ?? $portadaActual;

        if ($accion === "crear") {
            $stmt = $conexion->prepare(
                "INSERT INTO videos (titulo, descripcion, url, portada)
                 VALUES (?, ?, ?, ?)"
            );

            if (!$stmt) {
                if ($subida["archivo"] !== null) {
                    @unlink($subida["archivo"]);
                }

                redirigirVideos("error");
            }

            $stmt->bind_param(
                "ssss",
                $titulo,
                $descripcion,
                $url,
                $portada
            );
        } else {
            $stmt = $conexion->prepare(
                "UPDATE videos
                 SET titulo = ?, descripcion = ?, url = ?, portada = ?
                 WHERE id = ?"
            );

            if (!$stmt) {
                if ($subida["archivo"] !== null) {
                    @unlink($subida["archivo"]);
                }

                redirigirVideos("error");
            }

            $stmt->bind_param(
                "ssssi",
                $titulo,
                $descripcion,
                $url,
                $portada,
                $id
            );
        }

        if ($stmt->execute()) {
            $stmt->close();

            redirigirVideos(
                $accion === "crear" ? "ok" : "actualizado"
            );
        }

        $stmt->close();
        if ($subida["archivo"] !== null) {
            @unlink($subida["archivo"]);
        }

        redirigirVideos("error");
    }

    redirigirVideos("error");
}

$videoEditar = null;
$idEditar = filter_input(INPUT_GET, "editar", FILTER_VALIDATE_INT);

if ($idEditar && $idEditar > 0) {
    $stmt = $conexion->prepare("SELECT * FROM videos WHERE id = ?");

    if ($stmt) {
        $stmt->bind_param("i", $idEditar);
        $stmt->execute();
        $resultadoEditar = $stmt->get_result();
        $videoEditar = $resultadoEditar->fetch_assoc();
        $stmt->close();
    }
}

$resultado = $conexion->query(
    "SELECT * FROM videos ORDER BY id DESC"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar videos</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="panel">

    <h1>Videos</h1>

    <?php
    $mensajes = [
        "ok" => "✅ Video guardado correctamente.",
        "actualizado" => "✅ Video actualizado correctamente.",
        "eliminado" => "✅ Video eliminado correctamente.",
        "noexiste" => "El video seleccionado ya no existe.",
        "campos" => "Revisa los campos: el título y el enlace son obligatorios, y no deben exceder su longitud permitida.",
        "url" => "Introduce un enlace válido.",
        "plataforma" => "Utiliza un enlace válido de Instagram, TikTok o YouTube.",
        "imagen" => "La portada debe ser una imagen JPG, PNG, WEBP o GIF válida.",
        "tamano" => "La portada no debe superar los 5 MB.",
        "subida" => "No se pudo subir la portada. Comprueba la carpeta y vuelve a intentarlo.",
        "csrf" => "La solicitud expiró o no es válida. Recarga la página e inténtalo de nuevo.",
        "error" => "Ocurrió un error al procesar la operación. Inténtalo de nuevo."
    ];

    $mensaje = $_GET["msg"] ?? "";

    if (isset($mensajes[$mensaje])) {
        echo '<p role="status">' .
            htmlspecialchars($mensajes[$mensaje], ENT_QUOTES, "UTF-8") .
            '</p>';
    }
    ?>

    <h2>
        <?= $videoEditar ? "Editar video" : "Agregar video" ?>
    </h2>

    <form
        action="videos_admin.php"
        method="POST"
        enctype="multipart/form-data"
    >
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, "UTF-8") ?>"
        >

        <input
            type="hidden"
            name="accion"
            value="<?= $videoEditar ? "editar" : "crear" ?>"
        >

        <?php if ($videoEditar) { ?>
            <input
                type="hidden"
                name="id"
                value="<?= (int)$videoEditar["id"] ?>"
            >
        <?php } ?>

        <label for="titulo">Título</label>
        <input
            id="titulo"
            type="text"
            name="titulo"
            maxlength="150"
            required
            value="<?= htmlspecialchars(
                $videoEditar["titulo"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
        >

        <label for="descripcion">Descripción (opcional)</label>
        <textarea
            id="descripcion"
            name="descripcion"
            maxlength="255"
        ><?= htmlspecialchars(
            $videoEditar["descripcion"] ?? "",
            ENT_QUOTES,
            "UTF-8"
        ) ?></textarea>

        <label for="url">Enlace del video</label>
        <input
            id="url"
            type="url"
            name="url"
            maxlength="500"
            placeholder="https://..."
            required
            value="<?= htmlspecialchars(
                $videoEditar["url"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
        >

        <?php if ($videoEditar && !empty($videoEditar["portada"])) {
            $rutaPortadaActual = $videoEditar["portada"];

            if (strpos($rutaPortadaActual, "uploads/") === 0) {
                $srcPortada = "../" . $rutaPortadaActual;
            } else {
                $srcPortada = "../uploads/" . ltrim($rutaPortadaActual, "/");
            }
        ?>
            <p>Portada actual:</p>
            <img
                src="<?= htmlspecialchars($srcPortada, ENT_QUOTES, "UTF-8") ?>"
                alt="Portada actual"
                style="width:150px; max-width:100%; border-radius:8px;"
            >
        <?php } ?>

        <label for="portada">
            <?= $videoEditar
                ? "Cambiar portada (opcional)"
                : "Agrega la portada del vídeo" ?>
        </label>

        <input
            id="portada"
            type="file"
            name="portada"
            accept="image/jpeg,image/png,image/webp,image/gif"
        >

        <small>
            Formatos: JPG, PNG, WEBP o GIF. Máximo 5 MB.
            <?php if ($videoEditar) { ?>
                Si no seleccionas otra imagen, se conservará la portada actual.
            <?php } else { ?>
      
            <?php } ?>
        </small>

        <button type="submit">
            <?= $videoEditar ? "Guardar cambios" : "Guardar video" ?>
        </button>

        <?php if ($videoEditar) { ?>
            <a href="videos_admin.php">Cancelar edición</a>
        <?php } ?>
    </form>

    <hr>

    <h2>Videos registrados</h2>

    <?php if ($resultado && $resultado->num_rows > 0) { ?>

        <?php while ($video = $resultado->fetch_assoc()) { ?>

            <div class="galeria-admin-item">

                <h3>
                    <?= htmlspecialchars(
                        $video["titulo"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </h3>

                <?php if (!empty($video["portada"])) {
                    $rutaPortada = $video["portada"];

                    if (strpos($rutaPortada, "uploads/") === 0) {
                        $srcPortada = "../" . $rutaPortada;
                    } else {
                        $srcPortada = "../uploads/" . ltrim($rutaPortada, "/");
                    }
                ?>
                    <img
                        src="<?= htmlspecialchars(
                            $srcPortada,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>"
                        alt="Portada de <?= htmlspecialchars(
                            $video["titulo"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>"
                        style="width:150px; max-width:100%; border-radius:8px;"
                    >
                <?php } ?>

                <p>
                    <?= nl2br(htmlspecialchars(
                        $video["descripcion"] ?? "",
                        ENT_QUOTES,
                        "UTF-8"
                    )) ?>
                </p>

                <p>
                    <a
                        href="<?= htmlspecialchars(
                            $video["url"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Abrir video
                    </a>
                </p>

                <p>
                    <a href="videos_admin.php?editar=<?= (int)$video["id"] ?>">
                        Editar
                    </a>
                </p>

                <form
                    action="videos_admin.php"
                    method="POST"
                    onsubmit="return confirm('¿Seguro que deseas eliminar este video? Esta acción no se puede deshacer.');"
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            $csrfToken,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>"
                    >

                    <input type="hidden" name="accion" value="eliminar">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int)$video["id"] ?>"
                    >

                    <button type="submit">Eliminar video</button>
                </form>

            </div>

            <hr>

        <?php } ?>

    <?php } else { ?>

        <p>Aún no hay videos registrados.</p>

    <?php } ?>

    <a href="dashboard.php">Volver al administrador</a>

</div>
</body>
</html>