<?php

declare(strict_types=1);

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @author      Jan Kozak <galvani78@gmail.com>
 */

namespace MauticPlugin\MauticSlooceTransportBundle\Message;

/**
 * Class Message.
 *
 * @example
 * <message id="abcdef123">
 * <partnerpassword>jTUWufdis</partnerpassword>
 * <content>Correct! Mount Everest is the tallest peak in the world.</content>
 * </message>
 */
class MtMessage extends AbstractMessage
{
    /**
     * @var string The String to be sent in a message
     */
    private $content;

    /**
     * @var string The Phone number without leading plus sign
     */
    private $userId;

    /**
     * @var string The project identifier from the plugin's configuration
     */
    private $keyword;

    /**
     * The maximum allowed size of the message's string.
     */
    public const MAXIMUM_LENGTH = 160;

    public function getSerializable(): array
    {
        return ['content' => $this->getContent()];
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param $content
     */
    public function setContent($content): MtMessage
    {
        // Because this is XML based, these characters must be encoded based on Slooce docs
        $content = htmlspecialchars($content, ENT_QUOTES | ENT_HTML5);

        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId): MtMessage
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     */
    public function setKeyword($keyword): MtMessage
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getSanitizedArray(): array
    {
        $output            = parent::getSanitizedArray();
        $output['userId']  = $this->getUserId();
        $output['keyword'] = $this->getKeyword();

        return $output;
    }
}
