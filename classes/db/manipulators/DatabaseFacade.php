<?php

namespace TelegramBot;

class DatabaseFacade
{

    /**
     * @var DatabaseReader 
     */
    protected $reader;

    /**
     * @var DatabaseReader 
     */
    protected $writer;

    public function __construct(\PDO $connection, Factory $factory)
    {
        $this->reader = $factory->getDatabaseReader($connection);
        $this->writer = $factory->getDatabaseWriter($connection);
    }

    /**
     * @param int $updateId
     * @return int
     */
    public function writeLastMessageOffset($updateId)
    {
        return $this->writer->writeLastMessageOffset($updateId);
    }

    /**
     * @return int
     */
    public function readLastMessageOffset()
    {
        return $this->reader->readLastMessageOffset();
    }

    /**
     * @param string $messageId
     * @param string $type
     * @param string $groupId
     * @param string $groupName
     * @param string $content
     * @return int
     */
    public function writeNormalMessageLog($messageId, $type, $groupId, $groupName, string $content)
    {
        return $this->writer->writeNormalMessageLog($messageId, $type, $groupId, $groupName, $content);
    }

    /**
     * @return array
     */
    public function getUsersExcludedFromBans()
    {
        return $this->reader->getUsersExcludedFromBans();
    }

    /**
     * @return array
     */
    public function getContentExcludedFromBans()
    {
        return $this->reader->getContentExcludedFromBans();
    }

    /**
     * @return array
     */
    public function getSpam()
    {
        return $this->reader->getSpam();
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
    public function writeLoggedUser($userId, $groupId,
            $firstName, $lastName,
            $fullName, $userName,
            $banned)
    {
        return $this->writer->writeLoggedUser($userId, $groupId,
                        $firstName, $lastName,
                        $fullName, $userName,
                        $banned);
    }

    /**
     * @param string $userId
     * @param string $groupId
     * @return array
     */
    public function getSpamDataOnUser($userId, $groupId)
    {
        return $this->reader->getSpamDataOnUser($userId, $groupId);
    }

}
