<?php

namespace TelegramBot;

class WriteOwnMessageLogCommand extends SqlCommand
{

    /**
     * @var string
     */
    private $messageId;

    /**
     * @var string
     */
    private $chatId;

    /**
     * @var string
     */
    private $deletionTime;

    /**
     * @param string $messageId
     * @param string $chatId
     * @param string $deletionTime
     * @return WriteOwnMessageLogCommand
     */
    public static function fromParameters($messageId, $chatId, $deletionTime)
    {
        if (self::parametersAreValid($messageId, $chatId, $deletionTime)) {
            return new self($messageId, $chatId, $deletionTime);
        }
        throw new \UnexpectedValueException('Bad parameters supplied to ' . __CLASS__);
    }

    /**
     * @param \PDOStatement $statement
     * @return \PDOStatement
     */
    protected function prepare(\PDOStatement $statement)
    {
        $statement->bindValue(':message_id',
                $this->messageId, \PDO::PARAM_STR);
        $statement->bindValue(':chat_id',
                $this->chatId, \PDO::PARAM_STR);
        $statement->bindValue(':deletion_time',
                $this->deletion_time, \PDO::PARAM_STR);
        return $statement;
    }

    /**
     * @param string $messageId
     * @param string $chatId
     * @param string $deletionTime
     */
    private function __construct($messageId, $chatId, $deletionTime)
    {
        $this->messageId = $messageId;
        $this->chatId = $chatId;
        $this->deletionTime = $deletionTime;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $query = "
            INSERT IGNORE INTO own_messages (message_id, chat_id, deletion_time) VALUES (:message_id, :chat_id, :deletion_time);
            ";
        return $query;
    }

    /**
     * @param string $messageId
     * @param string $chatId
     * @param string $deletionTime
     * @return boolean
     */
    private static function parametersAreValid($messageId, $chatId, $deletionTime)
    {
        if (!isset($messageId) || empty($messageId)) {
            return false;
        }
        if (!isset($chatId) || empty($chatId)) {
            return false;
        }
        if (!isset($deletionTime) || empty($deletionTime)) {
            return false;
        }
        return true;
    }

}
