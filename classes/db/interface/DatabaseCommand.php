<?php

namespace TelegramBot;

interface DatabaseCommand extends DatabaseRequest
{

    /**
     * @param \PDO $connection
     * @return bool
     */
    public function execute(\PDO $connection);
}
