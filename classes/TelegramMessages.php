<?php

namespace TelegramBot;

class TelegramMessages
{

    const ApiUrl = "https://api.telegram.org/bot";

    /**
     * @param string $token
     * @param int $offset
     * @return string
     */
    public function getUpdates(string $token, $offset)
    {
        $requestUrl = self::ApiUrl . $token . '/getUpdates';
        $params = [
            'offset' => $offset,
            'timeout' => 400,
        ];
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param string $token
     * @param int $chatId
     * @param string $text
     * @param string $mode
     * @return type
     */
    public function sendMessage(string $token, $chatId, string $text, $mode = 'Markdown')
    {
        $requestUrl = self::ApiUrl . $token . '/sendMessage';
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $mode
        ];
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param string $token
     * @param int $chatId
     * @param int $messageId
     * @return type
     */
    public function deleteMessage(string $token, $chatId, $messageId)
    {
        $requestUrl = self::ApiUrl . $token . '/deleteMessage';
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId
        ];
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param string $token
     * @param int $chatId
     * @param string $text
     * @return type
     */
    public function restrictChatMember(string $token, $chatId, $userId, $untilDate)
    {
        $requestUrl = self::ApiUrl . $token . '/restrictChatMember';
        $params = [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'until_date' => $untilDate,
            'can_send_messages' => false,
            'can_send_media_messages' => false,
            'can_send_other_messages' => false,
            'can_add_web_page_previews' => false
        ];
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param string $token
     * @param int $chatId
     * @param string $text
     * @return type
     */
    public function kickChatMember(string $token, $chatId, $userId, $untilDate)
    {
        $requestUrl = self::ApiUrl . $token . '/kickChatMember';
        $params = [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'until_date' => $untilDate
        ];
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param string $token
     * @param int $chatId
     * @param int $messageId
     * @param string text
      @param string $inlineKeyboard
     * @return type
     */
    public function editMessageText(string $token, $chatId, $messageId, $text, $inlineKeyboard = null)
    {
        $requestUrl = self::ApiUrl . $token . '/editMessageText';
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ];
        if (!empty($inlineKeyboard)) {
            $params['reply_markup'] = $inlineKeyboard;
        }
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param string $token
     * @param int $chatId
     * @param int $messageId
     * @param string text
      @param string $inlineKeyboard
     * @return type
     */
    public function editMessageCaption(string $token, $chatId, $messageId, $text, $inlineKeyboard = null)
    {
        $requestUrl = self::ApiUrl . $token . '/editMessageCaption';
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'caption' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ];
        if (!empty($inlineKeyboard)) {
            $params['reply_markup'] = $inlineKeyboard;
        }
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param string $token
     * @param int $chatId
     * @param int $messageId
      @param string $inlineKeyboard
     * @return type
     */
    public function editMessageReplyMarkup(string $token, $chatId, $messageId, $inlineKeyboard = null)
    {
        $requestUrl = self::ApiUrl . $token . '/editMessageReplyMarkup';
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => $inlineKeyboard
        ];
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param string $token
     * @param string $fileId
     * @return type
     */
    public function getFile(string $token, $fileId)
    {
        $requestUrl = self::ApiUrl . $token . '/getFile';
        $params = [
            'file_id' => $fileId
        ];
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param string $token
     * @param int $chatId
     * @param string $text
     * @param string $mode
     * @return type
     */
    public function sendMp3(string $token, $chatId, string $audioPath, $artist = null, $title = null, $caption = null, $mode = 'HTML')
    {
        $requestUrl = self::ApiUrl . $token . '/sendAudio';
        $audio = new \CURLFile($audioPath);
        $params = [
            'chat_id' => $chatId,
            'audio' => $audio,
            'parse_mode' => $mode
        ];
        if (!empty($caption)) {
            $params['caption'] = $caption;
        }
        if (!empty($artist)) {
            $params['performer'] = $artist;
        }
        if (!empty($title)) {
            $params['title'] = $title;
        }
        $ch = curl_init($requestUrl);
        $this->setCurlOpts($ch, $requestUrl, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); //timeout in seconds
        $result = curl_exec($ch);
        curl_close($ch);
        return $this->decodeResult($result);
    }

    /**
     * @param cUrl $ch
     * @param string $requestUrl
     * @param array $params
     * @return type
     */
    protected function setCurlOpts($ch, string $requestUrl, array $params)
    {
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //timeout in seconds
    }

    /**
     * @param string $result
     * @return array | null
     */
    protected function decodeResult($result)
    {
        return json_decode($result, true);
    }

}
