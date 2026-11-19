<?php

/*
   Relación 7: gestión de puntos en fichero por cliente (puntos_dirip_navegador.dat).
   Carga los puntos del fichero del cliente al entrar, permite dar de alta puntos
   (validando x e y entre 0 y 500, color y grosor con errores junto a cada campo),
   mostrarlos en un textarea, borrar un punto desde un select y subir un .txt.
*/

// Cargo la plantilla y la clase Punto
include_once(dirname(__FILE__) . "/../../cabecera.php");
include_once(dirname(__FILE__) . "/../../scripts/clases/Punto.php");

// ---------- Nombre del fichero por IP y navegador ----------
// Saco la IP del cliente y limpio caracteres no válidos para el nombre de fichero
$ip = $_SERVER["REMOTE_ADDR"] ?? "0_0_0_0";
$ipLimpia = str_replace([".", ":"], "_", $ip);

// Miro el navegador desde HTTP_USER_AGENT
$agente = $_SERVER["HTTP_USER_AGENT"] ?? "";
$navegador = "otro";
if (stripos($agente, "Edg") !== false) {
    $navegador = "edge";
} elseif (stripos($agente, "Chrome") !== false) {
    $navegador = "chrome";
} elseif (stripos($agente, "Firefox") !== false) {
    $navegador = "firefox";
}

// Preparo la carpeta y el fichero puntos_dirip_navegador.dat
$carpetaDatos = dirname(__FILE__) . "/datos";
if (!file_exists($carpetaDatos)) {
    mkdir($carpetaDatos, 0777, true);
}
$rutaFichero = $carpetaDatos . "/puntos_" . $ipLimpia . "_" . $navegador . ".dat";


// controlador
// Primero cargo los puntos del fichero del cliente en $puntos.
// Después, si llega una petición POST, atiendo la acción correspondiente.

$puntos = [];
cargarPuntos($rutaFichero, $puntos);

$valores = [
    "x" => "",
    "y" => "",
    "color" => "",
    "grosor" => "",
];
$errores = [];

// Compruebo si se ha enviado algún formulario por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = textoPost("accion");

    if ($accion === "guardar") {
        // Valido y añado el nuevo punto si todo es correcto
        procesarAlta($rutaFichero, $puntos, $valores, $errores);
    } elseif ($accion === "borrar") {
        // Borro el punto seleccionado y actualizo el fichero
        procesarBorrado($rutaFichero, $puntos);
    } elseif ($accion === "subir") {
        // Leo el .txt subido y añado los puntos correctos al fichero
        procesarSubidaTxt($rutaFichero, $puntos);
    }
}

// Preparo los datos para la vista
$datos = [
    "puntos" => $puntos,
    "valores" => $valores,
    "errores" => $errores,
];

// Barra de ubicación
$ubicacion = [
    ["TEXTO" => "Inicio", "ENLACE" => "/index.php", "ADICIONAL" => " &gt; "],
    ["TEXTO" => "Relación 7", "ENLACE" => "index.php", "ADICIONAL" => " &gt; "],
    ["TEXTO" => "Ejercicio 7"],
];

// dibuja la plantilla de la vista
inicioCabecera("Relación 7");
cabecera();
finCabecera();
inicioCuerpo("Relación 7", $ubicacion);
cuerpo($datos);   // llamo a la vista y le paso los datos por parámetro
finCuerpo();
// **********************************************************

// ======================= FUNCIONES DEL CONTROLADOR =======================

/**
 * Coge un dato de $_POST y lo devuelve siempre como string.
 * Si no existe o no es string, devuelve una cadena vacía.
 *
 * @param string $campo Nombre del campo en $_POST.
 * @return string
 */
function textoPost(string $campo): string
{
    $valor = $_POST[$campo] ?? "";
    return is_string($valor) ? $valor : "";
}

/**
 * Carga en $puntos (por referencia) los puntos guardados en el fichero .dat.
 * Si el fichero no existe, deja $puntos vacío.
 *
 * @param string $ruta Ruta completa del fichero .dat.
 * @param array<int, Punto> &$puntos Array donde se cargan los puntos.
 * @return bool true si se pudo leer (o no existía), false si hubo error al abrir.
 */
function cargarPuntos(string $ruta, array &$puntos): bool
{
    // Vacío el array antes de cargar
    foreach ($puntos as $pos => $valor) {
        unset($puntos[$pos]);
    }
    $puntos = [];

    if (!file_exists($ruta)) {
        return true;
    }

    // Abro el fichero en modo lectura
    $fic = fopen($ruta, "r");
    if (!$fic) {
        return false;
    }

    // Leo línea a línea y creo los objetos Punto válidos
    while (($linea = fgets($fic)) !== false) {
        $linea = str_replace("\r", "", $linea);
        $linea = str_replace("\n", "", $linea);
        $linea = trim($linea);

        if ($linea !== "") {
            $partes = explode(";", $linea);
            if (count($partes) === 4) {
                try {
                    $puntos[] = new Punto(
                        (int) trim($partes[0]),
                        (int) trim($partes[1]),
                        trim($partes[2]),
                        (int) trim($partes[3])
                    );
                } catch (Exception $e) {
                    // Si alguna línea no cumple las reglas de Punto, la omito
                }
            }
        }
    }

    // Cierro el fichero
    fclose($fic);
    return true;
}

/**
 * Guarda todos los puntos del array en el fichero .dat (uno por línea).
 *
 * @param string $ruta Ruta completa del fichero .dat.
 * @param array<int, Punto> $puntos Array de objetos Punto.
 * @return bool true si se guardó correctamente, false si falló la apertura.
 */
function guardarPuntos(string $ruta, array $puntos): bool
{
    // Abro el fichero en modo escritura (truncando su contenido anterior)
    $fic = fopen($ruta, "w");
    if (!$fic) {
        return false;
    }

    foreach ($puntos as $punto) {
        fputs($fic, $punto . PHP_EOL);
    }

    // Cierro el fichero
    fclose($fic);
    return true;
}

/**
 * Valida el formulario de alta (x e y entre 0 y 500, color y grosor).
 * Si no hay errores, crea el Punto, lo guarda en $puntos y en el fichero, y redirige.
 * Si hay errores, rellena $valores y $errores por referencia.
 *
 * @param string $rutaFichero Ruta del fichero .dat.
 * @param array<int, Punto> &$puntos Array de puntos.
 * @param array &$valores Valores introducidos en el formulario.
 * @param array &$errores Mensajes de error por campo.
 * @return void
 */
function procesarAlta(string $rutaFichero, array &$puntos, array &$valores, array &$errores): void
{
    $xTexto = trim(textoPost("x"));
    $yTexto = trim(textoPost("y"));
    $color = textoPost("color");
    $grosorTexto = trim(textoPost("grosor"));

    $valores = [
        "x" => $xTexto,
        "y" => $yTexto,
        "color" => $color,
        "grosor" => $grosorTexto,
    ];
    $errores = [];

    // ---- Coordenada X: entero entre 0 y 500 ----
    if ($xTexto === "" || preg_match('/^-?\d+$/', $xTexto) !== 1) {
        $errores["x"] = "La coordenada X debe ser un número entero";
    } else {
        $xNum = (int) $xTexto;
        if ($xNum < 0 || $xNum > 500) {
            $errores["x"] = "La coordenada X debe estar entre 0 y 500";
        }
    }

    // ---- Coordenada Y: entero entre 0 y 500 ----
    if ($yTexto === "" || preg_match('/^-?\d+$/', $yTexto) !== 1) {
        $errores["y"] = "La coordenada Y debe ser un número entero";
    } else {
        $yNum = (int) $yTexto;
        if ($yNum < 0 || $yNum > 500) {
            $errores["y"] = "La coordenada Y debe estar entre 0 y 500";
        }
    }

    // ---- Color: debe existir en Punto::COLORES ----
    if ($color === "" || !array_key_exists($color, Punto::COLORES)) {
        $errores["color"] = "Debe seleccionar un color válido";
    }

    // ---- Grosor: debe existir en Punto::GROSORES ----
    $grosorNum = 0;
    if ($grosorTexto === "" || !ctype_digit($grosorTexto)) {
        $errores["grosor"] = "Debe seleccionar un grosor";
    } else {
        $grosorNum = (int) $grosorTexto;
        if (!array_key_exists($grosorNum, Punto::GROSORES)) {
            $errores["grosor"] = "El grosor seleccionado no es válido";
        }
    }

    // Si no hay errores, creo el objeto Punto, lo guardo y redirijo
    if (count($errores) === 0) {
        try {
            $puntos[] = new Punto((int) $xTexto, (int) $yTexto, $color, $grosorNum);
            guardarPuntos($rutaFichero, $puntos);
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            $errores["x"] = $e->getMessage();
        }
    }
}

/**
 * Borra el punto seleccionado en el desplegable, actualiza el fichero y redirige.
 *
 * @param string $rutaFichero Ruta del fichero .dat.
 * @param array<int, Punto> &$puntos Array de puntos.
 * @return void
 */
function procesarBorrado(string $rutaFichero, array &$puntos): void
{
    $indiceTexto = trim(textoPost("indice_borrar"));
    if ($indiceTexto !== "" && ctype_digit($indiceTexto)) {
        $indice = (int) $indiceTexto;
        if (isset($puntos[$indice])) {
            unset($puntos[$indice]);
            $puntos = array_values($puntos);
            guardarPuntos($rutaFichero, $puntos);
            header("Location: index.php");
            exit();
        }
    }
}

/**
 * Lee el fichero .txt subido mediante $_FILES y añade los puntos válidos.
 *
 * @param string $rutaFichero Ruta del fichero .dat.
 * @param array<int, Punto> &$puntos Array de puntos.
 * @return void
 */
function procesarSubidaTxt(string $rutaFichero, array &$puntos): void
{
    // Compruebo que se haya subido el fichero sin errores
    if (!isset($_FILES["fichero_txt"]) || $_FILES["fichero_txt"]["error"] !== UPLOAD_ERR_OK) {
        return;
    }

    $ficTxt = fopen($_FILES["fichero_txt"]["tmp_name"], "r");
    if (!$ficTxt) {
        return;
    }

    $hayCambios = false;
    while (($linea = fgets($ficTxt)) !== false) {
        $linea = str_replace("\r", "", $linea);
        $linea = str_replace("\n", "", $linea);
        $linea = trim($linea);

        if ($linea !== "") {
            $partes = explode(";", $linea);
            if (count($partes) === 4) {
                try {
                    $puntos[] = new Punto(
                        (int) trim($partes[0]),
                        (int) trim($partes[1]),
                        trim($partes[2]),
                        (int) trim($partes[3])
                    );
                    $hayCambios = true;
                } catch (Exception $e) {
                    // Si alguna línea no es válida, no la añado
                }
            }
        }
    }

    // Cierro el fichero temporal
    fclose($ficTxt);

    if ($hayCambios) {
        guardarPuntos($rutaFichero, $puntos);
        header("Location: index.php");
        exit();
    }
}

// =============================== VISTA =================================
