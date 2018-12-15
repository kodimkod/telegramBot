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

}
