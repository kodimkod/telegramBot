<?php

namespace TelegramBot;

class WriteLoggedUserCommand extends SqlCommand
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
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $fullName;

    /**
     * @var string
     */
    protected $userName;

    /**
     * @var bool
     */
    protected $banned;

    /**
     * @param string $userId
     * @param string $groupId
     * @param string $firstName
     * @param string $lastName
     * @param string $fullName
     * @param string $userName
     * @param bool $banned
     * @return WriteLoggedUserCommand
     */
    public static function fromParameters($userId, $groupId,
            $firstName, $lastName,
            $fullName, $userName,
            $banned)
    {
        if (self::parametersAreValid($userId, $groupId,
                        $firstName, $lastName,
                        $fullName, $userName,
                        $banned)) {
            return new self($userId, $groupId,
                    $firstName, $lastName,
                    $fullName, $userName,
                    $banned);
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
        $statement->bindValue(':first_name',
                $this->firstName, \PDO::PARAM_STR);
        $statement->bindValue(':last_name',
                $this->lastName, \PDO::PARAM_STR);
        $statement->bindValue(':full_name',
                $this->fullName, \PDO::PARAM_STR);
        $statement->bindValue(':username',
                $this->userName, \PDO::PARAM_STR);
        // false, because we don't want to log join message
        $statement->bindValue(':allowed_messages',
                false, \PDO::PARAM_BOOL);
        $statement->bindValue(':spam_messages',
                false, \PDO::PARAM_BOOL);
        return $statement;
    }

    /**
     * @param string $userId
     * @param string $groupId
     * @param string $firstName
     * @param string $lastName
     * @param string $fullName
     * @param string $userName
     * @param bool $banned
     */
    private function __construct($userId, $groupId,
            $firstName, $lastName,
            $fullName, $userName,
            $banned)
    {
        $this->userId = $userId;
        $this->groupId = $groupId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->fullName = $fullName;
        $this->userName = $userName;
        $this->banned = $banned;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $query = "
            INSERT INTO logged_users (id, chat_id, first_name,
	last_name,	name, username, allowed_messages,
	spam_messages)
            VALUES
            (:user_id, :chat_id, :first_name, :last_name, :full_name, :username, :allowed_messages,
	:spam_messages)
            ON DUPLICATE KEY
            UPDATE
            first_name = VALUES(first_name),
            last_name = VALUES(last_name),	
            name = VALUES(name),
            username = VALUES(username),
            ";
        if ($this->banned == true) {
                        $query .= " 
                       spam_messages = spam_messages + 1";

        } else {
            $query .= " 
                        allowed_messages = allowed_messages + 1";
        }
        return $query;
    }

    /**
     * @param string $userId
     * @param string $groupId
     * @param string $firstName
     * @param string $lastName
     * @param string $fullName
     * @param string $userName
     * @param bool $banned
     * @return boolean
     */
    private static function parametersAreValid($userId, $groupId,
            $firstName, $lastName,
            $fullName, $userName,
            $banned)
    {
        if (!isset($userId) || empty($userId)) {
            return false;
        }
        if (!isset($groupId) || empty($groupId)) {
            return false;
        }
        if (!is_bool($banned)) {
            return false;
        }
        return true;
    }

}
