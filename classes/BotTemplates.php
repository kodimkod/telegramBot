<?php

namespace TelegramBot;

use TelegramBot\ContentExtractor;

class BotTemplates
{

    /**
     * @var BotConfig 
     */
    protected $configuration;

    public function __construct(BotConfig $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param array $newUser
     * @param ContentExtractor $extractor
     * @return string
     */
    public function getWelcomeMessage(array $newUser, ContentExtractor $extractor): string
    {
        $name = $extractor->getNameFromUser($newUser);
        $id = $extractor->getIdFromUser($newUser);
        $message = "
      ❀͍͓※͍͓❀͍͓[" . $this->configuration->getWelcomeUserName() .
                "](tg://user?id=" . $this->configuration->getWelcomeUserId() . ")❀͍͓※͍͓❀͍͓:
ПРИВЕТИК [" . $name . "](tg://user?id=" . $id .
                ") ☺️😘! ДОБРО ПОЖАЛОВАТЬ!!! 🤗РАДЫ ВАС ВИДЕТЬ, В НАШЕЙ [ГРУППЕ](tg://join?invite=" .
                $this->configuration->getWelcomeUserGroupId() . ")!😘!ПРИЯТНОГО ВАМ ОБЩЕНИЯ! 💖💖💖💖
            ";
        return $message;
    }

    /**
     * @param array $newUser
     * @param ContentExtractor $extractor
     * @return string
     */
    public function getLeaveMessage(array $leftUser, ContentExtractor $extractor): string
    {
        $name = $extractor->getNameFromUser($leftUser);
        $id = $extractor->getIdFromUser($leftUser);
        $message = "
      ❀͍͓※͍͓❀͍͓[" . $this->configuration->getWelcomeUserName() . "](tg://user?id=" .
                $this->configuration->getWelcomeUserId() . ")❀͍͓※͍͓❀͍͓:
От нас ушёл [" . $name . "](tg://user?id=" . $id . "). До новых встреч, [" . $name . "](tg://user?id=" . $id . ")! Заходи к нам ещё! 💖💖💖💖
            ";
        return $message;
    }

    /**
     * @param array $bannedUser
     * @param ContentExtractor $extractor
     * @return string
     */
    public function getBanArabUserText($bannedUser, ContentExtractor $extractor): string
    {
        $name = $extractor->getNameFromUser($bannedUser);
        $id = $extractor->getIdFromUser($bannedUser);
        $rand = rand(1, 2);
        switch ($rand) {
            case 1:
                $message = "
     Моджахед [" . $name . "](tg://user?id=" . $id . ") к нам спустился с гор. Лови бан, чёрт ушастый! 👊👊👊
            ";
                break;
            case 2:
            default:
                $message = "
     В чате обнаружен моджахед [" . $name . "](tg://user?id=" . $id . ")! Катись отсюда, шакал! 👊
            ";
                break;
        }
        return $message;
    }
    
      /**
     * @param array $bannedUser
     * @param ContentExtractor $extractor
     * @return string
     */
    public function getSpamUserText($bannedUser, ContentExtractor $extractor): string
    {
        $name = $extractor->getNameFromUser($bannedUser);
        $id = $extractor->getIdFromUser($bannedUser);
        $rand = rand(1, 2);
        switch ($rand) {
            case 1:
                $message = "
     За спам [" . $name . "](tg://user?id=" . $id . ") утихомирен. Я тебя предупреждал, пёс! 😡😡😡
            ";
                break;
            case 2:
            default:
                $message = "
     За рекламу [" . $name . "](tg://user?id=" . $id . ") в бан попал! Уймись, собака! 😤😤😤
            ";
                break;
        }
        return $message;
    }

}
