<?php
/**
 * This is the session handler
 *
 * @package     php-simple-sessions
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     MIT License <https://github.com/likel/fizz-buzz/blob/master/LICENSE>
 * @link        https://github.com/likel/php-simple-sessions
 * @version     1.0.0
 */
namespace Likel\Session;

class Handler
{
    // Store the database variable
    private $db;

    /**
     * Construct the session Handler object
     *
     * @return void
     */
    function __construct()
    {
        $this->db = new DB();
    }

    /**
     *
     *
     * @return 
     */
    public function getSession()
    {
        $this->db->query("
            SELECT * FROM {$this->db->getTableName("sessions")}
        ");

        return $this->db->execute();
    }
}
