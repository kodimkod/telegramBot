<?php

namespace TelegramBot;

abstract class DatabaseManipulator
{

    /**
     * @var \PDO
     */
    protected $connection;
    
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param \PDO $connection
     */
    public function __construct(\PDO $connection, Factory $factory)
    {
        $this->connection = $connection;
        $this->factory = $factory;
    }

}
