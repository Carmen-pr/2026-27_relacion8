<?php

function paginaError($mensaje)
{
    header("HTTP/1.0 404 $mensaje");
    inicioCabecera("PRACTICA");
    finCabecera();
    inicioCuerpo("ERROR");
    echo "<br />\n";
    echo $mensaje;
    echo "<br />\n";
    echo "<br />\n";
    echo "<br />\n";
    echo "<a href='/index.php'>Ir a la pagina principal</a>\n";

    finCuerpo();
}

function inicioCabecera($titulo)
{
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="utf-8">

        <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
        Remove this if you use the .htaccess -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <title><?php echo $titulo ?></title>
        <meta name="description" content="">
        <meta name="author" content="Administrador">

        <meta name="viewport" content="width=device-width; initial-scale=1.0">

        <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
        <link rel="shortcut icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="stylesheet" type="text/css" href="/estilos/base.css">
    <?php
}

function finCabecera()
{
    ?>
    </head>
    <?php
}

function mostrarEnlacesRelacion8()
{
    $rutaActual = $_SERVER["PHP_SELF"] ?? "";
    $rutasRelacion8 = [
        "/aplicacion/Relacion8/",
        "/aplicacion/personalizar/",
        "/aplicacion/texto/",
        "/aplicaion/verTeto",
    ];

    foreach ($rutasRelacion8 as $ruta) {
        if (strpos($rutaActual, $ruta) !== false) {
    ?>
            <div class="relacion8">
                <a href="/aplicacion/personalizar/index.php">Personalizar</a>
                <a href="/aplicacion/personalizar/personalizar.php">Pag. Colores</a>
                <a href="/aplicacion/texto/verTextos.php">Ver Texto </a>
                <a href="/aplicacion/acceso/login.php">Inicio de Sesion </a>
                
            </div>
    <?php
            return;
        }
    }
}

function inicioCuerpo(string $cabecera, array $ubicacion = [])
{
    global $acceso;
    ?>

    <body>

        <div id="documento">

            <header>
                <!-- Esta es la imagen del punto seguro -->
                <a href="/index.php" id="punto-seguro">
                    <img src="/img/punto_seguro.png" alt="Ir al inicio" width="40">
                </a>

                <h1 id="titulo"><?php echo $cabecera; ?></h1>
            </header>

            <div id="barraLogin">

            </div>

            <div id="barraMenu">
                <ul>
                    <li><a href="/index.php">Inicio</a></li>
                    <!-- <li><a href="/aplicacion/pruebas/index.php">Ejemplos Basicos</a></li> -->
                    <li><a href="/aplicacion/Relacion8/index.php">Relación 8</a></li>
                </ul>
            </div>

            <?php mostrarEnlacesRelacion8(); ?>

            <div id="barraUbicacion">
                <?php
                if ($ubicacion) {
                    foreach ($ubicacion as $elemento) {
                        if (isset($elemento["ENLACE"])) {
                            echo "<a href='{$elemento["ENLACE"]}'>";
                        }

                        echo $elemento["TEXTO"];

                        if (isset($elemento["ENLACE"])) {
                            echo "</a>";
                        }

                        if (isset($elemento["ADICIONAL"])) {
                            echo $elemento["ADICIONAL"];
                        } else {
                            echo "&nbsp;&nbsp;";
                        }
                    }
                }
                ?>
            </div>

            <div>
            <?php
        }

        function finCuerpo()
        {
            ?>
                <br />
                <br />
            </div>

            <footer>
                <hr width="90%" />
                <div>
                    &copy; Copyright by Carmen
                </div>
            </footer>
        </div>
    </body>

    </html>
<?php

}
