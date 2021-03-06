<?php

namespace TelegramBot;

class ContentExtractor
{

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @param mixed $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    //$contentExtractor->getMessageId(), 'normal', $contentExtractor->getGroupId(), $contentExtractor->getMessage()

    /**
     * @return int | null
     */
    public function getMessageId()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['message_id'])) {
            return $message['message_id'];
        }
    }

    /**
     * @return int | null
     */
    public function getGroupId()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['chat']) && isset($message['chat']['id'])) {
            return $message['chat']['id'];
        }
    }

    /**
     * @return int 
     */
    public function getUserId()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['from']) && isset($message['from']['id'])) {
            return $message['from']['id'];
        }
        return 0;
    }

    /**
     * @return string 
     */
    public function getUserFirstName()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['from']) && isset($message['from']['first_name'])) {
            return $message['from']['first_name'];
        }
        return '';
    }

    /**
     * @return string 
     */
    public function getUserLastName()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['from']) && isset($message['from']['last_name'])) {
            return $message['from']['last_name'];
        }
        return '';
    }

    /**
     * @return string | null
     */
    public function getUserName()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['from']) && isset($message['from']['username'])) {
            return $message['from']['username'];
        }
        return null;
    }

    /**
     * @return string
     */
    public function getUserFullName()
    {
        $firstName = $this->getUserFirstName();
        $lastName = $this->getUserLastName();
        if (empty($firstName)) {
            return $lastName;
        }
        if (empty($lastName)) {
            return $firstName;
        }
        return $firstName . ' ' . $lastName;
    }

    /**
     * @return int 
     */
    public function getUser()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['from']) && is_array($message['from'])) {
            return $message['from'];
        }
        return [];
    }

    /**
     * @return string 
     */
    public function getMessageContent()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['text'])) {
            return $message['text'];
        }
        if (isset($message['caption'])) {
            return $message['caption'];
        }
        return 0;
    }

    /**
     * @return string 
     */
    public function getMessageDocumentContent()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['document']) && isset($message['document']['file_name'])) {
            return $message['document']['file_name'];
        }
        return 0;
    }

    /**
     * @return string | null
     */
    public function getGroupName()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['chat']) && isset($message['chat']['title'])) {
            return $message['chat']['title'];
        }
    }

    /**
     * @return int
     */
    public function getMessageDate()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['date'])) {
            return (int) $message['date'];
        }
        return time();
    }

    /**
     * @return string | null
     */
    public function getMessage()
    {
        return json_encode($this->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array $user
     * @return string
     */
    public function getNameFromUser($user): string
    {
        if (isset($user['first_name'])) {
            return $user['first_name'];
        }
        if (isset($user['last_name'])) {
            return $user['last_name'];
        }
        if (isset($user['username'])) {
            return '@' . $user['username'];
        }
        if (isset($user['id'])) {
            return $user['id'];
        }
        return 'Безымянный';
    }

    /**
     * @param array $user
     * @return string
     */
    public function getLastNameFromUser($user): string
    {
        if (isset($user['last_name'])) {
            return $user['last_name'];
        }
        return '';
    }

    /**
     * @param array $user
     * @return string
     */
    public function getUserNameFromUser($user): string
    {
        if (isset($user['username'])) {
            return $user['username'];
        }
        return '';
    }

    /**
     * @param array $user
     * @return bool
     */
    public function userIsBot($user): bool
    {
        if (isset($user['is_bot']) && $user['is_bot'] == true) {
            return true;
        }
        return false;
    }

    /**
     * @param array $user
     * @return string
     */
    public function getIdFromUser($user): string
    {
        if (isset($user['id'])) {
            return $user['id'];
        }
        return '0';
    }

    /**
     * @param array $result
     * @return bool
     */
    public function sendMessageResultIsSuccess($result): bool
    {
        //file_put_contents('sendmessageresults.txt', json_encode($result), FILE_APPEND);
        if (isset($result['result']) && isset($result['result']["message_id"])) {
            return true;
        }
        return false;
    }

    /**
     * @param array $result
     * @return string | null
     */
    public function getIdOfSentMessage($result)
    {
        if (isset($result['result']) && isset($result['result']["message_id"])) {
            return $result['result']["message_id"];
        }
        return null;
    }

    /**
     * @return bool
     */
    public function newUserIsDetected(): bool
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return false;
        }
        if (isset($message["new_chat_members"]) && is_array($message["new_chat_members"])) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function userLeftIsDetected(): bool
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return false;
        }
        if (isset($message["left_chat_participant"]) && is_array($message["left_chat_participant"])) {
            return true;
        }
        if (isset($message["left_chat_member"]) && is_array($message["left_chat_member"])) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getNewJoinedUsers(): array
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return [];
        }
        if (isset($message["new_chat_members"]) && is_array($message["new_chat_members"])) {
            return $message["new_chat_members"];
        }
        return [];
    }

    /**
     * @return array
     */
    public function getNewLeftUsers(): array
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return [];
        }
        if (isset($message["left_chat_participant"]) && is_array($message["left_chat_participant"])) {
            return [$message["left_chat_participant"]];
        }
        if (isset($message["left_chat_member"]) && is_array($message["left_chat_member"])) {
            return [$message["left_chat_member"]];
        }
        return [];
    }

    /**
     * @param type $string
     * @return bool
     */
    public function isArabicString($string): bool
    {
        $is_arabic = preg_match('/\p{Arabic}/u', $string);
        if ($is_arabic == 1) {
            return true;
        }
        return false;
    }

    /**
     * @param type $string
     * @return bool
     */
    public function isForbiddenFileString($string): bool
    {
        $forbidden = preg_match('/\.apk/u', $string);
        if ($forbidden == 1) {
            return true;
        }
        return false;
    }

    /**
     * @param type $text
     * @return bool
     */
    public function messageContains($text): bool
    {
        $message = $this->getMessageContent();
        $contentReady = preg_quote($text, '/');
        $isExcepted = preg_match('/' . $contentReady . '/ui', $message);
        if ($isExcepted == 1) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function messageContainsForward(): bool
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return false;
        }
        if (isset($message["forward_from_chat"]) && is_array($message["forward_from_chat"])) {
            return true;
        }
        if (isset($message["forward_from"]) && is_array($message["forward_from"])) {
            return true;
        }
        return false;
    }

    /**
     * @param array $spamData
     * @return int
     */
    public function getPreviousSpamsFromSpamData($spamData): int
    {
        if (isset($spamData['spam_messages'])) {
            return $spamData['spam_messages'];
        }
        return 0;
    }

    /**
     * @param array $spamData
     * @return int
     */
    public function getPreviousAllowedMessagesFromSpamData($spamData): int
    {
        if (isset($spamData['allowed_messages'])) {
            return $spamData['allowed_messages'];
        }
        return 0;
    }

    /**
     * @return bool
     */
    public function isChannelPost(): bool
    {
        if (!isset($this->content['message']) && isset($this->content['channel_post'])) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isPrivateMessage(): bool
    {
        $message = $this->getMessageSection();
        if (isset($message['chat']) && isset($message['chat']['type']) && $message['chat']['type'] == 'private') {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAudioFileMessage(): bool
    {
        $message = $this->getMessageSection();
        if (isset($message['audio']) && isset($message['audio']['file_id'])) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isCallBack(): bool
    {
        if (!isset($this->content['message']) && isset($this->content['callback_query'])) {
            return true;
        }
        return false;
    }

    /**
     * @return int | null
     */
    public function getChannelPostId()
    {
        $message = $this->getChannelPostSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['message_id'])) {
            return $message['message_id'];
        }
    }

    /**
     * @return int | null
     */
    public function getChannelPostChannelId()
    {
        $message = $this->getChannelPostSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['chat']) && isset($message['chat']['id'])) {
            return $message['chat']['id'];
        }
    }

    /**
     * @return string | null
     */
    public function getAudioFileId()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['audio']) && isset($message['audio']['file_id'])) {
            return $message['audio']['file_id'];
        }
    }

    /**
     * @return string | null
     */
    public function getAudioFileArtist()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['audio']) && isset($message['audio']['performer'])) {
            return $message['audio']['performer'];
        }
        return '';
    }

    /**
     * @return string | null
     */
    public function getAudioFileTitle()
    {
        if (!empty($this->getFileCaption())) {
            return $this->getFileCaption();
        }
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['audio']) && isset($message['audio']['title'])) {
            return $message['audio']['title'];
        }
        return $this->getFileCaption();
    }

    /**
     * @return string | null
     */
    public function getAudioFileTitleSafe()
    {
        $title = $this->getAudioFileTitle();
        $title = str_replace('/', '', $title);
        $title = str_replace('\\', '', $title);
        $title = str_replace('.', '', $title);
        $title = str_replace('*', '', $title);
        $title = str_replace('?', '', $title);
        $title = str_replace('"', '', $title);
        $title = str_replace('<', '', $title);
        $title = str_replace('>', '', $title);
        $title = str_replace('|', '', $title);
        $title = str_replace(':', '', $title);
        $title = str_replace('\r', '', $title);
        $title = str_replace('_', ' ', $title);
      // echo 'before change ' . $title . PHP_EOL;
        $title = preg_replace("/\r|\n/", "", $title);
        $title = strlen($title) > 30 ? substr($title, 0, 30) : $title;
        $encoding = mb_detect_encoding($title, mb_detect_order(), false);
        if ($encoding == "UTF-8") {
            $title = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
        }
        $title = iconv(mb_detect_encoding($title, mb_detect_order(), false), "UTF-8//IGNORE", $title);
        $title = str_replace('?', '', $title);

        if (empty($title)) {
            $title = $this->getAudioFileId();
        }
      //  echo 'after change ' . $title . PHP_EOL;
        return $title;
    }

    /**
     * @return string | null
     */
    public function getFileCaption()
    {
        $message = $this->getMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['caption'])) {
            return $message['caption'];
        }
    }

    /**
     * @return string 
     */
    public function getChannelPostText()
    {
        $message = $this->getChannelPostSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['text'])) {
            return $message['text'];
        }
        if (isset($message['caption'])) {
            return $message['caption'];
        }
        return null;
    }

    /**
     * @return string 
     */
    public function getChannelPostCaption()
    {
        $message = $this->getChannelPostSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['caption'])) {
            return $message['caption'];
        }
        return null;
    }

    /**
     * @return bool 
     */
    public function postContainsText(): bool
    {
        $message = $this->getChannelPostSection();
        if (empty($message)) {
            return false;
        }
        if (isset($message['text'])) {
            return true;
        }
        return false;
    }

    /**
     * @return string | null
     */
    public function getAuthorSignature()
    {
        $message = $this->getChannelPostSection();
        if (empty($message)) {
            return null;
        }
        if (isset($message['author_signature'])) {
            return $message['author_signature'];
        }
        return null;
    }

    /**
     * @return int | null
     */
    public function getCallbackPostId()
    {
        $message = $this->getCallbackMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['message_id'])) {
            return $message['message_id'];
        }
    }

    /**
     * @return string | null
     */
    public function getCallbackData()
    {
        if (isset($this->content['callback_query']) && isset($this->content['callback_query']['data'])) {
            return $this->content['callback_query']['data'];
        } else {
            return null;
        }
    }

    /**
     * @return int | null
     */
    public function getCallbackUserId()
    {
        if (isset($this->content['callback_query']) &&
                isset($this->content['callback_query']['from']) &&
                isset($this->content['callback_query']['from']['id'])
        ) {
            return $this->content['callback_query']['from']['id'];
        } else {
            return null;
        }
    }

    /**
     * @return int | null
     */
    public function getCallbackChannelId()
    {
        $message = $this->getCallbackMessageSection();
        if (empty($message)) {
            return;
        }
        if (isset($message['chat']) && isset($message['chat']['id'])) {
            return $message['chat']['id'];
        }
    }

    /**
     * @param array $fileResult
     * @return bool
     */
    public function audioPathIsFound($fileResult): bool
    {
        if (isset($fileResult['result']) && isset($fileResult['result']['file_path'])) {
            return true;
        }
        return false;
    }

    /**
     * @param array $fileResult
     * @return string null
     */
    public function getAudioPath($fileResult)
    {
        if (isset($fileResult['result']) && isset($fileResult['result']['file_path'])) {
            return $fileResult['result']['file_path'];
        }
        return;
    }

    /**
     * @return array | null
     */
    protected function getMessageSection()
    {
        if (isset($this->content['message'])) {
            return $this->content['message'];
        } else if (isset($this->content['edited_message'])) {
            return $this->content['edited_message'];
        } else {
            return;
        }
    }

    /**
     * @return array | null
     */
    protected function getChannelPostSection()
    {
        if (isset($this->content['channel_post'])) {
            return $this->content['channel_post'];
        } else if (isset($this->content['edited_channel_post'])) {
            return $this->content['edited_channel_post'];
        } else {
            return;
        }
    }

    /**
     * @return array | null
     */
    protected function getCallbackMessageSection()
    {
        if (isset($this->content['callback_query']) && isset($this->content['callback_query']['message'])) {
            return $this->content['callback_query']['message'];
        } else {
            return;
        }
    }

}
