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
    public function getWriteLoggedUserCommand($userId, $groupId,
            $firstName, $lastName,
            $fullName, $userName,
            $banned): WriteLoggedUserCommand
    {
        return WriteLoggedUserCommand::fromParameters($userId, $groupId,
                        $firstName, $lastName,
                        $fullName, $userName,
                        $banned);
    }

    /**
     * @param string $userId
     * @param string $groupId
     * @return ReadSpamQuery
     */
    public function getReadSpamDataOnUserQuery($userId, $groupId): ReadSpamDataOnUserQuery
    {
        return ReadSpamDataOnUserQuery::fromParameters($userId, $groupId);
    }

    /**
     * @param string $messageId
     * @param string $chatId
     * @param string $deletionTime
     * @return WriteOwnMessageLogCommand
     */
    public function getWriteOwnMessageLogCommand($messageId, $chatId, $deletionTime): WriteOwnMessageLogCommand
    {
        return WriteOwnMessageLogCommand::fromParameters($messageId, $chatId, $deletionTime);
    }

    /**
     * @param string $messageId
     * @param string $channelId
     * @param int | null $userId
     * @param string $mode
     * @return WriteCallbackLikeCommand
     */
    public function getWriteCallbackLikeCommand($messageId, $channelId, $userId, $mode = 'like'): WriteCallbackLikeCommand
    {
        return WriteCallbackLikeCommand::fromParameters($messageId, $channelId, $userId, $mode);
    }

    /**
     * @param string $messageId
     * @param string $channelId
     * @return ReadCallbackLikesQuery
     */
    public function getCallbackLikesQuery($messageId, $channelId): ReadCallbackLikesQuery
    {
        return ReadCallbackLikesQuery::fromParameters($messageId, $channelId);
    }

}
