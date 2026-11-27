<?php

// Clase que gestiona y mantiene en sesión la información del usuario autenticado y sus permisos
class Acceso
{
    // Clave utilizada dentro del array $_SESSION para persistir el estado de acceso
    private const CLAVE_SESION = "relacion8_acceso";

    // Indica si actualmente hay un usuario validado en la aplicación
    private bool $_validado = false;

    // Nick único del usuario registrado (vacío si no hay usuario)
    private string $_nick = "";

    // Nombre completo o visible del usuario registrado (vacío si no hay usuario)
    private string $_nombre = "";

    // Array indexado del 1 al 10 indicando si el usuario tiene cada permiso (true/false)
    private array $_permisos = [];

    // Constructor: recupera desde $_SESSION los datos guardados si existen y los carga en las propiedades privadas
    public function __construct()
    {
        // Inicializamos por defecto todos los permisos del 1 al 10 en false
        $this->_permisos = $this->permisosPorDefecto();

        // Comprobamos si existe información de acceso guardada en la sesión actual
        if (isset($_SESSION[self::CLAVE_SESION]) && is_array($_SESSION[self::CLAVE_SESION])) {
            $datos = $_SESSION[self::CLAVE_SESION];

            // Cargamos el estado de validación si es booleano
            if (isset($datos["validado"])) {
                $this->_validado = (bool) $datos["validado"];
            }

            // Cargamos el nick si es cadena
            if (isset($datos["nick"]) && is_string($datos["nick"])) {
                $this->_nick = $datos["nick"];
            }

            // Cargamos el nombre si es cadena
            if (isset($datos["nombre"]) && is_string($datos["nombre"])) {
                $this->_nombre = $datos["nombre"];
            }

            // Cargamos los permisos del 1 al 10 asegurándonos de que cada índice exista y sea booleano
            if (isset($datos["permisos"]) && is_array($datos["permisos"])) {
                for ($i = 1; $i <= 10; $i++) {
                    $this->_permisos[$i] = !empty($datos["permisos"][$i]);
                }
            }

            // Si está marcado como validado pero falta el nick, invalidamos por coherencia
            if ($this->_validado && $this->_nick === "") {
                $this->quitarRegistroUsuario();
            }
        }
    }

    // Genera un array base con los 10 permisos (claves 1..10) inicializados a false
    private function permisosPorDefecto(): array
    {
        // Creamos el array vacío
        $base = [];
        // Recorremos los permisos del 1 al 10
        for ($i = 1; $i <= 10; $i++) {
            // Por defecto ningún permiso está concedido
            $base[$i] = false;
        }
        // Devolvemos el array normalizado
        return $base;
    }

    // Guarda el estado actual de las propiedades privadas en la variable de sesión correspondiente
    private function guardarEnSesion(): void
    {
        // Guardamos un array asociativo con todos los datos del usuario registrado
        $_SESSION[self::CLAVE_SESION] = [
            "validado" => $this->_validado,
            "nick"     => $this->_nick,
            "nombre"   => $this->_nombre,
            "permisos" => $this->_permisos
        ];
    }

    // Registra un usuario validado en las propiedades privadas y en la sesión
    public function registrarUsuario(string $nick, string $nombre, array $permisos): bool
    {
        // Limpiamos espacios en los extremos de nick y nombre
        $nickLimpio = trim($nick);
        $nombreLimpio = trim($nombre);

        // Si falta el nick o el nombre, no podemos registrar un usuario válido
        if ($nickLimpio === "" || $nombreLimpio === "") {
            return false;
        }

        // Normalizamos los 10 permisos (1..10) a valores booleanos
        $permisosLimpios = $this->permisosPorDefecto();
        for ($i = 1; $i <= 10; $i++) {
            $permisosLimpios[$i] = !empty($permisos[$i]);
        }

        // Asignamos los valores a las propiedades privadas del objeto
        $this->_validado = true;
        $this->_nick = $nickLimpio;
        $this->_nombre = $nombreLimpio;
        $this->_permisos = $permisosLimpios;

        // Persistimos los datos en $_SESSION para que sigan disponibles en siguientes páginas
        $this->guardarEnSesion();

        // Devolvemos true indicando que el registro se realizó correctamente
        return true;
    }

    // Elimina el registro del usuario validado y deja el estado como no autenticado
    public function quitarRegistroUsuario(): bool
    {
        // Restablecemos las propiedades privadas a estado sin usuario
        $this->_validado = false;
        $this->_nick = "";
        $this->_nombre = "";
        $this->_permisos = $this->permisosPorDefecto();

        // Eliminamos o limpiamos la entrada correspondiente en $_SESSION sin destruir toda la sesión
        unset($_SESSION[self::CLAVE_SESION]);

        // Devolvemos true indicando que se ha quitado el registro de usuario
        return true;
    }

    // Devuelve true si hay un usuario validado actualmente, o false en caso contrario
    public function hayUsuario(): bool
    {
        // Retornamos el estado booleano de validación
        return $this->_validado;
    }

    // Devuelve true si el usuario validado tiene concedido el permiso indicado ($numero del 1 al 10)
    public function puedePermiso($numero): bool
    {
        // Convertimos a entero para comprobar el rango válido de permisos
        $numPermiso = (int) $numero;

        // Si no hay usuario validado o el permiso está fuera de 1..10, devolvemos false
        if (!$this->_validado || $numPermiso < 1 || $numPermiso > 10) {
            return false;
        }

        // Devolvemos el booleano del permiso solicitado
        return (bool) ($this->_permisos[$numPermiso] ?? false);
    }

    // Devuelve el nick del usuario registrado (o cadena vacía si no hay usuario validado)
    public function getNick(): string
    {
        // Devolvemos el nick almacenado
        return $this->_nick;
    }

    // Devuelve el nombre del usuario registrado (o cadena vacía si no hay usuario validado)
    public function getNombre(): string
    {
        // Devolvemos el nombre almacenado
        return $this->_nombre;
    }

    // --- DESHABILITAR SOBRECARGA DINÁMICA DE PROPIEDADES ---

    // Bloquea la creación o modificación dinámica de propiedades inexistentes
    public function __set(string $nombre, $valor): void
    {
        throw new Exception("No se permite la sobrecarga dinámica de propiedades en " . __CLASS__ . " (propiedad: $nombre).");
    }

    // Bloquea la lectura dinámica de propiedades inexistentes o inaccesibles
    public function __get(string $nombre)
    {
        throw new Exception("No se permite la sobrecarga dinámica de propiedades en " . __CLASS__ . " (propiedad: $nombre).");
    }

    // Bloquea isset() sobre propiedades dinámicas inexistentes
    public function __isset(string $nombre): bool
    {
        throw new Exception("No se permite la sobrecarga dinámica de propiedades en " . __CLASS__ . " (propiedad: $nombre).");
    }

    // Bloquea unset() sobre propiedades dinámicas inexistentes
    public function __unset(string $nombre): void
    {
        throw new Exception("No se permite la sobrecarga dinámica de propiedades en " . __CLASS__ . " (propiedad: $nombre).");
    }
}