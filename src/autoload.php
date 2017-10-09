<?php
/**
 * Load the models
 *
 * Sadly we can't use an autoloader here incase the end-user
 * is using one. Multiple autoloaders can cause conflicts
 *
 * Likel/Session/Handler can be called with the friendly name
 * LikelSession, for example:
 *
 *      $session = new LikelSession();
 *
 * @package     php-simple-sessions
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     MIT License <https://github.com/likel/fizz-buzz/blob/master/LICENSE>
 * @link        https://github.com/likel/php-simple-sessions
 * @version     1.0.0
 */
namespace Likel\Session;

// Require the models
require_once(__DIR__ . '/models/Handler.php');
require_once(__DIR__ . '/models/DB.php');

use Likel\Session\Handler as LikelSession;
