<?php

// Clase que representa un texto registrado junto con la fecha y hora de creación
class RegistroTexto
{
    // Propiedades privadas (no se modifican una vez creado el objeto)
    private string $texto;
    private DateTime $fechaHora;

    // Constructor: guarda el texto y asigna la fecha y hora actual del sistema
    public function __construct(string $texto)
    {
        $this->texto = $texto;
        $this->fechaHora = new DateTime();
    }

    // Devuelve el texto almacenado
    public function getTexto(): string
    {
        return $this->texto;
    }

    // Devuelve el objeto DateTime con la fecha y hora del registro
    public function getFechaHora(): DateTime
    {
        return $this->fechaHora;
    }

    // Devuelve la fecha y hora formateada como cadena
    public function getFechaHoraFormateada(string $formato = "Y-m-d H:i:s"): string
    {
        return $this->fechaHora->format($formato);
    }

    // Deshabilitamos la sobrecarga dinámica de propiedades (requisito general de la práctica)
    public function __set(string $nombre, $valor): void
    {
        throw new Exception("No se permite la sobrecarga dinámica de propiedades en " . __CLASS__ . " (propiedad: $nombre).");
    }

    public function __get(string $nombre)
    {
        throw new Exception("No se permite la sobrecarga dinámica de propiedades en " . __CLASS__ . " (propiedad: $nombre).");
    }

    public function __isset(string $nombre): bool
    {
        throw new Exception("No se permite la sobrecarga dinámica de propiedades en " . __CLASS__ . " (propiedad: $nombre).");
    }

    public function __unset(string $nombre): void
    {
        throw new Exception("No se permite la sobrecarga dinámica de propiedades en " . __CLASS__ . " (propiedad: $nombre).");
    }
}
