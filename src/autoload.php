<?php
/**
 * PSR-4 autoload
 *
 * After registering this autoload function with require_once()
 * Likel/Session/Handler can be called like this:
 *
 *      $session = new Likel\Session\Handler();
 *
 * @package     php-simple-sessions
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     MIT License <https://github.com/likel/php-simple-sessions/blob/master/LICENSE>
 * @link        https://github.com/likel/php-simple-sessions
 * @version     1.0.0
 */

// Require the models when called
spl_autoload_register(function ($class_name) {
    // Change these depending on the project
    $project_prefix = 'Likel\\';
    $models_dir = __DIR__ . '/models/';

    // Helper variables used in the autoloader
    $project_prefix_length = strlen($project_prefix);
    $relative_class = substr($class_name, $project_prefix_length);

    // Return if the requested class does not include the prefix
    if (strncmp($project_prefix, $class_name, $project_prefix_length) !== 0) {
        return;
    }

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the class name and append with .php
    $file = $models_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once($file);
    }
});
