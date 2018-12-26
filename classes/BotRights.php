<?php

namespace TelegramBot;

class BotRights
{

    /**
     * @var BotConfig 
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $usersExcludedFromBan;

    /**
     * @var array
     */
    protected $contentExcludedFromBan;

    /**
     * @param \TelegramBot\BotConfig $configuration
     * @param array $excludedUsers
     * @param array $excludedContent
     */
    public function __construct(BotConfig $configuration, array $excludedUsers, array $excludedContent)
    {
        $this->configuration = $configuration;
        $this->usersExcludedFromBan = $excludedUsers;
        $this->contentExcludedFromBan = $excludedContent;
    }

    /**
     * @param string $groupId
     * @return bool
     */
    public function welcomeMessageIsAllowedForGroup($groupId): bool
    {
        $groups = $this->configuration->getOwnGroups();
        foreach ($groups as $group) {
            if ($group == $groupId) {
                return true;
            }
        }
        $groups = $this->configuration->getFriendGroupsWhereBotWorks();
        foreach ($groups as $group) {
            if ($group == $groupId) {
                return true;
            }
        }
        return false;
    }
    
     /**
     * @param string $groupId
     * @return bool
     */
    public function welcomeMessageIsDifferentForGroup($groupId): bool
    {
        $groups = $this->configuration->getFriendGroupsWhereWelcomeMessageDiffers();
        foreach ($groups as $group) {
            if ($group == $groupId) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int $userId
     * @return boolean
     */
    public function userIsExcludedFromBans($userId)
    {
        foreach ($this->usersExcludedFromBan as $user) {
            if ($user['id'] == $userId) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $text
     * @return boolean
     */
    public function contentIsExcludedFromBans($text): bool
    {
        foreach ($this->contentExcludedFromBan as $content) {
            $contentText = isset($content['content']) ? $content['content'] : '';
            $contentReady = preg_quote($contentText, '/');
            $isExcepted = preg_match('/' . $contentReady . '/ui', $text);
            if ($isExcepted == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $groupId
     * @return boolean
     */
    public function botWorksInThisGroup($groupId): bool
    {
        $groups = $this->configuration->getOwnGroups();
        foreach ($groups as $group) {
            if ($group == $groupId) {
                return true;
            }
        }
        $groups = $this->configuration->getFriendGroupsWhereBotWorks();
        foreach ($groups as $group) {
            if ($group == $groupId) {
                return true;
            }
        }
        return false;
    }

}
