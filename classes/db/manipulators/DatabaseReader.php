<?php

namespace TelegramBot;

class DatabaseReader extends DatabaseManipulator
{

    /**
     * @return int
     */
    public function readLastMessageOffset()
    {
        $query = $this->factory->getReadLastMessageOffsetQuery();
        try {
            $results = $query->fetch($this->connection)->getIterator();
        } catch (\Exception $exception) {
            throw new \ErrorException('Error reading last message offset: ' . $exception->getMessage());
        }
        if (count($results) == 0) {
            return 0;
        }
        $result = reset($results);
        if (!isset($result['offset'])) {
            return 0;
        }
        return $result['offset'];
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getUsersExcludedFromBans(): array
    {
        $query = $this->factory->getReadUsersExcludedFromBansQuery();
        try {
            $results = $query->fetch($this->connection)->getIterator();
        } catch (\Exception $exception) {
            throw new \ErrorException('Error reading users excluded from bans: ' . $exception->getMessage());
        }
        if (count($results) == 0) {
            return [];
        }
        return iterator_to_array($results);
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getContentExcludedFromBans(): array
    {
        $query = $this->factory->getReadContentExcludedFromBansQuery();
        try {
            $results = $query->fetch($this->connection)->getIterator();
        } catch (\Exception $exception) {
            throw new \ErrorException('Error reading content excluded from bans: ' . $exception->getMessage());
        }
        if (count($results) == 0) {
            return [];
        }
        return iterator_to_array($results);
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getSpam(): array
    {
        $query = $this->factory->getReadSpamQuery();
        try {
            $results = $query->fetch($this->connection)->getIterator();
        } catch (\Exception $exception) {
            throw new \ErrorException('Error reading spam: ' . $exception->getMessage());
        }
        if (count($results) == 0) {
            return [];
        }
        return iterator_to_array($results);
    }

    /**
     * @param string $userId
     * @param string $groupId
     * @return array
     * @throws \ErrorException
     */
    public function getSpamDataOnUser($userId, $groupId): array
    {
        $query = $this->factory->getReadSpamDataOnUserQuery($userId, $groupId);
        try {
            $results = $query->fetch($this->connection)->getIterator();
        } catch (\Exception $exception) {
            throw new \ErrorException('Error reading spam data on user: ' . $exception->getMessage());
        }
        if (count($results) == 0) {
            return [
                'allowed_messages' => 0,
                'spam_messages' => 0
            ];
        }
        return reset($results);
    }

}
