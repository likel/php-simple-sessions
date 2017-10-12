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
        $session = new \Likel\Session\Handler();
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Non-array parameter supplied to constructor
     */
    public function testConstructorNonArray()
    {
        $session = new \Likel\Session\Handler("a");
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Supply a correct session_name parameter
     */
    public function testConstructorSessionNameSet()
    {
        $session = new \Likel\Session\Handler(array(
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
        $session = new \Likel\Session\Handler(array(
            'secure' => "true"
        ));
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Supply a correct credentials_location parameter
     */
    public function testConstructorCredentialsLocationSet()
    {
        $session = new \Likel\Session\Handler(array(
            'credentials_location' => __DIR__ . '/../src/ini/credentials.ini'
        ));
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Supply a parameter that doesn't exist
     */
    public function testConstructorNonExistantParameter()
    {
        $session = new \Likel\Session\Handler(array(
            'foo' => 'bar'
        ));
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
    }

    /**
     * Incorrect secure parameter type supplied
     */
    public function testConstructorIncorrectSecureType()
    {
        $session = new \Likel\Session\Handler(array(
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
        $session = new \Likel\Session\Handler(array(
            'credentials_location' => "path"
        ));
        $this->assertEquals(session_status(), PHP_SESSION_NONE);
    }

    /**
     * Check session is in the database
     */
    public function testSessionInDatabase()
    {
        $session = new \Likel\Session\Handler();
        $db = new \Likel\DB(__DIR__ . '/../src/ini/credentials.ini');
        $db->query("
            SELECT * FROM {$db->getTableName("sessions")}
            WHERE id = :id
        ");
        $db->bind(":id", session_id());
        $this->assertNotNull($db->result());
    }

    /**
     * Test ArrayAccess implementation
     */
    public function testArrayAccessImplementation()
    {
        $session = new \Likel\Session\Handler();

        // offsetSet test
        $session["set"] = "foo";
        $this->assertEquals($session["set"], "foo");

        // offsetGet tests
        $bar = $session["set"];
        $this->assertEquals($session["set"], $bar);
        $this->assertNull($session["not_set"]);

        // offsetExists tests
        $this->assertTrue(isset($session["set"]));
        $this->assertFalse(isset($session["not_set"]));

        // offsetUnset test
        unset($session["set"]);
        $this->assertNull($session["set"]);

        // __debugInfo test
        $session["set"] = "foo";
        $this->assertEquals('Likel\Session\HandlerObject([set]=>foo)', preg_replace('/\s+/', '', print_r($session, true)));
    }
}
