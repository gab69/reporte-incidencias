<?php
/**
 * Configuración de la aplicación. Edita solo los datos de conexión.
 */
return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'incidencias',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    'debug' => false, // true: muestra el detalle técnico de los errores

    'uploads' => [
        'path'        => BASE_PATH . '/uploads/',
        'url'         => 'uploads/',
        'max_px'      => 1200, // lado mayor máximo de las imágenes de los pasos
        'quality'     => 80,   // calidad WebP (0-100)
        'logo_max_px' => 480,
    ],

    'paginacion' => [
        'historial' => 20,
    ],
];
