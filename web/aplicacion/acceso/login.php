<?php
// Incluimos la cabecera general de la aplicación para cargar sesión, constantes, autocarga y plantilla
include_once(dirname(__FILE__) . "/../../cabecera.php");

// --- CONTROLADOR ---
// Variable para conservar el nick introducido si falla la validación
$nick = "";
// Variable para almacenar el mensaje de error si las credenciales no son válidas o faltan datos
$error = "";

// Si el usuario ya está validado, lo redirigimos directamente a la página principal
if (isset($acceso) && ($acceso instanceof Acceso) && $acceso->hayUsuario()) {
    // Redirección a /index.php
    header("Location: /index.php");
    // Detenemos la ejecución del script tras enviar la cabecera Location
    exit;
}

// Comprobamos si se ha enviado el formulario mediante POST pulsando el botón "entrar"
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["entrar"])) {
    // Recogemos el nick limpiando espacios y pasándolo a minúsculas (como almacena ACLArray)
    $nick = mb_strtolower(trim($_POST["nick"] ?? ""));
    // Recogemos la contraseña tal cual la ha escrito el usuario
    $contrasena = $_POST["contrasena"] ?? "";

    // Validamos que ambos campos obligatorios estén rellenos
    if ($nick === "" || $contrasena === "") {
        $error = "Debes introducir tu nick y tu contraseña.";

    // Comprobamos que exista la instancia $acl de tipo ACLBase
    } elseif (!isset($acl) || !($acl instanceof ACLBase)) {
        $error = "El sistema de control de acceso no está disponible.";
    // Comprobamos en la ACL si el nick existe, no está borrado y coincide la contraseña
    } elseif (!$acl->esValido($nick, $contrasena)) {
        $error = "nick o contraseña incorrectos.";
    } else {
        // Obtenemos el código numérico del usuario a partir de su nick
        $codUsuario = $acl->getCodUsuario($nick);
        // Obtenemos el nombre descriptivo del usuario a partir de su código
        $nombreUsuario = ($codUsuario !== false) ? $acl->getNombre($codUsuario) : false;
        // Obtenemos el array de permisos (1..10) asignados al usuario
        $permisosUsuario = ($codUsuario !== false) ? $acl->getPermisos($codUsuario) : false;

        // Verificamos que todos los datos sean válidos y exista la instancia $acceso
        if (
            $codUsuario !== false &&
            $nombreUsuario !== false &&
            is_array($permisosUsuario) &&
            isset($acceso) &&
            ($acceso instanceof Acceso)
        ) {
            // Registramos el usuario autenticado y sus permisos en la sesión mediante Acceso
            $acceso->registrarUsuario($nick, $nombreUsuario, $permisosUsuario);

            // Redirigimos al usuario validado hacia /index.php
            header("Location: /index.php");
            // Finalizamos el script para asegurar la redirección inmediata
            exit;
        } else {
            // Si algo falló al recuperar datos o registrar la sesión, mostramos aviso
            $error = "No se ha podido iniciar sesión con el usuario indicado.";
        }
    }
}

// Definimos la barra de ubicación superior para volver a inicio
$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
        "ADICIONAL" => ""
    ],
];

// Dibujamos la cabecera HTML y el cuerpo de la página usando las funciones de plantilla.php
inicioCabecera("Iniciar Sesión");
cabecera();
finCabecera();
inicioCuerpo("Iniciar Sesión", $barra);
cuerpo($nick, $error);
finCuerpo();
// **********************************************************

// --- VISTA ---
function cabecera()
{
?>
    <!-- Espacio reservado para etiquetas extra en <head> si hicieran falta -->
<?php
}

function cuerpo($nick, $error)
{
?>
    <h2 class="subtitulo-seccion">Inicio de sesión</h2>

    <?php if ($error !== ""): ?>
        <!-- Mostramos el mensaje de error escapado para evitar inyección HTML/XSS -->
        <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></div>
    <?php endif; ?>

    <!-- Formulario de autenticación enviando nick y contraseña por POST -->
    <form action="" method="post">
        <div class="campo-form">
            <!-- Campo para el nick del usuario -->
            <label for="nick">Nick:</label>
            <input type="text"
                   id="nick"
                   name="nick"
                   class="for_texto"
                   value="<?php echo htmlspecialchars($nick, ENT_QUOTES, "UTF-8"); ?>"
                   autocomplete="username"
                   required>
        </div>

        <div class="campo-form">
            <!-- Campo para la contraseña del usuario -->
            <label for="contrasena">Contraseña:</label>
            <input type="password"
                   id="contrasena"
                   name="contrasena"
                   class="for_texto"
                   autocomplete="current-password"
                   required>
        </div>

        <div class="campo-form">
            <!-- Botón de envío para validar las credenciales -->
            <button type="submit" name="entrar" value="1" class="boton">Entrar</button>
        </div>
    </form>
<?php
}
