<?php

namespace TelegramBot;

class WriteCallbackLikeCommand extends SqlCommand
{

    /**
     * @var string
     */
    private $messageId;

    /**
     * @var string
     */
    private $channelId;

    /**
     * @var int | null
     */
    private $userId;

    /**
     * @var string
     */
    private $mode;

    /**
     * @param string $messageId
     * @param string $channelId
     * @param int | null $userId
     * @param string $mode
     * @return WriteCallbackLikeCommand
     */
    public static function fromParameters($messageId, $channelId, $userId, $mode)
    {
        if (self::parametersAreValid($messageId, $channelId, $userId, $mode)) {
            return new self($messageId, $channelId, $userId, $mode);
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
        $statement->bindValue(':user_id',
                $this->userId, \PDO::PARAM_STR);
        $like = 0;
        $dislike = 0;
        if ($this->mode == 'like') {
            $like = 1;
        } else {
            $dislike = 1;
        }
        $statement->bindValue(':like',
                $like, \PDO::PARAM_INT);
        $statement->bindValue(':dislike',
                $dislike, \PDO::PARAM_INT);
        return $statement;
    }

    /**
     * @param string $messageId
     * @param string $channelId
     * @param int | null $userId
     * @param string $mode
     */
    private function __construct($messageId, $channelId, $userId, $mode)
    {
        $this->messageId = $messageId;
        $this->channelId = $channelId;
        $this->userId = $userId;
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $query = "
            INSERT INTO callback_likes (message_id, channel_id, user_id, `like`, dislike) 
            VALUES
            (:message_id, :channel_id, :user_id, :like, :dislike )
                 ON DUPLICATE KEY
            UPDATE
            `like` = VALUES(`like`),
            dislike = VALUES(dislike)
;
            ";
        return $query;
    }

    /**
     * @param string $messageId
     * @param string $channelId
     * @param int | null $userId
     * @param string $mode
     * @return boolean
     */
    private static function parametersAreValid($messageId, $channelId, $userId, $mode)
    {
        if (!isset($messageId) || empty($messageId)) {
            return false;
        }
        if (!isset($channelId) || empty($channelId)) {
            return false;
        }
        if (!isset($userId) || empty($userId)) {
            return false;
        }
        if (!isset($mode) || preg_match('/^like|dislike$/', $mode) !== 1) {
            return false;
        }
        return true;
    }

}
