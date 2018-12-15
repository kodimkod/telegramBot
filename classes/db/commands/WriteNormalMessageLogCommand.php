<?php

namespace TelegramBot;

class WriteNormalMessageLogCommand extends SqlCommand
{

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $messageId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $groupId;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @param string $messageId
     * @param string $type
     * @param string $groupId
     * @param string $groupName
     * @param string $content
     * @return WriteNormalMessageLogCommand
     */
    public static function fromParameters($messageId, $type, $groupId, $groupName, string $content)
    {
        if (self::contentIsValid($content)) {
            return new self($messageId, $type, $groupId, $groupName, $content);
        }
        throw new \UnexpectedValueException('Bad parameters supplied to ' . __CLASS__);
    }

    /**
     * @param \PDOStatement $statement
     * @return \PDOStatement
     */
    protected function prepare(\PDOStatement $statement)
    {

        $statement->bindValue(':telegram_id',
                $this->messageId, \PDO::PARAM_INT);
        $statement->bindValue(':the_type',
                $this->type, \PDO::PARAM_STR);
        $statement->bindValue(':group_id',
                $this->groupId, \PDO::PARAM_STR);
        $statement->bindValue(':group_name',
                $this->groupName, \PDO::PARAM_STR);
        $statement->bindValue(':the_text',
               $this->content, \PDO::PARAM_STR);
        return $statement;
    }

    /**
     * @param  string $content
     */
    private function __construct($messageId, $type, $groupId, $groupName, $content)
    {
        $this->content = $content;
        $this->messageId = $messageId;
        $this->type = $type;
        $this->groupId = $groupId;
        $this->groupName = $groupName;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $query = "
            INSERT INTO log_received_messages (telegram_id, `type`, group_id, group_name,  `text`) 
            VALUES
            (:telegram_id, :the_type, :group_id, :group_name,  :the_text);
            ";
        return $query;
    }

    /**
     * @param string $content
     * @return boolean
     */
    private static function contentIsValid(string $content)
    {
        if (!isset($content) || empty($content)) {
            return false;
        }
        return true;
    }

}
