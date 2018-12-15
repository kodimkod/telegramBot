<?php

namespace TelegramBot;

class ReadUsersExcludedFromBansQuery extends SqlQuery
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
        $sql = "SELECT * FROM excluded_users WHERE ignore_exclude = 0;";
        return $sql;
    }

}
