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
 * Class MessageContentValidator.
 *
 * It may be worth creating a service but right now I choose singleton
 */
class MessageContentValidator
{
    const MAX_CONTENT_LENGTH = 160;
    const VALID_CHARACTERS   = 'A-Za-z0-9\ @$_\/.,\'"():;\-=+*&%#!\\?<>';

    /**
     * @param MtMessage $message
     *
     * @return MtMessage
     *
     * @throws InvalidMessageArgumentsException
     */
    public static function validate(MtMessage $message): MtMessage
    {
        if (is_null($message->getKeyword())) {
            throw new InvalidMessageArgumentsException('Message has no keyword set.');
        }

        $content = $message->encodeStringToProviderEncoding($message->getContent());

        if (mb_strlen($content) > self::MAX_CONTENT_LENGTH) {
            throw new InvalidMessageArgumentsException('Message content is too long. Maximum is '.self::MAX_CONTENT_LENGTH.' characters');
        }

        return $message;
    }
}
