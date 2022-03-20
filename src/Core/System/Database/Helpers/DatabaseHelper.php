<?php declare(strict_types=1);

namespace SpawnCore\System\Database\Helpers;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;

class DatabaseHelper
{

    private string $host = '';
    private string $username = '';
    private string $password = '';
    private string $database = '';
    private string $port = '';
    private DatabaseConnection $connection;

    /**
     * @throws DatabaseConnectionException
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
    protected function checkConnection(): void
    {
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


    protected function loadDBConfig(): void
    {
        $this->host = DB_HOST;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->database = DB_DATABASE;
        $this->port = DB_PORT;
    }


    protected function createConnection(): void
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
    public function doesTableExist(string $tablename): bool
    {
        $results = $this->query("SHOW TABLES LIKE '$tablename'");
        return count($results) !== 0;
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
            $result = $this->connection::getConnection()->executeQuery($sql);
        } catch (\Exception $e) {
            return false;
        }

        if (!$preventFetchAll && $result) {
            return $result->fetchAllAssociative();
        }

        return $result;
    }

}