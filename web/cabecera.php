<?php
// Desactivamos el reporte de errores si se descomenta esta línea
//error_reporting(0);

// Configuramos la zona horaria del servidor para funciones de fecha y hora
date_default_timezone_set('Europe/Berlin');

// Iniciamos o reanudamos la sesión para disponer de $_SESSION en todas las páginas
session_start();

// Definimos la constante RUTABASE con la ruta absoluta del directorio donde está cabecera.php
define("RUTABASE", dirname(__FILE__));

// Definimos el modo de ejecución de la aplicación ("produccion" o "desarrollo")
//define("MODO_TRABAJO","produccion"); //en "produccion o en desarrollo
define("MODO_TRABAJO", "desarrollo"); //en "produccion o en desarrollo

// Ajustamos el nivel de errores de PHP según el modo de trabajo elegido
if (MODO_TRABAJO == "produccion") {
    // En producción no mostramos errores al usuario
    error_reporting(0);
} else {
    // En desarrollo mostramos todos los errores, avisos y advertencias
    error_reporting(E_ALL);
}

// Constantes de colores para el apartado 1 (clave CSS => nombre visible)
define("COLORESTEXTO", [
    "black" => "Negro",
    "blue"  => "Azul",
    "white" => "Blanco",
    "red"   => "Rojo"
]);

define("COLORESFONDO", [
    "white"     => "Blanco",
    "red"       => "Rojo",
    "green"     => "Verde",
    "yellow"    => "Amarillo",
    "lightgray" => "Gris claro"
]);

// Registramos la función de autocarga para localizar clases automáticamente
spl_autoload_register(function ($clase) {
    // Ruta donde se guardan las clases reutilizables (ACLBase, ACLArray, Acceso, etc.)
    $ficheroScripts = RUTABASE . "/scripts/clases/$clase.php";
    // Ruta donde se guardan las clases propias de la aplicación (RegistroTexto, etc.)
    $ficheroApp = RUTABASE . "/aplicacion/clases/$clase.php";

    // Cargamos la clase desde scripts/clases si existe
    if (file_exists($ficheroScripts)) {
        require_once($ficheroScripts);
    // Si no está ahí, la buscamos y cargamos desde aplicacion/clases
    } elseif (file_exists($ficheroApp)) {
        require_once($ficheroApp);
    // Si no existe en ninguna de las rutas previstas, lanzamos una excepción
    } else {
        throw new Exception("La clase $clase no se ha encontrado.");
    }
});

// Incluimos el archivo de plantilla con las funciones de cabecera, cuerpo y pie
include(RUTABASE . "/aplicacion/plantilla/plantilla.php");

// Creamos todos los objetos globales que necesita nuestra aplicación:
// Instancia de ACLArray para gestionar roles, usuarios y permisos en memoria
$acl = new ACLArray();
// Instancia de Acceso para leer y mantener en sesión el estado del usuario autenticado
$acceso = new Acceso();
