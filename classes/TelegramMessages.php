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
     * @return type
     */
    public function sendMessage(string $token, $chatId, string $text)
    {
        $requestUrl = self::ApiUrl . $token . '/sendMessage';
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
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
