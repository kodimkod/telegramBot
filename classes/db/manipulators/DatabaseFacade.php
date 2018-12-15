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
    public function writeNormalMessageLog($messageId, $type, $groupId, $groupName,  string $content) {
        return $this->writer->writeNormalMessageLog($messageId, $type, $groupId, $groupName, $content);
    }

    /**
     * @return array
     */
   public function getUsersExcludedFromBans() 
   {
       return $this->reader->getUsersExcludedFromBans();
   }
}
