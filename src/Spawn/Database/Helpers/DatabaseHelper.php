<?php declare(strict_types=1);

namespace spawnCore\Database\Helpers;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use spawnCore\Custom\Throwables\DatabaseConnectionException;

class DatabaseHelper
{

    private string $host = '';
    private string $username = '';
    private string $password = '';
    private string $database = '';
    private string $port = '';
    private string $dbUrl = '';
    private DatabaseConnection $connection;

    /**
     * DatabaseHelper constructor.
     */
    public function __construct()
    {
        $this->loadDBConfig();
        $this->createConnection();
        $this->checkConnection();
    }


    /**
     * @throws DatabaseConnectionException
     */
    protected function checkConnection() {
        try {
            $connection = $this->getConnection()::getConnection();
            if(!$connection->isConnected()) {
                $connection->connect();
            }
        } catch (DatabaseConnectionException $e) {
            throw new DatabaseConnectionException($this->host, $this->database, $this->port, '', $this->username, $this->password, $e);
        } catch (Exception $e) {
            throw new DatabaseConnectionException($this->host, $this->database, $this->port, '', $this->username, $this->password, $e);
        }
    }


    protected function loadDBConfig()
    {
        $this->host = DB_HOST;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->database = DB_DATABASE;
        $this->port = DB_PORT;
    }


    protected function createConnection()
    {
        $this->connection = new DatabaseConnection();
    }

    /**
     * @return DatabaseConnection
     */
    public function getConnection(): DatabaseConnection
    {
        return $this->connection;
    }

    /**
     * @param string $tablename
     * @return bool
     * @throws Exception
     */
    public function doesTableExist(string $tablename)
    {
        $results = $this->query("SHOW TABLES LIKE '$tablename'");
        return sizeof($results) != 0;
    }

    /**
     * @param $sql
     * @param bool $preventFetchAll
     * @return Result|bool|array
     * @throws Exception
     */
    public function query($sql, bool $preventFetchAll = false)
    {
        try {
            $result = $this->connection::getConnection()->query($sql);
        } catch (\Exception $e) {
            return false;
        }

        if (!$preventFetchAll && $result) {
            return $result->fetchAll();
        }

        return $result;
    }

}