<?php
/**
 * This is the session handler, the handler will create/save/encrypt
 * and cleanup sessions
 *
 * Can be instantiated like so:
 *
 *      use Likel\Session\Handler as LikelSession;
 *      $session = new LikelSession();
 *
 * @package     php-simple-sessions
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     MIT License <https://github.com/likel/php-simple-sessions/blob/master/LICENSE>
 * @link        https://github.com/likel/php-simple-sessions
 * @version     1.0.1
 */
namespace Likel\Session;

class Handler implements \ArrayAccess
{
    private $db; // Store the database connection
    private $secret_hash; // Hold the secret hash for encryption
    private $session_hash_algorithm = "sha512"; // Algorithm to hash the session variables

    /**
     * Construct the session Handler object
     * Set the secret_hash and start the session
     *
     * @param array $parameters An assoc. array that holds the session parameters
     * @return void
     */
    function __construct($parameters = array())
    {
        if(!is_array($parameters)) {
            $parameters = array();
        }

        // Defaults
        $parameters["session_name"] = empty($parameters["session_name"]) ? "likel_session" : $parameters["session_name"];
        $parameters["secure"] = empty($parameters["secure"]) ? false : is_bool($parameters["secure"] === true) ? true : false;
        $parameters["credentials_location"] = empty($parameters["credentials_location"]) ? __DIR__ . '/../../ini/credentials.ini' : $parameters["credentials_location"];

        // Setup the database class variable
        $this->db = new \Likel\DB($parameters["credentials_location"]);

        if($this->db->databaseInitialised()) {
            // Attempt to get the secret_hash from the credentials file
            try {
                $this->secret_hash = $this->loadSecretHash($parameters["credentials_location"]);

                // Start session
                $this->start_session($parameters["session_name"], $parameters["secure"]);
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }
        }
    }

    /**
     * Attempt to retrieve the secret_hash from the credentials file
     *
     * @param array $credentials likel_session from the credentials.ini file
     * @return string
     * @throws \Exception If credentials empty or not found
     */
    private function loadSecretHash($credentials_location)
    {
        if(file_exists($credentials_location)) {
            $session_credentials = parse_ini_file($credentials_location, true);
            $credentials = $session_credentials["likel_session"];

            if(!empty($credentials)){
                if(!empty($credentials["secret_hash"])) {
                    return $credentials["secret_hash"];
                } else {
                    throw new \Exception('The session_hash variable is empty.');
                }
            } else {
                throw new \Exception('The likel_session parameter in the credentials file cannot be found.');
            }
        } else {
            throw new \Exception('The credential file could not be located.');
        }
    }

    /**
     * Open the session if the db connection has been made
     *
     * @return bool
     */
    public function _open()
    {
        return $this->db ? true : false;
    }

    /**
     * Close the session
     *
     * @return true
     */
    public function _close()
    {
        return true;
    }

    /**
     * Read the $_SESSION variable by decrypting the database row
     *
     * @param string $id The ID of the current session
     * @return bool
     */
    public function _read($id)
    {
        // Setup the query
        $this->db->query("
            SELECT data FROM {$this->db->getTableName("sessions")}
            WHERE id = :id
            LIMIT 1
        ");

        // Bind data
        $this->db->bind(':id', $id, \PDO::PARAM_STR);

        // Execute and get result
        $data = $this->db->result();

        if(!empty($data)){
            // Decrypt the data
            $key_and_iv = $this->getKeyAndIv($id);
            $new_data = $this->decrypt($data["data"], $key_and_iv["key"], $key_and_iv["iv"]);
            return $new_data;
        } else {
            return "";
        }
    }

    /**
     * When a session variable is set or unset, the $_SESSION data is
     * encrypted and the database row is updated
     *
     * @param string $id The ID of the current session
     * @param array $data The $_SESSION data as an array
     * @return bool
     */
    public function _write($id, $data)
    {
        // Get unique key
        $key_and_iv = $this->getKeyAndIv($id);

        // Encrypt the data
        $data = $this->encrypt($data, $key_and_iv["key"], $key_and_iv["iv"]);

        // Setup the query
        $this->db->query("
            REPLACE INTO {$this->db->getTableName("sessions")} (id, set_time, data, session_key, iv)
            VALUES (:id, :set_time, :data, :session_key, :iv)
        ");

        // Bind data
        $this->db->bind(':id', $id, \PDO::PARAM_STR);
        $this->db->bind(':set_time', time(), \PDO::PARAM_INT);
        $this->db->bind(':data', $data, \PDO::PARAM_STR);
        $this->db->bind(':session_key', $key_and_iv["key"], \PDO::PARAM_STR);
        $this->db->bind(':iv', $key_and_iv["iv"], \PDO::PARAM_STR);

        // Execute and return result
        return $this->db->execute();
    }

    /**
     * Deletes the session
     * This function is called when session_destroy() is called
     *
     * @param string $id The ID of the current session
     * @return bool
     */
    public function _destroy($id)
    {
        // Setup the query
        $this->db->query("
            DELETE FROM {$this->db->getTableName("sessions")}
            WHERE id = :id
        ");

        // Bind data
        $this->db->bind(':id', $id, \PDO::PARAM_STR);

        // Execute and return result
        return $this->db->execute();
    }

    /**
     * Run by the server to clean up expired sessions from
     * the database. This depends on the session.gc_probability
     * and session.gc_divisor settings on your server
     *
     * Maximum session length is set by the session.gc_maxlifetime
     * in $this->start_session() and is determined by set_time
     * in the database
     *
     * @param int $max The maximum session length
     * @return bool
     */
    public function _garbageCollection($max)
    {
        // Setup the query
        $this->db->query("
            DELETE FROM {$this->db->getTableName("sessions")}
            WHERE set_time < :time
        ");

        // Bind data
        $this->db->bind(':time', time() - $max, \PDO::PARAM_INT);

        // Execute and return result
        return $this->db->execute();
    }

    /**
     * Encrypt the session data into a string
     *
     * @param array $data The $_SESSION data as an array
     * @param string $key The session_key from the database
     * @param string $iv The iv from the database
     * @return string
     */
    public function encrypt($data, $key, $iv)
    {
        return openssl_encrypt($data, "AES-256-CBC", $this->secret_hash . $key, 0, $iv);
    }

    /**
     * Decrypt the session data from the database
     *
     * @param string $data The $_SESSION data as a string from the database
     * @param string $key The session_key from the database
     * @param string $iv The iv from the database
     * @return array
     */
    public function decrypt($data, $key, $iv)
    {
        return openssl_decrypt($data, "AES-256-CBC", $this->secret_hash . $key, 0, $iv);
    }

    /**
     * Get or generate a secret session_key and initialization vector
     *
     * @param string $id The ID of the current session
     * @return array
     */
    private function getKeyAndIv($id)
    {
        // Setup the query
        $this->db->query("
            SELECT iv, session_key FROM {$this->db->getTableName("sessions")}
            WHERE id = :id
            LIMIT 1
        ");

        // Bind data
        $this->db->bind(':id', $id, \PDO::PARAM_STR);

        // Execute and get result
        $return = $this->db->result();

        if($return) {
            // Found the session in the DB
            return array("iv" => $return["iv"], "key" => $return["session_key"]);
        } else {
            // Starting a new session, create new iv and key
            return array("iv" => bin2hex(openssl_random_pseudo_bytes(8)), "key" => hash($this->session_hash_algorithm, uniqid(mt_rand(1, mt_getrandmax()), true)));
        }
    }

    /**
     * Setup and start the session
     *
     * @param string $session_name What to set the session name as
     * @param bool $secure The website is using HTTPS
     * @return void
     */
    private function start_session($session_name, $secure)
    {
        // Setup the session functions
        session_set_save_handler(
            array($this, '_open'),
            array($this, '_close'),
            array($this, '_read'),
            array($this, '_write'),
            array($this, '_destroy'),
            array($this, '_garbageCollection')
        );
        register_shutdown_function('session_write_close');

        // Miscellanious session helper variables
        ini_set('session.hash_function', $this->session_hash_algorithm);
        ini_set('session.hash_bits_per_character', 5);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.gc_maxlifetime', 3600);

        // Get session cookie parameters, set the cookies and start the session
        $cookie_params = session_get_cookie_params();
        session_set_cookie_params(3600, "/", $cookie_params["domain"], $secure, true);
        session_name($session_name);
        session_start();

        // Regenerate ID is recommended to reset the session every reload
        // Bug occurs if set to true that causes the current session to
        // be removed if loading pages too quickly
        // session_regenerate_id(true);
    }

    /**
     * Allows the Handler object to set $_SESSION variables:
     *      $SESSION = new LikelSession();
     *      $SESSION["foo"] = "bar";
     *
     * @param string $offset The array key e.g. "foo"
     * @param mixed $value The value to set e.g. "bar"
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!is_null($offset)) {
            $_SESSION[$offset] = $value;
        }
    }

    /**
     * Allows the Handler object to get $_SESSION variables:
     *      $SESSION = new LikelSession();
     *      $bar = $SESSION["foo"];
     *
     * @param string $offset The array key e.g. "foo"
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
    }

    /**
     * Checks if the $_SESSION offset exists:
     *      $SESSION = new LikelSession();
     *      $bar = isset($SESSION["foo"]);
     *
     * @param string $offset The array key e.g. "foo"
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    /**
     * Unset a $_SESSION offset:
     *      $SESSION = new LikelSession();
     *      unset($SESSION["foo"]);
     *
     * @param string $offset The array key e.g. "foo"
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($_SESSION[$offset]);
    }

    /**
     * Allows the Handler object to get $_SESSION debugInfo:
     *      $SESSION = new LikelSession();
     *      var_dump($SESSION);
     *
     * @return mixed
     */
    public function __debugInfo()
    {
        return $_SESSION;
    }
}
