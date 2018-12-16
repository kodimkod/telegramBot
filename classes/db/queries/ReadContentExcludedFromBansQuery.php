<?php

namespace TelegramBot;

class ReadContentExcludedFromBansQuery extends SqlQuery
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
        $sql = "SELECT content, our_group, friend_groups FROM spam_excepted_content WHERE our_group = 1;";
        return $sql;
    }

}
