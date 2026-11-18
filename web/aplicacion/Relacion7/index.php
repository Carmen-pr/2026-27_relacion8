<?php
// Cargo la plantilla y las librerias de validaciones
include_once(dirname(__FILE__) . "/../../cabecera.php");
// Relación 7 /scripts/clases/Punto.php
include_once(dirname(__FILE__) . "/../../scripts/clases/Punto.php");

//controlador
// Aquí solo se prepara la estructura de la página y se crean los objetos.
// Todo lo que hay que mostrar se guarda en el array $datos y se le pasa a la vista.


/**
 * Controlador y Vista de la Relación 7.
 * Gestiona la carga, guardado, alta, borrado y subida de ficheros de puntos
 * personalizados por IP y navegador.
 */







$ubicacion = [
    ["TEXTO" => "Inicio", "ENLACE" => "/index.php", "ADICIONAL" => " &gt;&gt; "],
    ["TEXTO" => "Relación 7"]
];




//dibuja la plantilla de la vista
inicioCabecera("Relación 7");
cabecera();
finCabecera();
inicioCuerpo("Relación 7: Ficheros", $ubicacion);
cuerpo();
finCuerpo();
// **********************************************************

//vista
function cabecera() {}

function cuerpo() {}
