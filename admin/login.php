<?php
session_start();
$usuarioCorrecto = "USUARIOCORRECTO";
$passwordHash = 'HASHCONTRASEÑACREADO';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"] ?? '';
    $password = $_POST["password"] ?? '';

    if (
        $usuario === $usuarioCorrecto &&
        password_verify($password, $passwordHash)
    ) {
        session_regenerate_id(true);

        $_SESSION["admin"] = true;

        header("Location: dashboard.php");
        exit();
    }

    $error = "Datos incorrectos";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login NDMU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #FAFAFA;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 30px 20px;
        }

        .panel {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
            width: 100%;
            max-width: 900px;
            position: relative;
        }

        .login-box {
            background: white;
            padding: 50px 40px 60px;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .08);
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            border: 1px solid #f0e2e6;
            text-align: center;
            animation: entrada .5s ease;
        }

        .volver-wrapper {
            margin-bottom: 30px;
            text-align: left;
        }

        .volver-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f8f0f2;
            padding: 10px 20px;
            border-radius: 40px;
            text-decoration: none;
            color: #9f7b86;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            border: 1px solid #f0dfe4;
        }

        .volver-btn:hover {
            background: #D8A7B1;
            color: white;
            border-color: #D8A7B1;
            transform: translateX(-3px);
        }

        .volver-btn::before {
            content: "←";
            font-size: 18px;
            font-weight: normal;
        }

        h1 {
            margin-bottom: 12px;
            color: #D8A7B1;
            text-align: center;
            font-size: 32px;
            font-weight: 600;
        }

        .subtitulo {
            color: #888;
            font-size: 15px;
            margin-bottom: 35px;
            text-align: center;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 16px 18px;
            margin-bottom: 20px;
            border: 1px solid #e2d4d8;
            border-radius: 16px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            background: #fefcfc;
            transition: all 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #D8A7B1;
            box-shadow: 0 0 0 3px rgba(216, 167, 177, 0.2);
        }

        button {
            width: 100%;
            padding: 16px;
            background: #D8A7B1;
            border: none;
            color: white;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-top: 10px;
            transition: 0.2s;
        }

        button:hover {
            background: #c98c9a;
            transform: translateY(-2px);
        }

        .error-msg {
            background: #ffe6e6;
            color: #c94f4f;
            padding: 12px;
            border-radius: 50px;
            margin-top: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .login-avatar {
            width: 100px;
            height: 100px;
            margin: 0 auto 25px;
            border-radius: 50%;
            background: linear-gradient(135deg, #D8A7B1, #f1ccd6);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 48px;
            box-shadow: 0 12px 25px rgba(216, 167, 177, 0.3);
        }

        @media (max-width: 600px) {
            body {
                padding: 20px 16px;
                align-items: flex-start;
                padding-top: 40px;
            }

            .login-box {
                padding: 35px 25px 45px;
                max-width: 100%;
                border-radius: 32px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            }

            .volver-wrapper {
                margin-bottom: 20px;
            }

            .volver-btn {
                font-size: 13px;
                padding: 8px 16px;
            }

            h1 {
                font-size: 28px;
                margin-bottom: 8px;
            }

            .subtitulo {
                font-size: 13px;
                margin-bottom: 28px;
                padding: 0 8px;
            }

            .login-avatar {
                width: 80px;
                height: 80px;
                font-size: 40px;
                margin-bottom: 18px;
            }

            input {
                padding: 15px;
                font-size: 15px;
                margin-bottom: 18px;
            }

            button {
                padding: 15px;
                font-size: 16px;
            }

            .error-msg {
                font-size: 13px;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 28px 20px 38px;
            }

            h1 {
                font-size: 26px;
            }

            .subtitulo {
                font-size: 12px;
            }

            input,
            button {
                padding: 14px;
            }
        }

        @keyframes entrada {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="volver-wrapper">
            <a href="../index.php" class="volver-btn">Volver al sitio</a>
        </div>

        <div class="login-avatar">
            🎀
        </div>

        <h1>Inicio de sesión NDMU</h1>
        <p class="subtitulo">Panel de administración para servicios, promociones y galería</p>

        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required autocomplete="off">
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>

        <?php if (isset($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>
</body>

</html>