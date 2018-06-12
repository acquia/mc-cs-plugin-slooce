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

use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidMessageArgumentsException;

/**
 * Class Message
 *
 * @package MauticPlugin\MauticSlooceTransportBundle\Slooce
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
     * The maximum allowed size of the message's string
     */
    const MAXIMUM_LENGTH = 160;

    /**
     * @return array
     */
    public function getSerializable()
    : array
    {
        return ['content' => $this->getContent()];
    }

    /**
     * @return string
     */
    public function getContent()
    : string
    {
        return $this->content;
    }

    /**
     * @param $content
     *
     * @return MtMessage
     * @throws InvalidMessageArgumentsException
     */
    public function setContent($content)
    : MtMessage
    {
        if (strlen($content) > self::MAXIMUM_LENGTH) {
            throw new InvalidMessageArgumentsException('Message may not be longer than ' . self::MAXIMUM_LENGTH . ' characters');
        }
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
     *
     * @return MtMessage
     */
    public function setUserId($userId)
    : MtMessage
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
     *
     * @return MtMessage
     */
    public function setKeyword($keyword)
    : MtMessage
    {
        $this->keyword = $keyword;

        return $this;
    }
}