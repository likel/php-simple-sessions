<?php
/**
 * Load the models
 *
 * Sadly we can't use an autoloader here in the case that the end-user
 * is using one. Multiple autoloaders can cause conflicts
 *
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

// Require the models
require_once(__DIR__ . '/models/DB.php');
require_once(__DIR__ . '/models/Session/Handler.php');
