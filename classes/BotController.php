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
    const MODE_NORMAL = 0;
    const MODE_DELETE_OWN = 1;

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
     * @param BotRights $rights
     * @param  BotTemplates $templates
     * @param string $mode
     */
    public function __construct(BotConfig $config, TelegramMessages $messages, DatabaseFacade $facade,
            Factory $factory, BotRights $rights, BotTemplates $templates, $mode = 'normal')
    {
        $this->config = $config;
        $this->messages = $messages;
        $this->database = $facade;
        $this->factory = $factory;
        $this->rights = $rights;
        $this->templates = $templates;
        $this->mode = $mode == 'normal' ? self::MODE_NORMAL : self::MODE_DELETE_OWN;
    }

    public function run()
    {
        if ($this->mode == self::MODE_DELETE_OWN) {
            $messagesToDelete = $this->database->getOwnMessagesToDeleteAfterTime(time());
            $contentExtractor = $this->factory->getContentExtractor(null);
            foreach ($messagesToDelete as $messageToDelete) {
                print_r($messageToDelete);
                $result = $this->messages->deleteMessage($this->config->getToken(), $messageToDelete['chat_id'], $messageToDelete['message_id']);
                print_r($result);
                $this->database->deleteOwnMessage($messageToDelete['message_id'], $messageToDelete['chat_id']);
            }
            return true;
        }
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
        if ($contentExtractor->isCallBack()) {
            $result = $this->processCallbackQuery($contentExtractor);
            return $result;
        }
        if ($contentExtractor->isChannelPost()) {
            $result = $this->processChannelPost($contentExtractor);
            return $result;
        }
        if ($contentExtractor->isPrivateMessage()) {
            $result = $this->processPrivateMessage($contentExtractor);
            return $result;
        }
        if (!$this->rights->userIsExcludedFromBans($contentExtractor->getUserId()) &&
                !$this->rights->contentIsExcludedFromBans($contentExtractor->getMessageContent()) &&
                $this->rights->botWorksInThisGroup($contentExtractor->getGroupId())) {
            $banned = $this->checkArabUser($contentExtractor);
            if (!$banned) {
                $banned = $this->checkSpamUsingSpamList($contentExtractor);
            }
            if (!$banned) {
                $banned = $this->checkNotAllowedForward($contentExtractor);
            }
            $this->database->writeLoggedUser($contentExtractor->getUserId(), $contentExtractor->getGroupId(),
                    $contentExtractor->getUserFirstName(), $contentExtractor->getUserLastName(),
                    $contentExtractor->getUserFullName(), $contentExtractor->getUserName(),
                    $banned);
        }
        if (!$banned) {
            $this->checkWelcomeNewUser($message);
            $this->checkGoodbyeLeftUser($message);
        }
        if ($this->rights->botWorksInThisGroup($contentExtractor->getGroupId())) {
            $this->deleteNewUserJoinedMessage($contentExtractor);
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

        if ($contentExtractor->isArabicString($contentExtractor->getMessageContent()) || $contentExtractor->isArabicString($contentExtractor->getMessageDocumentContent()) || $contentExtractor->isForbiddenFileString($contentExtractor->getMessageDocumentContent()) || $banInviter == 1) {
            $this->banArabUser($contentExtractor, $contentExtractor->getUser());
            if ($banInviter == 0) { // just normal message, not a new join
                $this->messages->deleteMessage($this->config->getToken(), $contentExtractor->getGroupId(), $contentExtractor->getMessageId());
            }
            $result = true;
        }
        return $result;
    }

    /**
     * @param ContentExtractor $contentExtractor 
     * @return bool
     */
    protected function deleteNewUserJoinedMessage($contentExtractor): bool
    {
        $result = false;
        if ($contentExtractor->newUserIsDetected()) {
            $chatId = $contentExtractor->getGroupId();
            $this->messages->deleteMessage($this->config->getToken(), $chatId, $contentExtractor->getMessageId());
            $result = true;
        }
        return $result;
    }

    /**
     * @param ContentExtractor $contentExtractor 
     * @return bool
     */
    protected function processChannelPost($contentExtractor): bool
    {
        $result = false;
        if ($contentExtractor->postContainsText()) {
            $mode = 'text';
            $text = $contentExtractor->getChannelPostText() . $this->templates->getChannelPostFooter($contentExtractor->getChannelPostChannelId());
        } else {
            $mode = 'caption';
            $text = $contentExtractor->getChannelPostCaption() . $this->templates->getChannelPostFooter($contentExtractor->getChannelPostChannelId());
        }
        if (in_array($contentExtractor->getAuthorSignature(), $this->config->getChannelNotEditableAuthors())) {
            return $result;
        }
        $keyboard = $this->templates->getChannelFooterKeyboard();
        if ($mode == 'text') {
            $resultText = $this->messages->editMessageText($this->config->getToken(),
                    $contentExtractor->getChannelPostChannelId(),
                    $contentExtractor->getChannelPostId(), $text, $keyboard);
        } else {
            $resultText = $this->messages->editMessageCaption($this->config->getToken(),
                    $contentExtractor->getChannelPostChannelId(),
                    $contentExtractor->getChannelPostId(), $text, $keyboard);
        }
        if (isset($resultText['ok']) && $resultText['ok'] == true) {
            $result = true;
        }
        return $result;
    }

    /**
     * @param ContentExtractor $contentExtractor 
     * @return bool
     */
    protected function processPrivateMessage($contentExtractor): bool
    {
        setlocale(LC_ALL, "en_US.UTF-8");
        $result = false;
        $personId = $contentExtractor->getGroupId();
        $text = 'Сообщение обработано.';
        $serverAnswer = '';
        if ($this->rights->postIsAllowedViaPrivateMessage($personId) && $contentExtractor->isAudioFileMessage()) {
            $text = 'Опознан авторизированный пользователь и сообщение аудио.';
              $this->messages->sendMessage($this->config->getToken(),
                $this->config->getLogUserId(), 'Пробую скачать и загрузить музыку ' . $contentExtractor->getAudioFileArtist() . ' - ' . $contentExtractor->getAudioFileTitle()  , 'HTML');
            $resultFile = $this->messages->getFile($this->config->getToken(), $contentExtractor->getAudioFileId());
            var_dump($resultFile);
            if ($contentExtractor->audioPathIsFound($resultFile)) {

                $filename = empty($contentExtractor->getAudioFileTitle()) ? $contentExtractor->getAudioFileId() : $contentExtractor->getAudioFileTitleSafe();
                $path = $this->config->getTempFilesDirectory() . 'telegram ' . $this->config->getGroupId3Tag() . ' - ' . $filename . '.mp3';
          //      var_dump($path);
         //       echo 'а теперь title' . PHP_EOL;
           //     var_dump($contentExtractor->getAudioFileTitleSafe());
                $resultDl = $this->downloadFile($this->config->getToken(),
                        $path,
                        $contentExtractor->getAudioPath($resultFile));
                $resultDecoded = json_decode($resultDl, true);
                $resultText = empty($resultDecoded) ? $resultDl : $resultDecoded;
                if (isset($resultDecoded['ok']) || strlen($resultDl) < 10000) {
                    $text = 'Ошибка загрузки: ' . $resultDl;
                } else {
                    $this->messages->sendMessage($this->config->getToken(),
                            $personId, 'Отсылаю музыку в канал, подождите...', 'HTML');
                    $text = 'Музыка загружена и отправлена в канал. ';
                    echo 'sending ' . $contentExtractor->getAudioFileId() . PHP_EOL;
                    $resultSent = $this->messages->sendMp3($this->config->getToken(),
                            $this->config->getGroupForRepostId(), $path, $this->config->getGroupId3Tag(),
                            $contentExtractor->getAudioFileTitle(),
                            $contentExtractor->getFileCaption() . $this->templates->getChannelPostFooter($this->config->getGroupForRepostId())
                    );
                //    var_dump($resultSent);
                       $serverAnswer = " \n Ответ телеграма: " .  json_encode($resultSent);
                }
                if (file_exists($path)) {
                    unlink($path); // remove from temp, not needed anymore
                }
            } else {
                $text = 'Не найден путь к аудиофайлу на сервере. Что-то пошло не так.' . json_encode($resultFile);
            }
        }
        $resultText = $this->messages->sendMessage($this->config->getToken(),
                $personId, $text, 'HTML');

                $this->messages->sendMessage($this->config->getToken(),
                $this->config->getLogUserId(), "[" . $contentExtractor->getGroupId() . "](tg://user?id=" . $contentExtractor->getGroupId() . ")" .
                        ' @' . $contentExtractor->getUserName() . ' ' .
                        $contentExtractor->getUserFullName() . 
                        ' : '.      $contentExtractor->getMessageContent() , 'Markdown');
        $this->messages->sendMessage($this->config->getToken(),
                $this->config->getLogUserId(),  "<a href='tg://user?id=" . $contentExtractor->getGroupId() . "'>" . $contentExtractor->getUserFullName() ."</a>" . 
                $contentExtractor->getAudioFileArtist() . ' - ' . $contentExtractor->getAudioFileTitle() . ' ' . $serverAnswer , 'HTML');
        if (isset($resultText['ok']) && $resultText['ok'] == true) {
            $result = true;
        }
        return $result;
    }

    /**
     * @param string $token
     * @param string $path
     * @param string $remotePath
     * @return string
     */
    protected function downloadFile(string $token, string $path, $remotePath)
    {
        $fileApiUrl = 'https://api.telegram.org/file/bot';
        $requestUrl = $fileApiUrl . $token . '/' . $remotePath;
        $fp = fopen($path, 'w+');
        $ch = curl_init($requestUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); //timeout in seconds
        $result = curl_exec($ch);
        fwrite($fp, $result);
        fclose($fp);
        curl_close($ch);
        return $result;
    }

    /**
     * @param ContentExtractor $contentExtractor 
     * @return bool
     */
    protected function processCallbackQuery($contentExtractor): bool
    {
        $result = false;
        $data = $contentExtractor->getCallbackData();
        if (preg_match('/^channel_like|channel_dislike$/', $data) != 1) {
            return $result;
        }
        if ($data == 'channel_like') {
            $this->database->writeNewCallbackLike($contentExtractor->getCallbackPostId(), $contentExtractor->getCallbackChannelId(),
                    $contentExtractor->getCallbackUserId());
        }
        $likeData = [
            'likes' => null,
            'dislikes' => null
        ];
        if ($data == 'channel_dislike') {
            $this->database->writeNewCallbackDislike($contentExtractor->getCallbackPostId(), $contentExtractor->getCallbackChannelId(),
                    $contentExtractor->getCallbackUserId());
        }
        if ($data == 'channel_like' || $data == 'channel_dislike') {
            $likeData = $this->database->getCallbackLikes($contentExtractor->getCallbackPostId(), $contentExtractor->getCallbackChannelId());
        }
        $keyboard = $this->templates->getChannelFooterKeyboard($likeData['likes'], $likeData['dislikes']);
        $resultText = $this->messages->editMessageReplyMarkup($this->config->getToken(),
                $contentExtractor->getCallbackChannelId(),
                $contentExtractor->getCallbackPostId(), $keyboard);
        if (isset($resultText['ok']) && $resultText['ok'] == true) {
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
     * @param ContentExtractor $contentExtractor 
     * @return bool
     */
    protected function checkNotAllowedForward($contentExtractor): bool
    {
        $result = false;
        if ($contentExtractor->messageContainsForward()) {
            $this->banForwardUser($contentExtractor, $contentExtractor->getUser());
            $result = true;
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
        $this->sendChatMessage($chatId, $this->templates->getBanArabUserText($bannedUser, $contentExtractor), $contentExtractor, true, 10);
        $banTime = $contentExtractor->getMessageDate() + (17 * 24 * 60 * 60);
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
        $spamData = $this->database->getSpamDataOnUser($id, $chatId);
        $spams = $contentExtractor->getPreviousSpamsFromSpamData($spamData);
        $allowedMessages = $contentExtractor->getPreviousAllowedMessagesFromSpamData($spamData);
        $this->sendChatMessage($chatId, $this->templates->getSpamUserText($bannedUser, $contentExtractor), $contentExtractor);
        $this->messages->deleteMessage($this->config->getToken(), $chatId, $contentExtractor->getMessageId());
        $banTime = $contentExtractor->getMessageDate() + $this->getBanMinutes($spams, $allowedMessages);
        $this->messages->restrictChatMember($this->config->getToken(), $chatId, $id, $banTime);
    }

    /**
     * @param int $spams
     * @param int $allowedMessages
     * @return int
     */
    protected function getBanMinutes($spams, $allowedMessages): int
    {
        if ($spams == 0 && $allowedMessages < 2) {
            return 2 * 60;
        }
        if ($spams == 1 && $allowedMessages < 2) {
            return 20 * 60;
        }
        if ($spams == 2 && $allowedMessages < 2) {
            return 120 * 60;
        }
        if ($spams == 3 && $allowedMessages < 2) {
            return 4 * 60 * 60;
        }
        if ($spams > 3 && $allowedMessages < 2) {
            return ($spams + 5) * 60 * 60;
        }
        if ($allowedMessages > 100 && $spams < 20) {
            return 60;
        }
        if ($allowedMessages > 20 && $spams < 2) {
            return 60;
        }
        if ($allowedMessages > 20 && $spams < 5) {
            return 2 * 60;
        }
        // now cases allowed > 100 and spam > 20
        // allowed < 100 and spam <> 20
        if ($allowedMessages > 100 && $spams > 20) {
            return 2 * 60;
        }
        if ($allowedMessages < 100 && $spams > 20) {
            return ($spams + 5) * 2 * 60;
        }
        if ($allowedMessages < 100 && $spams < 20) {
            return ($spams + 2) * 2 * 60;
        }
        // no idea what case, just ban
        return ($spams + 4) * 2 * 60;
    }

    /**
     * @param \TelegramBot\ContentExtractor $contentExtractor
     * @param array $bannedUser
     */
    protected function banForwardUser(ContentExtractor $contentExtractor, $bannedUser)
    {
        $chatId = $contentExtractor->getGroupId();
        $id = $contentExtractor->getIdFromUser($bannedUser);
        $spamData = $this->database->getSpamDataOnUser($id, $chatId);
        $spams = $contentExtractor->getPreviousSpamsFromSpamData($spamData);
        $allowedMessages = $contentExtractor->getPreviousAllowedMessagesFromSpamData($spamData);
        $this->sendChatMessage($chatId, $this->templates->getForwardUserText($bannedUser, $contentExtractor), $contentExtractor);
        $this->messages->deleteMessage($this->config->getToken(), $chatId, $contentExtractor->getMessageId());
        $banTime = $contentExtractor->getMessageDate() + $this->getBanMinutes($spams, $allowedMessages);
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
        $isFriendGroupMessage = $this->rights->welcomeMessageIsDifferentForGroup($contentExtractor->getGroupId());
        if ($contentExtractor->newUserIsDetected()) {
            $newUsers = $contentExtractor->getNewJoinedUsers();
            foreach ($newUsers as $newUser) {
                $currentResult = $this->sendChatMessage($contentExtractor->getGroupId(),
                        $this->templates->getWelcomeMessage($newUser, $contentExtractor,
                                $isFriendGroupMessage), $contentExtractor);
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
                $currentResult = $this->sendChatMessage($contentExtractor->getGroupId(),
                        $this->templates->getLeaveMessage($leftUser, $contentExtractor), $contentExtractor);
                $result &= $contentExtractor->sendMessageResultIsSuccess($currentResult);
            }
        }
        return $result;
    }

    /**
     * @param type $chatId
     * @param string $text
     * @param ContentExtractor $contentExtractor
     * @param bool $logMessage
     * @param string $deleteTime
     * @return array
     */
    protected function sendChatMessage($chatId, string $text, ContentExtractor $contentExtractor, bool $logMessage = true, string $deleteTime = 'standard')
    {
        $result = $this->messages->sendMessage($this->config->getToken(),
                $chatId, $text);
        if ($contentExtractor->sendMessageResultIsSuccess($result) == true && $logMessage == true) {
            $deletionTime = time();
            if ($deleteTime == 'standard') {
                $deletionTime = $deletionTime + 30;
            } else {
                $deletionTime = $deletionTime + $deleteTime;
            }
            $id = $contentExtractor->getIdOfSentMessage($result);
            if ($id !== null) {
                $this->database->writeOwnMessageLog($id, $chatId, $deletionTime);
            }
        }
        return $result;
    }

}
