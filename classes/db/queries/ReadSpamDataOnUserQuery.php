<?php

namespace TelegramBot;

class ReadSpamDataOnUserQuery extends SqlQuery
{

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $groupId;

    /**
     * @param string $userId
     * @param string $groupId
     * @return ReadSpamDataOnUserQuery
     */
    public static function fromParameters($userId, $groupId)
    {
        if (self::parametersAreValid($userId, $groupId)) {
            return new self($userId, $groupId);
        }
        throw new \UnexpectedValueException('Bad parameters supplied to ' . __CLASS__);
    }

    /**
     * @param \PDOStatement $statement
     * @return \PDOStatement
     */
    protected function prepare(\PDOStatement $statement)
    {
        $statement->bindValue(':user_id',
                $this->userId, \PDO::PARAM_INT);
        $statement->bindValue(':chat_id',
                $this->groupId, \PDO::PARAM_STR);
        return $statement;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $sql = "SELECT allowed_messages, spam_messages FROM logged_users WHERE
            id = :user_id AND chat_id = :chat_id AND  ratio_ignored = 0 LIMIT 1;";
        return $sql;
    }

    /**
     * @param string $userId
     * @param string $groupId
     */
    private function __construct($userId, $groupId)
    {
        $this->userId = $userId;
        $this->groupId = $groupId;
    }

    /**
     * @param string $userId
     * @param string $groupId
     * @return boolean
     */
    private static function parametersAreValid($userId, $groupId)
    {
        if (!isset($userId) || empty($userId)) {
            return false;
        }
        if (!isset($groupId) || empty($groupId)) {
            return false;
        }
        return true;
    }

}
