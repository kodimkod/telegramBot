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
            return '@' . $user['username'];
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

}
