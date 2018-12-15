<?php

namespace TelegramBot;

class ReadLastMessageOffsetQuery extends SqlQuery
{

    /**
     * @param \PDOStatement $statement
     * @return \PDOStatement
     */
    protected function prepare(\PDOStatement $statement)
    {
        return $statement;
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        $sql = "SELECT offset FROM last_message_offset ORDER BY offset DESC LIMIT 1";
        return $sql;
    }

}
