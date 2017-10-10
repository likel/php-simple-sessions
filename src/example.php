<?php
/**
 * Example usage of the php-simple-sessions package
 *
 * @package     php-simple-sessions
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     MIT License <https://github.com/likel/php-simple-sessions/blob/master/LICENSE>
 * @link        https://github.com/likel/php-simple-sessions
 * @version     1.0.0
 */

header('Content-Type: text/plain');

// Load the scripts, alternatively use a custom PSR-4 autoloader
include('autoload.php');

// This looks nicer and makes the session Handler easier to call
use Likel\Session\Handler as LikelSession;

// Create a new session. Example parameters include:
//      $session = new LikelSession(array(
//          'session_name' => "YourCustomSessionName",
//          'credentials_location' => "/path/to/new/credentials.ini",
//          'secure' => true
//      ));
$session = new LikelSession();

// Set some session variables
if(!isset($session["user_id"])) {
    $session["user_id"] = 1;
    $session["name"] = "Liam";
    $session["preferences"] = array(
        'language' => 'en-AU'
    );
    $_SESSION["test"] = "works";
}

// Unset our name
unset($session["name"]);

// Dump some variables and the session
echo "The session_id is: " . session_id() . PHP_EOL . PHP_EOL;
echo "The name is (should be blank): " . $session["name"] . PHP_EOL . PHP_EOL;
echo "The user_id is: " . $session["user_id"] . PHP_EOL . PHP_EOL;
echo "The preferences are: " . print_r($session["preferences"], true) . PHP_EOL;
echo "The \$_SESSION variable still: " . $_SESSION["test"] . PHP_EOL . PHP_EOL;
var_dump($session);

// You should no check the database table to see if a row has been added
