<?php

namespace TelegramBot;

class DeleteOwnMessageCommand extends SqlCommand
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
     * @param string $messageId
     * @param string $chatId
     * @return DeleteOwnMessageCommand
     */
    public static function fromParameters($messageId, $chatId)
    {
        if (self::parametersAreValid($messageId, $chatId)) {
            return new self($messageId, $chatId);
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
        return $statement;
    }

    /**
     * @param string $messageId
     * @param string $chatId
     */
    private function __construct($messageId, $chatId)
    {
        $this->messageId = $messageId;
        $this->chatId = $chatId;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $query = "
            DELETE FROM  own_messages 
            WHERE message_id = :message_id AND chat_id = :chat_id
            ;
            ";
        return $query;
    }

    /**
     * @param string $messageId
     * @param string $chatId
     * @return boolean
     */
    private static function parametersAreValid($messageId, $chatId)
    {
        if (!isset($messageId) || empty($messageId)) {
            return false;
        }
        if (!isset($chatId) || empty($chatId)) {
            return false;
        }
        return true;
    }

}
