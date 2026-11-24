<?php
// Incluimos la cabecera general de la aplicación para cargar constantes, sesión, autocarga y plantilla
include_once($_SERVER["DOCUMENT_ROOT"] . "/cabecera.php");

// Establecemos los colores por defecto según el enunciado: fondo blanco y texto negro
$colorFondo = "white";
$colorTexto = "black";

// Si existe la cookie color_fondo y es un color válido dentro de COLORESFONDO, lo aplicamos
if (isset($_COOKIE["color_fondo"]) && array_key_exists($_COOKIE["color_fondo"], COLORESFONDO)) {
    $colorFondo = $_COOKIE["color_fondo"];
}

// Si existe la cookie color_texto y es un color válido dentro de COLORESTEXTO, lo aplicamos
if (isset($_COOKIE["color_texto"]) && array_key_exists($_COOKIE["color_texto"], COLORESTEXTO)) {
    $colorTexto = $_COOKIE["color_texto"];
}

// Calculamos el número de visitas usando una cookie de contador
$visitas = 1;
if (isset($_COOKIE["contador_personalizar"])) {
    // Convertimos a entero y sumamos 1 por esta nueva visita
    $visitas = (int) $_COOKIE["contador_personalizar"] + 1;
}

// Actualizamos la cookie del contador (vigente por ejemplo durante 30 días)
setcookie("contador_personalizar", (string) $visitas, time() + 60 * 60 * 24 * 30, "/");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inicio Personalizar - Relación 8</title>
    <!-- Aplicamos los colores configurados al elemento con id="documento" -->
    <style>
        #documento {
            background-color: <?php echo htmlspecialchars($colorFondo, ENT_QUOTES, "UTF-8"); ?>;
            color: <?php echo htmlspecialchars($colorTexto, ENT_QUOTES, "UTF-8"); ?>;
        }
    </style>
</head>

<body>
    <!-- Contenedor principal al que se aplican los colores -->
    <div id="documento">
        <h1>Página de inicio de Personalizar</h1>

        <!-- Información de ejemplo mostrada en la página -->
        <p>Esta es la página principal de la sección de personalización de la Relación 8.</p>

        <!-- Contador de accesos almacenado mediante cookie -->
        <p>Número de veces que has accedido a esta página: <strong><?php echo $visitas; ?></strong></p>

        <!-- Enlace para ir a cambiar los colores -->
        <p><a href="personalizar.php">Ir a personalizar colores</a></p>
    </div>
</body>

</html>