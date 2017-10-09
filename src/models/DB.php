<?php
/**
 * This is the DB object
 *
 * @package     php-simple-sessions
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     MIT License <https://github.com/likel/fizz-buzz/blob/master/LICENSE>
 * @link        https://github.com/likel/php-simple-sessions
 * @version     1.0.0
 */
namespace Likel\Session;

class DB
{
    private $dbh;
    private $stmt;

    public function __construct($credentials_location = __DIR__ . '/../ini/credentials.ini')
    {
        try {
            $db_credentials = parse_ini_file($credentials_location, true);
            $this->dbh = loadDatabase($db_credentials["likel_db"]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function loadDatabase($credentials) {
        if(!empty($credentials["likel_db"])){
            try {
                $dsn = 'mysql:host=' . $credentials["likel_db"]['host'] . ';dbname=' . $credentials["likel_db"]['db_name'];

                $options = array(
                    PDO::ATTR_PERSISTENT    => true,
                    PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
                );

                $pdo_object = new PDO($dsn, $credentials["likel_db"]['username'], $credentials["likel_db"]['password'], $options);

                return $pdo_object;

            } catch(PDOException $e) {
                throw new Exception('An error occured when attempting to connect to the database.');
            }

        } else {
            throw new Exception('The credential file could not be located or is empty.');
        }
    }

    /**
     * Prepare the query from a supplied query string.
     */
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    /**
     * Bind properties to the statement.
     * E.G. $DB->bind(':fname', 'Liam');
     */
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Execute the statement.
     * Use result()/results() for insert queries.
     */
    public function execute()
    {
        return $this->stmt->execute();
    }

    /**
     * Return multiple rows.
     */
    public function results()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Return a single row.
     */
    public function result()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Return the row count.
     */
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    /**
     * Return if rows exists.
     */
    public function rowsExist()
    {
        return $this->rowCount() != 0;
    }

    /**
     * Return the id of the last inserted row.
     */
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * Begin a transaction for multiple statements.
     */
    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }

    /**
     * Commit the transaction for multiple statements.
     */
    public function endTransaction()
    {
        return $this->dbh->commit();
    }

    /**
     * Roll back the transaction.
     */
    public function cancelTransaction()
    {
        return $this->dbh->rollBack();
    }

    /**
     * Return the table name with prefix
     */
    public function tb($tablename)
    {
        return $this->tableprefix.$tablename;
    }

    /**
     * Dump the statement's current parameters.
     */
    public function dumpStatement()
    {
        return $this->stmt->debugDumpParams();
    }
}
