<?php

namespace TelegramBot;

class ReadCallbackLikesQuery extends SqlQuery
{

    /**
     * @var int
     */
    protected $messageId;

    /**
     * @var string
     */
    protected $channelId;

    /**
     * @param string $messageId
     * @param string $channelId
     * @return ReadCallbackLikesQuery
     */
    public static function fromParameters($messageId, $channelId)
    {
        if (self::parametersAreValid($messageId, $channelId)) {
            return new self($messageId, $channelId);
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
        $statement->bindValue(':channel_id',
                $this->channelId, \PDO::PARAM_STR);
        return $statement;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $sql = "SELECT SUM(`like`) as likes, SUM(dislike) as dislikes FROM callback_likes
WHERE message_id = :message_id and channel_id = :channel_id
GROUP BY message_id, channel_id
LIMIT 1;";
        return $sql;
    }

    /**
     * @param string $messageId
     * @param string $channelId
     */
    private function __construct($messageId, $channelId)
    {
        $this->messageId = $messageId;
        $this->channelId = $channelId;
    }

    /**
     * @param string $messageId
     * @param string $channelId
     * @return boolean
     */
    private static function parametersAreValid($messageId, $channelId)
    {
        if (!isset($messageId) || empty($messageId)) {
            return false;
        }
        if (!isset($channelId) || empty($channelId)) {
            return false;
        }
        return true;
    }

}
