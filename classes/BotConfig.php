<?php

namespace TelegramBot;

use TelegramBot\BotConfig;

class BotConfig
{

    /**
     * @var string
     */
    protected $appRoot;

    /**
     * @var int
     */
    protected $token;

    /**
     * @var string
     */
    protected $dbHost;

    /**
     * @var string
     */
    protected $dbName;

    /**
     * @var string
     */
    protected $dbUser;

    /**
     * @var string
     */
    protected $dbPassword;

    /**
     * @var int
     */
    protected $botId;

    /**
     * @var string
     */
    protected $welcomeUserName;

    /**
     * @var int
     */
    protected $welcomeUserId;

    /**
     * @var int
     */
    protected $welcomeUserGroupId;

    /**
     * @var array
     */
    protected $ownGroupIds;

    /**
     * @var array
     */
    protected $friendGroupIds;
    
    
    /**
     * @var array
     */
    protected $friendWelcomeDiffersGroupIds;

    public function __construct($appRoot)
    {
        $this->appRoot = $appRoot;
        $ini_array = parse_ini_file($appRoot . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . "settings.ini");
        $this->validateSettings($ini_array);
        $this->token = $ini_array['token'];
        $this->dbHost = $ini_array['dbHost'];
        $this->dbName = $ini_array['dbName'];
        $this->dbUser = $ini_array['dbUser'];
        $this->dbPassword = $ini_array['dbPassword'];
        $this->botId = $ini_array['botId'];
        $this->welcomeUserName = $ini_array['welcomeUserName'];
        $this->welcomeUserId = $ini_array['welcomeUserId'];
        $this->welcomeUserGroupId = $ini_array['welcomeUserGroupId'];
        $this->ownGroupIds = $ini_array['ownGroupIds'];
        $this->friendGroupIds = $ini_array['friendGroupIds'];
        $this->friendWelcomeDiffersGroupIds = $ini_array['friendWelcomeDiffersGroupIds'];
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getAppRootDirectory(): string
    {
        return $this->appRoot;
    }

    /**
     * @return string
     */
    public function getLogDirectory(): string
    {
        return $this->appRoot . DIRECTORY_SEPARATOR . 'logs';
    }

    /**
     * @return string
     */
    public function getDatabaseHost(): string
    {
        return $this->dbHost;
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->dbName;
    }

    /**
     * @return string
     */
    public function getDatabaseUser(): string
    {
        return $this->dbUser;
    }

    /**
     * @return string
     */
    public function getDatabasePassword(): string
    {
        return $this->dbPassword;
    }

    /**
     * @return array
     */
    public function getOwnGroups(): array
    {
        return $this->ownGroupIds;
    }

    /**
     * @return int
     */
    public function getBotId(): int
    {
        return $this->botId;
    }

    /**
     * @return string
     */
    public function getWelcomeUserName(): string
    {
        return $this->welcomeUserName;
    }

    /**
     * @return int
     */
    public function getWelcomeUserId(): string
    {
        return $this->welcomeUserId;
    }

    /**
     * @return string
     */
    public function getWelcomeUserGroupId(): string
    {
        return $this->welcomeUserGroupId;
    }

    /**
     * @return array
     */
    public function getFriendGroupsWhereBotWorks(): array
    {
        return $this->friendGroupIds;
    }
    
    /**
     * @return array
     */
    public function getFriendGroupsWhereWelcomeMessageDiffers(): array
    {
        return $this->friendWelcomeDiffersGroupIds;
    }

    /**
     * @param array $settings
     * @throws UnexpectedValueException
     */
    protected function validateSettings(array $settings)
    {
        if (!isset($settings['token']) || strlen($settings['token']) < 7) {
            throw new UnexpectedValueException('Wrong bot token');
        }
        if (!isset($settings['dbHost']) || strlen($settings['dbHost']) < 2) {
            throw new UnexpectedValueException('Wrong db host, probably less than 2 characters.');
        }
        if (!isset($settings['dbName']) || strlen($settings['dbName']) < 2) {
            throw new UnexpectedValueException('Wrong db name, probably less than 2 characters.');
        }
        if (!isset($settings['dbUser']) || strlen($settings['dbUser']) < 2) {
            throw new UnexpectedValueException('Wrong db user, probably less than 2 characters.');
        }
        if (!isset($settings['dbPassword']) || strlen($settings['dbPassword']) < 2) {
            throw new UnexpectedValueException('Wrong db password, probably less than 2 characters.');
        }
        if (!isset($settings['botId']) || strlen($settings['botId']) < 5) {
            throw new UnexpectedValueException('Wrong bot id, probably less than 5 characters.');
        }
        if (!isset($settings['welcomeUserName']) || strlen($settings['welcomeUserName']) < 2) {
            throw new UnexpectedValueException('Wrong welcome user name, probably less than 2 characters.');
        }
        if (!isset($settings['welcomeUserId']) || strlen($settings['welcomeUserId']) < 5) {
            throw new UnexpectedValueException('Wrong welcome user id, probably less than 5 characters.');
        }
        if (!isset($settings['welcomeUserGroupId']) || strlen($settings['welcomeUserGroupId']) < 5) {
            throw new UnexpectedValueException('Wrong welcome user group id, probably less than 5 characters.');
        }
        if (!isset($settings['ownGroupIds']) || !is_array($settings['ownGroupIds'])) {
            throw new UnexpectedValueException('Wrong own groups, not array?');
        }
    }

}
