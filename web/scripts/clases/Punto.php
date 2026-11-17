<?php

/**
 * Clase Punto
 * Representa un punto en el plano con coordenadas, color y grosor.
 * @author Alumno/a
 */
class Punto
{
    // Constantes públicas de clase con los colores y grosores permitidos
    public const COLORES = [
        "black" => ["nombre" => "negro", "rgb" => [0, 0, 0]],
        "red"   => ["nombre" => "rojo",  "rgb" => [255, 0, 0]],
        "green" => ["nombre" => "verde", "rgb" => [0, 255, 0]],
        "blue"  => ["nombre" => "azul",  "rgb" => [0, 0, 255]],
    ];

    public const GROSORES = [
        1 => "fino",
        2 => "medio",
        3 => "grueso",
    ];

    // Propiedades privadas de instancia (convención con guion bajo inicial)
    private int $_x;
    private int $_y;
    private string $_color;
    private int $_grosor;

    /**
     * Constructor de la clase Punto.
     * Valida cada parámetro mediante sus métodos set; si alguno no es válido,
     * lanza una excepción.
     *
     * @param int $x Coordenada X (0 a 20000)
     * @param int $y Coordenada Y (0 a 20000)
     * @param string $color Clave del color en inglés según COLORES
     * @param int $grosor Identificador del grosor según GROSORES
     * @throws Exception Si algún valor no cumple las restricciones
     */
    public function __construct(int $x, int $y, string $color, int $grosor)
    {
        if (!$this->setX($x) || !$this->setY($y) || !$this->setColor($color) || !$this->setGrosor($grosor)) {
            throw new Exception("No se puede crear el punto: valores fuera de rango o no válidos.");
        }
    }

    /**
     * Devuelve la coordenada X.
     * @return int
     */
    public function getX(): int
    {
        return $this->_x;
    }

    /**
     * Devuelve la coordenada Y.
     * @return int
     */
    public function getY(): int
    {
        return $this->_y;
    }

    /**
     * Devuelve el color (clave en inglés).
     * @return string
     */
    public function getColor(): string
    {
        return $this->_color;
    }

    /**
     * Devuelve el grosor (entero).
     * @return int
     */
    public function getGrosor(): int
    {
        return $this->_grosor;
    }

    /**
     * Establece la coordenada X si está entre 0 y 20000.
     * @param int $x
     * @return bool true si es correcto, false en caso contrario
     */
    public function setX(int $x): bool
    {
        if ($x >= 0 && $x <= 20000) {
            $this->_x = $x;
            return true;
        }
        return false;
    }

    /**
     * Establece la coordenada Y si está entre 0 y 20000.
     * @param int $y
     * @return bool true si es correcto, false en caso contrario
     */
    public function setY(int $y): bool
    {
        if ($y >= 0 && $y <= 20000) {
            $this->_y = $y;
            return true;
        }
        return false;
    }

    /**
     * Establece el color si existe como clave en la constante COLORES.
     * @param string $color
     * @return bool true si es correcto, false en caso contrario
     */
    public function setColor(string $color): bool
    {
        if (array_key_exists($color, self::COLORES)) {
            $this->_color = $color;
            return true;
        }
        return false;
    }

    /**
     * Establece el grosor si existe como clave en la constante GROSORES.
     * @param int $grosor
     * @return bool true si es correcto, false en caso contrario
     */
    public function setGrosor(int $grosor): bool
    {
        if (array_key_exists($grosor, self::GROSORES)) {
            $this->_grosor = $grosor;
            return true;
        }
        return false;
    }

    /**
     * Método mágico __set: impide crear propiedades dinámicas en la instancia.
     * @param string $nombre
     * @param mixed $valor
     * @throws Exception
     */
    public function __set(string $nombre, mixed $valor): void
    {
        throw new Exception("No se permite crear la propiedad dinámica: $nombre");
    }

    /**
     * Devuelve el punto formateado como texto: "x; y; color; grosor".
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->_x}; {$this->_y}; {$this->_color}; {$this->_grosor}";
    }
}
