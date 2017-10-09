<?php
/**
 * Example usage of the php-simple-sessions package
 *
 * @package     php-simple-sessions
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     MIT License <https://github.com/likel/fizz-buzz/blob/master/LICENSE>
 * @link        https://github.com/likel/php-simple-sessions
 * @version     1.0.0
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('autoload.php');

use Likel\Session\Handler as LikelSession;

$session = new LikelSession();
$session->getSession();
