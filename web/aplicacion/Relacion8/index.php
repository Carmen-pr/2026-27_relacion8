<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");
//controlador 
// en el conmtrolador solo va los conroladores lo demas va abajo, en el controlador va la esructura de la pagina, el resto va en la vista

$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
        "ADICIONAL" => ""
    ],
];

//dibuja la plantilla de la vista
inicioCabecera("Relacion 8");
cabecera();
finCabecera();
inicioCuerpo("Relacion 8", $barra);
cuerpo();  //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera()
{
?>
    <!-- esto va en el head -->
<?php
}

//vista
function cuerpo()
{
?>
    <p>Selecciona una opción de la Relación 8.</p>
<?php
}
