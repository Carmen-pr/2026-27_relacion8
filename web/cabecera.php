<?php
//error_reporting(0);

date_default_timezone_set('Europe/Berlin');
session_start();

define("RUTABASE", dirname(__FILE__));
//define("MODO_TRABAJO","produccion"); //en "produccion o en desarrollo
define("MODO_TRABAJO", "desarrollo"); //en "produccion o en desarrollo

if (MODO_TRABAJO == "produccion")
    error_reporting(0);
else
    error_reporting(E_ALL);

// Constantes de colores para el apartado 1 (clave CSS => nombre visible)
define("COLORESTEXTO", [
    "black" => "Negro",
    "blue" => "Azul",
    "white" => "Blanco",
    "red" => "Rojo"
]);

define("COLORESFONDO", [
    "white" => "Blanco",
    "red" => "Rojo",
    "green" => "Verde",
    "yellow" => "Amarillo",
    "lightgray" => "Gris claro"
]);

spl_autoload_register(function ($clase) {
    $ficheroScripts = RUTABASE . "/scripts/clases/$clase.php";
    $ficheroApp = RUTABASE . "/aplicacion/clases/$clase.php";

    if (file_exists($ficheroScripts)) {
        require_once($ficheroScripts);
    } elseif (file_exists($ficheroApp)) {
        require_once($ficheroApp);
    } else {
        throw new Exception("La clase $clase no se ha encontrado.");
    }
});

include(RUTABASE . "/aplicacion/plantilla/plantilla.php");

//creo todos los objetos que necesita mi aplicacion
