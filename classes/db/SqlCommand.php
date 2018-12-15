<?php

namespace TelegramBot;

abstract class SqlCommand implements DatabaseCommand
{

    /**
     * @param \PDO $connection
     * @return bool
     */
    public function execute(\PDO $connection)
    {
        $statement = $this->prepare($connection->prepare($this->asString()));

        return $statement->execute();
    }

    /**
     * @param \PDO $connection
     * @return int
     */
    public function insert(\PDO $connection)
    {
        $statement = $this->prepare($connection->prepare($this->asString()));
        $statement->execute();

        return (int) $connection->lastInsertId();
    }

    /**
     * @param \PDO $connection
     * @return int
     */
    public function insertWithRowCount(\PDO $connection)
    {
        $statement = $this->prepare($connection->prepare($this->asString()));
        $statement->execute();

        return $statement->rowCount();
    }

    /**
     * @return string
     */
    public function asString()
    {
        return $this->getQuery();
    }

    /**
     * @param \PDOStatement $statement
     *
     * @return \PDOStatement
     */
    abstract protected function prepare(\PDOStatement $statement);
}
