<?php

namespace TelegramBot;

class Factory
{

    /**
     * @param \PDO $connection
     * @return \TelegramBot\DatabaseReader
     */
    public function getDatabaseReader(\PDO $connection): DatabaseReader
    {
        return new DatabaseReader($connection, $this);
    }

    /**
     * @param \PDO $connection
     * @return \TelegramBot\DatabaseWriter
     */
    public function getDatabaseWriter(\PDO $connection): DatabaseWriter
    {
        return new DatabaseWriter($connection, $this);
    }

    /**
     * @return \TelegramBot\ReadLastMessageOffsetQuery
     */
    public function getReadLastMessageOffsetQuery(): ReadLastMessageOffsetQuery
    {
        return new ReadLastMessageOffsetQuery();
    }

    /**
     * @param int $updateId
     * @return \TelegramBot\WriteLastMessageOffsetCommand
     */
    public function getWriteLastMessageOffsetCommand($updateId): WriteLastMessageOffsetCommand
    {
        return WriteLastMessageOffsetCommand::fromParameters($updateId);
    }

    /**
     * @param string $messageId
     * @param string $type
     * @param string $groupId
     * @param string $groupName
     * @param string $content
     * @return \TelegramBot\WriteNormalMessageLogCommand
     */
    public function getWriteNormalMessageLogCommand($messageId, $type, $groupId, $groupName, string $content): WriteNormalMessageLogCommand
    {
        return WriteNormalMessageLogCommand::fromParameters($messageId, $type, $groupId, $groupName, $content);
    }

    /**
     * @param mixed $content
     * @return \TelegramBot\ContentExtractor
     */
    public function getContentExtractor($content): ContentExtractor
    {
        return new ContentExtractor($content);
    }

    /**
     * @return ReadUsersExcludedFromBansQuery
     */
    public function getReadUsersExcludedFromBansQuery(): ReadUsersExcludedFromBansQuery
    {
        return new ReadUsersExcludedFromBansQuery();
    }

    /**
     * @return ReadContentExcludedFromBansQuery
     */
    public function getReadContentExcludedFromBansQuery(): ReadContentExcludedFromBansQuery
    {
        return new ReadContentExcludedFromBansQuery();
    }

    /**
     * @return ReadSpamQuery
     */
    public function getReadSpamQuery(): ReadSpamQuery
    {
        return new ReadSpamQuery();
    }

}
