<?php

namespace TelegramBot;

class ReadOwnMessagesToDeleteAfterTimeQuery extends SqlQuery
{

    /**
     * @var int
     */
    protected $time;


    /**
     * @param int $time
     * @return ReadOwnMessagesToDeleteAfterTimeQuery
     */
    public static function fromParameters(int $time)
    {
        if (self::parametersAreValid($time)) {
            return new self($time);
        }
        throw new \UnexpectedValueException('Bad parameters supplied to ' . __CLASS__);
    }

    /**
     * @param \PDOStatement $statement
     * @return \PDOStatement
     */
    protected function prepare(\PDOStatement $statement)
    {
        $statement->bindValue(':current_time',
                $this->time, \PDO::PARAM_STR);
        return $statement;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $sql = "SELECT message_id, chat_id FROM own_messages
WHERE deletion_time <= FROM_UNIXTIME(:current_time);";
        return $sql;
    }

    /**
     * @param int $time
     */
    private function __construct(int $time)
    {
        $this->time = $time;
    }

    /**
     * @param int $time
     * @return boolean
     */
    private static function parametersAreValid(int $time)
    {
        if (!isset($time) || empty($time)) {
            return false;
        }
        return true;
    }

}
