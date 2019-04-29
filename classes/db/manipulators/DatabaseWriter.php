<?php

namespace TelegramBot;

class DatabaseWriter extends DatabaseManipulator
{

    /**
     * @param int $updateId
     * @return bool
     * @throws \ErrorException
     */
    public function writeLastMessageOffset($updateId)
    {
        $command = $this->factory->getWriteLastMessageOffsetCommand($updateId);
        try {
            $status = $command->execute($this->connection);
        } catch (\Exception $exception) {
            throw new \ErrorException('Error writing last message offset: ' . $exception->getMessage());
        }
        return $status;
    }

    /**
     * @param string $messageId
     * @param string $type
     * @param string $groupId
     * @param string $groupName
     * @param string $content
     * @return bool
     * @throws \ErrorException
     */
    public function writeNormalMessageLog($messageId, $type, $groupId, $groupName, string $content)
    {
        $command = $this->factory->getWriteNormalMessageLogCommand($messageId, $type, $groupId, $groupName, $content);
        try {
            $status = $command->execute($this->connection);
        } catch (\Exception $exception) {
            throw new \ErrorException('Error writing normal message log: ' . $exception->getMessage());
        }
        return $status;
    }

    /**
     * @param string $userId
     * @param string $groupId
     * @param string $firstName
     * @param string $lastName
     * @param string $fullName
     * @param string $userName
     * @param bool $banned
     * @return bool
     * @throws \ErrorException
     */
    public function writeLoggedUser($userId, $groupId,
            $firstName, $lastName,
            $fullName, $userName,
            $banned)
    {
        $command = $this->factory->getWriteLoggedUserCommand($userId, $groupId,
                $firstName, $lastName,
                $fullName, $userName,
                $banned);
        try {
            $status = $command->execute($this->connection);
        } catch (\Exception $exception) {
            throw new \ErrorException('Error writing logged user: ' . $exception->getMessage());
        }
        return $status;
    }

    /**
     * @param string $messageId
     * @param string $chatId
     * @param string $deletionTime
     * @return bool
     * @throws \ErrorException
     */
    public function writeOwnMessageLog($messageId, $chatId, $deletionTime)
    {
        $command = $this->factory->getWriteOwnMessageLogCommand($messageId, $chatId, $deletionTime);
        try {
            $status = $command->execute($this->connection);
        } catch (\Exception $exception) {
            throw new \ErrorException('Error writing own message log: ' . $exception->getMessage());
        }
        return $status;
    }

    /**
     * @param string $messageId
     * @param string $channelId
     * @param int | null $userId
     * @param string $mode
     * @return bool
     * @throws \ErrorException
     */
    public function writeNewCallbackLike($messageId, $channelId, $userId, $mode)
    {
        $command = $this->factory->getWriteCallbackLikeCommand($messageId, $channelId, $userId, $mode);
        try {
            $status = $command->execute($this->connection);
        } catch (\Exception $exception) {
            throw new \ErrorException('Error writing callback like: ' . $exception->getMessage());
        }
        return $status;
    }
    
        /**
     * @param string $messageId
     * @param string $chatId
     * @return bool
     * @throws \ErrorException
     */
    public function deleteOwnMessage($messageId, $chatId)
    {
        $command = $this->factory->getDeleteOwnMessageCommand($messageId, $chatId);
        try {
            $status = $command->execute($this->connection);
        } catch (\Exception $exception) {
            throw new \ErrorException('Error deleting own message: ' . $exception->getMessage());
        }
        return $status;
    }

}
