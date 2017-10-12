<?php
/**
 * PHPUnit tests for models/DB.php
 *
 * Test errors in PHPUnit 6.4.0 make sure to upgrade to 6.4.1
 *
 * @package     php-simple-sessions
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     MIT License <https://github.com/likel/php-simple-sessions/blob/master/LICENSE>
 * @link        https://github.com/likel/php-simple-sessions
 * @version     1.0.0
 */
use PHPUnit\Framework\TestCase;

// Require the autoloader to load the models when required
require_once(__DIR__ . '/../src/autoload.php');

/**
 * @runTestsInSeparateProcesses
 */
final class SessionDBTest extends TestCase
{
    /**
     * No parameters supplied to constructor
     */
    public function testConstructorNoParameters()
    {

    }
}
