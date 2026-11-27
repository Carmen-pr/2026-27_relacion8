<?php
// Incluimos la cabecera general de la aplicación (inicia sesión y carga clases/plantilla)
include_once(dirname(__FILE__) . "/../../cabecera.php");

// --- CONTROLADOR ---
$claveSesionTextos = "relacion8_textos";

// Al inicio del script, recogemos de la sesión los textos guardados y los pasamos al array $textos
$textos = [];
if (isset($_SESSION[$claveSesionTextos]) && is_array($_SESSION[$claveSesionTextos])) {
    foreach ($_SESSION[$claveSesionTextos] as $item) {
        if ($item instanceof RegistroTexto) {
            $textos[] = $item;
        }
    }
}

$error = "";

// Comprobamos si se ha enviado el formulario para registrar un texto o limpiar la lista
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["registrar"])) {
        $textoIntroducido = trim($_POST["texto"] ?? "");

        if ($textoIntroducido === "") {
            $error = "Debes escribir un texto antes de registrar.";
        } else {
            // Añadimos el nuevo objeto RegistroTexto al array $textos y actualizamos la sesión
            $textos[] = new RegistroTexto($textoIntroducido);
            $_SESSION[$claveSesionTextos] = $textos;
        }
    } elseif (isset($_POST["limpiar"])) {
        // Vaciamos el array $textos y actualizamos la sesión sin destruir el resto de datos de sesión
        $textos = [];
        $_SESSION[$claveSesionTextos] = $textos;
    }
}

// Preparamos el contenido del textarea con todos los textos y su fecha/hora
$lineasTextarea = [];
foreach ($textos as $registro) {
    $lineasTextarea[] = "[" . $registro->getFechaHoraFormateada("d/m/Y H:i:s") . "] " . $registro->getTexto();
}
$contenidoTextarea = implode("\n", $lineasTextarea);

// Barra de ubicación para volver al inicio o a Relación 8
$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
        "ADICIONAL" => ""
    ],
    [
        "TEXTO" => "Relacion 8",
        "ENLACE" => "/aplicacion/Relacion8/index.php",
        "ADICIONAL" => ""
    ],
];

// Dibujamos la estructura general de la página usando las funciones de la plantilla
inicioCabecera("Ver Textos");
cabecera();
finCabecera();

inicioCuerpo("Ver Textos", $barra);
cuerpo($contenidoTextarea, $error); // Llamamos a la vista del contenido principal
finCuerpo();
// **********************************************************

// --- VISTA ---
function cabecera()
{
?>
    <!-- Aquí irían etiquetas extra para el <head> si hicieran falta -->
<?php
}

function cuerpo(string $contenidoTextarea, string $error)
{
?>
    <h2 class="subtitulo-seccion">Registro de textos</h2>

    <?php if ($error !== ""): ?>
        <!-- Mostramos mensaje de error si se intenta registrar un texto vacío -->
        <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></div>
    <?php endif; ?>

    <!-- Formulario para introducir y registrar un nuevo texto -->
    <form action="" method="post">
        <div class="campo-form">
            <label for="texto">Texto a registrar:</label>
            <input type="text" id="texto" name="texto" class="for_texto" size="50" autocomplete="off">
            <button type="submit" name="registrar" value="1" class="boton">Registrar</button>
        </div>
    </form>

    <!-- Área de texto con el historial de textos registrados y botón para limpiar -->
    <form action="" method="post">
        <div class="campo-form">
            <label for="historial_textos">Textos registrados:</label><br>
            <textarea id="historial_textos" name="historial_textos" rows="10" cols="80" class="for_texto" readonly><?php
                                                                                                                    echo htmlspecialchars($contenidoTextarea, ENT_QUOTES, "UTF-8");
                                                                                                                    ?></textarea>
        </div>

        <div class="campo-form">
            <button type="submit" name="limpiar" value="1" class="boton">Limpiar</button>
        </div>
    </form>
<?php
}
