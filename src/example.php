<?php
/**
 * The index file, we can run our code from here
 *
 * A program which prints numbers from 1 to 100 with the following conditions:
 *    multiples of 3 print "Fizz" instead of the number
 *    multiples of 5 print "Buzz" instead of the number
 *    multiples of 3 & 5 print "FizzBuzz"
 *
 * @package     fizz-buzz
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     https://github.com/likel/fizz-buzz/blob/master/LICENSE GPL-3.0 License
 * @link        https://github.com/likel/fizz-buzz
 * @version     1.0.0
 */

require_once('autoload.php');

$db = new LikelSession();
