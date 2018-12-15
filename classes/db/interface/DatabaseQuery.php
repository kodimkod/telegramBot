<?php

namespace TelegramBot;

interface DatabaseQuery extends DatabaseRequest
{
    /**
     * @param \PDO $connection
     * @return DatabaseResult
     */
    public function fetch(\PDO $connection);

}
