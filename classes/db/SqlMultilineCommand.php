<?php

namespace TelegramBot;

abstract class SqlMultilineCommand extends SqlCommand implements DatabaseCommand
{

    /**
     * @param \PDO $connection
     * @return bool
     */
    public function execute(\PDO $connection)
    {
        $statement = $this->prepare($connection->prepare($this->asString()));
        $result = $statement->execute();
        while ($statement->nextRowset()) {/* https://bugs.php.net/bug.php?id=61613 */
        };
        return $result;
    }

    /**
     * @param \PDO $connection
     * @return int
     */
    public function insert(\PDO $connection)
    {
        $statement = $this->prepare($connection->prepare($this->asString()));
        $statement->execute();
        $insertId = (int) $connection->lastInsertId();
        while ($statement->nextRowset()) {/* https://bugs.php.net/bug.php?id=61613 */
        };
        return $insertId;
    }

    /**
     * @param \PDO $connection
     * @return int
     */
    public function insertWithRowCount(\PDO $connection)
    {
        $statement = $this->prepare($connection->prepare($this->asString()));
        $statement->execute();
        while ($statement->nextRowset()) {/* https://bugs.php.net/bug.php?id=61613 */
        };
        return $statement->rowCount();
    }

}
