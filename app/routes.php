<?php
/**
 * Tabla de rutas:  'ruta' => [método HTTP, controlador, acción, acceso]
 * Acceso: public (cualquiera) · auth (con sesión) · admin (solo administradores)
 */
return [
    'incidencias/index'   => ['GET',  'IncidenciaController', 'index',   'public'],
    'incidencias/ver'     => ['GET',  'IncidenciaController', 'ver',     'public'],
    'incidencias/form'    => ['GET',  'IncidenciaController', 'form',    'auth'],
    'incidencias/guardar' => ['POST', 'IncidenciaController', 'guardar', 'auth'],
    'incidencias/toggle'  => ['POST', 'IncidenciaController', 'toggle',  'auth'],

    'auth/login'          => ['GET',  'AuthController', 'login',  'public'],
    'auth/entrar'         => ['POST', 'AuthController', 'entrar', 'public'],
    'auth/salir'          => ['POST', 'AuthController', 'salir',  'auth'],

    'catalogos/index'     => ['GET',  'CatalogoController', 'index',   'admin'],
    'catalogos/guardar'   => ['POST', 'CatalogoController', 'guardar', 'admin'],

    'usuarios/index'      => ['GET',  'UsuarioController', 'index',   'admin'],
    'usuarios/guardar'    => ['POST', 'UsuarioController', 'guardar', 'admin'],

    'historial/index'     => ['GET',  'HistorialController', 'index', 'admin'],

    'ajustes/index'       => ['GET',  'AjusteController', 'index',   'admin'],
    'ajustes/guardar'     => ['POST', 'AjusteController', 'guardar', 'admin'],
];
