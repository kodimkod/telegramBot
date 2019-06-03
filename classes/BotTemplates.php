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
     * @param bool $isFriendGroup
     * @return string
     */
    public function getWelcomeMessage(array $newUser, ContentExtractor $extractor, $isFriendGroup): string
    {
        if ($isFriendGroup == true) {
            return $this->getWelcomeMessageForFriendGroup($newUser, $extractor);
        }
        return $this->getWelcomeMessageForOwnGroup($newUser, $extractor);
    }

    /**
     * @param array $newUser
     * @param ContentExtractor $extractor
     * @return string
     */
    public function getWelcomeMessageForOwnGroup(array $newUser, ContentExtractor $extractor): string
    {
        $name = $extractor->getNameFromUser($newUser);
        $id = $extractor->getIdFromUser($newUser);
        $message = "
      ❀͍͓※͍͓❀͍͓[" . $this->configuration->getWelcomeUserName() .
                "](tg://user?id=" . $this->configuration->getWelcomeUserId() . ")❀͍͓※͍͓❀͍͓:
ПРИВЕТИК [" . $name . "](tg://user?id=" . $id .
                ") ☺️😘!   Пусть счастье ❤️будет без всяких условий: без скобок,😊 кавычек,😊 пробелов 😊и точек. Цените тех, с кем можно быть собой.🤗 Без масок,😉 недомолвок😉 и амбиций…😊
▁▂▃▅▆█ДОБРО ПОЖАЛОВАТЬ✌️ МЫ РАДЫ 😁
                            КАЖДОМУ ИЗ ВАС!😁█▆▅▃▂▁
╔══╗ 
╚╗╔╝ 
╔╝(¯v´¯) 
╚══.¸.´you❤️❤️😊😘
            ";
        return $message;
    }

    /**
     * @param array $newUser
     * @param ContentExtractor $extractor
     * @return string
     */
    public function getWelcomeMessageForFriendGroup(array $newUser, ContentExtractor $extractor): string
    {
        $name = $extractor->getNameFromUser($newUser);
        $id = $extractor->getIdFromUser($newUser);
        $message = "
      ❀͍͓※͍͓❀͍͓❀͍͓※͍͓❀͍͓:
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

    /**
     * @param array $bannedUser
     * @param ContentExtractor $extractor
     * @return string
     */
    public function getForwardUserText($bannedUser, ContentExtractor $extractor): string
    {
        $name = $extractor->getNameFromUser($bannedUser);
        $id = $extractor->getIdFromUser($bannedUser);
        $rand = rand(1, 2);
        switch ($rand) {
            case 1:
                $message = "
     [" . $name . "](tg://user?id=" . $id . "), не кидай репосты из каналов, верблюд! Пенделя получишь!  😡
            ";
                break;
            case 2:
            default:
                $message = "
      [" . $name . "](tg://user?id=" . $id . "), не кидай рекламу чатов, чертила! Щас в бан улетишь! 😡
            ";
                break;
        }
        return $message;
    }

    /**
     * @return string
     */
    public function getChannelPostFooter($channelId): string
    {
        $footer = ' 
️<a href="' . $this->configuration->getChannelFooterLink($channelId) . '">' . $this->configuration->getChannelFooterText($channelId) . '</a>';
        return $footer;
    }

    /**
     * @param int | null $likes
     * @param int | null $dislikes
     * @return string
     */
    public function getChannelFooterKeyboard($likes = null, $dislikes = null): string
    {
        return '';
        if ($likes !== null) {
            $likes = ' ' . $likes;
        }
        if ($dislikes !== null) {
            $dislikes = ' ' . $dislikes;
        }
        $keyboard = ["inline_keyboard" => [[
            [
                "text" => "👍" . $likes,
                'callback_data' => 'channel_like'
            ],
            [
                "text" => "💔" . $dislikes,
                'callback_data' => 'channel_dislike'
            ]
                ],
                [
                    [
                        "text" => $this->configuration->getFooterText2(),
                        "url" => $this->configuration->getFooterLink2()
                    ]
                ]
            ]
        ];
        $keyboard = json_encode($keyboard);
        return $keyboard;
    }

}
