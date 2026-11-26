<?php

// Clase abstracta base para listas de control de acceso (ACL), reutilizable en futuros proyectos
abstract class ACLBase
{
    // --- GESTIÓN DE ROLES ---

    // Añade un nuevo rol con código autonumérico y hasta 10 permisos (1..10, por defecto false)
    abstract public function anadirRole(string $nombre, array $permisos = array()): bool;

    // Devuelve el código del rol por nombre o false si no existe
    abstract public function getCodRole(string $nombre): int|false;

    // Indica si existe un rol con el código indicado
    abstract public function existeRole(int $codRole): bool;

    // Devuelve los permisos asignados a un rol o false si no existe
    abstract public function getPermisosRole(int $codRole): array|false;

    // Devuelve si el rol tiene activado el permiso $numero (1..10)
    abstract public function getPermisoRole(int $codRole, int $numero): bool;

    // Devuelve un array indexado [codRole => nombre] con los roles del sistema
    abstract public function dameRoles(): array;

    // --- GESTIÓN DE USUARIOS ---

    // Añade un usuario con nick único, contraseña y rol existente; devuelve true si se crea
    abstract public function anadirUsuario(string $nombre, string $nick, string $contrasena, int $codRole): bool;

    // Devuelve el código de usuario de un nick o false si no existe
    abstract public function getCodUsuario(string $nick): int|false;

    // Indica si existe un usuario con el código indicado
    abstract public function existeCodUsuario(int $codUsuario): bool;

    // Indica si existe un usuario con el nick indicado
    abstract public function existeUsuario(string $nick): bool;

    // Comprueba si el nick existe y coincide la contraseña
    abstract public function esValido(string $nick, string $contrasena): bool;

    // Devuelve si el usuario indicado tiene el permiso $numero (1..10)
    abstract public function getPermiso(int $codUsuario, int $numero): bool;

    // Devuelve un array con los permisos del usuario
    abstract public function getPermisos(int $codUsuario): array;

    // Devuelve el nombre del usuario por código o false si no se encuentra
    abstract public function getNombre(int $codUsuario): string|false;

    // Indica si el usuario está marcado como borrado (borrado lógico)
    abstract public function getBorrado(int $codUsuario): bool;

    // Devuelve el código de rol del usuario o false si no se encuentra
    abstract public function getUsuarioRole(int $codUsuario): int|false;

    // Modifica el nombre de un usuario existente
    abstract public function setNombre(int $codUsuario, string $nombre): bool;

    // Establece/actualiza la contraseña de un usuario existente
    abstract public function setContrasenia(int $codUsuario, string $contra): bool;

    // Activa o desactiva el borrado lógico de un usuario
    abstract public function setBorrado(int $codUsuario, bool $borrado): bool;

    // Cambia el rol asignado a un usuario existente
    abstract public function setUsuarioRole(int $codUsuario, int $role): bool;

    // Devuelve un array asociativo [codUsuario => nick] con los usuarios
    abstract public function dameUsuarios(): array;

    // --- DESHABILITAR SOBRECARGA DINÁMICA DE PROPIEDADES ---

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
