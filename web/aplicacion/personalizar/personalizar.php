<?php
// Incluimos la cabecera general de la aplicación
include_once($_SERVER["DOCUMENT_ROOT"] . "/cabecera.php");

// Colores por defecto: fondo blanco y texto negro
$colorFondo = "white";
$colorTexto = "black";

// Recuperamos cookies anteriores si existen y son válidas
if (isset($_COOKIE["color_fondo"]) && array_key_exists($_COOKIE["color_fondo"], COLORESFONDO)) {
    $colorFondo = $_COOKIE["color_fondo"];
}

if (isset($_COOKIE["color_texto"]) && array_key_exists($_COOKIE["color_texto"], COLORESTEXTO)) {
    $colorTexto = $_COOKIE["color_texto"];
}

// Si se envía el formulario, guardamos los colores en cookies
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["guardar_colores"])) {
    $nuevoFondo = $_POST["color_fondo"] ?? "";
    $nuevoTexto = $_POST["color_texto"] ?? "";

    if (array_key_exists($nuevoFondo, COLORESFONDO)) {
        $colorFondo = $nuevoFondo;
        setcookie("color_fondo", $colorFondo, time() + 60 * 60 * 24 * 30, "/");
    }

    if (array_key_exists($nuevoTexto, COLORESTEXTO)) {
        $colorTexto = $nuevoTexto;
        setcookie("color_texto", $colorTexto, time() + 60 * 60 * 24 * 30, "/");
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Personalizar - Relación 8</title>
    <style>
        #documento {
            background-color: <?php echo htmlspecialchars($colorFondo, ENT_QUOTES, "UTF-8"); ?>;
            color: <?php echo htmlspecialchars($colorTexto, ENT_QUOTES, "UTF-8"); ?>;
        }
    </style>
</head>

<body>
    <div id="documento">
        <h1>Personalizar colores</h1>

        <form action="" method="post">
            <p>
                <label for="color_fondo">Color de fondo:</label>
                <select id="color_fondo" name="color_fondo">
                    <?php foreach (COLORESFONDO as $valorCss => $nombreVisible): ?>
                        <?php $selected = ($valorCss === $colorFondo) ? "selected" : ""; ?>
                        <option value="<?php echo htmlspecialchars($valorCss, ENT_QUOTES, "UTF-8"); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($nombreVisible, ENT_QUOTES, "UTF-8"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="color_texto">Color del texto:</label>
                <select id="color_texto" name="color_texto">
                    <?php foreach (COLORESTEXTO as $valorCss => $nombreVisible): ?>
                        <?php $selected = ($valorCss === $colorTexto) ? "selected" : ""; ?>
                        <option value="<?php echo htmlspecialchars($valorCss, ENT_QUOTES, "UTF-8"); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($nombreVisible, ENT_QUOTES, "UTF-8"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <button type="submit" name="guardar_colores" value="1">Guardar colores</button>
            </p>
        </form>
    </div>
</body>

</html>