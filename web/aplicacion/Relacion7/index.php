<?php
/*
   Relación 7: gestión de puntos guardados en un fichero por cliente
   (puntos_dirip_navegador.dat).
   Incluye alta de punto con errores junto a cada campo, listado en textarea,
   borrado de un punto y subida de fichero .txt.
   El controlador procesa las acciones y prepara los datos; la vista los muestra.
*/

// Cargo la plantilla y la clase Punto
include_once(dirname(__FILE__) . "/../../cabecera.php");
include_once(dirname(__FILE__) . "/../../scripts/clases/Punto.php");

// ---------- Nombre del fichero personalizado por IP y navegador ----------
// Cojo la IP del cliente y sustituyo puntos y dos puntos por guiones bajos
$ip = $_SERVER["REMOTE_ADDR"] ?? "0_0_0_0";
$ipLimpia = str_replace([".", ":"], "_", $ip);

// Miro el navegador en HTTP_USER_AGENT para identificar el fichero del cliente
$agente = $_SERVER["HTTP_USER_AGENT"] ?? "";
$navegador = "otro";
if (stripos($agente, "Edg") !== false) {
    $navegador = "edge";
} elseif (stripos($agente, "Chrome") !== false) {
    $navegador = "chrome";
} elseif (stripos($agente, "Firefox") !== false) {
    $navegador = "firefox";
}

// Preparo la carpeta de datos y la ruta del fichero puntos_dirip_navegador.dat
$carpetaDatos = dirname(__FILE__) . "/datos";
if (!file_exists($carpetaDatos)) {
    mkdir($carpetaDatos, 0777, true);
}
$rutaFichero = $carpetaDatos . "/puntos_" . $ipLimpia . "_" . $navegador . ".dat";


// controlador
// Al entrar cargo los puntos del fichero y, si llega POST, ejecuto la acción
// que corresponda (guardar, borrar o subir un .txt).

// Cargo los puntos guardados anteriormente en el fichero del cliente
$puntos = cargarPuntos($rutaFichero);

// Valores iniciales y errores para el formulario de alta
$valores = [
    "x" => "",
    "y" => "",
    "color" => "",
    "grosor" => "",
];
$errores = [];

// Miro qué formulario se ha enviado por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";

    if ($accion === "guardar") {
        // Valido x, y, color y grosor; si está bien, añado el punto y guardo
        [$puntos, $valores, $errores] = procesarAlta($rutaFichero, $puntos);
    } elseif ($accion === "borrar") {
        // Borro el punto seleccionado, actualizo el fichero y recargo
        $puntos = procesarBorrado($rutaFichero, $puntos);
    } elseif ($accion === "subir") {
        // Leo el .txt subido, añado los puntos correctos y actualizo el fichero
        $puntos = procesarSubidaTxt($rutaFichero, $puntos);
    }
}

// Preparo el array que recibe la vista
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
 * Coge un dato de $_POST y lo devuelve siempre como texto.
 * Si no existe o llega un array, devuelve una cadena vacía.
 *
 * @param string $campo Nombre del campo del formulario.
 * @return string El valor enviado como texto.
 */
function textoPost(string $campo): string
{
    $valor = $_POST[$campo] ?? "";
    return is_string($valor) ? $valor : "";
}

/**
 * Carga los puntos del fichero .dat y devuelve un array de objetos Punto.
 * Si el fichero todavía no existe, devuelve un array vacío.
 *
 * @param string $ruta Ruta completa del fichero .dat.
 * @return array<int, Punto> Puntos cargados.
 */
function cargarPuntos(string $ruta): array
{
    $lista = [];
    if (!file_exists($ruta)) {
        return $lista;
    }

    // Abro el fichero en modo lectura
    $fic = fopen($ruta, "r");
    if ($fic === false) {
        return $lista;
    }

    // Leo cada línea con formato "x; y; color; grosor" e instancio Punto
    while (($linea = fgets($fic)) !== false) {
        $linea = trim($linea);
        if ($linea === "") {
            continue;
        }
        $partes = explode(";", $linea);
        if (count($partes) === 4) {
            try {
                $lista[] = new Punto(
                    (int) trim($partes[0]),
                    (int) trim($partes[1]),
                    trim($partes[2]),
                    (int) trim($partes[3])
                );
            } catch (Exception $e) {
                // Si una línea no cumple las reglas de Punto, la ignoro
            }
        }
    }

    fclose($fic);
    return $lista;
}

/**
 * Guarda todos los puntos del array en el fichero .dat (uno por línea).
 *
 * @param string $ruta Ruta completa del fichero .dat.
 * @param array<int, Punto> $puntos Array de objetos Punto.
 * @return void
 */
function guardarPuntos(string $ruta, array $puntos): void
{
    // Abro con "w" para reescribir el fichero con la lista actualizada
    $fic = fopen($ruta, "w");
    if ($fic === false) {
        return;
    }

    foreach ($puntos as $punto) {
        fputs($fic, $punto . PHP_EOL);
    }

    fclose($fic);
}

/**
 * Valida los datos del formulario de alta (x e y entre 0 y 500, color y grosor).
 * Si no hay errores, crea el Punto, lo guarda en el fichero y redirige.
 * Si hay errores, devuelve los valores escritos y los errores por campo.
 *
 * @param string $rutaFichero Ruta del fichero .dat.
 * @param array<int, Punto> $puntos Puntos actuales.
 * @return array [puntos actualizados, valores del formulario, errores por campo].
 */
function procesarAlta(string $rutaFichero, array $puntos): array
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

    // ---- Coordenada X: número entero entre 0 y 500 ----
    if ($xTexto === "" || preg_match('/^-?\d+$/', $xTexto) !== 1) {
        $errores["x"] = "La coordenada X debe ser un número entero";
    } else {
        $xNum = (int) $xTexto;
        if ($xNum < 0 || $xNum > 500) {
            $errores["x"] = "La coordenada X debe estar entre 0 y 500";
        }
    }

    // ---- Coordenada Y: número entero entre 0 y 500 ----
    if ($yTexto === "" || preg_match('/^-?\d+$/', $yTexto) !== 1) {
        $errores["y"] = "La coordenada Y debe ser un número entero";
    } else {
        $yNum = (int) $yTexto;
        if ($yNum < 0 || $yNum > 500) {
            $errores["y"] = "La coordenada Y debe estar entre 0 y 500";
        }
    }

    // ---- Color: tiene que existir en Punto::COLORES ----
    if ($color === "" || !array_key_exists($color, Punto::COLORES)) {
        $errores["color"] = "Debe seleccionar un color válido";
    }

    // ---- Grosor: tiene que existir en Punto::GROSORES ----
    $grosorNum = 0;
    if ($grosorTexto === "" || !ctype_digit($grosorTexto)) {
        $errores["grosor"] = "Debe seleccionar un grosor";
    } else {
        $grosorNum = (int) $grosorTexto;
        if (!array_key_exists($grosorNum, Punto::GROSORES)) {
            $errores["grosor"] = "El grosor seleccionado no es válido";
        }
    }

    // Si todo es correcto, añado el punto, actualizo el fichero y redirijo
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

    return [$puntos, $valores, $errores];
}

/**
 * Borra el punto elegido en el combo, actualiza el fichero .dat y recarga.
 *
 * @param string $rutaFichero Ruta del fichero .dat.
 * @param array<int, Punto> $puntos Puntos actuales.
 * @return array<int, Punto> Puntos actualizados.
 */
function procesarBorrado(string $rutaFichero, array $puntos): array
{
    $indiceTexto = trim(textoPost("indice_borrar"));
    if ($indiceTexto !== "" && ctype_digit($indiceTexto)) {
        $indice = (int) $indiceTexto;
        if (isset($puntos[$indice])) {
            // Elimino el punto y reindexo el array para no dejar huecos
            unset($puntos[$indice]);
            $puntos = array_values($puntos);
            guardarPuntos($rutaFichero, $puntos);
            header("Location: index.php");
            exit();
        }
    }

    return $puntos;
}

/**
 * Lee el fichero .txt subido y añade los puntos válidos al fichero .dat.
 *
 * @param string $rutaFichero Ruta del fichero .dat.
 * @param array<int, Punto> $puntos Puntos actuales.
 * @return array<int, Punto> Puntos actualizados.
 */
function procesarSubidaTxt(string $rutaFichero, array $puntos): array
{
    // Compruebo que se haya subido el archivo sin errores
    if (!isset($_FILES["fichero_txt"]) || $_FILES["fichero_txt"]["error"] !== UPLOAD_ERR_OK) {
        return $puntos;
    }

    $ficTxt = fopen($_FILES["fichero_txt"]["tmp_name"], "r");
    if ($ficTxt === false) {
        return $puntos;
    }

    $hayCambios = false;
    while (($linea = fgets($ficTxt)) !== false) {
        $linea = trim($linea);
        if ($linea === "") {
            continue;
        }
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
                // Si una línea no es válida, no la añado
            }
        }
    }

    fclose($ficTxt);

    if ($hayCambios) {
        guardarPuntos($rutaFichero, $puntos);
        header("Location: index.php");
        exit();
    }

    return $puntos;
}
