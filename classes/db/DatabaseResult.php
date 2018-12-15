<?php

namespace TelegramBot;

class DatabaseResult implements \IteratorAggregate
{
    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * @var array
     */
    private $results = [];

    /**
     * @var bool
     */
    private $fetched = false;

    /**
     * @param \PDOStatement $statement
     * @return DatabaseResult
     */
    public static function fromStatement(\PDOStatement $statement)
    {
        return new self($statement);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->fetchResults();
        return new \ArrayIterator($this->results);
    }

    private function fetchResults()
    {
        if ($this->fetched) {
            return;
        }

        $results = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
        if (!is_array($results)) {
            $this->fetched = true;
            return;
        }

        foreach ($results as $result) {
            $this->results[] = $result;
        }
        $this->fetched = true;
    }

    /**
     * @param \PDOStatement $statement
     */
    private function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
    }
}
