<?php
/**
 * Front controller: único punto de entrada de la aplicación.
 */
define('BASE_PATH', __DIR__);

spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

require BASE_PATH . '/app/helpers.php';

App\Core\Config::load(require BASE_PATH . '/config/config.php');
(new App\Core\App(require BASE_PATH . '/app/routes.php'))->run();
