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

namespace MauticPlugin\MauticSlooceTransportBundle\Message\Validator;

use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidMessageArgumentsException;
use MauticPlugin\MauticSlooceTransportBundle\Message\MtMessage;

/**
 * Class MessageContentValidator
 *
 * It may be worth creating a service but right now I choose singleton
 *
 * @package MauticPlugin\MauticSlooceTransportBundle\Message\Validator
 */
class MessageContentValidator
{
    const MAX_CONTENT_LENGTH = 160;
    const VALID_CHARACTERS = 'A-Za-z0-9\ @$_\/.,\'"():;\-=+*&%#!\\?<>';

    /**
     * @param MtMessage $message
     *
     * @return MtMessage
     * @throws InvalidMessageArgumentsException
     */
    public static function validate(MtMessage $message) : MtMessage
    {
        $content = $message->encodeStringToProviderEncoding($message->getContent());

        if (mb_strlen($content)>self::MAX_CONTENT_LENGTH) {
            throw new InvalidMessageArgumentsException('Message content is too long. Maximum is ' . self::MAX_CONTENT_LENGTH . " characters");
        }

        self::validate($content);

        return $message;
    }

    /**
     * @param $message
     *
     * @see Supported Characters
     * Generally speaking, only a subset of the standard ASCII character set is supported for content being
     * delivered to the user via SMS. The list of supported characters are A-Z, a-z, 0-9 and the following:
     * @$_/.,"():;-=+*&%#!?<>' plus space and newline "\n".
     * Most special characters are not supported and will cause messages to be rejected by the wireless
     * operators. In particular, accented characters and the following are NOT supported: tab [ ] ~ { } ^ | € \
     * When authoring content for delivery via SMS, it is also important to use the simple ASCII characters
     * for the apostrophe, the ellipsis, and single and double quotes:
     * use ' instead of  <`> and <’>
     * use " instead of  <“> and <”>
     * use ... instead of ...   (Note: that's three separate periods instead of the single ellipsis character)
     * @throws InvalidMessageArgumentsException
     *
     * @return void
     */
    public static function validateString($content)
    {
        $matches = null;
        $regexp = '|^[' . self::VALID_CHARACTERS . ']+$|';

        if (!preg_match($regexp, $content, $matches)) {
            throw new InvalidMessageArgumentsException("Message content contains invalid characters");
        }
    }
}