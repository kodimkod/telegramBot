<?php

namespace TelegramBot;

class DatabaseWriter extends DatabaseManipulator
{

    /**
     * @param int $updateId
     * @return int
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
     * @return int
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

}