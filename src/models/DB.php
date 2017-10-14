<?php
/**
 * The database object which helps to abstract database functions
 *
 * Uses and requires PDO, generally available after PHP 5.1
 *
 * @package     php-simple-sessions
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     MIT License <https://github.com/likel/php-simple-sessions/blob/master/LICENSE>
 * @link        https://github.com/likel/php-simple-sessions
 * @version     1.0.1
 */
namespace Likel;

class DB
{
    private $database_handler; // Stores the database connection
    private $statement; // The MySQL query with prepared values
    private $table_prefix; // The table prefix from the credentials.ini file

    /**
     * Construct the database object
     *
     * @param string $credentials_location The location of the credential file
     * @return void
     */
    public function __construct($credentials_location)
    {
        try {
            $this->database_handler = $this->loadDatabase($credentials_location);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * Attempt to retrieve the likel_db ini array and connect to the database
     *
     * @param array $credentials likel_db from the credentials.ini file
     * @return mixed
     * @throws \Exception If credentials empty or not found
     * @throws \PDOException If PDO connection is unsuccessful
     */
    private function loadDatabase($credentials_location)
    {
        if(file_exists($credentials_location)) {
            $db_credentials = parse_ini_file($credentials_location, true);
            $credentials = $db_credentials["likel_db"];

            if(!empty($credentials)){
                try {
                    $dsn = 'mysql:host=' . $credentials['host'] . ';dbname=' . $credentials['db_name'];

                    $options = array(
                        \PDO::ATTR_PERSISTENT    => true,
                        \PDO::ATTR_ERRMODE       => \PDO::ERRMODE_EXCEPTION
                    );

                    $pdo_object = new \PDO($dsn, $credentials['username'], $credentials['password'], $options);

                    $this->table_prefix = $db_credentials["likel_db"]["table_prefix"];

                    return $pdo_object;
                } catch(\PDOException $e) {
                    throw new \Exception($e->getMessage());
                }
            } else {
                throw new \Exception('The likel_db parameter in the credentials file cannot be found.');
            }
        } else {
            throw new \Exception('The credential file could not be located.');
        }
    }

    /**
     * Prepare the query from a supplied query string
     *
     * @param string $query The prepared query
     * @return void
     */
    public function query($query)
    {
        $this->statement = $this->database_handler->prepare($query);
    }

    /**
     * Bind properties to the statement
     * E.G. $DB->bind(':fname', 'Liam');
     *
     * @param string $param The parameter to replace
     * @param mixed $value The value replacement
     * @param mixed $type Force the PDO::PARAM type
     * @return void
     */
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
            }
        }

        $this->statement->bindValue($param, $value, $type);
    }

    /**
     * Execute the statement
     * Use result()/results() for insert queries
     *
     * @return bool
     */
    public function execute()
    {
        return $this->statement->execute();
    }

    /**
     * Return multiple rows
     *
     * @return array
     */
    public function results()
    {
        $this->execute();
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Return a single row
     *
     * @return array
     */
    public function result()
    {
        $this->execute();
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return the row count
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    /**
     * Return if rows exists
     *
     * @return bool
     */
    public function rowsExist()
    {
        return $this->rowCount() != 0;
    }

    /**
     * Return the id of the last inserted row
     *
     * @return mixed
     */
    public function lastInsertId()
    {
        return $this->database_handler->lastInsertId();
    }

    /**
     * Begin a transaction for multiple statements
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->database_handler->beginTransaction();
    }

    /**
     * Commit the transaction for multiple statements
     *
     * @return bool
     */
    public function endTransaction()
    {
        return $this->database_handler->commit();
    }

    /**
     * Roll back the transaction
     *
     * @return bool
     */
    public function cancelTransaction()
    {
        return $this->database_handler->rollBack();
    }

    /**
     * Return the table name with prefix
     *
     * @param string $table_name The table name that's accessed
     * @return string
     */
    public function getTableName($table_name)
    {
        return $this->table_prefix . $table_name;
    }

    /**
     * Dump the statement's current parameters
     *
     * @return void
     */
    public function dumpStatement()
    {
        $this->statement->debugDumpParams();
    }
    
    /**
     * Return if the database has been initialised
     *
     * @return bool
     */
    public function databaseInitialised()
    {
        return !empty($this->database_handler);
    }
}
