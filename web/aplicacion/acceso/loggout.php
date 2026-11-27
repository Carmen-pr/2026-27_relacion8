<?php
// Incluimos la cabecera general de la aplicación para cargar sesión, constantes, autocarga y plantilla
include_once(dirname(__FILE__) . "/../../cabecera.php");

// --- CONTROLADOR ---
// Variable para indicar si el cierre de sesión se ha realizado correctamente
$sesionCerrada = false;

// Comprobamos que exista la instancia global $acceso de tipo Acceso
if (isset($acceso) && ($acceso instanceof Acceso)) {
    // Eliminamos los datos del usuario validado tanto del objeto como de $_SESSION
    $sesionCerrada = $acceso->quitarRegistroUsuario();
}

// Definimos la barra de ubicación superior para volver a la página principal
$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
        "ADICIONAL" => ""
    ],
];

// Redirigimos inmediatamente a /index.php tras quitar el registro del usuario
header("Location: /index.php");
// Detenemos la ejecución del script para evitar salidas adicionales tras la redirección
exit;

// Dibujamos la cabecera HTML y el cuerpo de la página usando las funciones de plantilla.php
inicioCabecera("Cerrar Sesión");
cabecera();
finCabecera();
inicioCuerpo("Cerrar Sesión", $barra);
cuerpo($sesionCerrada);
finCuerpo();
// **********************************************************

// --- VISTA ---
function cabecera()
{
?>
    <!-- Espacio reservado para etiquetas extra en <head> si hicieran falta -->
<?php
}

function cuerpo($sesionCerrada)
{
?>
    <h2 class="subtitulo-seccion">Cierre de sesión</h2>

    <?php if ($sesionCerrada): ?>
        <!-- Confirmación de que el usuario ha cerrado sesión correctamente -->
        <p>Has cerrado sesión correctamente.</p>
    <?php else: ?>
        <!-- Aviso en caso de que no hubiera un usuario validado previamente -->
        <p>No había ninguna sesión de usuario activa.</p>
    <?php endif; ?>

    <!-- Enlace directo para regresar a la página principal -->
    <p><a href="/index.php">Ir a la página principal</a></p>
<?php
}
