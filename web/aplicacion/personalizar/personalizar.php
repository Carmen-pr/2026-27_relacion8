<?php
// Incluimos la cabecera general de la aplicación para cargar constantes, sesión, autocarga y plantilla
include_once(dirname(__FILE__) . "/../../cabecera.php");

// --- CONTROLADOR ---
// Valores por defecto según el enunciado: fondo blanco y texto negro
$colorFondo = "white";
$colorTexto = "black";

// Recuperamos los valores actuales desde cookies si existen y son válidos
if (isset($_COOKIE["color_fondo"]) && array_key_exists($_COOKIE["color_fondo"], COLORESFONDO)) {
    $colorFondo = $_COOKIE["color_fondo"];
}

if (isset($_COOKIE["color_texto"]) && array_key_exists($_COOKIE["color_texto"], COLORESTEXTO)) {
    $colorTexto = $_COOKIE["color_texto"];
}

$mensajeGuardado = "";
$errores = [];

// Si se envía el formulario, validamos las opciones seleccionadas y guardamos las cookies
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["guardar"])) {
    $fondoSeleccionado = $_POST["color_fondo"] ?? "";
    $textoSeleccionado = $_POST["color_texto"] ?? "";

    if (!array_key_exists($fondoSeleccionado, COLORESFONDO)) {
        $errores[] = "El color de fondo seleccionado no es válido.";
    } else {
        $colorFondo = $fondoSeleccionado;
        setcookie("color_fondo", $colorFondo, time() + 60 * 60 * 24 * 30, "/");
    }

    if (!array_key_exists($textoSeleccionado, COLORESTEXTO)) {
        $errores[] = "El color de texto seleccionado no es válido.";
    } else {
        $colorTexto = $textoSeleccionado;
        setcookie("color_texto", $colorTexto, time() + 60 * 60 * 24 * 30, "/");
    }

    if (empty($errores)) {
        $mensajeGuardado = "Preferencias de color actualizadas correctamente.";
    }
}

// Barra de navegación superior de la plantilla
$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
        "ADICIONAL" => " "
    ],
    [
        "TEXTO" => "Relacion 8",
        "ENLACE" => "/aplicacion/Relacion8/index.php",
        "ADICIONAL" => ""
    ],
];

// Dibujamos la página usando las funciones de la plantilla
inicioCabecera("Personalizar Colores");
cabecera($colorFondo, $colorTexto);
finCabecera();
inicioCuerpo("Personalizar Colores", $barra);
cuerpo($colorFondo, $colorTexto, $mensajeGuardado, $errores);
finCuerpo();
// **********************************************************

// --- VISTA ---
function cabecera($colorFondo, $colorTexto)
{
?>
    <!-- Aplicamos los colores configurados al elemento con id="documento" -->
    <style>
        #documento {
            background-color: <?php echo htmlspecialchars($colorFondo, ENT_QUOTES, "UTF-8"); ?>;
            color: <?php echo htmlspecialchars($colorTexto, ENT_QUOTES, "UTF-8"); ?>;
        }
    </style>
<?php
}

function cuerpo($colorFondo, $colorTexto, $mensajeGuardado, array $errores)
{
?>
    <h2 class="subtitulo-seccion">Configurar color de fondo y de texto</h2>

    <?php if (!empty($errores)): ?>
        <!-- Mostramos errores de validación si se envían valores no permitidos -->
        <div class="error">
            <?php foreach ($errores as $error): ?>
                <div><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($mensajeGuardado !== ""): ?>
        <!-- Confirmación de que las cookies se han guardado -->
        <p><strong><?php echo htmlspecialchars($mensajeGuardado, ENT_QUOTES, "UTF-8"); ?></strong></p>
    <?php endif; ?>

    <!-- Formulario con combos para seleccionar color de fondo y color de texto -->
    <form action="" method="post">
        <div class="campo-form">
            <label for="color_fondo">Color de fondo:</label>
            <select id="color_fondo" name="color_fondo" class="for_texto">
                <?php foreach (COLORESFONDO as $clave => $etiqueta): ?>
                    <option value="<?php echo htmlspecialchars($clave, ENT_QUOTES, "UTF-8"); ?>"
                        <?php echo ($colorFondo === $clave) ? "selected" : ""; ?>>
                        <?php echo htmlspecialchars($etiqueta, ENT_QUOTES, "UTF-8"); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo-form">
            <label for="color_texto">Color de texto:</label>
            <select id="color_texto" name="color_texto" class="for_texto">
                <?php foreach (COLORESTEXTO as $clave => $etiqueta): ?>
                    <option value="<?php echo htmlspecialchars($clave, ENT_QUOTES, "UTF-8"); ?>"
                        <?php echo ($colorTexto === $clave) ? "selected" : ""; ?>>
                        <?php echo htmlspecialchars($etiqueta, ENT_QUOTES, "UTF-8"); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo-form">
            <button type="submit" name="guardar" value="1" class="boton">Guardar colores</button>
        </div>
    </form>

    <p><a href="/aplicacion/personalizar/index.php">Volver a Personalizar - Inicio</a></p>
<?php
}
