<?php

namespace TelegramBot;

class WriteLastMessageOffsetCommand extends SqlCommand
{

    /**
     * @var int
     */
    private $updateId;

    /**
     * @param int $updateId
     * @return WriteLastMessageOffsetCommand
     */
    public static function fromParameters($updateId)
    {
        if (self::idIsValid($updateId)) {
            return new self($updateId);
        }
        throw new \UnexpectedValueException('Bad parameters supplied to ' . __CLASS__);
    }

    /**
     * @param \PDOStatement $statement
     * @return \PDOStatement
     */
    protected function prepare(\PDOStatement $statement)
    {
         $statement->bindValue(':update_id',
                $this->updateId, \PDO::PARAM_INT);
        return $statement;
    }

    /**
     * @param  int $updateId
     */
    private function __construct($updateId)
    {
        $this->updateId = $updateId;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $query = "
            INSERT IGNORE INTO last_message_offset (offset) VALUES (:update_id);
            ";
        return $query;
    }

    /**
     * @param int $updateId
     * @return boolean
     */
    private static function idIsValid($updateId)
    {
        if (!isset($updateId) || empty($updateId)) {
            return false;
        }
        return true;
    }

}
