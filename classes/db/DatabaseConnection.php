<?php

namespace TelegramBot;

class DatabaseConnection extends \PDO
{

    /**
     * @param string $host
     * @param string $databaseName
     * @param string $username
     * @param string $password
     * @param string $countAffectedRows
     * @param bool $databaseType
     * @return TelegramBot\DatabaseConnection
     * @throws \RuntimeException
     */
    public static function fromParameters($host, $databaseName, $username,
            $password, $databasePort = 3306, $databaseType = 'mysql',
            $countAffectedRows = false)
    {
        $dsn = $databaseType;
        if ($databaseType === 'odbc') {
            $dsn .= ':' . $host;
        } else {
            $dsn .= ':host=' . $host . ';port=' . $databasePort . ';dbname=' . $databaseName . ';charset=utf8mb4';
        }

        try {
            $connection = new self($dsn, $username, $password,
                    [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                \PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                \PDO::MYSQL_ATTR_FOUND_ROWS => $countAffectedRows
                    ]
            );
            $connection->setAttribute(\PDO::ATTR_ERRMODE,
                    \PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            $connection->setAttribute(\PDO::ATTR_PERSISTENT, false);

            if ($connection->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'mysql') {
                $connection->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,
                        true);
            }

            return $connection;
        } catch (\PDOException $exception) {
            throw new \RuntimeException("Cannot create connection to database: " . $exception->getMessage());
        }
    }

}

