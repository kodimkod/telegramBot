<?php

namespace TelegramBot;

abstract class SqlQuery implements DatabaseQuery
{

    /**
     * @param \PDO $connection
     * @return \ExporterCronjob\DatabaseResult
     */
    public function fetch(\PDO $connection)
    {
        $statement = $this->prepare($connection->prepare($this->asString()));
        $statement->execute();

        return DatabaseResult::fromStatement($statement);
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
