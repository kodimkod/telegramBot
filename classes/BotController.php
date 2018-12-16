<?php

namespace TelegramBot;

use TelegramBot\{
    TelegramMessages,
    BotConfig,
    DatabaseFacade,
    BotRights,
    BotTemplates
};

class BotController
{

    const LOG_TYPE_UNRECOGNIZED = 0;
    const LOG_TYPE_NORMAL_MESSAGE = 1;

    /**
     * @var TelegramMessages
     */
    protected $messages;

    /**
     * @var BotConfig
     */
    protected $config;

    /**
     * @var DatabaseFacade
     */
    protected $database;

    /**
     * @var Factory $factory
     */
    protected $factory;

    /**
     * @var BotRights
     */
    protected $rights;

    /**
     * @var BotTemplates
     */
    protected $templates;

    /**
     * @param BotConfig $config
     * @param TelegramMessages $messages
     * @param DatabaseFacade $facade
     * @param Factory $factory
     */
    public function __construct(BotConfig $config, TelegramMessages $messages, DatabaseFacade $facade,
            Factory $factory, BotRights $rights, BotTemplates $templates)
    {
        $this->config = $config;
        $this->messages = $messages;
        $this->database = $facade;
        $this->factory = $factory;
        $this->rights = $rights;
        $this->templates = $templates;
    }

    public function run()
    {
        $offset = $this->database->readLastMessageOffset();
        $offset = (int) $offset + 1;
        echo $offset . PHP_EOL;
        $messages = $this->messages->getUpdates($this->config->getToken(), $offset);
        if ($this->shouldNotProcessMessages($messages)) {
            $this->logContent($messages, self::LOG_TYPE_UNRECOGNIZED);
            return false;
        }
        foreach ($messages['result'] as $message) {
            $this->logContent($message, self::LOG_TYPE_NORMAL_MESSAGE);
            $this->processSingleMessage($message);
            if (isset($message['update_id'])) {
                $this->saveLastUpdateId($message['update_id']);
            }
        }
    }

    /**
     * @param mixed $messages
     * @return bool
     */
    protected function shouldNotProcessMessages($messages): bool
    {
        if (!is_array($messages) || empty($messages)) {
            return true;
        }
        if (!isset($messages['result']) || !is_array($messages['result']) || empty($messages['result'])) {
            return true;
        }
        return false;
    }

    /**
     * @param mixed $content
     * @param int $messageType
     */
    protected function logContent($content, $messageType)
    {
        $contentExtractor = $this->factory->getContentExtractor($content);
        switch ($messageType) {
            case self::LOG_TYPE_NORMAL_MESSAGE:
                $this->database->writeNormalMessageLog($contentExtractor->getMessageId(), 'normal',
                        $contentExtractor->getGroupId(), $contentExtractor->getGroupName(),
                        $contentExtractor->getMessage());
                break;
            case self::LOG_TYPE_UNRECOGNIZED:
            default:
                $this->database->writeNormalMessageLog($contentExtractor->getMessageId(), 'unrecognized',
                        $contentExtractor->getGroupId(), $contentExtractor->getGroupName(),
                        $contentExtractor->getMessage());
                break;
        }
    }

    /**
     * @param string $updateId
     */
    protected function saveLastUpdateId(string $updateId)
    {
        $this->database->writeLastMessageOffset($updateId);
    }

    /**
     * @param array $message
     */
    protected function processSingleMessage($message)
    {
        $contentExtractor = $this->factory->getContentExtractor($message);
        $banned = false;
        if (!$this->rights->userIsExcludedFromBans($contentExtractor->getUserId()) &&
                !$this->rights->contentIsExcludedFromBans($contentExtractor->getMessageContent()) &&
                $this->rights->botWorksInThisGroup($contentExtractor->getGroupId())) {
            $banned = $this->checkArabUser($contentExtractor);
            if (!$banned) {
                $banned = $this->checkSpamUsingSpamList($contentExtractor);
            }
        }
        if (!$banned) {
            $this->checkWelcomeNewUser($message);
            $this->checkGoodbyeLeftUser($message);
        }
    }

    /**
     * @param ContentExtractor $contentExtractor 
     * @return bool
     */
    protected function checkArabUser($contentExtractor): bool
    {
        $result = false;
        $banInviter = 0;
        $botsFound = 0;
        if ($contentExtractor->newUserIsDetected()) {
            $newUsers = $contentExtractor->getNewJoinedUsers();
            foreach ($newUsers as $newUser) {
                if ($contentExtractor->isArabicString($contentExtractor->getNameFromUser($newUser)) ||
                        $contentExtractor->isArabicString($contentExtractor->getLastNameFromUser($newUser)) ||
                        $contentExtractor->isArabicString($contentExtractor->getUserNameFromUser($newUser)) ||
                        $contentExtractor->userIsBot($newUser)) { // no separated checks arab/bot, else we may ban same user 2 times
                    $this->banArabUser($contentExtractor, $newUser);
                    $result = true;
                    if ($contentExtractor->userIsBot($newUser)) { // 1 bot is ok, but we count
                        $botsFound += 1;
                    }
                    if ($contentExtractor->isArabicString($contentExtractor->getNameFromUser($newUser)) ||
                            $contentExtractor->isArabicString($contentExtractor->getLastNameFromUser($newUser)) ||
                            $contentExtractor->isArabicString($contentExtractor->getUserNameFromUser($newUser))) {
                        // arab detected, ban inviter
                        $banInviter = 1;
                    }
                }
            }
            if ($botsFound > 1) {
                $banInviter = 1;
            }

            if (count($newUsers) == 1 && isset($newUsers[0]) &&
                    $contentExtractor->getIdFromUser($newUsers[0]) == $contentExtractor->getUserId()) {
                // from user and new user same, no second ban
                $banInviter = 0;
            }
        }

        if ($contentExtractor->isArabicString($contentExtractor->getMessageContent()) || $banInviter == 1) {
            $this->banArabUser($contentExtractor, $contentExtractor->getUser());
            $result = true;
        }
        return $result;
    }

    /**
     * @param ContentExtractor $contentExtractor 
     * @return bool
     */
    protected function checkSpamUsingSpamList($contentExtractor): bool
    {
        $result = false;
        $spamMessages = $this->database->getSpam();
        foreach ($spamMessages as $spamMessage) {
            $spamText = isset($spamMessage['content']) ? $spamMessage['content'] : '';
            if ($contentExtractor->messageContains($spamText)) {
                $this->banSpamUser($contentExtractor, $contentExtractor->getUser());
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * @param \TelegramBot\ContentExtractor $contentExtractor
     * @param array $bannedUser
     */
    protected function banArabUser(ContentExtractor $contentExtractor, $bannedUser)
    {
        $chatId = $contentExtractor->getGroupId();
        $id = $contentExtractor->getIdFromUser($bannedUser);
        $this->messages->sendMessage($this->config->getToken(), $chatId, $this->templates->getBanArabUserText($bannedUser, $contentExtractor));
        $banTime = $contentExtractor->getMessageDate() + (7 * 24 * 60 * 60);
        $this->messages->restrictChatMember($this->config->getToken(), $chatId, $id, $banTime);
        $this->messages->kickChatMember($this->config->getToken(), $chatId, $id, $banTime);
    }

    /**
     * @param \TelegramBot\ContentExtractor $contentExtractor
     * @param array $bannedUser
     */
    protected function banSpamUser(ContentExtractor $contentExtractor, $bannedUser)
    {
        $chatId = $contentExtractor->getGroupId();
        $id = $contentExtractor->getIdFromUser($bannedUser);
        $this->messages->sendMessage($this->config->getToken(), $chatId, $this->templates->getSpamUserText($bannedUser, $contentExtractor));
        $this->messages->deleteMessage($this->config->getToken(), $chatId, $contentExtractor->getMessageId());
        $banTime = $contentExtractor->getMessageDate() + (2 * 60);
         $this->messages->restrictChatMember($this->config->getToken(), $chatId, $id, $banTime);
    }

    /**
     * @param array $message
     * @return bool
     */
    protected function checkWelcomeNewUser($message): bool
    {
        $result = true;
        $contentExtractor = $this->factory->getContentExtractor($message);
        if (!$this->rights->welcomeMessageIsAllowedForGroup($contentExtractor->getGroupId())) {
            return false;
        }
        if ($contentExtractor->newUserIsDetected()) {
            $newUsers = $contentExtractor->getNewJoinedUsers();
            foreach ($newUsers as $newUser) {
                $currentResult = $this->messages->sendMessage($this->config->getToken(),
                        $contentExtractor->getGroupId(), $this->templates->getWelcomeMessage($newUser, $contentExtractor));
                $result &= $contentExtractor->sendMessageResultIsSuccess($currentResult);
            }
        }
        return $result;
    }

    /**
     * @param array $message
     * @return bool
     */
    protected function checkGoodbyeLeftUser($message): bool
    {
        $result = true;
        $contentExtractor = $this->factory->getContentExtractor($message);
        if (!$this->rights->welcomeMessageIsAllowedForGroup($contentExtractor->getGroupId())) {
            return false;
        }
        if ($contentExtractor->userLeftIsDetected() && $contentExtractor->getUserId() != $this->config->getBotId()) {
            $leftUsers = $contentExtractor->getNewLeftUsers();
            foreach ($leftUsers as $leftUser) {
                $currentResult = $this->messages->sendMessage($this->config->getToken(),
                        $contentExtractor->getGroupId(), $this->templates->getLeaveMessage($leftUser, $contentExtractor));
                $result &= $contentExtractor->sendMessageResultIsSuccess($currentResult);
            }
        }
        return $result;
    }

}
