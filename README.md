# NDMU - Nayeli Díaz MakeUp

Sitio web para un negocio de maquillaje profesional, desarrollado con PHP, MySQL, HTML, CSS y JavaScript.

## Descripción

NDMU (Nayeli Díaz MakeUp) es un sitio web desarrollado para un negocio de maquillaje profesional, con el propósito de presentar sus servicios, paquetes, promociones y trabajos realizados.

La aplicación cuenta con un panel administrativo que permite gestionar el contenido del sitio, actualizar información y mantener organizadas las galerías de imágenes y videos. Su desarrollo busca facilitar la presencia digital del negocio y ofrecer a sus clientes un espacio para conocer sus servicios y consultar información de contacto.

## Funcionalidades

- Página principal con presentación del negocio.
- Catálogo de servicios de maquillaje.
- Administración de servicios y paquetes.
- Visualización de precios, categorías y descripciones.
- Gestión de promociones.
- Galería de trabajos con visualización ampliada de imágenes.
- Galería de videos con integración de YouTube, TikTok e Instagram.
- Panel administrativo con inicio de sesión.
- Administración de imágenes y contenido multimedia.
- Edición de la sección de bienvenida.
- Administración de la sección "Sobre mí".
- Buzón de reseñas de clientes.
- Sistema de calificaciones mediante estrellas.
- Validación de comentarios con filtro de palabras no permitidas.
- Enlaces a WhatsApp, Instagram, Facebook y TikTok.
- Mapa de ubicación para consultar la zona de servicio a domicilio.
- Diseño adaptable a dispositivos móviles.

## Tecnologías utilizadas

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- MySQLi
- Google Fonts (Poppins)
- cURL

## Estructura del proyecto

```text
NDMU/
├── admin/
├── css/
├── includes/
├── index.php
├── galeria.php
├── videos.php
├── buzon.php
├── .gitignore
└── README.md
```

## Instalación local

1. Instala un entorno de desarrollo local como XAMPP, que incluye Apache, PHP y MySQL.

2. Clona este repositorio:

```bash
git clone https://github.com/7Marilu7/NDMU.git
```

3. Coloca la carpeta del proyecto dentro del directorio `htdocs` de XAMPP.

4. Inicia los servicios de Apache y MySQL desde el panel de control de XAMPP.

5. Crea la base de datos MySQL correspondiente al proyecto e importa su estructura y datos iniciales.

6. Configura la conexión a la base de datos en:

```text
includes/conexion.php
```

7. Verifica que estén creadas las carpetas necesarias para almacenar imágenes y archivos multimedia:

```text
uploads/
├── img/
├── servicios/
├── promociones/
├── galeria/
├── videos/
└── reseñas/
```

8. Abre el navegador y accede a:

`http://localhost/NDMU/`

## Base de datos

El proyecto utiliza MySQL para almacenar y administrar información relacionada con:

- Servicios y paquetes.
- Promociones.
- Galería de imágenes.
- Videos.
- Información de bienvenida.
- Información personal.
- Reseñas y calificaciones de clientes.

La conexión se realiza mediante la extensión MySQLi de PHP.

## Estado del proyecto

Proyecto desarrollado e implementado para un negocio real de maquillaje profesional.

## Autoría

Desarrollado por **María Guadalupe Cruz Paz**.

[GitHub](https://github.com/7Marilu7)
