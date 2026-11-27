<?php

// Implementación de la ACL almacenada en memoria mediante arrays asociativos
class ACLArray extends ACLBase
{
    /**
     * $_roles contiene los roles definidos en la ACL
     * Cada role sera un array asociativo de
     * cod => [ "cod"=> ,
     *            "nombre" =>  ,
     *            "permisos" => [1=>false, 2=> XX, .........10=> XXX]
     *          ]
     */
    private $_roles = [];

    /**
     * $_usuarios contiene los usuarios definidos en la ACL
     * Cada usuario sera un array asociativo de
     *
     * cod => [
     *          "cod" => ,
     *          "nick" =>  ,
     *          "nombre" => ,
     *          "contrasenia" =>  , usamos una función de cifrado.
     *          "cod_role" => ,
     *          "borrado" =>
     *        ]
     */
    private $_usuarios = [];

    // Constructor: carga los roles y usuarios iniciales de prueba
    function __construct()
    {
        // Añade los roles base del sistema
        $this->anadirRole("normales", [1 => true]);
        $this->anadirRole("administradores", [1 => true, 2 => true]);

        // Añade los usuarios de prueba asociados a su rol correspondiente
        $this->anadirUsuario(
            "Es usuario alumno",
            "alumno",
            "alum",
            $this->getCodRole("normales")
        );
        $this->anadirUsuario(
            "Es usuario profesor",
            "profesor",
            "profe",
            $this->getCodRole("administradores")
        );
        $this->anadirUsuario(
            "Vicente Tejero",
            "vicente",
            "profesor",
            $this->getCodRole("administradores")
        );
    }

    /**
     * Añade un role a nuesta ACL
     *
     * @param string $nombre Nombre del role a añadir
     * @param array $permisos Permisos que tendrá el role. Array con hasta 10 permisos
     * @return bool True si se ha podido crear, false en caso contrario
     */
    function anadirRole(string $nombre, array $permisos = array()): bool
    {
        // Pongo el nombre en minúscula
        $nombre = mb_strtolower($nombre);

        // Inicializo un array con todos los permisos (1..10) a false
        $per = [
            1 => false,
            false,
            false,
            false,
            false,
            false,
            false,
            false,
            false,
            false
        ];

        // Compruebo si ya existe el role
        if ($this->getCodRole($nombre) !== false) {
            return false;
        }

        // Reviso los permisos recibidos y los convierto a booleano
        foreach ($per as $clave => $val) {
            if (isset($permisos[$clave])) {
                $per[$clave] = boolval($permisos[$clave]);
            }
        }

        // Busco cuál es la última clave/código de rol usado
        $cont = 0;
        foreach ($this->_roles as $clave => $role) {
            if ($role["cod"] > $cont) {
                $cont = $role["cod"];
            }
        }

        // Incremento para asignar el siguiente código de rol
        $cont++;

        // Creo el role en $_roles
        $this->_roles[$cont] = [
            "cod" => $cont,
            "nombre" => $nombre,
            "permisos" => $per
        ];

        return true;
    }

    /**
     * Función que localiza un role y devuelve el código de ese role o false si no
     * lo encuentra
     *
     * @param string $nombre nombre del role
     * @return integer|false Devuelve el código de role para el nombre indicado o false si no encuentra el role
     */
    function getCodRole(string $nombre): int|false
    {
        // Pongo el nombre en minúscula para comparar
        $nombre = mb_strtolower($nombre);

        // Busco el rol por nombre
        foreach ($this->_roles as $clave => $role) {
            if ($role["nombre"] == $nombre) {
                return $role["cod"];
            }
        }

        return false;
    }

    /**
     * Función que comprueba la existencia de un role
     *
     * @param integer $codRole role a buscar
     * @return boolean Devuelve true si lo encuentra o false en caso contrario
     */
    function existeRole(int $codRole): bool
    {
        // Compruebo directamente si existe la clave $codRole en $_roles
        return isset($this->_roles[$codRole]);
    }

    /**
     * Función que devuelve los permisos de un role dado
     *
     * @param integer $codRole Role a buscar
     * @return array|false Devuelve los permisos o false si no encuentra el role
     */
    function getPermisosRole(int $codRole): array|false
    {
        // Compruebo que existe el código de role
        if (!$this->existeRole($codRole)) {
            return false;
        }

        // Devuelvo el array de permisos del role solicitado
        return ($this->_roles[$codRole]["permisos"]);
    }

    /**
     * Función que devuelve si un role tiene o no un permiso concreto
     *
     * @param integer $codRole Role a buscar
     * @param integer $numero Número de permiso
     * @return boolean True si encuentra el role y lo tiene. False en cualquier otro caso
     */
    function getPermisoRole(int $codRole, int $numero): bool
    {
        // Si no existe el rol o el número de permiso está fuera del rango 1..10, devuelvo false
        if (!$this->existeRole($codRole) || $numero < 1 || $numero > 10) {
            return false;
        }

        // Devuelvo el estado booleano del permiso solicitado en el rol
        return boolval($this->_roles[$codRole]["permisos"][$numero] ?? false);
    }

    /**
     * Función que añade un nuevo usuario a nuestra ACL
     *
     * @param string $nombre Nombre del usuario
     * @param string $nick Nick unico para el usuario
     * @param string $contrasena contraseña del usuario
     * @param integer $codRole Role a asignarle
     * @return boolean Devuelve true si puede crearlo. False en caso contrario
     */
    function anadirUsuario(string $nombre, string $nick, string $contrasena, int $codRole): bool
    {
        // Pongo el nick en minúscula
        $nick = mb_strtolower($nick);

        // El nick debe ser único: compruebo que no exista ya
        if ($this->existeUsuario($nick)) {
            return false;
        }

        // Compruebo que exista el role
        if (!$this->existeRole($codRole)) {
            return false;
        }

        // Cifro la contraseña con password_hash y PASSWORD_BCRYPT
        $contrasena = password_hash($contrasena, PASSWORD_BCRYPT);

        // Busco cuál es el último código de usuario
        $cont = 0;
        foreach ($this->_usuarios as $clave => $usu) {
            if ($usu["cod"] > $cont) {
                $cont = $usu["cod"];
            }
        }

        // Incremento para obtener el nuevo código
        $cont++;

        // Añado el usuario con borrado lógico en false
        $this->_usuarios[$cont] = [
            "cod" => $cont,
            "nick" => $nick,
            "nombre" => $nombre,
            "contrasenia" => $contrasena,
            "cod_role" => $codRole,
            "borrado" => false
        ];

        return true;
    }

    /**
     * Obtiene el código de usuario para un nick dado
     *
     * @param string $nick nick del usuario a buscar
     * @return integer|false Devuelve el codigo del usuario o false si no lo encuentra
     */
    function getCodUsuario(string $nick): int|false
    {
        // Pongo el nick en minúscula
        $nick = mb_strtolower($nick);

        // Busco el usuario por nick y devuelvo su clave/código
        foreach ($this->_usuarios as $clave => $usu) {
            if ($usu["nick"] == $nick) {
                return $clave;
            }
        }

        // No se encuentra
        return false;
    }

    /**
     * Verifica si existe un usuario dado un código
     *
     * @param integer $codUsuario Código del usuario a verificar
     * @return boolean Devuelve si existe o no el usuario
     */
    function existeCodUsuario(int $codUsuario): bool
    {
        // Compruebo si existe la clave $codUsuario dentro de $_usuarios
        if (isset($this->_usuarios[$codUsuario])) {
            return true;
        }

        // Si no existe esa clave, devuelvo false explícitamente
        return false;
    }

    /**
     * Verifica si existe o no un usuario con el nick dado
     *
     * @param string $nick Nick del usuario a comprobar
     * @return boolean Devuelve true si encuentra el usuario y
     * false en caso contrario
     */
    function existeUsuario(string $nick): bool
    {
        // Normalizo a minúscula el nick indicado
        $nick = mb_strtolower($nick);

        // Recorro todos los usuarios almacenados en $_usuarios
        foreach ($this->_usuarios as $usuario) {
            // Compruebo si el campo "nick" del usuario actual coincide con el nick indicado
            if (isset($usuario["nick"]) && $usuario["nick"] === $nick) {
                return true;
            }
        }

        // Si no se encuentra coincidencia, devuelvo false
        return false;
    }

    /**
     * Función que comprueba que existe un usuario y la contraseña indicada es la correcta
     *
     * @param string $nick Nick del usuario a comprobar
     * @param string $contrasena Contraseña del usuario a comprobar
     * @return boolean Devuelve true si existe el usuario y tiene la contraseña indicada.
     * False en otro caso
     */
    function esValido(string $nick, string $contrasena): bool
    {
        // Pongo el nick en minúscula
        $nick = mb_strtolower($nick);

        // Compruebo si existe el nick
        if (!$this->existeUsuario($nick)) {
            return false;
        }

        // Recojo cuál es el código del usuario
        $codigo = $this->getCodUsuario($nick);
        if ($codigo === false) {
            return false;
        }

        // Si el usuario tiene borrado lógico activo, no puede validarse
        if (!empty($this->_usuarios[$codigo]["borrado"])) {
            return false;
        }

        // Verifico la contraseña en claro contra el hash guardado mediante password_verify
        if (!password_verify($contrasena, $this->_usuarios[$codigo]["contrasenia"])) {
            return false;
        }

        // Es válido
        return true;
    }

    /**
     * Función que comprueba si un usuario tiene un permiso concreto
     *
     * @param integer $codUsuario Usuario a buscar
     * @param integer $numero Permiso a buscar
     * @return boolean Devuelve true si existe el usuario y tiene el permiso.
     * False en otro caso
     */
    function getPermiso(int $codUsuario, int $numero): bool
    {
        // Verifico que el usuario exista y no esté borrado
        if (!$this->existeCodUsuario($codUsuario) || !empty($this->_usuarios[$codUsuario]["borrado"])) {
            return false;
        }

        // Obtengo el código de rol del usuario
        $codRole = $this->getUsuarioRole($codUsuario);
        if ($codRole === false) {
            return false;
        }

        // Consulto si ese rol tiene activo el permiso $numero
        return $this->getPermisoRole($codRole, $numero);
    }

    /**
     * Función que devuelve los permisos de un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @return array|false Devuelve los permisos del usuario o false si
     * no existe el usuario
     */
    function getPermisos(int $codUsuario): array|false
    {
        // Si no existe el usuario, devuelvo false
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        // Obtengo el rol del usuario
        $codRole = $this->getUsuarioRole($codUsuario);
        if ($codRole === false) {
            return false;
        }

        // Devuelvo los permisos asociados al rol del usuario
        return $this->getPermisosRole($codRole);
    }

    /**
     * Función que devuelve el nombre de un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @return string|false Devuelve el nombre del usuario o false si no existe
     */
    function getNombre(int $codUsuario): string|false
    {
        // Compruebo que exista el usuario
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        // Devuelvo su nombre
        return $this->_usuarios[$codUsuario]["nombre"];
    }

    /**
     * Devuelve si un usuario está borrado
     *
     * @param integer $codUsuario Usuario a buscar.
     * @return boolean true si el usuario existe y no está borrado.
     * False en otro caso
     */
    function getBorrado(int $codUsuario): bool
    {
        // Si no existe el usuario, devuelvo false
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        // Devuelvo true si el usuario existe y NO está borrado; false en otro caso
        return !boolval($this->_usuarios[$codUsuario]["borrado"]);
    }

    /**
     * Devuelve el role que tiene un usuario concreto
     *
     * @param integer $codUsuario Usuario a buscar
     * @return integer|false Devuelve el role del usuario o false si no existe.
     */
    function getUsuarioRole(int $codUsuario): int|false
    {
        // Compruebo que exista el usuario
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        // Devuelvo el código de rol asignado
        return $this->_usuarios[$codUsuario]["cod_role"];
    }

    /**
     * Función que asigna un nombre a un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @param string $nombre Nombre a asignar
     * @return boolean Devuelve true si ha podido asignar el nombre, false en otro caso
     */
    function setNombre(int $codUsuario, string $nombre): bool
    {
        // Compruebo que exista el usuario
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        // Asigno el nuevo nombre
        $this->_usuarios[$codUsuario]["nombre"] = $nombre;

        return true;
    }

    /**
     * Función que asigna una contraseña a un usuario
     *
     * @param integer $codUsuario usuario a buscar
     * @param string $contrasenia contraseña a asignar
     * @return boolean Devuelve true si ha podido asignar la contraseña
     * False en otro caso
     */
    function setContrasenia(int $codUsuario, string $contrasenia): bool
    {
        // Verifico que el usuario exista y la nueva contraseña no esté vacía
        if (!$this->existeCodUsuario($codUsuario) || $contrasenia === "") {
            return false;
        }

        // Cifro la nueva contraseña con PASSWORD_BCRYPT
        $hash = password_hash($contrasenia, PASSWORD_BCRYPT);
        if ($hash === false) {
            return false;
        }

        // Guardo el hash en $_usuarios
        $this->_usuarios[$codUsuario]["contrasenia"] = $hash;
        return true;
    }

    /**
     * Función que borra/desborra lógicamente un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @param boolean $borrado Estado a asignar
     * @return boolean Devuelve true si ha podido asignar el estado.
     * False en otro caso
     */
    function setBorrado(int $codUsuario, bool $borrado): bool
    {
        // Compruebo que exista el usuario antes de cambiar su estado de borrado
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        // Asigno el estado booleano de borrado lógico
        $this->_usuarios[$codUsuario]["borrado"] = $borrado;
        return true;
    }

    /**
     * Función que cambia el role de un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @param integer $role Role a asignar
     * @return boolean Devuelve true si ha podido asignar el role al usuario.
     * False si no existe el usuario, role o no ha podido asignarlo
     */
    function setUsuarioRole(int $codUsuario, int $role): bool
    {
        // Compruebo que existan tanto el usuario como el rol destino
        if (!$this->existeCodUsuario($codUsuario) || !$this->existeRole($role)) {
            return false;
        }

        // Actualizo el rol asignado al usuario y devuelvo true
        $this->_usuarios[$codUsuario]["cod_role"] = $role;
        return true;
    }

    /**
     * Devuelve un array con todos los usuarios existentes.
     * La clave es el codigo de usuario, el valor es el nick del usuario
     *
     * @return array Array con todos los usuarios existentes
     */
    function dameUsuarios(): array
    {
        // Inicializo el array resultado
        $usuarios = [];

        // Recorro $_usuarios mapeando [cod => nick]
        foreach ($this->_usuarios as $clave => $usu) {
            $usuarios[$clave] = $usu["nick"];
        }

        // Devuelvo el array construido
        return $usuarios;
    }

    /**
     * Devuelve un array con todos los roles existentes.
     * La clave es el codigo de role, el valor es el nombre del role
     *
     * @return array Array con todos los roles existentes
     */
    function dameRoles(): array
    {
        // Inicializo el array resultado
        $roles = [];

        // Recorro $_roles mapeando [cod => nombre]
        foreach ($this->_roles as $clave => $role) {
            $roles[$clave] = $role["nombre"];
        }

        // Devuelvo el array construido
        return $roles;
    }
}
