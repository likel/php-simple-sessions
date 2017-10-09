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
    // Helper variables used in the object
    private $DB;

    /**
     * Construct the FizzBuzz object
     * Sets up the range, defaults to 1-100 if no range set
     *
     * @param int $range_min The minimum range for the sequence
     * @param int $range_max The maximum range for the sequence
     * @return void
     */
    function __construct()
    {
        $this->DB = new Likel\Session\DB();
    }

    /**
     * Sets the range for the FizzBuzz test
     * Expects is_numeric params
     *
     * @param int $range_min The minimum range for the sequence
     * @param int $range_max The maximum range for the sequence
     * @return void
     */
    public function setRange($range_min, $range_max)
    {

    }
}
