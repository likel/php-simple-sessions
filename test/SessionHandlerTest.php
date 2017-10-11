<?php
/**
 * PHPUnit tests for models/Session/Handler.php
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
final class SessionHandlerTest extends TestCase
{
    private $session;

    /**
     * Destroy the session at the end of each test
     */
    protected function tearDown()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * No parameters supplied to constructor
     */
    public function testConstructorNoParameters()
    {
        $this->session = new \Likel\Session\Handler();
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Non-array parameter supplied to constructor
     */
    public function testConstructorNonArray()
    {
        $this->session = new \Likel\Session\Handler("a");
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Supply a correct session_name parameter
     */
    public function testConstructorSessionNameSet()
    {
        $this->session = new \Likel\Session\Handler(array(
            'session_name' => "test_session"
        ));
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
        $this->assertEquals(session_name(), "test_session");
    }

    /**
     * Supply a correct secure parameter
     */
    public function testConstructorSecureSet()
    {
        $this->session = new \Likel\Session\Handler(array(
            'secure' => "true"
        ));
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Supply a correct credentials_location parameter
     */
    public function testConstructorCredentialsLocationSet()
    {
        $this->session = new \Likel\Session\Handler(array(
            'credentials_location' => __DIR__ . '/../src/ini/credentials.ini'
        ));
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Supply a parameter that doesn't exist
     */
    public function testConstructorNonExistantParameter()
    {
        $this->session = new \Likel\Session\Handler(array(
            'foo' => 'bar'
        ));
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Incorrect secure parameter type supplied
     */
    public function testConstructorIncorrectSecureType()
    {
        $this->session = new \Likel\Session\Handler(array(
            'secure' => "false"
        ));
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Incorrect credentials_location file path supplied
     */
    public function testConstructorIncorrectCredentialsLocation()
    {
        $this->expectOutputString('The credential file could not be located.');
        $this->session = new \Likel\Session\Handler(array(
            'credentials_location' => "path"
        ));
        $this->assertEquals(session_status(), PHP_SESSION_NONE);
    }
}
